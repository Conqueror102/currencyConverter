<?php

namespace App\Domain\ExchangeRates\Services;

use App\Domain\ExchangeRates\Contracts\ExchangeRateProvider;
use App\Domain\ExchangeRates\Exceptions\RateProviderException;
use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;

class ProviderManager
{
    /**
     * @var array<string, ExchangeRateProvider>
     */
    private array $resolvedProviders = [];

    public function __construct(private readonly Application $app) {}

    /**
     * Get the default or specified rate provider.
     */
    public function provider(?string $name = null): ExchangeRateProvider
    {
        $providerName = $name ?? (string) config('exchange-rates.default', 'frankfurter');

        if (isset($this->resolvedProviders[$providerName])) {
            return $this->resolvedProviders[$providerName];
        }

        $config = config("exchange-rates.providers.{$providerName}");

        if (! is_array($config) || ! isset($config['driver'])) {
            throw new InvalidArgumentException("Exchange rate provider [{$providerName}] is not configured.");
        }

        $driverClass = $config['driver'];

        if (! class_exists($driverClass)) {
            throw new InvalidArgumentException("Driver class [{$driverClass}] for provider [{$providerName}] does not exist.");
        }

        /** @var ExchangeRateProvider $instance */
        $instance = $this->app->make($driverClass);

        if (! $instance instanceof ExchangeRateProvider) {
            throw new RateProviderException("Driver [{$driverClass}] must implement ".ExchangeRateProvider::class);
        }

        $this->resolvedProviders[$providerName] = $instance;

        return $instance;
    }

    /**
     * Bind a custom or mock provider instance at runtime.
     */
    public function setProvider(string $name, ExchangeRateProvider $provider): self
    {
        $this->resolvedProviders[$name] = $provider;

        return $this;
    }
}
