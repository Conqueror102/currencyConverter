<?php

use App\Domain\ExchangeRates\Services\ConversionService;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Currency::create(['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'minor_unit' => 2, 'is_active' => true]);
    Currency::create(['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'minor_unit' => 2, 'is_active' => true]);
    Currency::create(['code' => 'NGN', 'name' => 'Nigerian Naira', 'symbol' => '₦', 'minor_unit' => 2, 'is_active' => true]);
    Currency::create(['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'minor_unit' => 2, 'is_active' => true]);

    $today = CarbonImmutable::today();

    // Official Rates (EUR base)
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

    // Parallel / P2P Rates (USD base)
    ExchangeRate::create([
        'rate_date' => $today,
        'base_currency' => 'USD',
        'quote_currency' => 'NGN',
        'rate' => '1620.000000',
        'market_type' => 'parallel',
        'provider' => 'parallel_p2p',
        'fetched_at' => $today->toDateTimeString(),
    ]);
});

test('converts direct USD to NGN in parallel market', function () {
    /** @var ConversionService $service */
    $service = app(ConversionService::class);

    $result = $service->convert('USD', 'NGN', '100', null, null, 'parallel');

    expect($result->marketType)->toBe('parallel')
        ->and($result->rate)->toBe('1620.000000')
        ->and($result->result)->toBe('162000.00')
        ->and($result->spreadPercentage)->not->toBeNull();
});

test('derives EUR to NGN parallel cross rate accurately', function () {
    /** @var ConversionService $service */
    $service = app(ConversionService::class);

    // EUR -> NGN parallel = EUR/USD (1.1598) * USD/NGN (1620.00) = 1878.876
    $result = $service->convert('EUR', 'NGN', '100', null, null, 'parallel');

    expect($result->marketType)->toBe('parallel')
        ->and($result->isDerived)->toBeTrue()
        ->and($result->result)->toBe('187887.60');
});

test('calculates black market spread against official central bank rate', function () {
    /** @var ConversionService $service */
    $service = app(ConversionService::class);

    $result = $service->convert('USD', 'NGN', '100', null, null, 'parallel');

    expect($result->officialRate)->not->toBeNull()
        ->and($result->spreadPercentage)->toContain('+');
});
