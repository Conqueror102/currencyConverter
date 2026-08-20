<div class="space-y-6">
    <!-- Automated Schedule Cadence Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-zinc-200 bg-white p-4.5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-1.5">
            <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-zinc-900 dark:text-white">Parallel & P2P Rates</span>
                <span class="rounded bg-emerald-100 px-1.5 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Every 30 Mins</span>
            </div>
            <p class="text-xs text-zinc-500">Automated AbokiFX & Binance P2P black market quotes refresh.</p>
            <div class="text-[11px] font-mono text-zinc-400">Command: <code class="text-zinc-700 dark:text-zinc-300">rates:sync-parallel</code></div>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-4.5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-1.5">
            <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-zinc-900 dark:text-white">Official Central Bank Rates</span>
                <span class="rounded bg-blue-100 px-1.5 py-0.5 text-[10px] font-bold text-blue-800 dark:bg-blue-950 dark:text-blue-300">Every 3 Hours</span>
            </div>
            <p class="text-xs text-zinc-500">Official central bank benchmark rates via Frankfurter / ECB.</p>
            <div class="text-[11px] font-mono text-zinc-400">Command: <code class="text-zinc-700 dark:text-zinc-300">rates:sync-latest</code></div>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-4.5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-1.5">
            <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-zinc-900 dark:text-white">ISO Currency Registry</span>
                <span class="rounded bg-zinc-100 px-1.5 py-0.5 text-[10px] font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">Daily Midnight</span>
            </div>
            <p class="text-xs text-zinc-500">Syncs 165+ global ISO currency metadata and minor units.</p>
            <div class="text-[11px] font-mono text-zinc-400">Command: <code class="text-zinc-700 dark:text-zinc-300">rates:sync-currencies</code></div>
        </div>
    </div>

    <!-- Audit Log History Table -->
    <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-4">
        <div class="border-b border-zinc-100 pb-4 dark:border-zinc-800">
            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Pipeline Sync Runs & Ingestion Logs</h3>
            <p class="text-xs text-zinc-500">Audit trail of remote API calls, durations, records ingested, and errors.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-mono">
                <thead class="border-b border-zinc-200 bg-zinc-50/70 dark:border-zinc-800 dark:bg-zinc-800/40 font-sans">
                    <tr>
                        <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Provider</th>
                        <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Type</th>
                        <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Status</th>
                        <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Records Saved</th>
                        <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Duration</th>
                        <th class="px-3.5 py-2.5 text-right font-semibold text-zinc-600 dark:text-zinc-300">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @forelse($this->syncRunsList as $run)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                            <td class="px-3.5 py-2.5 font-bold text-zinc-900 dark:text-white">{{ $run['provider'] }}</td>
                            <td class="px-3.5 py-2.5 text-zinc-500 font-sans">{{ $run['sync_type'] }}</td>
                            <td class="px-3.5 py-2.5 font-sans">
                                <span class="rounded px-1.5 py-0.5 text-[10px] font-bold uppercase {{ strtolower($run['status']) === 'success' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' }}">
                                    {{ $run['status'] }}
                                </span>
                                @if($run['error_message'])
                                    <span class="block text-[10px] text-rose-500 font-mono mt-0.5 max-w-xs truncate" title="{{ $run['error_message'] }}">{{ $run['error_message'] }}</span>
                                @endif
                            </td>
                            <td class="px-3.5 py-2.5 text-zinc-900 dark:text-white">{{ $run['records_saved'] }}</td>
                            <td class="px-3.5 py-2.5 text-zinc-500">{{ $run['duration'] }}</td>
                            <td class="px-3.5 py-2.5 text-right text-zinc-400 font-sans">{{ $run['started_at'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3.5 py-6 text-center font-sans text-zinc-400">
                                No synchronization logs recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
