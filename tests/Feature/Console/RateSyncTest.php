<?php

use App\Domain\ExchangeRates\Services\ProviderManager;
use App\Infrastructure\ExchangeRates\Fake\FakeExchangeRateProvider;
use App\Models\ApiKey;
use App\Models\Currency;
use App\Models\ExchangeRate;

beforeEach(function () {
    // Bind Fake provider for console tests
    $fake = new FakeExchangeRateProvider;
    app(ProviderManager::class)->setProvider('fake', $fake);
    config(['exchange-rates.default' => 'fake']);
});

test('rates:sync-currencies command populates currencies table', function () {
    $this->artisan('rates:sync-currencies --provider=fake')
        ->assertSuccessful();

    expect(Currency::count())->toBeGreaterThan(0)
        ->and(Currency::find('USD'))->not->toBeNull()
        ->and(Currency::find('JPY')->minor_unit)->toBe(0);
});

test('rates:sync-latest command saves exchange rates into database', function () {
    $this->artisan('rates:sync-latest --provider=fake --base=EUR')
        ->assertSuccessful();

    expect(ExchangeRate::count())->toBeGreaterThan(0)
        ->and(ExchangeRate::where('quote_currency', 'USD')->exists())->toBeTrue();
});

test('api-keys:generate creates active api key token', function () {
    $this->artisan('api-keys:generate "Mobile App" --tier=developer --rate-limit=120')
        ->assertSuccessful();

    $key = ApiKey::where('name', 'Mobile App')->first();
    expect($key)->not->toBeNull()
        ->and($key->tier)->toBe('developer')
        ->and($key->rate_limit_per_minute)->toBe(120);
});
