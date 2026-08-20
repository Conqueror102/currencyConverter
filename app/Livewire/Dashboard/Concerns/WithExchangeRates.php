<?php

namespace App\Livewire\Dashboard\Concerns;

use App\Domain\ExchangeRates\Services\RateSyncService;
use App\Domain\ExchangeRates\Support\CurrencyHelper;
use App\Models\ApiKey;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\RateSyncRun;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;

trait WithExchangeRates
{
    public string $marketFilter = 'all';

    public string $baseFilter = 'all';

    public string $search = '';

    // Rate Edit Modal
    public ?int $editingRateId = null;

    public string $editingRateVal = '';

    public string $editingBuyRateVal = '';

    public string $editingSellRateVal = '';

    public string $editingPairLabel = '';

    /**
     * Trigger synchronization for official central bank rates.
     */
    public function syncOfficial(): void
    {
        abort_unless($this->isAdmin, 403, 'Unauthorized. Administrator privileges required.');

        $this->statusMessage = null;
        $this->errorMessage = null;

        try {
            /** @var RateSyncService $syncService */
            $syncService = app(RateSyncService::class);
            $res = $syncService->syncLatestRates('frankfurter', 'EUR');
            $count = $res['saved'] ?? 0;
            $this->statusMessage = "Successfully synchronized {$count} official central bank rates from Frankfurter.";
        } catch (\Throwable) {
            $this->errorMessage = 'Failed to sync official rates. Please check network connection or rate provider.';
        }
    }

    /**
     * Trigger synchronization for parallel/street market rates.
     */
    public function syncParallel(): void
    {
        abort_unless($this->isAdmin, 403, 'Unauthorized. Administrator privileges required.');

        $this->statusMessage = null;
        $this->errorMessage = null;

        try {
            /** @var RateSyncService $syncService */
            $syncService = app(RateSyncService::class);
            $res = $syncService->syncParallelRates('parallel_p2p');
            $count = $res['saved'] ?? 0;
            $this->statusMessage = "Successfully synchronized {$count} parallel/street market rates from AbokiFX and Binance P2P.";
        } catch (\Throwable) {
            $this->errorMessage = 'Failed to sync parallel rates. Please check network connection or rate provider.';
        }
    }

    /**
     * Toggle a currency active status.
     */
    public function toggleCurrency(string $code): void
    {
        abort_unless($this->isAdmin, 403, 'Unauthorized. Administrator privileges required.');

        $currency = Currency::find($code);
        if ($currency) {
            $currency->update(['is_active' => ! $currency->is_active]);
            Cache::forget('currencies:active');
            $this->statusMessage = "Currency {$code} status updated.";
        }
    }

    /**
     * Open rate override editor.
     */
    public function editRate(int $id): void
    {
        abort_unless($this->isAdmin, 403, 'Unauthorized. Administrator privileges required.');

        $rate = ExchangeRate::find($id);
        if ($rate) {
            $this->editingRateId = $rate->id;
            $this->editingRateVal = (string) $rate->rate;
            $this->editingBuyRateVal = (string) ($rate->buy_rate ?? '');
            $this->editingSellRateVal = (string) ($rate->sell_rate ?? '');
            $this->editingPairLabel = "{$rate->base_currency} / {$rate->quote_currency} ({$rate->market_type})";
        }
    }

    /**
     * Save rate override.
     */
    public function saveRateOverride(): void
    {
        abort_unless($this->isAdmin, 403, 'Unauthorized. Administrator privileges required.');

        $this->validate([
            'editingRateVal' => 'required|numeric|gt:0',
            'editingBuyRateVal' => 'nullable|numeric|gt:0',
            'editingSellRateVal' => 'nullable|numeric|gt:0',
        ]);

        $rate = ExchangeRate::find($this->editingRateId);
        if ($rate) {
            $rate->update([
                'rate' => $this->editingRateVal,
                'buy_rate' => ! empty($this->editingBuyRateVal) ? $this->editingBuyRateVal : null,
                'sell_rate' => ! empty($this->editingSellRateVal) ? $this->editingSellRateVal : null,
                'fetched_at' => now(),
            ]);

            Cache::flush();
            $this->statusMessage = "Exchange rate {$this->editingPairLabel} manually updated.";
            $this->editingRateId = null;
        }
    }

    public function cancelEditRate(): void
    {
        $this->editingRateId = null;
    }

    /**
     * Top-level summary statistics.
     */
    #[Computed]
    public function stats(): array
    {
        $totalRates = ExchangeRate::count();
        $parallelRates = ExchangeRate::where('market_type', 'parallel')->count();
        $activeCurrencies = Currency::where('is_active', true)->count();
        $totalApiKeys = ApiKey::where('is_active', true)->count();
        $lastSync = RateSyncRun::latest('started_at')->first();

        return [
            'total_rates' => $totalRates,
            'parallel_rates' => $parallelRates,
            'active_currencies' => $activeCurrencies,
            'total_api_keys' => $totalApiKeys,
            'last_sync_status' => $lastSync?->status ?? 'IDLE',
            'last_sync_time' => $lastSync?->started_at?->diffForHumans() ?? 'Never',
        ];
    }

    /**
     * Exchange rates query.
     */
    #[Computed]
    public function exchangeRates(): array
    {
        $query = ExchangeRate::query()->orderByDesc('fetched_at');

        if ($this->marketFilter !== 'all') {
            $query->where('market_type', $this->marketFilter);
        }

        if ($this->baseFilter !== 'all') {
            $query->where('base_currency', $this->baseFilter);
        }

        if (! empty($this->search)) {
            $s = strtoupper(trim($this->search));
            $query->where(function ($q) use ($s) {
                $q->where('base_currency', 'like', "%{$s}%")
                    ->orWhere('quote_currency', 'like', "%{$s}%")
                    ->orWhere('provider', 'like', "%{$s}%");
            });
        }

        return $query->take(40)->get()->map(fn (ExchangeRate $r) => [
            'id' => $r->id,
            'base' => $r->base_currency,
            'quote' => $r->quote_currency,
            'base_flag' => CurrencyHelper::flag($r->base_currency),
            'quote_flag' => CurrencyHelper::flag($r->quote_currency),
            'market_type' => $r->market_type,
            'rate' => number_format((float) $r->rate, 4),
            'buy_rate' => $r->buy_rate ? number_format((float) $r->buy_rate, 2) : '—',
            'sell_rate' => $r->sell_rate ? number_format((float) $r->sell_rate, 2) : '—',
            'provider' => $r->provider,
            'fetched_at' => $r->fetched_at ? $r->fetched_at->diffForHumans() : '—',
        ])->all();
    }

    /**
     * Available base currencies for filtering.
     */
    #[Computed]
    public function availableBases(): array
    {
        return ExchangeRate::distinct('base_currency')->pluck('base_currency')->all();
    }

    /**
     * Currencies query.
     */
    #[Computed]
    public function currenciesList(): array
    {
        $query = Currency::query()->orderBy('code');

        if (! empty($this->search)) {
            $s = strtoupper(trim($this->search));
            $query->where('code', 'like', "%{$s}%")
                ->orWhere('name', 'like', "%{$s}%");
        }

        return $query->take(50)->get()->map(fn (Currency $c) => [
            'code' => $c->code,
            'name' => $c->name,
            'flag' => CurrencyHelper::flag($c->code),
            'symbol' => $c->symbol ?? CurrencyHelper::symbol($c->code),
            'minor_unit' => $c->minor_unit,
            'is_active' => $c->is_active,
        ])->all();
    }

    /**
     * Sync history query.
     */
    #[Computed]
    public function syncRunsList(): array
    {
        return RateSyncRun::latest('started_at')->take(20)->get()->map(fn (RateSyncRun $run) => [
            'id' => $run->id,
            'provider' => $run->provider,
            'sync_type' => $run->sync_type,
            'status' => $run->status,
            'records_saved' => $run->records_saved,
            'duration' => $run->completed_at ? $run->started_at->diffInMilliseconds($run->completed_at).'ms' : '—',
            'started_at' => $run->started_at->diffForHumans(),
            'error_message' => $run->error_message,
        ])->all();
    }
}
