<?php

use App\Domain\ExchangeRates\Services\RateSyncService;
use App\Livewire\Dashboard\Concerns\WithAnalytics;
use App\Livewire\Dashboard\Concerns\WithApiKeys;
use App\Livewire\Dashboard\Concerns\WithExchangeRates;
use App\Livewire\Dashboard\Concerns\WithPlayground;
use App\Models\ApiKey;
use App\Models\Currency;
use App\Models\PageView;
use Database\Seeders\AnalyticsDataSeeder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
    use WithAnalytics;
    use WithApiKeys;
    use WithExchangeRates;
    use WithPlayground;

    #[Url(as: 'tab')]
    public string $activeTab = 'analytics';

    public ?string $statusMessage = null;

    public ?string $errorMessage = null;

    public function mount(): void
    {
        if (! $this->isAdmin && ! in_array($this->activeTab, ['keys', 'logs', 'playground'])) {
            $this->activeTab = 'keys';
        }

        // Pre-select first active API key for Playground
        if (auth()->check()) {
            $firstKey = ApiKey::where('user_id', auth()->id())->where('is_active', true)->first();
            $this->playgroundKeyId = $firstKey?->id;
        }

        // Auto-seed sample analytics telemetry if empty
        try {
            if ($this->isAdmin && $this->hasAnalyticsTables && PageView::count() < 10) {
                (new AnalyticsDataSeeder)->run();
            }
            if (Currency::count() === 0) {
                app(RateSyncService::class)->syncCurrencies('frankfurter');
            }
        } catch (\Throwable) {
            // Non-blocking fail-safe
        }
    }

    /**
     * Check if the authenticated user is an administrator.
     */
    #[Computed]
    public function isAdmin(): bool
    {
        return (bool) auth()->user()?->isAdmin();
    }
};
?>

<div class="space-y-6">
    <!-- Page Header & Action Toolbar -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-2.5">
                @if($this->isAdmin)
                    <span>Admin Control Center</span>
                    <span class="inline-flex items-center gap-1.5 rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                        <span class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Pipeline Active</span>
                    </span>
                @else
                    <span>Developer API Portal</span>
                    <span class="inline-flex items-center gap-1.5 rounded-md bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-950/60 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                        <flux:icon icon="code-bracket" class="size-3.5" />
                        <span>Developer Account</span>
                    </span>
                @endif
            </h1>
            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                @if($this->isAdmin)
                    Live metrics, traffic telemetry, multi-market rate matrix, API keys, and conversion insights.
                @else
                    Manage your developer API tokens, monitor rate limit quotas, and test live FX conversions.
                @endif
            </p>
        </div>

        <!-- Global Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            @if($this->isAdmin)
                <button
                    type="button"
                    wire:click="syncOfficial"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 shadow-2xs transition hover:bg-zinc-100 hover:text-zinc-900 active:scale-95 disabled:opacity-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700 dark:hover:text-white"
                >
                    <flux:icon icon="arrow-path" class="size-3.5" wire:loading.class="animate-spin" wire:target="syncOfficial" />
                    <span>Sync Official</span>
                </button>
                <button
                    type="button"
                    wire:click="syncParallel"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-900 px-3.5 py-2 text-xs font-semibold text-white shadow-2xs transition hover:bg-zinc-800 active:scale-95 disabled:opacity-50 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200"
                >
                    <flux:icon icon="bolt" class="size-3.5 text-amber-400 dark:text-amber-600" wire:loading.class="animate-spin" wire:target="syncParallel" />
                    <span>Sync Parallel & P2P</span>
                </button>
            @else
                <button
                    type="button"
                    wire:click="$set('activeTab', 'playground')"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-700 shadow-2xs transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700 dark:hover:text-white"
                >
                    <flux:icon icon="code-bracket-square" class="size-3.5 text-emerald-500" />
                    <span>Open API Playground</span>
                </button>
                <button
                    type="button"
                    wire:click="$set('activeTab', 'keys')"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-900 px-3.5 py-2 text-xs font-semibold text-white shadow-2xs transition hover:bg-zinc-800 active:scale-95 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200"
                >
                    <flux:icon icon="plus" class="size-3.5" />
                    <span>New API Key</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Status Alerts -->
    @if($statusMessage)
        <div class="flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-xs text-emerald-900 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
            <div class="flex items-center gap-2">
                <flux:icon icon="check-circle" class="size-4 text-emerald-600 dark:text-emerald-400" />
                <span>{{ $statusMessage }}</span>
            </div>
            <button type="button" wire:click="$set('statusMessage', null)" class="text-emerald-600 hover:text-emerald-900 dark:text-emerald-400">&times;</button>
        </div>
    @endif

    @if($errorMessage)
        <div class="flex items-center justify-between rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-xs text-rose-900 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-300">
            <div class="flex items-center gap-2">
                <flux:icon icon="exclamation-triangle" class="size-4 text-rose-600 dark:text-rose-400" />
                <span>{{ $errorMessage }}</span>
            </div>
            <button type="button" wire:click="$set('errorMessage', null)" class="text-rose-600 hover:text-rose-900 dark:text-rose-400">&times;</button>
        </div>
    @endif

    <!-- Top KPI Cards -->
    @if($this->isAdmin && $activeTab === 'analytics')
        @include('components.dashboard.kpis.admin-kpi-cards')
    @elseif(! $this->isAdmin && $activeTab === 'keys')
        @include('components.dashboard.kpis.developer-kpi-cards')
    @endif



    <!-- Modular Tab Views -->
    @if($this->isAdmin && $activeTab === 'analytics')
        @include('components.dashboard.tabs.analytics')
    @elseif($this->isAdmin && $activeTab === 'rates')
        @include('components.dashboard.tabs.rates-matrix')
    @elseif($activeTab === 'keys')
        @include('components.dashboard.tabs.api-keys')
    @elseif($this->isAdmin && $activeTab === 'currencies')
        @include('components.dashboard.tabs.currencies')
    @elseif($this->isAdmin && $activeTab === 'logs')
        @include('components.dashboard.tabs.ingestion-logs')
    @elseif(! $this->isAdmin && $activeTab === 'logs')
        @include('components.dashboard.tabs.request-logs')
    @elseif($activeTab === 'playground')
        @include('components.dashboard.tabs.api-playground')
    @endif

    <!-- Modals -->
    @include('components.dashboard.modals.api-key-created-modal')
    @include('components.dashboard.modals.api-key-revoke-modal')
    @include('components.dashboard.modals.rate-override-modal')
</div>
