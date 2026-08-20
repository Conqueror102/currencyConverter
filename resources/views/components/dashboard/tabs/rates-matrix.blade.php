<div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-zinc-100 pb-4 dark:border-zinc-800">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Live Rates Matrix</h3>
            <p class="text-xs text-zinc-500">Real-time official and street market rate pairs ingested across providers.</p>
        </div>

        <!-- Filters Bar -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Market Filter -->
            <div class="inline-flex rounded-lg bg-zinc-100 p-0.5 dark:bg-zinc-800 text-xs">
                <button
                    type="button"
                    wire:click="$set('marketFilter', 'all')"
                    class="rounded-md px-2.5 py-1 transition {{ $marketFilter === 'all' ? 'bg-white font-semibold text-zinc-900 shadow-2xs dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' }}"
                >
                    All Markets
                </button>
                <button
                    type="button"
                    wire:click="$set('marketFilter', 'official')"
                    class="rounded-md px-2.5 py-1 transition {{ $marketFilter === 'official' ? 'bg-white font-semibold text-zinc-900 shadow-2xs dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' }}"
                >
                    Official Only
                </button>
                <button
                    type="button"
                    wire:click="$set('marketFilter', 'parallel')"
                    class="rounded-md px-2.5 py-1 transition {{ $marketFilter === 'parallel' ? 'bg-white font-semibold text-zinc-900 shadow-2xs dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' }}"
                >
                    Parallel Only
                </button>
            </div>

            <!-- Base Currency Filter -->
            <select
                wire:model.live="baseFilter"
                class="rounded-lg border border-zinc-200 bg-white px-2.5 py-1 text-xs font-semibold text-zinc-700 outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300"
            >
                <option value="all">Base: All</option>
                @foreach($this->availableBases as $b)
                    <option value="{{ $b }}">Base: {{ $b }}</option>
                @endforeach
            </select>

            <!-- Search Input -->
            <input
                type="text"
                wire:model.live.debounce.250ms="search"
                placeholder="Search pair..."
                class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-1 text-xs outline-none focus:border-zinc-900 focus:bg-white dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
            />
        </div>
    </div>

    <!-- Rates Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="border-b border-zinc-200 bg-zinc-50/70 dark:border-zinc-800 dark:bg-zinc-800/40">
                <tr>
                    <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Pair</th>
                    <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Market</th>
                    <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Mid Rate</th>
                    <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Buy (Bid)</th>
                    <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Sell (Ask)</th>
                    <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Provider</th>
                    <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Last Synced</th>
                    <th class="px-3.5 py-2.5 text-right font-semibold text-zinc-600 dark:text-zinc-300">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60 font-mono">
                @forelse($this->exchangeRates as $rate)
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                        <td class="px-3.5 py-3 font-bold text-zinc-900 dark:text-white">
                            <span>{{ \App\Domain\ExchangeRates\Support\CurrencyHelper::flag($rate->base_currency) }}</span>
                            <span>{{ $rate->base_currency }}</span>
                            <span class="text-zinc-400">/</span>
                            <span>{{ \App\Domain\ExchangeRates\Support\CurrencyHelper::flag($rate->quote_currency) }}</span>
                            <span>{{ $rate->quote_currency }}</span>
                        </td>
                        <td class="px-3.5 py-3 font-sans">
                            <span class="rounded px-1.5 py-0.5 text-[10px] font-bold uppercase {{ $rate->market_type === 'parallel' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' }}">
                                {{ $rate->market_type }}
                            </span>
                        </td>
                        <td class="px-3.5 py-3 font-bold text-zinc-900 dark:text-white">{{ number_format((float) $rate->rate, 4) }}</td>
                        <td class="px-3.5 py-3 text-zinc-600 dark:text-zinc-300">{{ $rate->buy_rate ? number_format((float) $rate->buy_rate, 2) : '—' }}</td>
                        <td class="px-3.5 py-3 text-zinc-600 dark:text-zinc-300">{{ $rate->sell_rate ? number_format((float) $rate->sell_rate, 2) : '—' }}</td>
                        <td class="px-3.5 py-3 font-sans text-zinc-500 text-[11px]">{{ $rate->provider }}</td>
                        <td class="px-3.5 py-3 font-sans text-zinc-400 text-[11px]">{{ $rate->fetched_at ? $rate->fetched_at->diffForHumans() : '—' }}</td>
                        <td class="px-3.5 py-3 text-right font-sans">
                            <button
                                type="button"
                                wire:click="editRate({{ $rate->id }})"
                                class="text-xs font-semibold text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                            >
                                Edit
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-3.5 py-6 text-center font-sans text-zinc-400">
                            No exchange rates matching filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    @if($this->exchangeRates->hasPages())
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-zinc-100 pt-4 dark:border-zinc-800 text-xs">
            <div class="text-zinc-500 text-xs">
                Showing <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $this->exchangeRates->firstItem() }}</span> to <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $this->exchangeRates->lastItem() }}</span> of <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $this->exchangeRates->total() }}</span> exchange rates
            </div>
            <div>
                {{ $this->exchangeRates->links() }}
            </div>
        </div>
    @endif
</div>
