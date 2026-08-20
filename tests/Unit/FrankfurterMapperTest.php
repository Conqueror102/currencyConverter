<?php

use App\Infrastructure\ExchangeRates\Frankfurter\FrankfurterMapper;

test('maps currencies response accurately with ISO 4217 minor units', function () {
    $mapper = new FrankfurterMapper;

    $mockResponse = [
        [
            'code' => 'JPY',
            'name' => 'Japanese Yen',
            'symbol' => '¥',
            'iso_numeric' => '392',
            'start_date' => '1999-01-04',
            'end_date' => null,
        ],
        [
            'code' => 'BHD',
            'name' => 'Bahraini Dinar',
            'symbol' => 'BD',
            'iso_numeric' => '048',
            'start_date' => '2001-01-01',
            'end_date' => null,
        ],
    ];

    $collection = $mapper->mapCurrencies($mockResponse);

    expect($collection)->toHaveCount(2);

    $jpy = $collection->firstWhere('code', 'JPY');
    expect($jpy->name)->toBe('Japanese Yen')
        ->and($jpy->minorUnit)->toBe(0)
        ->and($jpy->isActive)->toBeTrue();

    $bhd = $collection->firstWhere('code', 'BHD');
    expect($bhd->minorUnit)->toBe(3);
});

test('maps rates response accurately to RateData DTOs', function () {
    $mapper = new FrankfurterMapper;

    $mockResponse = [
        [
            'date' => '2026-08-20',
            'base' => 'EUR',
            'quote' => 'USD',
            'rate' => 1.085,
        ],
    ];

    $collection = $mapper->mapRates($mockResponse, 'frankfurter');

    expect($collection)->toHaveCount(1);
    $rateData = $collection->first();

    expect($rateData->baseCurrency)->toBe('EUR')
        ->and($rateData->quoteCurrency)->toBe('USD')
        ->and($rateData->rate)->toBe('1.085')
        ->and($rateData->provider)->toBe('frankfurter');
});
