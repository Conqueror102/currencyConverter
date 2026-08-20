<?php

namespace App\Infrastructure\ExchangeRates\Frankfurter;

use App\Domain\ExchangeRates\Data\CurrencyData;
use App\Domain\ExchangeRates\Data\RateData;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class FrankfurterMapper
{
    /**
     * Map of ISO 4217 standard currency minor units (decimal places).
     *
     * @var array<string, int>
     */
    private const MINOR_UNITS = [
        'BIF' => 0, 'CLP' => 0, 'DJF' => 0, 'GNF' => 0, 'ISK' => 0, 'JPY' => 0,
        'KMF' => 0, 'KRW' => 0, 'PYG' => 0, 'RWF' => 0, 'UGX' => 0, 'UYI' => 0,
        'VND' => 0, 'VUV' => 0, 'XAF' => 0, 'XOF' => 0, 'XPF' => 0,
        'BHD' => 3, 'IQD' => 3, 'JOD' => 3, 'KWD' => 3, 'LYD' => 3, 'OMR' => 3,
        'TND' => 3,
        'CLF' => 4,
    ];

    /**
     * Map Frankfurter v2 currencies JSON array to CurrencyData DTO collection.
     *
     * @param  array<int, array<string, mixed>>  $currenciesResponse
     * @return Collection<int, CurrencyData>
     */
    public function mapCurrencies(array $currenciesResponse): Collection
    {
        return collect($currenciesResponse)->map(function (array $item): CurrencyData {
            $code = strtoupper((string) ($item['iso_code'] ?? $item['code'] ?? ''));
            $startDate = ! empty($item['start_date']) ? CarbonImmutable::parse($item['start_date']) : null;
            $endDate = ! empty($item['end_date']) ? CarbonImmutable::parse($item['end_date']) : null;
            $isActive = $endDate === null || $endDate->isFuture() || $endDate->greaterThanOrEqualTo(CarbonImmutable::now()->subDays(30));

            return new CurrencyData(
                code: $code,
                name: (string) ($item['name'] ?? $code),
                numericCode: isset($item['iso_numeric']) ? (string) $item['iso_numeric'] : null,
                symbol: isset($item['symbol']) ? (string) $item['symbol'] : null,
                minorUnit: self::MINOR_UNITS[$code] ?? 2,
                isActive: $isActive,
                availableFrom: $startDate,
                availableUntil: $endDate,
                metadata: [
                    'pegs' => $item['pegs'] ?? null,
                    'providers' => $item['providers'] ?? null,
                ],
            );
        });
    }

    /**
     * Map Frankfurter v2 rates JSON array to RateData DTO collection.
     *
     * @param  array<int, array<string, mixed>>  $ratesResponse
     * @return Collection<int, RateData>
     */
    public function mapRates(array $ratesResponse, string $providerName = 'frankfurter'): Collection
    {
        $now = CarbonImmutable::now();

        return collect($ratesResponse)->map(function (array $item) use ($providerName, $now): RateData {
            $rateDate = CarbonImmutable::parse($item['date'] ?? $now->toDateString());
            $base = strtoupper((string) ($item['base'] ?? 'EUR'));
            $quote = strtoupper((string) ($item['quote'] ?? ''));

            return new RateData(
                rateDate: $rateDate,
                baseCurrency: $base,
                quoteCurrency: $quote,
                rate: (string) $item['rate'],
                provider: $providerName,
                providerReference: $item['provider'] ?? null,
                fetchedAt: $now,
                rawPayload: $item,
            );
        });
    }
}
