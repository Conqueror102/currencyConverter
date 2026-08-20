<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\ExchangeRates\Exceptions\CurrencyNotFoundException;
use App\Domain\ExchangeRates\Exceptions\RateNotFoundException;
use App\Domain\ExchangeRates\Services\ConversionService;
use App\Http\Controllers\Controller;
use App\Models\ConversionLog;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConvertController extends Controller
{
    public function __construct(private readonly ConversionService $conversionService) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['required', 'string', 'size:3'],
            'to' => ['required', 'string', 'size:3'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'market' => ['nullable', 'string', 'in:official,parallel'],
            'action' => ['nullable', 'string', 'in:mid,buy,sell'],
        ]);

        $from = strtoupper($validated['from']);
        $to = strtoupper($validated['to']);
        $amount = (string) $validated['amount'];
        $date = ! empty($validated['date']) ? CarbonImmutable::parse($validated['date']) : null;
        $market = strtolower($validated['market'] ?? 'official');
        $action = strtolower($validated['action'] ?? 'mid');

        try {
            $conversion = $this->conversionService->convert(
                from: $from,
                to: $to,
                amount: $amount,
                date: $date,
                marketType: $market,
                tradeAction: $action,
            );

            try {
                ConversionLog::create([
                    'source' => 'api',
                    'api_key_id' => $request->attributes->get('api_key')?->id,
                    'base_currency' => $from,
                    'quote_currency' => $to,
                    'market_type' => $market,
                    'trade_action' => $action,
                    'amount' => $amount,
                    'rate' => $conversion->rate,
                    'result' => $conversion->convertedAmount,
                    'ip_address' => $request->ip(),
                ]);
            } catch (\Throwable) {
                // Non-blocking
            }

            return response()->json($conversion->toArray());
        } catch (CurrencyNotFoundException $e) {
            return response()->json([
                'error' => [
                    'code' => 'CURRENCY_NOT_FOUND',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_NOT_FOUND);
        } catch (RateNotFoundException $e) {
            return response()->json([
                'error' => [
                    'code' => 'RATE_NOT_AVAILABLE',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
