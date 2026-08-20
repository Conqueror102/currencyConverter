<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Active Keys -->
    <div class="rounded-2xl border border-zinc-200 bg-white p-4.5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-1">
        <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
            <span class="font-medium">My Active API Keys</span>
            <flux:icon icon="key" class="size-4 text-emerald-500" />
        </div>
        <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
            {{ $this->developerKPIs['active_keys'] }} <span class="text-xs font-normal text-zinc-400">/ {{ $this->developerKPIs['total_keys'] }} total</span>
        </div>
        <div class="text-[11px] text-zinc-400">
            Ready for REST API integration
        </div>
    </div>

    <!-- Quota Tier -->
    <div class="rounded-2xl border border-zinc-200 bg-white p-4.5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-1">
        <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
            <span class="font-medium">Plan Tier & Quota</span>
            <flux:icon icon="shield-check" class="size-4 text-blue-500" />
        </div>
        <div class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">
            {{ $this->developerKPIs['tier'] }}
        </div>
        <div class="text-[11px] text-zinc-400 font-mono">
            {{ $this->developerKPIs['rate_limit'] }} sliding limit
        </div>
    </div>

    <!-- Total Requests -->
    <div class="rounded-2xl border border-zinc-200 bg-white p-4.5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-1">
        <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
            <span class="font-medium">Total API Requests</span>
            <flux:icon icon="chart-bar" class="size-4 text-purple-500" />
        </div>
        <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
            {{ $this->developerKPIs['total_requests'] }}
        </div>
        <div class="text-[11px] text-zinc-400">
            Logged across all your tokens
        </div>
    </div>

    <!-- Success Rate & Latency -->
    <div class="rounded-2xl border border-zinc-200 bg-white p-4.5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-1">
        <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
            <span class="font-medium">Success Rate & Speed</span>
            <flux:icon icon="bolt" class="size-4 text-amber-500" />
        </div>
        <div class="text-2xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400">
            {{ $this->developerKPIs['success_rate'] }}%
        </div>
        <div class="text-[11px] text-zinc-400 font-mono">
            ~{{ $this->developerKPIs['avg_latency'] }}ms avg response time
        </div>
    </div>
</div>
