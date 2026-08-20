<?php

namespace App\Infrastructure\ExchangeRates\Frankfurter;

use App\Domain\ExchangeRates\Contracts\ExchangeRateProvider;
use App\Domain\ExchangeRates\Data\RateQuery;
use App\Domain\ExchangeRates\Exceptions\RateProviderException;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Throwable;

class FrankfurterProvider implements ExchangeRateProvider
{
    private string $baseUrl;

    private int $timeout;

    private int $retryTimes;

    private int $retrySleepMs;

    public function __construct(
        private readonly FrankfurterMapper $mapper,
        ?string $baseUrl = null,
        ?int $timeout = null,
        ?int $retryTimes = null,
        ?int $retrySleepMs = null,
    ) {
        $this->baseUrl = rtrim($baseUrl ?? config('exchange-rates.providers.frankfurter.base_url', 'https://api.frankfurter.dev/v2'), '/');
        $this->timeout = $timeout ?? (int) config('exchange-rates.providers.frankfurter.timeout', 10);
        $this->retryTimes = $retryTimes ?? (int) config('exchange-rates.providers.frankfurter.retry_times', 3);
        $this->retrySleepMs = $retrySleepMs ?? (int) config('exchange-rates.providers.frankfurter.retry_sleep_ms', 200);
    }

    public function name(): string
    {
        return 'frankfurter';
    }

    public function currencies(): Collection
    {
        $response = $this->sendRequest('/currencies');

        return $this->mapper->mapCurrencies($response->json() ?? []);
    }

    public function latest(RateQuery $query): Collection
    {
        $params = [];
        if ($query->baseCurrency !== 'EUR') {
            $params['base'] = $query->baseCurrency;
        }

        if (! empty($query->quoteCurrencies)) {
            $params['quotes'] = implode(',', $query->quoteCurrencies);
        }

        $response = $this->sendRequest('/rates', $params);

        return $this->mapper->mapRates($response->json() ?? [], $this->name());
    }

    public function historical(RateQuery $query, CarbonImmutable $date): Collection
    {
        $params = [
            'date' => $date->toDateString(),
        ];

        if ($query->baseCurrency !== 'EUR') {
            $params['base'] = $query->baseCurrency;
        }

        if (! empty($query->quoteCurrencies)) {
            $params['quotes'] = implode(',', $query->quoteCurrencies);
        }

        $response = $this->sendRequest('/rates', $params);

        return $this->mapper->mapRates($response->json() ?? [], $this->name());
    }

    public function timeSeries(
        RateQuery $query,
        CarbonImmutable $from,
        CarbonImmutable $to,
    ): Collection {
        $params = [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ];

        if ($query->baseCurrency !== 'EUR') {
            $params['base'] = $query->baseCurrency;
        }

        if (! empty($query->quoteCurrencies)) {
            $params['quotes'] = implode(',', $query->quoteCurrencies);
        }

        $response = $this->sendRequest('/rates', $params);

        return $this->mapper->mapRates($response->json() ?? [], $this->name());
    }

    /**
     * Send resilient HTTP GET request to Frankfurter v2.
     *
     * @param  array<string, mixed>  $params
     *
     * @throws RateProviderException
     */
    private function sendRequest(string $endpoint, array $params = []): Response
    {
        $url = $this->baseUrl.$endpoint;

        try {
            $response = Http::timeout($this->timeout)
                ->retry($this->retryTimes, $this->retrySleepMs, function (Throwable $exception): bool {
                    return $exception instanceof ConnectionException;
                })
                ->acceptJson()
                ->get($url, $params);

            if ($response->failed()) {
                $errorBody = $response->json();
                $errorMessage = is_array($errorBody) && isset($errorBody['message'])
                    ? $errorBody['message']
                    : "Frankfurter HTTP request failed with status [{$response->status()}].";

                throw new RateProviderException(
                    message: $errorMessage,
                    provider: $this->name(),
                    statusCode: $response->status(),
                );
            }

            return $response;
        } catch (RateProviderException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new RateProviderException(
                message: "Frankfurter request failed: {$e->getMessage()}",
                previous: $e,
                provider: $this->name(),
            );
        }
    }
}
