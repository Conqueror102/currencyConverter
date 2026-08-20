<div class="space-y-6">
    <!-- Telemetry Summary Row -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-zinc-200 bg-white p-4.5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-1">
            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total API Calls Logged</span>
            <div class="text-xl font-bold text-zinc-900 dark:text-white">{{ $this->developerUsageMetrics['total_requests'] }}</div>
            <p class="text-[11px] text-zinc-400">Audited across all your active and revoked keys</p>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-4.5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-1">
            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Success Rate</span>
            <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400">{{ $this->developerUsageMetrics['success_rate'] }}%</div>
            <p class="text-[11px] text-zinc-400">HTTP 200 / 2xx response ratio</p>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-4.5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-1">
            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Average Latency</span>
            <div class="text-xl font-bold text-zinc-900 dark:text-white">{{ $this->developerUsageMetrics['avg_latency'] }} ms</div>
            <p class="text-[11px] text-zinc-400">Global edge execution benchmark</p>
        </div>
    </div>

    <!-- Live Request Stream Table -->
    <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-4">
        <div class="flex items-center justify-between border-b border-zinc-100 pb-4 dark:border-zinc-800">
            <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                    <flux:icon icon="queue-list" class="size-4 text-zinc-400" />
                    <span>Recent API Request Stream</span>
                </h3>
                <p class="text-xs text-zinc-500">Live inspection of recent requests authenticated by your API keys.</p>
            </div>
            <button
                type="button"
                wire:click="$refresh"
                class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 dark:hover:text-white"
            >
                <flux:icon icon="arrow-path" class="size-3.5" />
                <span>Refresh Stream</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="border-b border-zinc-200 bg-zinc-50/70 font-semibold text-zinc-600 dark:border-zinc-800 dark:bg-zinc-800/40 dark:text-zinc-300">
                    <tr>
                        <th class="px-3.5 py-2.5">Key Used</th>
                        <th class="px-3.5 py-2.5">Method & Route</th>
                        <th class="px-3.5 py-2.5">Status</th>
                        <th class="px-3.5 py-2.5">Latency</th>
                        <th class="px-3.5 py-2.5">Client IP</th>
                        <th class="px-3.5 py-2.5 text-right">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 font-mono dark:divide-zinc-800/60">
                    @forelse($this->developerRequestLogs as $log)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                            <td class="px-3.5 py-3 font-sans font-semibold text-zinc-900 dark:text-white">
                                <span>{{ $log['key_name'] }}</span>
                                <span class="block text-[11px] text-zinc-400 font-mono">{{ $log['key_prefix'] }}•••</span>
                            </td>
                            <td class="px-3.5 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="rounded bg-blue-50 px-1.5 py-0.5 text-[10px] font-bold text-blue-700 dark:bg-blue-950 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        {{ $log['method'] }}
                                    </span>
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ $log['endpoint'] }}</span>
                                </div>
                            </td>
                            <td class="px-3.5 py-3 font-sans">
                                <span class="rounded px-1.5 py-0.5 text-[10px] font-bold {{ $log['status_code'] >= 200 && $log['status_code'] < 300 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : ($log['status_code'] === 429 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300') }}">
                                    {{ $log['status_code'] }} {{ $log['status_code'] === 200 ? 'OK' : ($log['status_code'] === 429 ? 'Too Many Requests' : 'Error') }}
                                </span>
                            </td>
                            <td class="px-3.5 py-3 text-zinc-600 dark:text-zinc-400">
                                {{ $log['response_time_ms'] }}ms
                            </td>
                            <td class="px-3.5 py-3 text-zinc-400">
                                {{ $log['ip_address'] }}
                            </td>
                            <td class="px-3.5 py-3 text-right font-sans text-zinc-400">
                                {{ $log['created_at'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3.5 py-12 text-center font-sans text-zinc-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <flux:icon icon="queue-list" class="size-8 text-zinc-300 dark:text-zinc-600" />
                                    <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">No API calls recorded yet</p>
                                    <p class="text-xs text-zinc-400 max-w-sm">
                                        Start sending requests using your API key or use the in-browser playground to test endpoints live.
                                    </p>
                                    <button
                                        type="button"
                                        wire:click="$set('activeTab', 'playground')"
                                        class="mt-2 rounded-xl bg-zinc-900 px-4 py-2 text-xs font-semibold text-white shadow-2xs transition hover:bg-zinc-800 active:scale-95 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200"
                                    >
                                        Open API Playground
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
