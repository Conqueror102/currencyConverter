<div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-4">
    <div class="flex items-center justify-between border-b border-zinc-100 pb-4 dark:border-zinc-800">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Active ISO Currencies ({{ $this->stats['active_currencies'] }})</h3>
            <p class="text-xs text-zinc-500">Toggle currencies available for public conversion and matrix calculation.</p>
        </div>
        <input
            type="text"
            wire:model.live.debounce.250ms="search"
            placeholder="Filter currency..."
            class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-1 text-xs outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
        />
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="border-b border-zinc-200 bg-zinc-50/70 dark:border-zinc-800 dark:bg-zinc-800/40">
                <tr>
                    <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Code</th>
                    <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Name</th>
                    <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Symbol</th>
                    <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Minor Unit (Decimals)</th>
                    <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">State</th>
                    <th class="px-3.5 py-2.5 text-right font-semibold text-zinc-600 dark:text-zinc-300">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                @foreach($this->currenciesList as $c)
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                        <td class="px-3.5 py-2.5 font-bold font-mono text-zinc-900 dark:text-white">
                            <span>{{ $c['flag'] }}</span>
                            <span class="ml-1">{{ $c['code'] }}</span>
                        </td>
                        <td class="px-3.5 py-2.5 text-zinc-600 dark:text-zinc-300">{{ $c['name'] }}</td>
                        <td class="px-3.5 py-2.5 font-mono text-zinc-500">{{ $c['symbol'] }}</td>
                        <td class="px-3.5 py-2.5 font-mono text-zinc-500">{{ $c['minor_unit'] }}</td>
                        <td class="px-3.5 py-2.5">
                            <span class="rounded px-1.5 py-0.5 text-[10px] font-bold {{ $c['is_active'] ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400' }}">
                                {{ $c['is_active'] ? 'Active' : 'Disabled' }}
                            </span>
                        </td>
                        <td class="px-3.5 py-2.5 text-right">
                            <button
                                type="button"
                                wire:click="toggleCurrency('{{ $c['code'] }}')"
                                class="font-semibold text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                            >
                                {{ $c['is_active'] ? 'Disable' : 'Enable' }}
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
