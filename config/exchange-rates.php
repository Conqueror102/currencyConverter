<?php

use App\Infrastructure\ExchangeRates\Fake\FakeExchangeRateProvider;
use App\Infrastructure\ExchangeRates\Frankfurter\FrankfurterProvider;
use App\Infrastructure\ExchangeRates\Parallel\ParallelMarketProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Exchange Rate Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default exchange rate provider used by your
    | application to fetch live, historical, and timeseries rates.
    |
    */

    'default' => env('EXCHANGE_RATE_PROVIDER', 'frankfurter'),

    /*
    |--------------------------------------------------------------------------
    | Fallback Exchange Rate Provider
    |--------------------------------------------------------------------------
    |
    | If the primary provider fails, the system can attempt to failover to
    | this provider if configured.
    |
    */

    'fallback' => env('EXCHANGE_RATE_FALLBACK_PROVIDER'),

    /*
    |--------------------------------------------------------------------------
    | Canonical Base Currency
    |--------------------------------------------------------------------------
    |
    | The default base currency used when deriving cross rates and storing
    | normalized rate indexes.
    |
    */

    'canonical_base' => env('EXCHANGE_RATE_CANONICAL_BASE', 'EUR'),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Duration (in seconds) to cache rate queries in memory/store before
    | re-querying the database. Default: 3600 (1 hour).
    |
    */

    'cache_ttl' => (int) env('EXCHANGE_RATE_CACHE_TTL', 3600),

    /*
    |--------------------------------------------------------------------------
    | Registered Providers
    |--------------------------------------------------------------------------
    |
    | Here each supported rate provider driver is configured.
    |
    */

    'providers' => [
        'frankfurter' => [
            'name' => 'Frankfurter v2 (Central Banks & ECB)',
            'driver' => FrankfurterProvider::class,
            'base_url' => env('FRANKFURTER_BASE_URL', 'https://api.frankfurter.dev/v2'),
            'timeout' => (int) env('FRANKFURTER_TIMEOUT', 10),
            'retry_times' => (int) env('FRANKFURTER_RETRY_TIMES', 3),
            'retry_sleep_ms' => (int) env('FRANKFURTER_RETRY_SLEEP_MS', 200),
        ],

        'parallel_p2p' => [
            'name' => 'Crypto P2P & Parallel Market Aggregator (Binance / Street)',
            'driver' => ParallelMarketProvider::class,
            'timeout' => (int) env('PARALLEL_P2P_TIMEOUT', 8),
        ],

        'fake' => [
            'name' => 'In-Memory Fake Provider',
            'driver' => FakeExchangeRateProvider::class,
        ],
    ],
];
