<?php

use App\Domain\ExchangeRates\Services\ConversionService;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    // Seed essential currencies
    Currency::create(['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'minor_unit' => 2, 'is_active' => true]);
    Currency::create(['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'minor_unit' => 2, 'is_active' => true]);
    Currency::create(['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'minor_unit' => 2, 'is_active' => true]);
    Currency::create(['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥', 'minor_unit' => 0, 'is_active' => true]);
    Currency::create(['code' => 'BHD', 'name' => 'Bahraini Dinar', 'symbol' => 'BD', 'minor_unit' => 3, 'is_active' => true]);

    $today = CarbonImmutable::today();

    // Rates stored against canonical base EUR
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

    ExchangeRate::create([
        'rate_date' => $today,
        'base_currency' => 'EUR',
        'quote_currency' => 'JPY',
        'rate' => '165.420000000000000',
        'provider' => 'frankfurter',
        'fetched_at' => now(),
    ]);
});

test('converts same currency with 1.0 rate', function () {
    $service = app(ConversionService::class);
    $result = $service->convert('USD', 'USD', '250.50');

    expect($result->from)->toBe('USD')
        ->and($result->to)->toBe('USD')
        ->and($result->amount)->toBe('250.50')
        ->and($result->rate)->toBe('1.000000')
        ->and($result->result)->toBe('250.50');
});

test('converts direct rate from canonical base EUR to USD', function () {
    $service = app(ConversionService::class);
    $result = $service->convert('EUR', 'USD', '100');

    expect($result->from)->toBe('EUR')
        ->and($result->to)->toBe('USD')
        ->and($result->amount)->toBe('100.00')
        ->and($result->rate)->toBe('1.085000')
        ->and($result->result)->toBe('108.50');
});

test('converts inverted rate from USD to EUR with high precision', function () {
    $service = app(ConversionService::class);
    $result = $service->convert('USD', 'EUR', '108.50');

    expect($result->from)->toBe('USD')
        ->and($result->to)->toBe('EUR')
        ->and($result->result)->toBe('100.00');
});

test('derives cross rates accurately between USD and GBP', function () {
    // 1 EUR = 1.085 USD, 1 EUR = 0.852 GBP
    // Rate(USD -> GBP) = 0.852 / 1.085 ≈ 0.785253
    $service = app(ConversionService::class);
    $result = $service->convert('USD', 'GBP', '100');

    expect($result->from)->toBe('USD')
        ->and($result->to)->toBe('GBP')
        ->and($result->isDerived)->toBeTrue()
        ->and($result->result)->toBe('78.53');
});

test('respects JPY 0 minor units and BHD 3 minor units', function () {
    $service = app(ConversionService::class);

    // EUR -> JPY (0 minor units)
    $jpyResult = $service->convert('EUR', 'JPY', '10.55');
    // 10.55 * 165.42 = 1745.181 -> rounded to 1745
    expect($jpyResult->result)->toBe('1745');
});
