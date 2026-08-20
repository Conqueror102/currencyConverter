<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use App\Models\ApiRequestLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $durationMs = (int) round((microtime(true) - $startTime) * 1000);

        /** @var ApiKey|null $apiKey */
        $apiKey = $request->attributes->get('apiKey');
        $endpoint = $request->path();
        $method = $request->method();
        $statusCode = $response->getStatusCode();
        $ip = $request->ip();

        defer(function () use ($apiKey, $endpoint, $method, $statusCode, $durationMs, $ip): void {
            ApiRequestLog::create([
                'api_key_id' => $apiKey?->id,
                'ip_address' => $ip,
                'endpoint' => $endpoint,
                'method' => $method,
                'status_code' => $statusCode,
                'response_time_ms' => $durationMs,
            ]);
        });

        return $response;
    }
}
