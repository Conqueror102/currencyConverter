<?php

namespace App\Livewire\Dashboard\Concerns;

use App\Models\ApiKey;
use Illuminate\Http\Request;

trait WithPlayground
{
    public ?int $playgroundKeyId = null;

    public string $playgroundEndpoint = '/api/v1/convert';

    public string $playgroundFrom = 'USD';

    public string $playgroundTo = 'NGN';

    public string $playgroundAmount = '100';

    public string $playgroundMarket = 'parallel';

    public ?string $playgroundResponseJson = null;

    public ?int $playgroundResponseStatus = null;

    public ?int $playgroundResponseLatencyMs = null;

    public string $playgroundSdkTab = 'curl';

    /**
     * Execute live request in the Developer Playground.
     */
    public function executePlayground(): void
    {
        $startTime = microtime(true);
        $userId = auth()->id();

        // Resolve target key
        $apiKey = null;
        if ($this->playgroundKeyId) {
            $apiKey = ApiKey::where('id', $this->playgroundKeyId)->where('user_id', $userId)->first();
        }
        if (! $apiKey) {
            $apiKey = ApiKey::where('user_id', $userId)->where('is_active', true)->first();
        }

        $params = [];
        if ($this->playgroundEndpoint === '/api/v1/convert') {
            $params = [
                'from' => strtoupper(trim($this->playgroundFrom)),
                'to' => strtoupper(trim($this->playgroundTo)),
                'amount' => $this->playgroundAmount,
                'market' => $this->playgroundMarket,
            ];
        } elseif ($this->playgroundEndpoint === '/api/v1/rates/latest') {
            $params = [
                'base' => strtoupper(trim($this->playgroundFrom)),
                'market' => $this->playgroundMarket,
            ];
        }

        try {
            $req = Request::create(
                $this->playgroundEndpoint,
                'GET',
                $params
            );
            $req->headers->set('Accept', 'application/json');

            if ($apiKey) {
                $req->attributes->set('apiKey', $apiKey);
                $req->headers->set('X-API-Key', $apiKey->secret_prefix.'_playground');
            }

            $response = app()->handle($req);
            $this->playgroundResponseStatus = $response->getStatusCode();
            $content = $response->getContent();
            $decoded = json_decode((string) $content, true);
            $this->playgroundResponseJson = json_encode($decoded ?: ['raw' => $content], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } catch (\Throwable $e) {
            $this->playgroundResponseStatus = 500;
            $this->playgroundResponseJson = json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
        }

        $this->playgroundResponseLatencyMs = (int) max(4, round((microtime(true) - $startTime) * 1000));
    }
}
