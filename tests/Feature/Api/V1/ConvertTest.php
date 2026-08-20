<?php

use App\Models\ApiKey;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Carbon\CarbonImmutable;

beforeEach(function () {
    Currency::create(['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'minor_unit' => 2, 'is_active' => true]);
    Currency::create(['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'minor_unit' => 2, 'is_active' => true]);

    ExchangeRate::create([
        'rate_date' => CarbonImmutable::today(),
        'base_currency' => 'EUR',
        'quote_currency' => 'USD',
        'rate' => '1.085000000000000',
        'provider' => 'frankfurter',
        'fetched_at' => now(),
    ]);
});

test('convert endpoint returns accurate conversion result', function () {
    $response = $this->getJson('/api/v1/convert?from=EUR&to=USD&amount=100');

    $response->assertOk()
        ->assertJsonPath('data.from', 'EUR')
        ->assertJsonPath('data.to', 'USD')
        ->assertJsonPath('data.amount', '100.00')
        ->assertJsonPath('data.rate', '1.085000')
        ->assertJsonPath('data.result', '108.50')
        ->assertJsonStructure([
            'data' => ['from', 'to', 'amount', 'rate', 'inverse_rate', 'result'],
            'meta' => ['rate_date', 'provider', 'is_derived', 'is_stale', 'age_days', 'fetched_at'],
        ]);
});

test('convert endpoint validates required parameters', function () {
    $response = $this->getJson('/api/v1/convert');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['from', 'to', 'amount']);
});

test('convert endpoint accepts valid API key header', function () {
    $tokenResult = ApiKey::createToken(name: 'Test App');

    $response = $this->withHeader('X-API-Key', $tokenResult['plainToken'])
        ->getJson('/api/v1/convert?from=EUR&to=USD&amount=50');

    $response->assertOk()
        ->assertHeader('X-RateLimit-Limit', '60');
});
