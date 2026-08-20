<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Total Page Views -->
    <div class="rounded-2xl border border-zinc-200 bg-white p-4.5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-1">
        <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
            <span class="font-medium">Total Web Visits</span>
            <span class="rounded bg-emerald-100 px-1.5 py-0.2 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">+{{ $this->analyticsKPIs['today_visits'] }} today</span>
        </div>
        <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
            {{ $this->analyticsKPIs['total_visits'] }}
        </div>
        <div class="flex items-center gap-1.5 text-[11px] text-zinc-400">
            <flux:icon icon="users" class="size-3 text-zinc-400" />
            <span>{{ $this->analyticsKPIs['unique_visitors'] }} unique IP visitors</span>
        </div>
    </div>

    <!-- Total FX Conversions -->
    <div class="rounded-2xl border border-zinc-200 bg-white p-4.5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-1">
        <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
            <span class="font-medium">Calculated Conversions</span>
            <span class="rounded bg-blue-100 px-1.5 py-0.2 text-[10px] font-bold text-blue-800 dark:bg-blue-950 dark:text-blue-300">+{{ $this->analyticsKPIs['today_conversions'] }} today</span>
        </div>
        <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
            {{ $this->analyticsKPIs['total_conversions'] }}
        </div>
        <div class="flex items-center gap-1.5 text-[11px] text-zinc-400">
            <flux:icon icon="arrow-path-rounded-square" class="size-3 text-zinc-400" />
            <span>{{ $this->analyticsKPIs['web_conversions'] }} web • {{ $this->analyticsKPIs['api_conversions'] }} API</span>
        </div>
    </div>

    <!-- Parallel Market Share -->
    <div class="rounded-2xl border border-zinc-200 bg-white p-4.5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-1">
        <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
            <span class="font-medium">Black Market Demand</span>
            <span class="font-mono text-[10px] text-amber-600 font-bold uppercase">Parallel / P2P</span>
        </div>
        <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
            {{ $this->analyticsKPIs['parallel_share'] }}%
        </div>
        <div class="flex items-center gap-1.5 text-[11px] text-zinc-400">
            <flux:icon icon="bolt" class="size-3 text-amber-500" />
            <span>Share of all FX requests</span>
        </div>
    </div>

    <!-- Most Popular FX Pair -->
    <div class="rounded-2xl border border-zinc-200 bg-white p-4.5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-1">
        <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
            <span class="font-medium">Top Currency Pair</span>
            <span class="font-mono text-[10px] text-zinc-400">{{ $this->analyticsKPIs['top_pair_percent'] }}% volume</span>
        </div>
        <div class="text-2xl font-bold font-mono tracking-tight text-zinc-900 dark:text-white">
            {{ $this->analyticsKPIs['top_pair'] }}
        </div>
        <div class="flex items-center gap-1.5 text-[11px] text-zinc-400">
            <flux:icon icon="chart-bar" class="size-3 text-zinc-400" />
            <span>Highest trade action volume</span>
        </div>
    </div>
</div>
