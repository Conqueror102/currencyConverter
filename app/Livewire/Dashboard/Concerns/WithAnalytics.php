<?php

namespace App\Livewire\Dashboard\Concerns;

use App\Domain\ExchangeRates\Support\CurrencyHelper;
use App\Models\ConversionLog;
use App\Models\PageView;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Computed;

trait WithAnalytics
{
    public string $activityStreamTab = 'conversions';

    /**
     * Check if analytics tables exist.
     */
    #[Computed]
    public function hasAnalyticsTables(): bool
    {
        try {
            return Schema::hasTable('page_views') && Schema::hasTable('conversion_logs');
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Analytics high-level KPIs.
     */
    #[Computed]
    public function analyticsKPIs(): array
    {
        if (! $this->hasAnalyticsTables) {
            return [
                'total_visits' => '0',
                'unique_visitors' => '0',
                'today_visits' => 0,
                'total_conversions' => '0',
                'today_conversions' => 0,
                'web_conversions' => 0,
                'api_conversions' => 0,
                'parallel_share' => 0,
                'top_pair' => 'USD/NGN',
                'top_pair_percent' => 0,
            ];
        }

        try {
            $totalVisits = PageView::count();
            $uniqueVisitors = PageView::distinct('ip_address')->count('ip_address');
            $todayVisits = PageView::whereDate('created_at', Carbon::today())->count();

            $totalConversions = ConversionLog::count();
            $todayConversions = ConversionLog::whereDate('created_at', Carbon::today())->count();
            $apiConversions = ConversionLog::where('source', 'api')->count();
            $webConversions = ConversionLog::where('source', 'web_ui')->count();

            $parallelConversions = ConversionLog::where('market_type', 'parallel')->count();
            $parallelShare = $totalConversions > 0 ? round(($parallelConversions / $totalConversions) * 100) : 0;

            $topPairRecord = ConversionLog::select('base_currency', 'quote_currency', DB::raw('count(*) as count'))
                ->groupBy('base_currency', 'quote_currency')
                ->orderByDesc('count')
                ->first();

            $topPair = $topPairRecord ? "{$topPairRecord->base_currency}/{$topPairRecord->quote_currency}" : 'USD/NGN';
            $topPairPercent = $totalConversions > 0 && $topPairRecord ? round(($topPairRecord->count / $totalConversions) * 100) : 0;

            return [
                'total_visits' => number_format($totalVisits),
                'unique_visitors' => number_format($uniqueVisitors),
                'today_visits' => $todayVisits,
                'total_conversions' => number_format($totalConversions),
                'today_conversions' => $todayConversions,
                'web_conversions' => $webConversions,
                'api_conversions' => $apiConversions,
                'parallel_share' => $parallelShare,
                'top_pair' => $topPair,
                'top_pair_percent' => $topPairPercent,
            ];
        } catch (\Throwable) {
            return [
                'total_visits' => '0',
                'unique_visitors' => '0',
                'today_visits' => 0,
                'total_conversions' => '0',
                'today_conversions' => 0,
                'web_conversions' => 0,
                'api_conversions' => 0,
                'parallel_share' => 0,
                'top_pair' => 'USD/NGN',
                'top_pair_percent' => 0,
            ];
        }
    }

    /**
     * 14-Day Activity Time Series Data for Interactive Bar Graph.
     */
    #[Computed]
    public function activityChart(): array
    {
        if (! $this->hasAnalyticsTables) {
            return [];
        }

        try {
            $days = [];
            $maxVal = 1;

            for ($i = 13; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $dateStr = $date->toDateString();

                $visits = PageView::whereDate('created_at', $dateStr)->count();
                $conversions = ConversionLog::whereDate('created_at', $dateStr)->count();

                $maxVal = max($maxVal, $visits, $conversions);

                $days[] = [
                    'date_label' => $date->format('M d'),
                    'is_today' => $i === 0,
                    'visits' => $visits,
                    'conversions' => $conversions,
                ];
            }

            return array_map(function ($item) use ($maxVal) {
                $item['visits_percent'] = max(6, round(($item['visits'] / $maxVal) * 100));
                $item['conversions_percent'] = max(6, round(($item['conversions'] / $maxVal) * 100));

                return $item;
            }, $days);
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Top 5 Most Converted Currency Pairs.
     */
    #[Computed]
    public function topPairs(): array
    {
        if (! $this->hasAnalyticsTables) {
            return [];
        }

        try {
            $total = ConversionLog::count() ?: 1;

            return ConversionLog::select('base_currency', 'quote_currency', DB::raw('count(*) as total_count'))
                ->groupBy('base_currency', 'quote_currency')
                ->orderByDesc('total_count')
                ->take(5)
                ->get()
                ->map(function ($row) use ($total) {
                    return [
                        'base' => $row->base_currency,
                        'quote' => $row->quote_currency,
                        'base_flag' => CurrencyHelper::flag($row->base_currency),
                        'quote_flag' => CurrencyHelper::flag($row->quote_currency),
                        'count' => $row->total_count,
                        'percentage' => round(($row->total_count / $total) * 100),
                    ];
                })->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Market Intent & Device Breakdown.
     */
    #[Computed]
    public function breakdownMetrics(): array
    {
        if (! $this->hasAnalyticsTables) {
            return [
                'actions' => ['mid' => 0, 'buy' => 0, 'sell' => 0],
                'devices' => ['desktop' => 0, 'mobile' => 0, 'tablet' => 0],
            ];
        }

        try {
            $totalConversions = ConversionLog::count() ?: 1;
            $mid = ConversionLog::where('trade_action', 'mid')->count();
            $buy = ConversionLog::where('trade_action', 'buy')->count();
            $sell = ConversionLog::where('trade_action', 'sell')->count();

            $totalViews = PageView::count() ?: 1;
            $desktop = PageView::where('device_type', 'desktop')->count();
            $mobile = PageView::where('device_type', 'mobile')->count();
            $tablet = PageView::where('device_type', 'tablet')->count();

            return [
                'actions' => [
                    'mid' => round(($mid / $totalConversions) * 100),
                    'buy' => round(($buy / $totalConversions) * 100),
                    'sell' => round(($sell / $totalConversions) * 100),
                ],
                'devices' => [
                    'desktop' => round(($desktop / $totalViews) * 100),
                    'mobile' => round(($mobile / $totalViews) * 100),
                    'tablet' => round(($tablet / $totalViews) * 100),
                ],
            ];
        } catch (\Throwable) {
            return [
                'actions' => ['mid' => 0, 'buy' => 0, 'sell' => 0],
                'devices' => ['desktop' => 0, 'mobile' => 0, 'tablet' => 0],
            ];
        }
    }

    /**
     * Recent Live Conversions Stream.
     */
    #[Computed]
    public function recentConversions(): array
    {
        if (! $this->hasAnalyticsTables) {
            return [];
        }

        try {
            return ConversionLog::orderByDesc('id')->take(12)->get()->map(function (ConversionLog $log) {
                return [
                    'id' => $log->id,
                    'source' => $log->source,
                    'base' => $log->base_currency,
                    'quote' => $log->quote_currency,
                    'base_flag' => CurrencyHelper::flag($log->base_currency),
                    'quote_flag' => CurrencyHelper::flag($log->quote_currency),
                    'market_type' => $log->market_type,
                    'trade_action' => $log->trade_action,
                    'amount' => number_format((float) $log->amount, 2),
                    'rate' => number_format((float) $log->rate, 4),
                    'result' => number_format((float) $log->result, 2),
                    'time' => $log->created_at ? $log->created_at->diffForHumans() : 'Just now',
                ];
            })->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Top visited routes and page views distribution.
     */
    #[Computed]
    public function topVisitedPages(): array
    {
        if (! $this->hasAnalyticsTables) {
            return [];
        }

        try {
            $total = PageView::count() ?: 1;

            return PageView::select('path', DB::raw('count(*) as total_count'))
                ->groupBy('path')
                ->orderByDesc('total_count')
                ->take(5)
                ->get()
                ->map(function ($row) use ($total) {
                    $label = match ($row->path) {
                        '/' => 'Universal FX Converter (Home)',
                        '/docs', 'docs' => 'API Developer Documentation',
                        '/dashboard', 'dashboard' => 'Admin Control Center',
                        default => $row->path,
                    };

                    return [
                        'path' => $row->path,
                        'label' => $label,
                        'count' => $row->total_count,
                        'percentage' => round(($row->total_count / $total) * 100),
                    ];
                })->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Recent Live Page Views Stream.
     */
    #[Computed]
    public function recentPageViews(): array
    {
        if (! $this->hasAnalyticsTables) {
            return [];
        }

        try {
            return PageView::orderByDesc('id')->take(12)->get()->map(function (PageView $view) {
                return [
                    'id' => $view->id,
                    'path' => $view->path,
                    'ip' => $view->ip_address ? substr($view->ip_address, 0, 8).'•••' : '127.0.0.1',
                    'device' => $view->device_type,
                    'browser' => $view->browser,
                    'country' => $view->country_code ?? 'US',
                    'time' => $view->created_at ? $view->created_at->diffForHumans() : 'Just now',
                ];
            })->all();
        } catch (\Throwable) {
            return [];
        }
    }
}
