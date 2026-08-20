<?php

use App\Http\Controllers\Api\V1\ConvertController;
use App\Http\Controllers\Api\V1\CurrencyController;
use App\Http\Controllers\Api\V1\RateController;
use App\Http\Middleware\AuthenticateApiKey;
use App\Http\Middleware\LogApiRequests;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
|
| Public API endpoints for the Currency Converter & Exchange Rates Engine.
|
*/

Route::prefix('v1')
    ->middleware([
        AuthenticateApiKey::class.':optional',
        LogApiRequests::class,
    ])
    ->group(function (): void {
        Route::get('/currencies', [CurrencyController::class, 'index'])->name('api.v1.currencies');
        Route::get('/convert', ConvertController::class)->name('api.v1.convert');
        Route::get('/rates/latest', [RateController::class, 'latest'])->name('api.v1.rates.latest');
        Route::get('/rates/historical/{date}', [RateController::class, 'historical'])->name('api.v1.rates.historical');
        Route::get('/rates/timeseries', [RateController::class, 'timeseries'])->name('api.v1.rates.timeseries');
        Route::get('/rates/{base}/{quote}', [RateController::class, 'pair'])->name('api.v1.rates.pair');
    });
