<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $mode = 'required'): Response
    {
        /** @var ApiKey|null $apiKey */
        $apiKey = $request->attributes->get('apiKey');

        if (! $apiKey) {
            $token = $request->header('X-API-Key')
                ?? $request->bearerToken()
                ?? $request->query('api_key');

            if (! $token) {
                if ($mode === 'optional') {
                    return $next($request);
                }

                return new JsonResponse([
                    'error' => [
                        'code' => 'UNAUTHENTICATED',
                        'message' => 'API Key is missing. Pass your key in the X-API-Key header or as a Bearer token.',
                        'hint' => 'Generate an API key on the developer dashboard or CLI.',
                    ],
                ], Response::HTTP_UNAUTHORIZED);
            }

            $apiKey = ApiKey::findByToken((string) $token);

            if (! $apiKey) {
                return new JsonResponse([
                    'error' => [
                        'code' => 'INVALID_API_KEY',
                        'message' => 'The provided API Key is invalid, expired, or revoked.',
                    ],
                ], Response::HTTP_UNAUTHORIZED);
            }
        }

        // Apply dynamic per-key rate limiting
        $rateLimitKey = 'api_rate_limit:key:'.$apiKey->id;
        if (RateLimiter::tooManyAttempts($rateLimitKey, $apiKey->rate_limit_per_minute)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);

            return new JsonResponse([
                'error' => [
                    'code' => 'RATE_LIMIT_EXCEEDED',
                    'message' => "Rate limit exceeded. Tier [{$apiKey->tier}] allows {$apiKey->rate_limit_per_minute} req/min.",
                    'retry_after_seconds' => $seconds,
                ],
            ], Response::HTTP_TOO_MANY_REQUESTS, [
                'Retry-After' => (string) $seconds,
                'X-RateLimit-Limit' => (string) $apiKey->rate_limit_per_minute,
                'X-RateLimit-Remaining' => '0',
            ]);
        }

        RateLimiter::hit($rateLimitKey, 60);

        // Track last used timestamp (lightweight deferred update)
        defer(fn () => $apiKey->update(['last_used_at' => now()]));

        $request->attributes->set('apiKey', $apiKey);

        $response = $next($request);

        $response->headers->set('X-RateLimit-Limit', (string) $apiKey->rate_limit_per_minute);
        $response->headers->set('X-RateLimit-Remaining', (string) RateLimiter::remaining($rateLimitKey, $apiKey->rate_limit_per_minute));

        return $response;
    }
}
