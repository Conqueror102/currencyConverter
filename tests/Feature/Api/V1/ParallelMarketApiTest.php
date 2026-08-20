<?php

use App\Models\ApiKey;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Currency::create(['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'minor_unit' => 2, 'is_active' => true]);
    Currency::create(['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'minor_unit' => 2, 'is_active' => true]);
    Currency::create(['code' => 'NGN', 'name' => 'Nigerian Naira', 'symbol' => '₦', 'minor_unit' => 2, 'is_active' => true]);

    $today = CarbonImmutable::today();

    // Official
    ExchangeRate::create([
        'rate_date' => $today,
        'base_currency' => 'EUR',
        'quote_currency' => 'USD',
        'rate' => '1.159800',
        'market_type' => 'official',
        'provider' => 'frankfurter',
        'fetched_at' => $today->toDateTimeString(),
    ]);

    ExchangeRate::create([
        'rate_date' => $today,
        'base_currency' => 'EUR',
        'quote_currency' => 'NGN',
        'rate' => '1563.110000',
        'market_type' => 'official',
        'provider' => 'frankfurter',
        'fetched_at' => $today->toDateTimeString(),
    ]);

    // Parallel
    ExchangeRate::create([
        'rate_date' => $today,
        'base_currency' => 'USD',
        'quote_currency' => 'NGN',
        'rate' => '1620.000000',
        'market_type' => 'parallel',
        'provider' => 'parallel_p2p',
        'fetched_at' => $today->toDateTimeString(),
    ]);

    $tokenResult = ApiKey::createToken(name: 'Test Parallel Client', tier: 'pro');
    $this->plainToken = $tokenResult['plainToken'];
});

test('convert endpoint returns parallel market rate when market=parallel requested', function () {
    $response = $this->withHeaders([
        'X-API-Key' => $this->plainToken,
    ])->getJson('/api/v1/convert?from=USD&to=NGN&amount=100&market=parallel');

    $response->assertOk()
        ->assertJsonPath('data.from', 'USD')
        ->assertJsonPath('data.to', 'NGN')
        ->assertJsonPath('data.result', '162000.00')
        ->assertJsonPath('meta.market_type', 'parallel')
        ->assertJsonStructure([
            'data' => ['from', 'to', 'amount', 'rate', 'inverse_rate', 'result'],
            'meta' => ['rate_date', 'market_type', 'provider', 'official_rate', 'spread_percentage'],
        ]);
});

test('pair endpoint returns spread percentage on parallel market', function () {
    $response = $this->withHeaders([
        'X-API-Key' => $this->plainToken,
    ])->getJson('/api/v1/rates/USD/NGN?market=parallel');

    $response->assertOk()
        ->assertJsonPath('data.base', 'USD')
        ->assertJsonPath('data.quote', 'NGN')
        ->assertJsonPath('data.rate', '1620.000000')
        ->assertJsonPath('meta.market_type', 'parallel');
});
