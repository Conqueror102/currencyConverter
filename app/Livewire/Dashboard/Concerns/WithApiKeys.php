<?php

namespace App\Livewire\Dashboard\Concerns;

use App\Models\ApiKey;
use App\Models\ApiRequestLog;
use Livewire\Attributes\Computed;

trait WithApiKeys
{
    // New API Key Form State
    public string $newKeyName = '';

    public string $newKeyTier = 'free';

    public int $newKeyRateLimit = 60;

    public ?string $newlyCreatedPlainToken = null;

    // Revoke Confirmation Modal State
    public ?int $revokingApiKeyId = null;

    public ?string $revokingApiKeyName = null;

    /**
     * Create a new developer API key.
     */
    public function createApiKey(): void
    {
        if (! $this->isAdmin) {
            $this->validate([
                'newKeyName' => 'required|string|max:60',
            ]);
            $tier = 'free';
            $rateLimit = 60;
        } else {
            $this->validate([
                'newKeyName' => 'required|string|max:60',
                'newKeyTier' => 'required|string|in:free,starter,pro,enterprise',
                'newKeyRateLimit' => 'required|integer|min:10|max:10000',
            ]);
            $tier = $this->newKeyTier;
            $rateLimit = $this->newKeyRateLimit;
        }

        $result = ApiKey::createToken(
            name: $this->newKeyName,
            user: auth()->user(),
            tier: $tier,
            rateLimit: $rateLimit
        );

        $this->newlyCreatedPlainToken = $result['plainToken'];
        $this->playgroundKeyId = $result['apiKey']->id;
        $this->newKeyName = '';
        $this->statusMessage = 'API Key generated successfully.';
    }

    /**
     * Toggle API key active status.
     */
    public function toggleApiKey(int $id): void
    {
        $key = ApiKey::find($id);
        if ($key) {
            abort_unless($this->isAdmin || $key->user_id === auth()->id(), 403, 'Unauthorized action.');
            $key->update(['is_active' => ! $key->is_active]);
            $this->statusMessage = "API Key '{$key->name}' status updated.";
        }
    }

    /**
     * Open revoke confirmation modal for an API key.
     */
    public function confirmRevokeApiKey(int $id): void
    {
        $key = ApiKey::find($id);
        if ($key) {
            abort_unless($this->isAdmin || $key->user_id === auth()->id(), 403, 'Unauthorized action.');
            $this->revokingApiKeyId = $key->id;
            $this->revokingApiKeyName = $key->name;
        }
    }

    /**
     * Cancel revoke confirmation.
     */
    public function cancelRevokeApiKey(): void
    {
        $this->revokingApiKeyId = null;
        $this->revokingApiKeyName = null;
    }

    /**
     * Delete/Revoke an API key.
     */
    public function deleteApiKey(?int $id = null): void
    {
        $targetId = $id ?? $this->revokingApiKeyId;
        if (! $targetId) {
            return;
        }

        $key = ApiKey::find($targetId);
        if ($key) {
            abort_unless($this->isAdmin || $key->user_id === auth()->id(), 403, 'Unauthorized action.');
            $name = $key->name;
            $key->delete();
            $this->statusMessage = "API Key '{$name}' has been permanently revoked.";
        }

        $this->revokingApiKeyId = null;
        $this->revokingApiKeyName = null;
    }

    /**
     * Developer personal KPI summary.
     */
    #[Computed]
    public function developerKPIs(): array
    {
        $userId = auth()->id();
        $totalKeys = ApiKey::where('user_id', $userId)->count();
        $activeKeys = ApiKey::where('user_id', $userId)->where('is_active', true)->count();
        $metrics = $this->developerUsageMetrics;

        return [
            'total_keys' => $totalKeys,
            'active_keys' => $activeKeys,
            'tier' => 'Free Developer',
            'rate_limit' => '60 req/min',
            'total_requests' => $metrics['total_requests'],
            'success_rate' => $metrics['success_rate'],
            'avg_latency' => $metrics['avg_latency'],
        ];
    }

    /**
     * Scoped developer usage metrics.
     */
    #[Computed]
    public function developerUsageMetrics(): array
    {
        $userId = auth()->id();
        $userKeyIds = ApiKey::where('user_id', $userId)->pluck('id')->all();

        if (empty($userKeyIds)) {
            return [
                'total_requests' => '0',
                'success_rate' => 100.0,
                'avg_latency' => 14,
                'error_count' => 0,
                'has_requests' => false,
            ];
        }

        $totalRequests = ApiRequestLog::whereIn('api_key_id', $userKeyIds)->count();
        $successRequests = ApiRequestLog::whereIn('api_key_id', $userKeyIds)->where('status_code', '>=', 200)->where('status_code', '<', 300)->count();
        $errorRequests = ApiRequestLog::whereIn('api_key_id', $userKeyIds)->where('status_code', '>=', 400)->count();
        $avgLatency = (int) round(ApiRequestLog::whereIn('api_key_id', $userKeyIds)->avg('response_time_ms') ?? 14);
        $successRate = $totalRequests > 0 ? round(($successRequests / $totalRequests) * 100, 1) : 100.0;

        return [
            'total_requests' => number_format($totalRequests),
            'success_rate' => $successRate,
            'avg_latency' => $avgLatency > 0 ? $avgLatency : 14,
            'error_count' => $errorRequests,
            'has_requests' => $totalRequests > 0,
        ];
    }

    /**
     * Scoped developer request logs stream.
     */
    #[Computed]
    public function developerRequestLogs(): array
    {
        $userId = auth()->id();
        $userKeyIds = ApiKey::where('user_id', $userId)->pluck('id')->all();

        if (empty($userKeyIds)) {
            return [];
        }

        return ApiRequestLog::with('apiKey')
            ->whereIn('api_key_id', $userKeyIds)
            ->latest('created_at')
            ->take(30)
            ->get()
            ->map(fn (ApiRequestLog $log) => [
                'id' => $log->id,
                'key_name' => $log->apiKey?->name ?? 'API Key',
                'key_prefix' => $log->apiKey?->secret_prefix ?? 'cc_live',
                'method' => $log->method,
                'endpoint' => $log->endpoint,
                'status_code' => $log->status_code,
                'response_time_ms' => $log->response_time_ms ?? 14,
                'ip_address' => $log->ip_address ? substr($log->ip_address, 0, 8).'•••' : '127.0.0.1',
                'created_at' => $log->created_at?->diffForHumans() ?? 'Just now',
            ])->all();
    }

    /**
     * API Keys query.
     */
    #[Computed]
    public function apiKeysList(): array
    {
        $query = ApiKey::with('user')->latest();

        if (! $this->isAdmin) {
            $query->where('user_id', auth()->id());
        }

        return $query->get()->map(fn (ApiKey $k) => [
            'id' => $k->id,
            'name' => $k->name,
            'prefix' => $k->secret_prefix,
            'tier' => strtoupper($k->tier),
            'rate_limit' => $k->rate_limit_per_minute,
            'is_active' => $k->is_active,
            'user' => $k->user?->name ?? 'System / Anonymous',
            'last_used' => $k->last_used_at ? $k->last_used_at->diffForHumans() : 'Never',
            'created_at' => $k->created_at?->format('M d, Y'),
        ])->all();
    }
}
