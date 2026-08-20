<?php

namespace App\Domain\ExchangeRates\Services;

use App\Domain\ExchangeRates\Data\ConversionResult;
use App\Domain\ExchangeRates\Exceptions\CurrencyNotFoundException;
use App\Domain\ExchangeRates\Exceptions\RateNotFoundException;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ConversionService
{
    private const INTERNAL_SCALE = 15;

    public function __construct(
        private readonly ?RateSyncService $rateSyncService = null,
    ) {}

    /**
     * Convert an amount from one currency to another with high precision.
     *
     * @throws CurrencyNotFoundException|RateNotFoundException
     */
    public function convert(
        string $from,
        string $to,
        string|float|int $amount,
        ?CarbonImmutable $date = null,
        ?string $provider = null,
        string $marketType = 'official',
        string $tradeAction = 'mid'
    ): ConversionResult {
        $from = strtoupper(trim($from));
        $to = strtoupper(trim($to));
        $marketType = strtolower(trim($marketType));
        if (! in_array($marketType, ['official', 'parallel'], true)) {
            $marketType = 'official';
        }
        $tradeAction = strtolower(trim($tradeAction));
        if (! in_array($tradeAction, ['mid', 'buy', 'sell'], true)) {
            $tradeAction = 'mid';
        }

        $fromCurrency = $this->getCurrency($from);
        $toCurrency = $this->getCurrency($to);

        $parsedAmount = BigDecimal::of((string) $amount);
        $canonicalBase = (string) config('exchange-rates.canonical_base', 'EUR');

        // Case 1: Same currency conversion
        if ($from === $to) {
            $formattedAmount = (string) $parsedAmount->toScale($fromCurrency->minor_unit, RoundingMode::HalfUp);
            $now = CarbonImmutable::now();

            return new ConversionResult(
                from: $from,
                to: $to,
                amount: $formattedAmount,
                rate: '1.000000',
                inverseRate: '1.000000',
                result: $formattedAmount,
                rateDate: $date ?? CarbonImmutable::today(),
                provider: $provider ?? (string) config('exchange-rates.default', 'frankfurter'),
                marketType: $marketType,
                tradeAction: $tradeAction,
                isDerived: false,
                isStale: false,
                fetchedAt: $now,
                ageDays: 0,
            );
        }

        // Resolve rate and provenance for requested market
        $rateInfo = $this->resolveRate($from, $to, $canonicalBase, $date, $provider, $marketType);

        // Select rate based on trade action (Mid vs Buy vs Sell)
        $effectiveRateStr = $rateInfo['rate'];
        if ($tradeAction === 'buy' && ! empty($rateInfo['sell_rate'])) {
            $effectiveRateStr = $rateInfo['sell_rate'];
        } elseif ($tradeAction === 'sell' && ! empty($rateInfo['buy_rate'])) {
            $effectiveRateStr = $rateInfo['buy_rate'];
        }

        $rateDecimal = BigDecimal::of($effectiveRateStr);
        $inverseDecimal = BigDecimal::of(1)->dividedBy($rateDecimal, self::INTERNAL_SCALE, RoundingMode::HalfUp);

        // High-precision monetary multiplication
        $resultDecimal = $parsedAmount->multipliedBy($rateDecimal)
            ->toScale($toCurrency->minor_unit, RoundingMode::HalfUp);

        $formattedAmount = (string) $parsedAmount->toScale($fromCurrency->minor_unit, RoundingMode::HalfUp);

        $rateDate = CarbonImmutable::parse($rateInfo['rate_date']);
        $ageDays = (int) $rateDate->diffInDays(CarbonImmutable::today());
        $isStale = $ageDays > 4;

        $officialRateStr = null;
        $spreadPercentage = null;
        $bidAskSpreadStr = null;

        // Calculate Bid/Ask spread string if buy & sell are present
        if (! empty($rateInfo['buy_rate']) && ! empty($rateInfo['sell_rate'])) {
            $buyDec = BigDecimal::of($rateInfo['buy_rate']);
            $sellDec = BigDecimal::of($rateInfo['sell_rate']);
            $spreadDiff = $sellDec->minus($buyDec);
            if (! $buyDec->isZero()) {
                $pct = $spreadDiff->dividedBy($buyDec, 4, RoundingMode::HalfUp)->multipliedBy(BigDecimal::of(100));
                $bidAskSpreadStr = (string) $spreadDiff->toScale(2, RoundingMode::HalfUp).' ('.number_format((float) (string) $pct, 2).'%)';
            }
        }

        // If market is parallel, calculate official rate comparison
        if ($marketType === 'parallel') {
            try {
                $officialInfo = $this->resolveRate($from, $to, $canonicalBase, $date, null, 'official');
                $officialDec = BigDecimal::of($officialInfo['rate']);
                $officialRateStr = (string) $officialDec->toScale(6, RoundingMode::HalfUp);

                if (! $officialDec->isZero()) {
                    $diff = $rateDecimal->minus($officialDec);
                    $spreadDec = $diff->dividedBy($officialDec, 6, RoundingMode::HalfUp)->multipliedBy(BigDecimal::of(100));
                    $spreadVal = (float) (string) $spreadDec->toScale(2, RoundingMode::HalfUp);
                    $spreadPercentage = ($spreadVal >= 0 ? '+' : '').number_format($spreadVal, 2).'%';
                }
            } catch (RateNotFoundException) {
                // Ignore official comparison if unavailable
            }
        }

        return new ConversionResult(
            from: $from,
            to: $to,
            amount: $formattedAmount,
            rate: (string) $rateDecimal->toScale(6, RoundingMode::HalfUp),
            inverseRate: (string) $inverseDecimal->toScale(6, RoundingMode::HalfUp),
            result: (string) $resultDecimal,
            rateDate: $rateDate,
            provider: $rateInfo['provider'],
            marketType: $marketType,
            officialRate: $officialRateStr,
            spreadPercentage: $spreadPercentage,
            buyRate: $rateInfo['buy_rate'] ?? null,
            sellRate: $rateInfo['sell_rate'] ?? null,
            tradeAction: $tradeAction,
            bidAskSpread: $bidAskSpreadStr,
            isDerived: $rateInfo['is_derived'],
            isStale: $isStale,
            fetchedAt: $rateInfo['fetched_at'] ? CarbonImmutable::parse($rateInfo['fetched_at']) : null,
            ageDays: $ageDays,
        );
    }

    /**
     * Get latest rates for a base currency against specified quote currencies.
     *
     * @param  array<int, string>  $symbols
     * @return array{base: string, date: string, market: string, provider: string, rates: array<string, string>}
     */
    public function getLatestRates(
        string $base,
        array $symbols = [],
        ?string $provider = null,
        string $marketType = 'official'
    ): array {
        $base = strtoupper(trim($base));
        $symbols = array_map('strtoupper', $symbols);
        $marketType = strtolower(trim($marketType));

        $cacheKey = "rates:latest:{$marketType}:{$base}:".implode(',', $symbols).':'.($provider ?? 'default');

        return Cache::remember($cacheKey, config('exchange-rates.cache_ttl', 3600), function () use ($base, $symbols, $provider, $marketType) {
            $canonicalBase = (string) config('exchange-rates.canonical_base', 'EUR');
            $currencies = $this->getActiveCurrencies();

            $rates = [];
            $rateDate = CarbonImmutable::today()->toDateString();
            $activeProvider = $provider ?? ($marketType === 'parallel' ? 'parallel_p2p' : (string) config('exchange-rates.default', 'frankfurter'));

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
                    $rateInfo = $this->resolveRate($base, $code, $canonicalBase, null, $provider, $marketType);
                    $rates[$code] = (string) BigDecimal::of($rateInfo['rate'])->toScale(6, RoundingMode::HalfUp);
                    $rateDate = $rateInfo['rate_date'];
                    $activeProvider = $rateInfo['provider'];
                } catch (RateNotFoundException) {
                    continue;
                }
            }

            return [
                'base' => $base,
                'date' => $rateDate,
                'market' => $marketType,
                'provider' => $activeProvider,
                'rates' => $rates,
            ];
        });
    }

    /**
     * Get active currencies collection (cached).
     *
     * @return Collection<int, Currency>
     */
    public function getActiveCurrencies(): Collection
    {
        try {
            $cached = Cache::get('currencies:active');
            if ($cached instanceof Collection && $cached->isNotEmpty() && $cached->first() instanceof Currency) {
                return $cached;
            }
        } catch (\Throwable) {
            // Re-fetch from database if cache corrupted
        }

        try {
            $currencies = Currency::where('is_active', true)->orderBy('code')->get();

            if ($currencies->isNotEmpty()) {
                Cache::put('currencies:active', $currencies, 86400);

                return $currencies;
            }
        } catch (\Throwable) {
            // Proceed to fallback
        }

        return collect([]);
    }

    /**
     * Find currency model by code with fallback to default structure.
     */
    private function getCurrency(string $code): Currency
    {
        $code = strtoupper(trim($code));

        try {
            $currency = $this->getActiveCurrencies()->firstWhere('code', $code);

            if ($currency) {
                return $currency;
            }

            $dbCurrency = Currency::find($code);
            if ($dbCurrency) {
                return $dbCurrency;
            }
        } catch (\Throwable) {
            // Proceed to in-memory fallback
        }

        $fallback = new Currency;
        $fallback->code = $code;
        $fallback->name = $code;
        $fallback->minor_unit = match ($code) {
            'JPY', 'KRW', 'UGX', 'VND', 'CLP', 'PYG', 'RWF' => 0,
            'BHD', 'IQD', 'JOD', 'KWD', 'LYD', 'OMR', 'TND' => 3,
            default => 2,
        };
        $fallback->is_active = true;

        return $fallback;
    }

    /**
     * Resolve exchange rate between two currencies (direct, inverted, or cross-derived).
     *
     * @return array{rate: string, inverse_rate: string, buy_rate: ?string, sell_rate: ?string, rate_date: string, provider: string, is_derived: bool, fetched_at: ?string}
     *
     * @throws RateNotFoundException
     */
    private function resolveRate(
        string $from,
        string $to,
        string $canonicalBase,
        ?CarbonImmutable $date,
        ?string $provider,
        string $marketType = 'official'
    ): array {
        // Direct pair lookup (from -> to)
        $directRate = $this->findStoredRate($from, $to, $date, $provider, $marketType);
        if ($directRate) {
            $rateDec = BigDecimal::of($directRate->rate);
            $invDec = BigDecimal::of(1)->dividedBy($rateDec, self::INTERNAL_SCALE, RoundingMode::HalfUp);

            return [
                'rate' => (string) $rateDec,
                'inverse_rate' => (string) $invDec,
                'buy_rate' => $directRate->buy_rate,
                'sell_rate' => $directRate->sell_rate,
                'rate_date' => $directRate->rate_date->toDateString(),
                'provider' => $directRate->provider,
                'is_derived' => false,
                'fetched_at' => $directRate->fetched_at?->toIso8601String(),
            ];
        }

        // Inverted direct lookup (to -> from)
        $invertedRate = $this->findStoredRate($to, $from, $date, $provider, $marketType);
        if ($invertedRate) {
            $invDirect = BigDecimal::of($invertedRate->rate);
            $rateDec = BigDecimal::of(1)->dividedBy($invDirect, self::INTERNAL_SCALE, RoundingMode::HalfUp);

            return [
                'rate' => (string) $rateDec,
                'inverse_rate' => (string) $invDirect,
                'buy_rate' => $invertedRate->sell_rate ? (string) BigDecimal::of(1)->dividedBy(BigDecimal::of($invertedRate->sell_rate), self::INTERNAL_SCALE, RoundingMode::HalfUp) : null,
                'sell_rate' => $invertedRate->buy_rate ? (string) BigDecimal::of(1)->dividedBy(BigDecimal::of($invertedRate->buy_rate), self::INTERNAL_SCALE, RoundingMode::HalfUp) : null,
                'rate_date' => $invertedRate->rate_date->toDateString(),
                'provider' => $invertedRate->provider,
                'is_derived' => false,
                'fetched_at' => $invertedRate->fetched_at?->toIso8601String(),
            ];
        }

        // Cross-rate derivation for Parallel Market using USD as the parallel bridge
        if ($marketType === 'parallel') {
            $parallelBridge = 'USD';

            // Check if from or to has direct parallel rate with USD
            $fromUsdParallel = $from === $parallelBridge ? BigDecimal::of(1) : $this->findStoredRate($parallelBridge, $from, $date, null, 'parallel');
            $toUsdParallel = $to === $parallelBridge ? BigDecimal::of(1) : $this->findStoredRate($parallelBridge, $to, $date, null, 'parallel');

            if ($toUsdParallel) {
                $p2pRate = $to === $parallelBridge ? BigDecimal::of(1) : BigDecimal::of($toUsdParallel->rate);

                if ($from === $parallelBridge) {
                    $invDec = BigDecimal::of(1)->dividedBy($p2pRate, self::INTERNAL_SCALE, RoundingMode::HalfUp);

                    return [
                        'rate' => (string) $p2pRate,
                        'inverse_rate' => (string) $invDec,
                        'buy_rate' => $toUsdParallel->buy_rate,
                        'sell_rate' => $toUsdParallel->sell_rate,
                        'rate_date' => $toUsdParallel->rate_date->toDateString(),
                        'provider' => $toUsdParallel->provider,
                        'is_derived' => false,
                        'fetched_at' => $toUsdParallel->fetched_at?->toIso8601String(),
                    ];
                }

                // Get From -> USD Official rate
                $officialFromUsd = $this->resolveRate($from, $parallelBridge, $canonicalBase, $date, null, 'official');
                $crossRate = BigDecimal::of($officialFromUsd['rate'])->multipliedBy($p2pRate);
                $invCross = BigDecimal::of(1)->dividedBy($crossRate, self::INTERNAL_SCALE, RoundingMode::HalfUp);

                $crossBuy = $toUsdParallel->buy_rate ? (string) BigDecimal::of($officialFromUsd['rate'])->multipliedBy(BigDecimal::of($toUsdParallel->buy_rate)) : null;
                $crossSell = $toUsdParallel->sell_rate ? (string) BigDecimal::of($officialFromUsd['rate'])->multipliedBy(BigDecimal::of($toUsdParallel->sell_rate)) : null;

                return [
                    'rate' => (string) $crossRate,
                    'inverse_rate' => (string) $invCross,
                    'buy_rate' => $crossBuy,
                    'sell_rate' => $crossSell,
                    'rate_date' => $toUsdParallel->rate_date->toDateString(),
                    'provider' => $toUsdParallel->provider,
                    'is_derived' => true,
                    'fetched_at' => $toUsdParallel->fetched_at?->toIso8601String(),
                ];
            }
        }

        // Cross-rate derivation through canonical base (usually EUR for Official rates)
        $baseToQuoteA = $from === $canonicalBase ? null : $this->findStoredRate($canonicalBase, $from, $date, $provider, $marketType);
        $baseToQuoteB = $to === $canonicalBase ? null : $this->findStoredRate($canonicalBase, $to, $date, $provider, $marketType);

        $rateA = $from === $canonicalBase ? BigDecimal::of(1) : ($baseToQuoteA ? BigDecimal::of($baseToQuoteA->rate) : null);
        $rateB = $to === $canonicalBase ? BigDecimal::of(1) : ($baseToQuoteB ? BigDecimal::of($baseToQuoteB->rate) : null);

        if ($rateA !== null && $rateB !== null && ! $rateA->isZero()) {
            $crossRate = $rateB->dividedBy($rateA, self::INTERNAL_SCALE, RoundingMode::HalfUp);
            $inverseCross = $rateA->dividedBy($rateB, self::INTERNAL_SCALE, RoundingMode::HalfUp);

            $provenance = $baseToQuoteB ?? $baseToQuoteA;

            return [
                'rate' => (string) $crossRate,
                'inverse_rate' => (string) $inverseCross,
                'buy_rate' => null,
                'sell_rate' => null,
                'rate_date' => $provenance?->rate_date->toDateString() ?? CarbonImmutable::today()->toDateString(),
                'provider' => $provenance?->provider ?? 'derived',
                'is_derived' => true,
                'fetched_at' => $provenance?->fetched_at?->toIso8601String(),
            ];
        }

        // Auto-seed rates on-demand if database is fresh
        if ($this->rateSyncService && $date === null) {
            if ($marketType === 'parallel') {
                $this->rateSyncService->syncParallelRates();
            } elseif (ExchangeRate::where('market_type', 'official')->count() === 0) {
                $this->rateSyncService->syncLatestRates($provider, $canonicalBase);
            }

            // Retry lookup
            $retryRate = $this->findStoredRate($from, $to, $date, $provider, $marketType);
            if ($retryRate) {
                $rateDec = BigDecimal::of($retryRate->rate);
                $invDec = BigDecimal::of(1)->dividedBy($rateDec, self::INTERNAL_SCALE, RoundingMode::HalfUp);

                return [
                    'rate' => (string) $rateDec,
                    'inverse_rate' => (string) $invDec,
                    'buy_rate' => $retryRate->buy_rate,
                    'sell_rate' => $retryRate->sell_rate,
                    'rate_date' => $retryRate->rate_date->toDateString(),
                    'provider' => $retryRate->provider,
                    'is_derived' => false,
                    'fetched_at' => $retryRate->fetched_at?->toIso8601String(),
                ];
            }
        }

        throw new RateNotFoundException($from, $to, $date?->toDateString());
    }

    /**
     * Find latest or point-in-time stored rate from database.
     */
    private function findStoredRate(
        string $base,
        string $quote,
        ?CarbonImmutable $date,
        ?string $provider,
        string $marketType = 'official'
    ): ?ExchangeRate {
        $query = ExchangeRate::where('base_currency', $base)
            ->where('quote_currency', $quote)
            ->where('market_type', $marketType);

        if ($provider) {
            $query->where('provider', $provider);
        }

        if ($date) {
            $query->where('rate_date', '<=', $date->toDateString())
                ->orderByDesc('rate_date');
        } else {
            $query->orderByDesc('rate_date');
        }

        return $query->first();
    }
}
