<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\ExchangeRates\Exceptions\CurrencyNotFoundException;
use App\Domain\ExchangeRates\Exceptions\RateNotFoundException;
use App\Domain\ExchangeRates\Services\ConversionService;
use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RateController extends Controller
{
    public function __construct(private readonly ConversionService $conversionService) {}

    /**
     * Get latest exchange rates for a base currency and market.
     */
    public function latest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'base' => ['nullable', 'string', 'size:3'],
            'symbols' => ['nullable', 'string'],
            'market' => ['nullable', 'string', 'in:official,parallel'],
        ]);

        $base = strtoupper($validated['base'] ?? 'EUR');
        $market = strtolower($validated['market'] ?? 'official');
        $symbols = ! empty($validated['symbols'])
            ? array_map('trim', explode(',', $validated['symbols']))
            : [];

        try {
            $ratesData = $this->conversionService->getLatestRates(
                base: $base,
                symbols: $symbols,
                marketType: $market,
            );

            return response()->json([
                'data' => [
                    'base' => $ratesData['base'],
                    'date' => $ratesData['date'],
                    'rates' => $ratesData['rates'],
                ],
                'meta' => [
                    'market_type' => $ratesData['market'] ?? $market,
                    'provider' => $ratesData['provider'],
                    'count' => count($ratesData['rates']),
                ],
            ]);
        } catch (CurrencyNotFoundException $e) {
            return response()->json([
                'error' => [
                    'code' => 'CURRENCY_NOT_FOUND',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Get a specific currency pair rate.
     */
    public function pair(Request $request, string $base, string $quote): JsonResponse
    {
        $dateStr = $request->query('date');
        $market = strtolower((string) $request->query('market', 'official'));
        if (! in_array($market, ['official', 'parallel'], true)) {
            $market = 'official';
        }
        $date = $dateStr ? CarbonImmutable::parse($dateStr) : null;

        try {
            $conversion = $this->conversionService->convert(
                from: $base,
                to: $quote,
                amount: '1.0',
                date: $date,
                marketType: $market,
            );

            return response()->json([
                'data' => [
                    'base' => $conversion->from,
                    'quote' => $conversion->to,
                    'rate' => $conversion->rate,
                    'inverse_rate' => $conversion->inverseRate,
                    'rate_date' => $conversion->rateDate->toDateString(),
                ],
                'meta' => [
                    'market_type' => $conversion->marketType,
                    'official_rate' => $conversion->officialRate,
                    'spread_percentage' => $conversion->spreadPercentage,
                    'provider' => $conversion->provider,
                    'is_derived' => $conversion->isDerived,
                    'is_stale' => $conversion->isStale,
                ],
            ]);
        } catch (CurrencyNotFoundException $e) {
            return response()->json([
                'error' => [
                    'code' => 'CURRENCY_NOT_FOUND',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_NOT_FOUND);
        } catch (RateNotFoundException $e) {
            return response()->json([
                'error' => [
                    'code' => 'RATE_NOT_AVAILABLE',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Get historical point-in-time snapshot.
     */
    public function historical(Request $request, string $date): JsonResponse
    {
        $parsedDate = CarbonImmutable::parse($date);
        $base = strtoupper($request->query('base', 'EUR'));
        $market = strtolower((string) $request->query('market', 'official'));
        $symbols = $request->query('symbols')
            ? array_map('trim', explode(',', (string) $request->query('symbols')))
            : [];

        $currencies = $this->conversionService->getActiveCurrencies();
        $rates = [];

        foreach ($currencies as $currency) {
            $code = $currency->code;
            if (! empty($symbols) && ! in_array($code, $symbols, true)) {
                continue;
            }

            if ($code === $base) {
                $rates[$code] = '1.000000';

                continue;
            }

            try {
                $res = $this->conversionService->convert(
                    from: $base,
                    to: $code,
                    amount: '1.0',
                    date: $parsedDate,
                    marketType: $market,
                );
                $rates[$code] = $res->rate;
            } catch (RateNotFoundException) {
                continue;
            }
        }

        return response()->json([
            'data' => [
                'base' => $base,
                'date' => $parsedDate->toDateString(),
                'rates' => $rates,
            ],
            'meta' => [
                'market_type' => $market,
                'count' => count($rates),
            ],
        ]);
    }

    /**
     * Get timeseries rates for charting.
     */
    public function timeseries(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['required', 'date_format:Y-m-d'],
            'to' => ['required', 'date_format:Y-m-d', 'after_or_equal:from'],
            'base' => ['nullable', 'string', 'size:3'],
            'symbols' => ['nullable', 'string'],
            'market' => ['nullable', 'string', 'in:official,parallel'],
        ]);

        $from = CarbonImmutable::parse($validated['from']);
        $to = CarbonImmutable::parse($validated['to']);
        $base = strtoupper($validated['base'] ?? 'EUR');
        $market = strtolower($validated['market'] ?? 'official');
        $symbols = ! empty($validated['symbols'])
            ? array_map('trim', explode(',', $validated['symbols']))
            : ['USD', 'GBP', 'JPY'];

        $storedRates = ExchangeRate::where('base_currency', $base)
            ->where('market_type', $market)
            ->whereIn('quote_currency', $symbols)
            ->whereBetween('rate_date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('rate_date')
            ->get()
            ->groupBy(fn (ExchangeRate $r) => $r->rate_date->toDateString());

        $series = [];
        foreach ($storedRates as $dateKey => $records) {
            $dateRates = [];
            foreach ($records as $record) {
                $dateRates[$record->quote_currency] = (string) round((float) $record->rate, 6);
            }
            $series[$dateKey] = $dateRates;
        }

        return response()->json([
            'data' => [
                'base' => $base,
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'series' => $series,
            ],
            'meta' => [
                'market_type' => $market,
                'days' => count($series),
            ],
        ]);
    }
}
