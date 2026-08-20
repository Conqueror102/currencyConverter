<?php

use App\Models\Currency;
use App\Models\ExchangeRate;
use Carbon\CarbonImmutable;

beforeEach(function () {
    Currency::create(['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'minor_unit' => 2, 'is_active' => true]);
    Currency::create(['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'minor_unit' => 2, 'is_active' => true]);
    Currency::create(['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'minor_unit' => 2, 'is_active' => true]);

    $today = CarbonImmutable::today();

    ExchangeRate::create([
        'rate_date' => $today,
        'base_currency' => 'EUR',
        'quote_currency' => 'USD',
        'rate' => '1.085000000000000',
        'provider' => 'frankfurter',
        'fetched_at' => now(),
    ]);

    ExchangeRate::create([
        'rate_date' => $today,
        'base_currency' => 'EUR',
        'quote_currency' => 'GBP',
        'rate' => '0.852000000000000',
        'provider' => 'frankfurter',
        'fetched_at' => now(),
    ]);
});

test('latest rates endpoint returns quotes for base currency', function () {
    $response = $this->getJson('/api/v1/rates/latest?base=EUR');

    $response->assertOk()
        ->assertJsonPath('data.base', 'EUR')
        ->assertJsonPath('data.rates.USD', '1.085000')
        ->assertJsonPath('data.rates.GBP', '0.852000');
});

test('single pair endpoint returns rate between two currencies', function () {
    $response = $this->getJson('/api/v1/rates/EUR/USD');

    $response->assertOk()
        ->assertJsonPath('data.base', 'EUR')
        ->assertJsonPath('data.quote', 'USD')
        ->assertJsonPath('data.rate', '1.085000');
});
