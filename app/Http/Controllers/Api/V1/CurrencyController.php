<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\ExchangeRates\Services\ConversionService;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function __construct(private readonly ConversionService $conversionService) {}

    public function index(Request $request): JsonResponse
    {
        $currencies = $this->conversionService->getActiveCurrencies();

        $data = $currencies->map(function (Currency $c): array {
            return [
                'code' => $c->code,
                'name' => $c->name,
                'numeric_code' => $c->numeric_code,
                'symbol' => $c->symbol,
                'minor_unit' => $c->minor_unit,
                'available_from' => $c->available_from?->toDateString(),
                'available_until' => $c->available_until?->toDateString(),
                'pegs' => $c->metadata['pegs'] ?? null,
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'count' => $data->count(),
            ],
        ]);
    }
}
