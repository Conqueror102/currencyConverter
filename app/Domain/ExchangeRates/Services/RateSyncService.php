<?php

namespace App\Domain\ExchangeRates\Services;

use App\Domain\ExchangeRates\Data\CurrencyData;
use App\Domain\ExchangeRates\Data\RateData;
use App\Domain\ExchangeRates\Data\RateQuery;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\RateSyncRun;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class RateSyncService
{
    public function __construct(private readonly ProviderManager $providerManager) {}

    /**
     * Synchronize currency list and metadata from provider into database.
     *
     * @return array{received: int, saved: int, status: string}
     */
    public function syncCurrencies(?string $providerName = null): array
    {
        $provider = $this->providerManager->provider($providerName);
        $startedAt = CarbonImmutable::now();

        $syncRun = RateSyncRun::create([
            'provider' => $provider->name(),
            'sync_type' => 'currencies',
            'started_at' => $startedAt,
            'status' => 'running',
        ]);

        try {
            $currencies = $provider->currencies();
            $receivedCount = $currencies->count();
            $savedCount = 0;

            if ($receivedCount > 0) {
                $payload = $currencies->map(function (CurrencyData $data): array {
                    return [
                        'code' => $data->code,
                        'name' => $data->name,
                        'numeric_code' => $data->numericCode,
                        'symbol' => $data->symbol,
                        'minor_unit' => $data->minorUnit,
                        'available_from' => $data->availableFrom?->toDateString(),
                        'available_until' => $data->availableUntil?->toDateString(),
                        'is_active' => $data->isActive,
                        'metadata' => json_encode($data->metadata),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->all();

                foreach (array_chunk($payload, 100) as $chunk) {
                    Currency::upsert(
                        $chunk,
                        ['code'],
                        ['name', 'numeric_code', 'symbol', 'minor_unit', 'available_from', 'available_until', 'is_active', 'metadata', 'updated_at']
                    );
                    $savedCount += count($chunk);
                }
            }

            $syncRun->update([
                'completed_at' => now(),
                'status' => 'success',
                'records_received' => $receivedCount,
                'records_saved' => $savedCount,
            ]);

            Cache::forget('currencies:all');
            Cache::forget('currencies:active');

            return ['received' => $receivedCount, 'saved' => $savedCount, 'status' => 'success'];
        } catch (Throwable $e) {
            Log::error("Currency sync failed for [{$provider->name()}]: {$e->getMessage()}", ['exception' => $e]);

            $syncRun->update([
                'completed_at' => now(),
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return ['received' => 0, 'saved' => 0, 'status' => 'failed'];
        }
    }

    /**
     * Synchronize latest rates from provider into database.
     *
     * @return array{received: int, saved: int, status: string}
     */
    public function syncLatestRates(?string $providerName = null, string $baseCurrency = 'EUR'): array
    {
        $provider = $this->providerManager->provider($providerName);
        $startedAt = CarbonImmutable::now();

        $syncRun = RateSyncRun::create([
            'provider' => $provider->name(),
            'sync_type' => 'latest_rates',
            'started_at' => $startedAt,
            'status' => 'running',
        ]);

        try {
            $rates = $provider->latest(RateQuery::make($baseCurrency));
            $receivedCount = $rates->count();
            $savedCount = $this->saveRates($rates, $provider->name());

            $syncRun->update([
                'completed_at' => now(),
                'status' => 'success',
                'records_received' => $receivedCount,
                'records_saved' => $savedCount,
            ]);

            // Flush relevant cached rate queries
            Cache::flush();

            return ['received' => $receivedCount, 'saved' => $savedCount, 'status' => 'success'];
        } catch (Throwable $e) {
            Log::error("Latest rates sync failed for [{$provider->name()}]: {$e->getMessage()}", ['exception' => $e]);

            $syncRun->update([
                'completed_at' => now(),
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return ['received' => 0, 'saved' => 0, 'status' => 'failed'];
        }
    }

    /**
     * Backfill rates over a historical time range.
     *
     * @return array{received: int, saved: int, status: string}
     */
    public function backfillRates(
        CarbonImmutable $from,
        CarbonImmutable $to,
        ?string $providerName = null,
        string $baseCurrency = 'EUR',
    ): array {
        $provider = $this->providerManager->provider($providerName);
        $startedAt = CarbonImmutable::now();

        $syncRun = RateSyncRun::create([
            'provider' => $provider->name(),
            'sync_type' => 'backfill',
            'started_at' => $startedAt,
            'status' => 'running',
            'metadata' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
        ]);

        try {
            $rates = $provider->timeSeries(
                RateQuery::make($baseCurrency),
                $from,
                $to
            );

            $receivedCount = $rates->count();
            $savedCount = $this->saveRates($rates, $provider->name());

            $syncRun->update([
                'completed_at' => now(),
                'status' => 'success',
                'records_received' => $receivedCount,
                'records_saved' => $savedCount,
            ]);

            return ['received' => $receivedCount, 'saved' => $savedCount, 'status' => 'success'];
        } catch (Throwable $e) {
            Log::error("Backfill rates failed for [{$provider->name()}]: {$e->getMessage()}", ['exception' => $e]);

            $syncRun->update([
                'completed_at' => now(),
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return ['received' => 0, 'saved' => 0, 'status' => 'failed'];
        }
    }

    /**
     * Synchronize real-time parallel and P2P market rates.
     *
     * @return array{received: int, saved: int, status: string}
     */
    public function syncParallelRates(?string $providerName = null): array
    {
        $providerName = $providerName ?: 'parallel_p2p';
        $provider = $this->providerManager->provider($providerName);
        $startedAt = CarbonImmutable::now();

        $syncRun = RateSyncRun::create([
            'provider' => $provider->name(),
            'sync_type' => 'parallel_rates',
            'started_at' => $startedAt,
            'status' => 'running',
        ]);

        try {
            $rates = $provider->latest(RateQuery::make('USD'));
            $receivedCount = $rates->count();
            $savedCount = $this->saveRates($rates, $provider->name());

            $syncRun->update([
                'completed_at' => now(),
                'status' => 'success',
                'records_received' => $receivedCount,
                'records_saved' => $savedCount,
            ]);

            Cache::flush();

            return ['received' => $receivedCount, 'saved' => $savedCount, 'status' => 'success'];
        } catch (Throwable $e) {
            Log::error("Parallel rates sync failed for [{$provider->name()}]: {$e->getMessage()}", ['exception' => $e]);

            $syncRun->update([
                'completed_at' => now(),
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return ['received' => 0, 'saved' => 0, 'status' => 'failed'];
        }
    }

    /**
     * Persist rates collection into database within a single transaction.
     *
     * @param  Collection<int, RateData>  $rates
     */
    private function saveRates(Collection $rates, string $providerName): int
    {
        $saved = 0;
        $now = now();

        DB::transaction(function () use ($rates, $providerName, $now, &$saved) {
            foreach ($rates as $rate) {
                $base = strtoupper($rate->baseCurrency);
                $quote = strtoupper($rate->quoteCurrency);
                $dateStr = $rate->rateDate->toDateString();

                $existing = ExchangeRate::where('provider', $providerName)
                    ->where('base_currency', $base)
                    ->where('quote_currency', $quote)
                    ->where(function ($q) use ($dateStr) {
                        $q->where('rate_date', $dateStr)
                            ->orWhere('rate_date', "{$dateStr} 00:00:00");
                    })
                    ->first();

                if ($existing) {
                    $existing->update([
                        'rate' => $rate->rate,
                        'buy_rate' => $rate->buyRate,
                        'sell_rate' => $rate->sellRate,
                        'market_type' => $rate->marketType ?? 'official',
                        'provider_reference' => $rate->providerReference,
                        'fetched_at' => $rate->fetchedAt?->toDateTimeString() ?? $now->toDateTimeString(),
                        'raw_payload' => $rate->rawPayload,
                    ]);
                } else {
                    ExchangeRate::create([
                        'provider' => $providerName,
                        'rate_date' => $dateStr,
                        'base_currency' => $base,
                        'quote_currency' => $quote,
                        'market_type' => $rate->marketType ?? 'official',
                        'rate' => $rate->rate,
                        'buy_rate' => $rate->buyRate,
                        'sell_rate' => $rate->sellRate,
                        'provider_reference' => $rate->providerReference,
                        'fetched_at' => $rate->fetchedAt?->toDateTimeString() ?? $now->toDateTimeString(),
                        'raw_payload' => $rate->rawPayload,
                    ]);
                }
                $saved++;
            }
        });

        return $saved;
    }
}
