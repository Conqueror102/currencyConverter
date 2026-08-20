<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class TrackPageViews
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track successful GET web page visits (skip assets, livewire updates, api routes)
        if ($request->isMethod('GET')
            && ! $request->is('livewire/*')
            && ! $request->is('api/*')
            && ! $request->is('up')
            && ! $request->ajax()
            && $response->getStatusCode() < 400
            && Schema::hasTable('page_views')
        ) {
            try {
                $userAgent = $request->userAgent() ?? '';
                $device = 'desktop';
                if (preg_match('/(mobile|android|iphone|ipad|phone)/i', $userAgent)) {
                    $device = preg_match('/(ipad|tablet)/i', $userAgent) ? 'tablet' : 'mobile';
                }

                $browser = 'Other';
                if (str_contains($userAgent, 'Chrome') && ! str_contains($userAgent, 'Edg')) {
                    $browser = 'Chrome';
                } elseif (str_contains($userAgent, 'Safari') && ! str_contains($userAgent, 'Chrome')) {
                    $browser = 'Safari';
                } elseif (str_contains($userAgent, 'Firefox')) {
                    $browser = 'Firefox';
                } elseif (str_contains($userAgent, 'Edg')) {
                    $browser = 'Edge';
                }

                PageView::create([
                    'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                    'ip_address' => $request->ip(),
                    'path' => '/'.ltrim($request->path(), '/'),
                    'referrer' => $request->header('referer'),
                    'device_type' => $device,
                    'browser' => $browser,
                    'country_code' => $request->header('CF-IPCountry', 'US'),
                ]);
            } catch (\Throwable) {
                // Non-blocking fail-safe
            }
        }

        return $response;
    }
}
