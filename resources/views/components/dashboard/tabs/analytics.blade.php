<div class="space-y-6">
    <!-- Row 1: Interactive 14-Day Activity Chart & Top Pairs Breakdown -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <!-- Left: 14-Day Volume Chart (7 cols) -->
        <div class="lg:col-span-7 rounded-2xl border border-zinc-200 bg-white p-6 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-white">14-Day Traffic & Conversion Volume</h3>
                    <p class="text-xs text-zinc-500">Daily breakdown of page visits vs. calculated FX conversions.</p>
                </div>
                <div class="flex items-center gap-3 text-[11px] font-semibold">
                    <div class="flex items-center gap-1.5">
                        <span class="size-2.5 rounded-full bg-zinc-900 dark:bg-zinc-100"></span>
                        <span class="text-zinc-600 dark:text-zinc-300">Page Visits</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="size-2.5 rounded-full bg-emerald-500"></span>
                        <span class="text-zinc-600 dark:text-zinc-300">Conversions</span>
                    </div>
                </div>
            </div>

            <!-- Bar Graph Container -->
            <div class="pt-6">
                @if(count($this->activityChart) > 0)
                    <div class="flex h-48 items-end gap-2 sm:gap-3 border-b border-zinc-200 pb-2 dark:border-zinc-800">
                        @foreach($this->activityChart as $bar)
                            <div class="group relative flex flex-1 flex-col items-center h-full justify-end">
                                <!-- Tooltip Hover Popover -->
                                <div class="absolute -top-12 z-20 hidden rounded-lg bg-zinc-900 px-2.5 py-1.5 text-[10px] font-semibold text-white shadow-xl group-hover:block dark:bg-zinc-800 whitespace-nowrap">
                                    <div>{{ $bar['date_label'] }}</div>
                                    <div class="text-zinc-300">{{ $bar['visits'] }} visits • {{ $bar['conversions'] }} conv.</div>
                                </div>

                                <!-- Bars Pair -->
                                <div class="flex items-end gap-1 w-full justify-center h-full">
                                    <!-- Visits Bar -->
                                    <div
                                        style="height: {{ $bar['visits_percent'] }}%"
                                        class="w-full max-w-[10px] rounded-t-sm bg-zinc-800 transition-all duration-300 group-hover:bg-zinc-950 dark:bg-zinc-300 dark:group-hover:bg-white"
                                    ></div>
                                    <!-- Conversions Bar -->
                                    <div
                                        style="height: {{ $bar['conversions_percent'] }}%"
                                        class="w-full max-w-[10px] rounded-t-sm bg-emerald-500 transition-all duration-300 group-hover:bg-emerald-400"
                                    ></div>
                                </div>

                                <!-- Date Label -->
                                <span class="mt-2 text-[10px] font-mono text-zinc-400 dark:text-zinc-500 {{ $bar['is_today'] ? 'font-bold text-zinc-900 dark:text-white' : '' }}">
                                    {{ $bar['date_label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex h-48 items-center justify-center text-xs text-zinc-400">
                        No activity recorded yet. Run migrations to enable activity tracking.
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Top 5 Converted Currency Pairs (5 cols) -->
        <div class="lg:col-span-5 rounded-2xl border border-zinc-200 bg-white p-6 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-4">
            <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Most Converted Currency Pairs</h3>
                <p class="text-xs text-zinc-500">Ranking by total conversions across web and API.</p>
            </div>

            <div class="space-y-3.5 pt-2">
                @forelse($this->topPairs as $pair)
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-1.5 font-bold font-mono text-zinc-900 dark:text-white">
                                <span>{{ $pair['base_flag'] }}</span>
                                <span>{{ $pair['base'] }}</span>
                                <span class="text-zinc-400">/</span>
                                <span>{{ $pair['quote_flag'] }}</span>
                                <span>{{ $pair['quote'] }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-zinc-500 font-mono text-[11px]">
                                <span>{{ $pair['count'] }} trades</span>
                                <span class="font-bold text-zinc-800 dark:text-zinc-200">{{ $pair['percentage'] }}%</span>
                            </div>
                        </div>
                        <!-- Progress Bar -->
                        <div class="h-2 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div
                                class="h-full rounded-full bg-zinc-900 dark:bg-zinc-100 transition-all duration-500"
                                style="width: {{ $pair['percentage'] }}%"
                            ></div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-zinc-400">
                        No currency conversion pairs logged yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Row 2: Page Views by Route, Trade Action Intent, and Visitor Devices -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Card 1: Top Visited Routes -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-3">
            <div class="flex items-center justify-between">
                <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-500">Page Views by Route</h4>
                <span class="text-[10px] font-mono text-zinc-400">Total: {{ $this->analyticsKPIs['total_visits'] }}</span>
            </div>
            <div class="space-y-3 pt-1">
                @forelse($this->topVisitedPages as $page)
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-medium text-zinc-700 dark:text-zinc-200 truncate max-w-[180px]">{{ $page['label'] }}</span>
                            <span class="font-mono text-zinc-500 text-[11px]">{{ $page['count'] }} ({{ $page['percentage'] }}%)</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                            <div class="h-full bg-emerald-500" style="width: {{ $page['percentage'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="py-4 text-center text-xs text-zinc-400">
                        No page views logged yet.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Card 2: Devices Breakdown -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-500">Visitor Devices</h4>
            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-medium text-zinc-600 dark:text-zinc-300">Desktop Workstations</span>
                    <span class="font-mono font-bold text-zinc-900 dark:text-white">{{ $this->breakdownMetrics['devices']['desktop'] }}%</span>
                </div>
                <div class="h-1.5 w-full rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                    <div class="h-full bg-zinc-900 dark:bg-zinc-100" style="width: {{ $this->breakdownMetrics['devices']['desktop'] }}%"></div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <span class="font-medium text-zinc-600 dark:text-zinc-300">Mobile Smartphones</span>
                    <span class="font-mono font-bold text-zinc-900 dark:text-white">{{ $this->breakdownMetrics['devices']['mobile'] }}%</span>
                </div>
                <div class="h-1.5 w-full rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                    <div class="h-full bg-indigo-500" style="width: {{ $this->breakdownMetrics['devices']['mobile'] }}%"></div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <span class="font-medium text-zinc-600 dark:text-zinc-300">Tablets</span>
                    <span class="font-mono font-bold text-zinc-900 dark:text-white">{{ $this->breakdownMetrics['devices']['tablet'] }}%</span>
                </div>
                <div class="h-1.5 w-full rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                    <div class="h-full bg-zinc-400" style="width: {{ $this->breakdownMetrics['devices']['tablet'] }}%"></div>
                </div>
            </div>
        </div>

        <!-- Card 3: Platform Telemetry & Health -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-500">System Telemetry</h4>
            <div class="space-y-2 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-zinc-600 dark:text-zinc-400">Database Engine</span>
                    <span class="font-mono font-semibold text-zinc-900 dark:text-white">SQLite 3 / WAL</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-zinc-600 dark:text-zinc-400">Mathematical Scale</span>
                    <span class="font-mono font-semibold text-zinc-900 dark:text-white">18 Decimals (Brick)</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-zinc-600 dark:text-zinc-400">Active Rate Providers</span>
                    <span class="font-mono font-semibold text-emerald-600 dark:text-emerald-400">Frankfurter + AbokiFX + P2P</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-zinc-600 dark:text-zinc-400">API Health</span>
                    <span class="inline-flex items-center gap-1 font-bold text-emerald-600">
                        <span class="size-1.5 rounded-full bg-emerald-500"></span>
                        <span>100% Operational</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Interactive Live Activity Streams (Conversions & Page Views) -->
    <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-zinc-100 pb-3 dark:border-zinc-800">
            <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Live Real-Time Activity Streams</h3>
                <p class="text-xs text-zinc-500">Switch between FX conversions stream and web page visitor telemetry.</p>
            </div>

            <!-- Activity Stream Switcher Pills -->
            <div class="inline-flex rounded-lg bg-zinc-100 p-0.5 dark:bg-zinc-800 text-xs">
                <button
                    type="button"
                    wire:click="$set('activityStreamTab', 'conversions')"
                    class="rounded-md px-3 py-1 font-semibold transition {{ $activityStreamTab === 'conversions' ? 'bg-white text-zinc-900 shadow-2xs dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white' }}"
                >
                    Live Conversions ({{ $this->analyticsKPIs['total_conversions'] }})
                </button>
                <button
                    type="button"
                    wire:click="$set('activityStreamTab', 'pageviews')"
                    class="rounded-md px-3 py-1 font-semibold transition {{ $activityStreamTab === 'pageviews' ? 'bg-white text-zinc-900 shadow-2xs dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white' }}"
                >
                    Live Page Visits ({{ $this->analyticsKPIs['total_visits'] }})
                </button>
            </div>
        </div>

        <!-- Stream 1: Conversions Table -->
        @if($activityStreamTab === 'conversions')
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-mono">
                    <thead class="border-b border-zinc-200 bg-zinc-50/70 dark:border-zinc-800 dark:bg-zinc-800/40 font-sans">
                        <tr>
                            <th class="px-3.5 py-2 font-semibold text-zinc-600 dark:text-zinc-300">Pair</th>
                            <th class="px-3.5 py-2 font-semibold text-zinc-600 dark:text-zinc-300">Source</th>
                            <th class="px-3.5 py-2 font-semibold text-zinc-600 dark:text-zinc-300">Market</th>
                            <th class="px-3.5 py-2 font-semibold text-zinc-600 dark:text-zinc-300">Trade Action</th>
                            <th class="px-3.5 py-2 font-semibold text-zinc-600 dark:text-zinc-300">Amount</th>
                            <th class="px-3.5 py-2 font-semibold text-zinc-600 dark:text-zinc-300">Rate Applied</th>
                            <th class="px-3.5 py-2 font-semibold text-zinc-600 dark:text-zinc-300">Result</th>
                            <th class="px-3.5 py-2 text-right font-semibold text-zinc-600 dark:text-zinc-300">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        @forelse($this->recentConversions as $conv)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                                <td class="px-3.5 py-2.5 font-bold text-zinc-900 dark:text-white">
                                    <span>{{ $conv['base_flag'] }}</span>
                                    <span>{{ $conv['base'] }}</span>
                                    <span class="text-zinc-400">/</span>
                                    <span>{{ $conv['quote_flag'] }}</span>
                                    <span>{{ $conv['quote'] }}</span>
                                </td>
                                <td class="px-3.5 py-2.5 font-sans">
                                    <span class="rounded px-1.5 py-0.5 text-[10px] font-semibold {{ $conv['source'] === 'api' ? 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300' : 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300' }}">
                                        {{ $conv['source'] === 'api' ? 'REST API' : 'Web UI' }}
                                    </span>
                                </td>
                                <td class="px-3.5 py-2.5 font-sans">
                                    <span class="rounded px-1.5 py-0.5 text-[10px] font-bold uppercase {{ $conv['market_type'] === 'parallel' ? 'text-amber-600' : 'text-blue-600' }}">
                                        {{ $conv['market_type'] }}
                                    </span>
                                </td>
                                <td class="px-3.5 py-2.5 font-sans text-zinc-500 uppercase text-[10px] font-bold">
                                    {{ $conv['trade_action'] }}
                                </td>
                                <td class="px-3.5 py-2.5 text-zinc-700 dark:text-zinc-300">{{ $conv['amount'] }} {{ $conv['base'] }}</td>
                                <td class="px-3.5 py-2.5 text-zinc-500">{{ $conv['rate'] }}</td>
                                <td class="px-3.5 py-2.5 font-bold text-zinc-900 dark:text-white">{{ $conv['result'] }} {{ $conv['quote'] }}</td>
                                <td class="px-3.5 py-2.5 text-right font-sans text-zinc-400 text-[11px]">{{ $conv['time'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3.5 py-6 text-center font-sans text-zinc-400">
                                    No conversion activity recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <!-- Stream 2: Page Views Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-mono">
                    <thead class="border-b border-zinc-200 bg-zinc-50/70 dark:border-zinc-800 dark:bg-zinc-800/40 font-sans">
                        <tr>
                            <th class="px-3.5 py-2 font-semibold text-zinc-600 dark:text-zinc-300">Visited Path</th>
                            <th class="px-3.5 py-2 font-semibold text-zinc-600 dark:text-zinc-300">IP Hash</th>
                            <th class="px-3.5 py-2 font-semibold text-zinc-600 dark:text-zinc-300">Device</th>
                            <th class="px-3.5 py-2 font-semibold text-zinc-600 dark:text-zinc-300">Browser</th>
                            <th class="px-3.5 py-2 font-semibold text-zinc-600 dark:text-zinc-300">Country</th>
                            <th class="px-3.5 py-2 text-right font-semibold text-zinc-600 dark:text-zinc-300">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        @forelse($this->recentPageViews as $view)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                                <td class="px-3.5 py-2.5 font-bold text-zinc-900 dark:text-white font-sans">
                                    <span class="rounded bg-zinc-100 px-2 py-0.5 font-mono text-[11px] text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                                        {{ $view['path'] }}
                                    </span>
                                </td>
                                <td class="px-3.5 py-2.5 text-zinc-500">{{ $view['ip'] }}</td>
                                <td class="px-3.5 py-2.5 font-sans">
                                    <span class="capitalize text-zinc-700 dark:text-zinc-300">{{ $view['device'] }}</span>
                                </td>
                                <td class="px-3.5 py-2.5 font-sans">
                                    <span class="text-zinc-600 dark:text-zinc-400">{{ $view['browser'] }}</span>
                                </td>
                                <td class="px-3.5 py-2.5 text-zinc-600 dark:text-zinc-400 font-sans">
                                    {{ $view['country'] }}
                                </td>
                                <td class="px-3.5 py-2.5 text-right font-sans text-zinc-400 text-[11px]">{{ $view['time'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3.5 py-6 text-center font-sans text-zinc-400">
                                    No page visits recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
