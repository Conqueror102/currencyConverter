<x-layouts::app :title="__('System Architecture & Operations Guide')">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-12">
            <!-- Main Editorial Content Area (Laravel Docs Style) -->
            <main class="lg:col-span-9 space-y-14">
                <!-- Page Header -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-mono font-bold uppercase tracking-wider text-red-600 dark:text-red-400">Architecture & Operations</span>
                        <span class="text-zinc-400 dark:text-zinc-600">/</span>
                        <span class="text-xs font-mono text-zinc-500">Comprehensive Engineering Manual</span>
                    </div>
                    <h1 class="text-4xl font-extrabold tracking-tight text-zinc-900 dark:text-white sm:text-5xl">
                        System Architecture & Financial Calculation Engine
                    </h1>
                    <p class="text-lg leading-relaxed text-zinc-600 dark:text-zinc-400 max-w-3xl">
                        An exhaustive operational and mathematical reference for LemonWaresEX: multi-market ingestion pipelines, provider source selection algorithms, crypto P2P order book scraping, arbitrary-precision arithmetic, triangular cross-rate derivations via the USD Pivot Bridge, and administrative controls.
                    </p>
                </div>

                <!-- SECTION 1: System Architecture & Visual Diagram -->
                <section id="architecture" class="scroll-mt-24 space-y-6">
                    <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        System Architecture & Data Pipeline
                    </h2>
                    
                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        LemonWaresEX is structured using <strong>Domain-Driven Design (DDD)</strong> and Clean Architecture patterns. The platform decouples external market scouts from financial domain calculation, persistence caching, and API delivery layers.
                    </p>

                    <!-- Modern Visual Flowchart -->
                    <div class="my-8 rounded-2xl border border-zinc-200 bg-zinc-50/70 p-6 dark:border-zinc-800 dark:bg-zinc-900/60 shadow-xs">
                        <div class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-6">
                            End-to-End System Pipeline & Data Flow
                        </div>

                        <div class="space-y-6">
                            <!-- Tier 1: Ingestion Sources -->
                            <div>
                                <div class="text-[11px] font-mono text-zinc-400 uppercase mb-2">Tier 1: Multi-Market Ingestion Sources</div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div class="rounded-xl border border-blue-200 bg-white p-3.5 dark:border-blue-900/60 dark:bg-zinc-900">
                                        <div class="font-mono text-xs font-bold text-blue-700 dark:text-blue-400">Frankfurter / ECB</div>
                                        <div class="text-[11px] text-zinc-500 mt-1">Official G10 interbank benchmark exchange rates (EUR Base).</div>
                                    </div>
                                    <div class="rounded-xl border border-emerald-200 bg-white p-3.5 dark:border-emerald-900/60 dark:bg-zinc-900">
                                        <div class="font-mono text-xs font-bold text-emerald-700 dark:text-emerald-400">Binance P2P Order Book</div>
                                        <div class="text-[11px] text-zinc-500 mt-1">USDT/NGN real-time top-10 verified merchant median price quotes.</div>
                                    </div>
                                    <div class="rounded-xl border border-amber-200 bg-white p-3.5 dark:border-amber-900/60 dark:bg-zinc-900">
                                        <div class="font-mono text-xs font-bold text-amber-700 dark:text-amber-400">AbokiFX BDC Desks</div>
                                        <div class="text-[11px] text-zinc-500 mt-1">Physical street cash Bureau De Change Buy (Bid) & Sell (Ask) spreads.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Flow Arrow -->
                            <div class="flex justify-center text-zinc-400 dark:text-zinc-600">
                                <flux:icon icon="arrow-down" class="size-5 animate-pulse" />
                            </div>

                            <!-- Tier 2: Storage Vault -->
                            <div>
                                <div class="text-[11px] font-mono text-zinc-400 uppercase mb-2">Tier 2: Ingestion Normalization & Storage Vault</div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="rounded-xl border border-zinc-200 bg-white p-3.5 dark:border-zinc-800 dark:bg-zinc-900">
                                        <div class="font-mono text-xs font-bold text-zinc-900 dark:text-white">RateSyncService</div>
                                        <div class="text-[11px] text-zinc-500 mt-1">Normalizes DTOs, validates schemas, runs atomic SQL upserts, flushes stale cache tags.</div>
                                    </div>
                                    <div class="rounded-xl border border-zinc-200 bg-white p-3.5 dark:border-zinc-800 dark:bg-zinc-900">
                                        <div class="font-mono text-xs font-bold text-zinc-900 dark:text-white">Database & Fast Memory Cache</div>
                                        <div class="text-[11px] text-zinc-500 mt-1">Persistent SQLite/MySQL tables + in-memory Redis keys for sub-millisecond lookups.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Flow Arrow -->
                            <div class="flex justify-center text-zinc-400 dark:text-zinc-600">
                                <flux:icon icon="arrow-down" class="size-5 animate-pulse" />
                            </div>

                            <!-- Tier 3: Core Domain Math Engine -->
                            <div>
                                <div class="text-[11px] font-mono text-zinc-400 uppercase mb-2">Tier 3: 15-Decimal Arbitrary Precision Engine</div>
                                <div class="rounded-xl border border-purple-200 bg-purple-50/40 p-4 dark:border-purple-900/60 dark:bg-purple-950/20 space-y-2">
                                    <div class="flex items-center justify-between font-mono text-xs font-bold text-purple-900 dark:text-purple-300">
                                        <span>ConversionService (Core Financial Domain)</span>
                                        <span class="rounded bg-purple-200/70 dark:bg-purple-900/60 px-2 py-0.5 text-[10px]">Brick\Math\BigDecimal</span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[11px] text-purple-950 dark:text-purple-200 font-mono pt-1">
                                        <div>• Zero IEEE-754 Float Error</div>
                                        <div>• USD Pivot Cross-Triangulation</div>
                                        <div>• Bid/Ask Spread Intent</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Flow Arrow -->
                            <div class="flex justify-center text-zinc-400 dark:text-zinc-600">
                                <flux:icon icon="arrow-down" class="size-5 animate-pulse" />
                            </div>

                            <!-- Tier 4: Client Delivery -->
                            <div>
                                <div class="text-[11px] font-mono text-zinc-400 uppercase mb-2">Tier 4: Metered Delivery & Client Presentation</div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div class="rounded-xl border border-zinc-200 bg-white p-3.5 dark:border-zinc-800 dark:bg-zinc-900">
                                        <div class="font-mono text-xs font-bold text-zinc-900 dark:text-white">Developer REST API</div>
                                        <div class="text-[11px] text-zinc-500 mt-1">/api/v1/convert & /rates with SHA-256 tokens & tiered rate limits.</div>
                                    </div>
                                    <div class="rounded-xl border border-zinc-200 bg-white p-3.5 dark:border-zinc-800 dark:bg-zinc-900">
                                        <div class="font-mono text-xs font-bold text-zinc-900 dark:text-white">Livewire 3 Converter</div>
                                        <div class="text-[11px] text-zinc-500 mt-1">Reactive user interface calculating instant conversions without full-page refreshes.</div>
                                    </div>
                                    <div class="rounded-xl border border-zinc-200 bg-white p-3.5 dark:border-zinc-800 dark:bg-zinc-900">
                                        <div class="font-mono text-xs font-bold text-zinc-900 dark:text-white">Admin Control Center</div>
                                        <div class="text-[11px] text-zinc-500 mt-1">Real-time telemetry, rate overrides, API key provisioning, and currency activation.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- SECTION 2: How the System Chooses Sources & Resolves Rates -->
                <section id="source-selection" class="scroll-mt-24 space-y-6 border-t border-zinc-200 pt-10 dark:border-zinc-800">
                    <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        How the System Chooses Sources & Resolves Rates
                    </h2>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        LemonWaresEX employs an intelligent multi-tiered <strong>Source Resolution Algorithm</strong> in <code class="rounded bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 font-mono text-xs text-zinc-800 dark:text-zinc-200">ConversionService::resolveRate()</code> and <code class="rounded bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 font-mono text-xs text-zinc-800 dark:text-zinc-200">ProviderManager</code> to determine which rate source to query, cache, or derive.
                    </p>

                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        1. How the System Decides Which Source to Query
                    </h3>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        A common operational question is: <em>"How does LemonWaresEX know when to use Frankfurter, when to use AbokiFX, when to use Binance P2P, and when to combine them?"</em>
                    </p>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        The decision is governed by the <strong>currency pairs in the query</strong> and the requested <strong>market type</strong>:
                    </p>

                    <div class="space-y-6 my-6 text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        <!-- Case 1 -->
                        <div class="border-l-2 border-blue-500 pl-4 space-y-1">
                            <div class="font-bold text-zinc-900 dark:text-white">Case 1: Standard Major Currencies (EUR, USD, GBP, JPY, CAD)</div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                <strong>Source Chosen:</strong> <span class="font-mono font-semibold text-blue-600 dark:text-blue-400">Frankfurter (European Central Bank)</span><br>
                                <strong>Why:</strong> There is no black market between developed western currencies. They trade on official interbank order books worldwide. Frankfurter provides the benchmark rate relative to the Euro canonical base.
                            </p>
                        </div>

                        <!-- Case 2 -->
                        <div class="border-l-2 border-emerald-500 pl-4 space-y-1">
                            <div class="font-bold text-zinc-900 dark:text-white">Case 2: Nigeria Street Cash Direct Pair (USD → NGN Parallel)</div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                <strong>Source Chosen:</strong> <span class="font-mono font-semibold text-emerald-600 dark:text-emerald-400">AbokiFX BDC + Binance P2P (USDT/NGN)</span><br>
                                <strong>Why:</strong> Nigeria has an active parallel market driven by Broad Street BDC cash desks and USDT digital order books. The system polls AbokiFX for physical street cash spreads and Binance P2P for digital liquidity.
                            </p>
                        </div>

                        <!-- Case 3 -->
                        <div class="border-l-2 border-amber-500 pl-4 space-y-1">
                            <div class="font-bold text-zinc-900 dark:text-white">Case 3: Other Frontier Parallel Currencies (USD → ARS, GHS, KES, EGP, VES)</div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                <strong>Source Chosen:</strong> <span class="font-mono font-semibold text-amber-600 dark:text-amber-400">Binance P2P Order Books</span><br>
                                <strong>Why:</strong> In Argentina (Dólar Blue), Ghana (Cedi P2P), and Kenya (Shilling P2P), Binance P2P provides the most liquid, real-time median pricing from verified local merchants.
                            </p>
                        </div>

                        <!-- Case 4 -->
                        <div class="border-l-2 border-purple-500 pl-4 space-y-1">
                            <div class="font-bold text-zinc-900 dark:text-white">Case 4: Non-USD Parallel Conversions (e.g. EUR → NGN or CAD → NGN Parallel)</div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                <strong>Sources Chosen (Coordinated):</strong> <span class="font-mono font-semibold text-purple-600 dark:text-purple-400">Frankfurter (Source 1) + Binance P2P / AbokiFX (Source 2)</span><br>
                                <strong>Why:</strong> In emerging markets, there are virtually no direct EUR/NGN or CAD/NGN street cash order books—95%+ of liquidity is denominated in USD. Therefore, the system automatically pulls <code class="font-mono text-xs">EUR → USD</code> from <strong>Frankfurter</strong> and <code class="font-mono text-xs">USD → NGN</code> from <strong>Binance P2P / AbokiFX</strong>, multiplying them together to create the exact parallel rate.
                            </p>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        2. Query-by-Query Source Decision Matrix
                    </h3>

                    <div class="overflow-x-auto my-6">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-zinc-300 dark:border-zinc-700">
                                    <th class="py-3 px-4 font-semibold text-zinc-900 dark:text-white">Input Query</th>
                                    <th class="py-3 px-4 font-semibold text-zinc-900 dark:text-white">Market</th>
                                    <th class="py-3 px-4 font-semibold text-zinc-900 dark:text-white">Source 1 (Base/Pivot)</th>
                                    <th class="py-3 px-4 font-semibold text-zinc-900 dark:text-white">Source 2 (Local Bridge)</th>
                                    <th class="py-3 px-4 font-semibold text-zinc-900 dark:text-white">Resolution Method</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 text-zinc-600 dark:text-zinc-400">
                                <tr>
                                    <td class="py-3.5 px-4 font-mono font-bold text-zinc-900 dark:text-white">EUR → USD</td>
                                    <td class="py-3.5 px-4 font-mono">official</td>
                                    <td class="py-3.5 px-4">Frankfurter (ECB)</td>
                                    <td class="py-3.5 px-4 text-zinc-400">— None (Direct)</td>
                                    <td class="py-3.5 px-4">Direct official interbank rate.</td>
                                </tr>
                                <tr>
                                    <td class="py-3.5 px-4 font-mono font-bold text-zinc-900 dark:text-white">USD → NGN</td>
                                    <td class="py-3.5 px-4 font-mono">official</td>
                                    <td class="py-3.5 px-4">Official Central Bank</td>
                                    <td class="py-3.5 px-4 text-zinc-400">— None (Direct)</td>
                                    <td class="py-3.5 px-4">Official CBN benchmark rate.</td>
                                </tr>
                                <tr>
                                    <td class="py-3.5 px-4 font-mono font-bold text-zinc-900 dark:text-white">USD → NGN</td>
                                    <td class="py-3.5 px-4 font-mono">parallel</td>
                                    <td class="py-3.5 px-4">AbokiFX / Binance P2P</td>
                                    <td class="py-3.5 px-4 text-zinc-400">— None (Direct)</td>
                                    <td class="py-3.5 px-4">Live street cash / USDT order book median.</td>
                                </tr>
                                <tr>
                                    <td class="py-3.5 px-4 font-mono font-bold text-zinc-900 dark:text-white">EUR → NGN</td>
                                    <td class="py-3.5 px-4 font-mono">parallel</td>
                                    <td class="py-3.5 px-4 font-bold text-blue-600 dark:text-blue-400">Frankfurter (EUR→USD)</td>
                                    <td class="py-3.5 px-4 font-bold text-emerald-600 dark:text-emerald-400">AbokiFX / P2P (USD→NGN)</td>
                                    <td class="py-3.5 px-4"><strong>USD Pivot Bridge</strong> ($1.1598 \times 1,620.00 = 1,878.88$).</td>
                                </tr>
                                <tr>
                                    <td class="py-3.5 px-4 font-mono font-bold text-zinc-900 dark:text-white">GBP → NGN</td>
                                    <td class="py-3.5 px-4 font-mono">parallel</td>
                                    <td class="py-3.5 px-4 font-bold text-blue-600 dark:text-blue-400">Frankfurter (GBP→USD)</td>
                                    <td class="py-3.5 px-4 font-bold text-emerald-600 dark:text-emerald-400">AbokiFX / P2P (USD→NGN)</td>
                                    <td class="py-3.5 px-4"><strong>USD Pivot Bridge</strong> ($1.3548 \times 1,620.00 = 2,194.78$).</td>
                                </tr>
                                <tr>
                                    <td class="py-3.5 px-4 font-mono font-bold text-zinc-900 dark:text-white">USD → ARS</td>
                                    <td class="py-3.5 px-4 font-mono">parallel</td>
                                    <td class="py-3.5 px-4">Binance P2P (USDT/ARS)</td>
                                    <td class="py-3.5 px-4 text-zinc-400">— None (Direct)</td>
                                    <td class="py-3.5 px-4">Argentine Dólar Blue P2P median.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        3. The 6-Step Technical Rate Resolution Hierarchy
                    </h3>

                    <div class="space-y-4 my-6 text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        <div class="border-l-2 border-zinc-300 dark:border-zinc-700 pl-4">
                            <strong class="text-zinc-900 dark:text-white">Step 1 — Market Type Routing (<code class="font-mono text-xs">official</code> vs <code class="font-mono text-xs">parallel</code>):</strong>
                            <p class="text-zinc-600 dark:text-zinc-400 text-sm mt-1">
                                The system branches the search space based on client intent. Official requests target central bank interbank datasets; parallel requests target street BDC and crypto P2P order books.
                            </p>
                        </div>

                        <div class="border-l-2 border-zinc-300 dark:border-zinc-700 pl-4">
                            <strong class="text-zinc-900 dark:text-white">Step 2 — Explicit Provider Override:</strong>
                            <p class="text-zinc-600 dark:text-zinc-400 text-sm mt-1">
                                If a developer requests a specific provider (e.g. <code class="font-mono text-xs">?provider=frankfurter</code> or <code class="font-mono text-xs">?provider=abokifx</code>), the system filters queries exclusively to rates ingested from that provider. If omitted, the default provider configured in <code class="font-mono text-xs">config/exchange-rates.php</code> is used.
                            </p>
                        </div>

                        <div class="border-l-2 border-zinc-300 dark:border-zinc-700 pl-4">
                            <strong class="text-zinc-900 dark:text-white">Step 3 — Direct Stored Pair Match:</strong>
                            <p class="text-zinc-600 dark:text-zinc-400 text-sm mt-1">
                                Queries the database and in-memory cache for an exact match (<code class="font-mono text-xs">base = FROM</code>, <code class="font-mono text-xs">quote = TO</code>, <code class="font-mono text-xs">market = marketType</code>).
                            </p>
                        </div>

                        <div class="border-l-2 border-zinc-300 dark:border-zinc-700 pl-4">
                            <strong class="text-zinc-900 dark:text-white">Step 4 — Inverted Direct Match:</strong>
                            <p class="text-zinc-600 dark:text-zinc-400 text-sm mt-1">
                                If direct is absent, queries the inverse (<code class="font-mono text-xs">base = TO</code>, <code class="font-mono text-xs">quote = FROM</code>) and calculates the exact mathematical inverse <code class="font-mono text-xs">1 / rate</code> with inverted bid/ask spreads.
                            </p>
                        </div>

                        <div class="border-l-2 border-zinc-300 dark:border-zinc-700 pl-4">
                            <strong class="text-zinc-900 dark:text-white">Step 5 — Bridge-Aware Cross-Rate Triangulation:</strong>
                            <p class="text-zinc-600 dark:text-zinc-400 text-sm mt-1">
                                For pairs without direct trading books, the engine dynamically selects the appropriate pivot bridge:
                            </p>
                            <ul class="list-disc list-inside text-xs text-zinc-500 font-mono mt-1 space-y-0.5">
                                <li><strong>Parallel Market Bridge:</strong> Automatically selects <strong>USD</strong> as the bridge (<code class="font-mono text-xs">Target → USD Official × USD → Local Parallel</code>) because global P2P liquidity centers on USDT/USD.</li>
                                <li><strong>Official Market Bridge:</strong> Automatically selects <strong>EUR (Canonical Base)</strong> as the bridge (<code class="font-mono text-xs">Rate(TO) / Rate(FROM)</code>) because the European Central Bank standardizes all rates relative to the Euro.</li>
                            </ul>
                        </div>

                        <div class="border-l-2 border-zinc-300 dark:border-zinc-700 pl-4">
                            <strong class="text-zinc-900 dark:text-white">Step 6 — On-Demand Auto-Ingestion & Fallback:</strong>
                            <p class="text-zinc-600 dark:text-zinc-400 text-sm mt-1">
                                If a required rate is missing on a fresh installation, the domain service invokes <code class="font-mono text-xs">RateSyncService</code> on-demand to fetch from upstream providers before returning the result.
                            </p>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        2. Administrative Manual Override Priority
                    </h3>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        When an administrator modifies an exchange rate in the <strong>Live Rates Matrix</strong> (<code class="font-mono text-xs">/dashboard?tab=rates</code>), the system tags the record with <code class="font-mono text-xs">provider = "manual_override"</code> and immediately flushes the memory cache. <strong>Manual administrative overrides always take precedence</strong> over automated ingestion until updated or re-synced.
                    </p>
                </section>

                <!-- SECTION 3: Part 1 - How We Get the P2P / Street Rate -->
                <section id="p2p-rate" class="scroll-mt-24 space-y-6 border-t border-zinc-200 pt-10 dark:border-zinc-800">
                    <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        Part 1: Ingesting the Real-Time Parallel / Street Rate (USD → NGN)
                    </h2>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        In traditional finance, obtaining black-market street cash rates required physically dispatching agents to Broad Street, Lagos, or local Bureau De Change clusters. Today, digital liquidity order books have completely transformed foreign exchange pricing.
                    </p>

                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        Step 1: Why USDT (Tether) Represents the True Street Price of the Dollar
                    </h3>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        USDT (Tether) is a dollar-backed stablecoin structurally pegged $1:1$ to the United States Dollar ($1\text{ USDT} = \$1.00\text{ USD}$). Tens of millions of dollars are exchanged every minute between verified merchants and individuals on crypto peer-to-peer (P2P) platforms like Binance, Bybit, and KuCoin.
                    </p>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        Because money flows without central bank quotas or foreign exchange restrictions on these platforms, <strong>the price people pay for 1 USDT in Nigerian Naira represents the true, unadulterated street market clearing price of $1.00 USD cash</strong>.
                    </p>

                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        Step 2: The Data Ingestion Request
                    </h3>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        Our background worker queries the open P2P liquidity order book endpoint:
                    </p>

                    <!-- Code Block: POST request -->
                    <div class="my-6 rounded-xl border border-zinc-800 bg-[#18181b] overflow-hidden shadow-md">
                        <div class="border-b border-zinc-800 bg-[#121214] px-4 py-2 text-xs font-mono text-zinc-400 flex items-center justify-between">
                            <span>HTTP POST /bapi/c2c/v2/friendly/c2c/adv/search</span>
                            <span class="text-emerald-400">Content-Type: application/json</span>
                        </div>
                        <pre class="p-4 font-mono text-xs leading-relaxed text-zinc-300 overflow-x-auto"><code><span class="text-zinc-500">// Request Payload sent by ParallelMarketProvider</span>
{
  <span class="text-cyan-400">"asset"</span>: <span class="text-emerald-300">"USDT"</span>,
  <span class="text-cyan-400">"fiat"</span>: <span class="text-emerald-300">"NGN"</span>,
  <span class="text-cyan-400">"tradeType"</span>: <span class="text-emerald-300">"BUY"</span>,
  <span class="text-cyan-400">"page"</span>: <span class="text-amber-300">1</span>,
  <span class="text-cyan-400">"rows"</span>: <span class="text-amber-300">10</span>
}</code></pre>
                    </div>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        The upstream provider returns the active price listings from top verified merchants:
                    </p>

                    <!-- JSON Ads response -->
                    <div class="my-6 rounded-xl border border-zinc-800 bg-[#18181b] overflow-hidden shadow-md">
                        <div class="border-b border-zinc-800 bg-[#121214] px-4 py-2 text-xs font-mono text-zinc-400">
                            Response JSON (Top Merchant Ads)
                        </div>
                        <pre class="p-4 font-mono text-xs leading-relaxed text-zinc-300 overflow-x-auto"><code>[
  { <span class="text-cyan-400">"price"</span>: <span class="text-emerald-300">"1619.50"</span>, <span class="text-cyan-400">"merchant"</span>: <span class="text-emerald-300">"VerifiedFX_Pro"</span> },
  { <span class="text-cyan-400">"price"</span>: <span class="text-emerald-300">"1620.00"</span>, <span class="text-cyan-400">"merchant"</span>: <span class="text-emerald-300">"Lagos_Liquidity_Desk"</span> },
  { <span class="text-cyan-400">"price"</span>: <span class="text-emerald-300">"1620.20"</span>, <span class="text-cyan-400">"merchant"</span>: <span class="text-emerald-300">"GlobalP2P_Fast"</span> },
  { <span class="text-cyan-400">"price"</span>: <span class="text-emerald-300">"1621.00"</span>, <span class="text-cyan-400">"merchant"</span>: <span class="text-emerald-300">"Capital_Bridge"</span> }
]</code></pre>
                    </div>

                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        Step 3: Median Filtering, Normalization & Storage
                    </h3>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        To prevent scam ads or wash-trading outliers from distorting rates, LemonWaresEX calculates the <strong>median price</strong> of the top verified ads ($1,620.00$) and persists it into the <code class="font-mono text-xs">exchange_rates</code> database table:
                    </p>

                    <div class="my-6 rounded-xl border border-zinc-800 bg-[#18181b] p-4 font-mono text-xs text-zinc-300 space-y-1">
                        <div><span class="text-zinc-500">base_currency:</span> <span class="text-emerald-400">"USD"</span></div>
                        <div><span class="text-zinc-500">quote_currency:</span> <span class="text-emerald-400">"NGN"</span></div>
                        <div><span class="text-zinc-500">rate:</span> <span class="text-amber-300">1620.000000</span></div>
                        <div><span class="text-zinc-500">market_type:</span> <span class="text-emerald-400">"parallel"</span></div>
                        <div><span class="text-zinc-500">provider:</span> <span class="text-emerald-400">"parallel_p2p"</span></div>
                    </div>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        This single anchor rate—<code class="font-mono text-xs font-bold text-zinc-900 dark:text-white">1 USD = ₦1,620.00 NGN</code> on the parallel market—serves as the foundational liquidity benchmark for all subsequent global calculations.
                    </p>
                </section>

                <!-- SECTION 4: Part 2 - Deriving All Other Currencies (USD Pivot Bridge) -->
                <section id="math-derivation" class="scroll-mt-24 space-y-6 border-t border-zinc-200 pt-10 dark:border-zinc-800">
                    <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        Part 2: The Mathematics: Deriving ALL Other Currencies (EUR, GBP, CAD, JPY)
                    </h2>

                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        The Financial Principle: The USD Pivot Bridge
                    </h3>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        Between major developed currencies like the US Dollar (USD), Euro (EUR), British Pound (GBP), Canadian Dollar (CAD), and Japanese Yen (JPY), <strong>there is no black market</strong>. These currencies trade at pure, transparent, official interbank rates across global foreign exchange markets:
                    </p>

                    <ul class="list-disc list-inside space-y-1.5 text-[15px] text-zinc-700 dark:text-zinc-300 font-mono my-4 pl-2">
                        <li>1 EUR = 1.1598 USD (Official ECB Interbank)</li>
                        <li>1 GBP = 1.3548 USD (Official Bank of England Interbank)</li>
                        <li>1 CAD = 0.7320 USD (Official Bank of Canada Interbank)</li>
                    </ul>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        Because we know the official value of EUR against USD, and we know the live street value of USD against NGN (Parallel), we apply the <strong>Triangular Cross-Rate Formula</strong>:
                    </p>

                    <!-- Core Formula Box -->
                    <div class="my-6 rounded-xl border border-zinc-800 bg-[#18181b] p-5 font-mono text-xs text-zinc-300 space-y-2 shadow-md">
                        <div class="text-zinc-500">// TRIANGULAR CROSS-RATE FORMULA (PARALLEL MARKET)</div>
                        <div class="text-emerald-400 font-bold text-sm sm:text-base">
                            Rate(Target → NGN Parallel) = Rate(Target → USD Official) × Rate(USD → NGN Parallel)
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        Concrete Worked Examples
                    </h3>

                    <!-- Example 1: EUR to NGN -->
                    <div class="space-y-3 my-6">
                        <h4 class="text-base font-bold text-zinc-900 dark:text-white">
                            Example A: Euro (€) to Nigerian Naira (₦) Parallel Rate
                        </h4>
                        <div class="rounded-xl border border-zinc-200 bg-zinc-50/70 p-4 font-mono text-xs dark:border-zinc-800 dark:bg-zinc-900/60 space-y-1.5 leading-relaxed text-zinc-700 dark:text-zinc-300">
                            <div>• Euro to USD (Official ECB): <strong>1 EUR = 1.1598 USD</strong></div>
                            <div>• USD to NGN (Parallel Street): <strong>1 USD = 1,620.00 NGN</strong></div>
                            <div class="text-emerald-600 dark:text-emerald-400 font-bold pt-1 border-t border-zinc-200 dark:border-zinc-800">
                                Calculation: 1.1598 × 1,620.00 = 1,878.88 NGN per Euro
                            </div>
                            <div class="text-zinc-500 text-[11px] font-sans">Result: On the parallel street market, €100 converts to <strong>₦187,888.00 NGN</strong>.</div>
                        </div>
                    </div>

                    <!-- Example 2: GBP to NGN -->
                    <div class="space-y-3 my-6">
                        <h4 class="text-base font-bold text-zinc-900 dark:text-white">
                            Example B: British Pound (£) to Nigerian Naira (₦) Parallel Rate
                        </h4>
                        <div class="rounded-xl border border-zinc-200 bg-zinc-50/70 p-4 font-mono text-xs dark:border-zinc-800 dark:bg-zinc-900/60 space-y-1.5 leading-relaxed text-zinc-700 dark:text-zinc-300">
                            <div>• GBP to USD (Official): <strong>1 GBP = 1.3548 USD</strong></div>
                            <div>• USD to NGN (Parallel Street): <strong>1 USD = 1,620.00 NGN</strong></div>
                            <div class="text-emerald-600 dark:text-emerald-400 font-bold pt-1 border-t border-zinc-200 dark:border-zinc-800">
                                Calculation: 1.3548 × 1,620.00 = 2,194.78 NGN per Pound
                            </div>
                            <div class="text-zinc-500 text-[11px] font-sans">Result: On the parallel street market, £100 converts to <strong>₦219,478.00 NGN</strong>.</div>
                        </div>
                    </div>

                    <!-- Example 3: CAD to NGN -->
                    <div class="space-y-3 my-6">
                        <h4 class="text-base font-bold text-zinc-900 dark:text-white">
                            Example C: Canadian Dollar ($ CAD) to Nigerian Naira (₦) Parallel Rate
                        </h4>
                        <div class="rounded-xl border border-zinc-200 bg-zinc-50/70 p-4 font-mono text-xs dark:border-zinc-800 dark:bg-zinc-900/60 space-y-1.5 leading-relaxed text-zinc-700 dark:text-zinc-300">
                            <div>• CAD to USD (Official): <strong>1 CAD = 0.7320 USD</strong></div>
                            <div>• USD to NGN (Parallel Street): <strong>1 USD = 1,620.00 NGN</strong></div>
                            <div class="text-emerald-600 dark:text-emerald-400 font-bold pt-1 border-t border-zinc-200 dark:border-zinc-800">
                                Calculation: 0.7320 × 1,620.00 = 1,185.84 NGN per Canadian Dollar
                            </div>
                            <div class="text-zinc-500 text-[11px] font-sans">Result: On the parallel street market, $100 CAD converts to <strong>₦118,584.00 NGN</strong>.</div>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        The Inverse: Converting Naira (₦) into Foreign Currencies
                    </h3>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        When converting from local currency into foreign currency (e.g. converting ₦1,000,000 NGN), the mathematical engine executes division by the effective parallel rate:
                    </p>

                    <div class="my-6 rounded-xl border border-zinc-800 bg-[#18181b] p-4 font-mono text-xs text-zinc-300 space-y-2">
                        <div>• Into USD: <span class="text-cyan-400">1,000,000 ÷ 1,620.00</span> = <strong class="text-emerald-400">$617.28 USD</strong></div>
                        <div>• Into EUR: <span class="text-cyan-400">1,000,000 ÷ 1,878.88</span> = <strong class="text-emerald-400">€532.23 EUR</strong></div>
                        <div>• Into GBP: <span class="text-cyan-400">1,000,000 ÷ 2,194.78</span> = <strong class="text-emerald-400">£455.63 GBP</strong></div>
                    </div>
                </section>

                <!-- SECTION 5: Part 3 - Black Market Premium & Spread Formula -->
                <section id="spread-calculation" class="scroll-mt-24 space-y-6 border-t border-zinc-200 pt-10 dark:border-zinc-800">
                    <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        Part 3: The Spread & Black Market Premium Calculation
                    </h2>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        To provide complete market transparency, the system automatically compares the live street clearing rate against the official Central Bank benchmark rate using the relative spread formula:
                    </p>

                    <!-- Formula Box -->
                    <div class="my-6 rounded-xl border border-zinc-800 bg-[#18181b] p-5 font-mono text-xs text-zinc-300 space-y-3 shadow-md">
                        <div class="text-zinc-500">// BLACK MARKET PREMIUM (PERCENTAGE SPREAD)</div>
                        <div class="text-emerald-400 font-bold text-sm sm:text-base">
                            Spread (%) = ((Parallel Rate − Official Rate) ÷ Official Rate) × 100
                        </div>
                        <div class="pt-2 border-t border-zinc-800 text-zinc-400 space-y-1">
                            <div>Worked Real-World Calculation:</div>
                            <div class="text-zinc-200">Spread (%) = ((1,620.00 − 1,347.74) ÷ 1,347.74) × 100 = <strong class="text-emerald-400">+20.20%</strong></div>
                        </div>
                    </div>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        This metric is delivered across our REST API metadata (<code class="font-mono text-xs">meta.spread_percentage: "+20.20%"</code>) and live web interface, informing users immediately: <em>"The Parallel street rate is +20.2% higher than the official CBN benchmark."</em>
                    </p>
                </section>

                <!-- SECTION 6: Zero Float Error & Trade Intent Spreads -->
                <section id="float-math" class="scroll-mt-24 space-y-6 border-t border-zinc-200 pt-10 dark:border-zinc-800">
                    <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        Part 4: Precision Mathematics & Trade Intent Spreads
                    </h2>

                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        1. Elimination of IEEE 754 Floating-Point Inaccuracies
                    </h3>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        Standard programming languages use IEEE 754 binary floating-point representations. Because binary cannot represent fractions like <code class="font-mono text-xs">0.1</code> or <code class="font-mono text-xs">0.2</code> cleanly, arithmetic operations drift over time:
                    </p>

                    <!-- Code Example Comparison -->
                    <div class="my-6 rounded-xl border border-zinc-800 bg-[#18181b] overflow-hidden shadow-md">
                        <div class="border-b border-zinc-800 bg-[#121214] px-4 py-2 text-xs font-mono text-zinc-400">
                            Comparison: Standard Float Math vs. LemonWaresEX BigDecimal Engine
                        </div>
                        <pre class="p-4 font-mono text-xs leading-relaxed text-zinc-300 overflow-x-auto"><code><span class="text-zinc-500">// 1. Standard PHP Float (Vulnerable to financial drift)</span>
<span class="text-rose-400">$result</span> = <span class="text-amber-300">0.1</span> + <span class="text-amber-300">0.2</span>; <span class="text-zinc-500">// Output: 0.30000000000000004</span>

<span class="text-zinc-500">// 2. LemonWaresEX Domain Engine (Deterministic Arbitrary Precision)</span>
<span class="text-emerald-400">$amount</span> = <span class="text-cyan-300">BigDecimal</span>::<span class="text-blue-400">of</span>(<span class="text-emerald-300">'100.00'</span>);
<span class="text-emerald-400">$rate</span>   = <span class="text-cyan-300">BigDecimal</span>::<span class="text-blue-400">of</span>(<span class="text-emerald-300">'1.159800'</span>);
<span class="text-emerald-400">$output</span> = <span class="text-emerald-400">$amount</span>-><span class="text-blue-400">multipliedBy</span>(<span class="text-emerald-400">$rate</span>)
    -><span class="text-blue-400">toScale</span>(<span class="text-amber-300">2</span>, <span class="text-cyan-300">RoundingMode</span>::<span class="text-blue-400">HALF_UP</span>); <span class="text-zinc-500">// Output: '115.98'</span></code></pre>
                    </div>

                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        2. Trade Intent: Midpoint, Buy (Ask), and Sell (Bid)
                    </h3>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        Physical Bureau De Change desks and P2P liquidity order books maintain a spread between what they pay to acquire currency and what they charge to dispense currency. LemonWaresEX evaluates conversions according to three trade intent modes:
                    </p>

                    <div class="space-y-4 my-6 text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        <div class="border-l-2 border-zinc-300 dark:border-zinc-700 pl-4">
                            <strong class="text-zinc-900 dark:text-white">Mid-Market (<code class="font-mono text-xs">action=mid</code>):</strong>
                            <p class="text-zinc-600 dark:text-zinc-400 text-sm mt-1">
                                The mathematical midpoint <code class="font-mono text-xs">(Buy + Sell) / 2</code>. Used as an unbiased benchmark for accounting entries, charts, and public dashboards.
                            </p>
                        </div>

                        <div class="border-l-2 border-zinc-300 dark:border-zinc-700 pl-4">
                            <strong class="text-zinc-900 dark:text-white">Sell Foreign Cash (<code class="font-mono text-xs">action=sell</code>):</strong>
                            <p class="text-zinc-600 dark:text-zinc-400 text-sm mt-1">
                                Applies the dealer's <strong>Buy (Bid)</strong> rate. This is what the FX dealer pays the user when the user hands over foreign cash (e.g. converting $100 cash into ₦139,800 at ₦1,398/$).
                            </p>
                        </div>

                        <div class="border-l-2 border-zinc-300 dark:border-zinc-700 pl-4">
                            <strong class="text-zinc-900 dark:text-white">Buy Foreign Cash (<code class="font-mono text-xs">action=buy</code>):</strong>
                            <p class="text-zinc-600 dark:text-zinc-400 text-sm mt-1">
                                Applies the dealer's <strong>Sell (Ask)</strong> rate. This is what the user must pay the dealer in local currency to acquire foreign cash (e.g. paying ₦141,000 to receive $100 cash at ₦1,410/$).
                            </p>
                        </div>
                    </div>
                </section>

                <!-- SECTION 7: Ingestion Automation & Cadence -->
                <section id="automation" class="scroll-mt-24 space-y-6 border-t border-zinc-200 pt-10 dark:border-zinc-800">
                    <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        Part 5: Data Ingestion & Automated Scheduler Cadence
                    </h2>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        All background synchronization is registered in <code class="rounded bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 font-mono text-xs text-zinc-800 dark:text-zinc-200">routes/console.php</code> using the Laravel Scheduler:
                    </p>

                    <!-- Code block: console.php -->
                    <div class="my-6 rounded-xl border border-zinc-800 bg-[#18181b] overflow-hidden shadow-md">
                        <div class="border-b border-zinc-800 bg-[#121214] px-4 py-2 text-xs font-mono text-zinc-400">
                            routes/console.php
                        </div>
                        <pre class="p-4 font-mono text-xs leading-relaxed text-zinc-300 overflow-x-auto"><code><span class="text-zinc-500">// 1. Synchronize currency metadata daily at midnight</span>
<span class="text-cyan-300">Schedule</span>::<span class="text-blue-400">command</span>(<span class="text-emerald-300">'rates:sync-currencies'</span>)
    -><span class="text-blue-400">daily</span>()
    -><span class="text-blue-400">withoutOverlapping</span>();

<span class="text-zinc-500">// 2. Synchronize official ECB rates every 3 hours on weekdays</span>
<span class="text-cyan-300">Schedule</span>::<span class="text-blue-400">command</span>(<span class="text-emerald-300">'rates:sync-latest'</span>)
    -><span class="text-blue-400">cron</span>(<span class="text-emerald-300">'0 */3 * * 1-5'</span>)
    -><span class="text-blue-400">withoutOverlapping</span>();

<span class="text-zinc-500">// 3. Synchronize crypto P2P & parallel street cash rates every 30 minutes</span>
<span class="text-cyan-300">Schedule</span>::<span class="text-blue-400">command</span>(<span class="text-emerald-300">'rates:sync-parallel'</span>)
    -><span class="text-blue-400">everyThirtyMinutes</span>()
    -><span class="text-blue-400">withoutOverlapping</span>();</code></pre>
                    </div>

                    <div class="overflow-x-auto my-6">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-zinc-300 dark:border-zinc-700">
                                    <th class="py-3 px-4 font-semibold text-zinc-900 dark:text-white">Scheduled Task</th>
                                    <th class="py-3 px-4 font-semibold text-zinc-900 dark:text-white">Execution Cadence</th>
                                    <th class="py-3 px-4 font-semibold text-zinc-900 dark:text-white">Description & Source</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 text-zinc-600 dark:text-zinc-400 font-mono">
                                <tr>
                                    <td class="py-3.5 px-4 font-bold text-zinc-900 dark:text-white">rates:sync-parallel</td>
                                    <td class="py-3.5 px-4 text-emerald-600 dark:text-emerald-400 font-sans">Every 30 Minutes</td>
                                    <td class="py-3.5 px-4 font-sans text-zinc-500">Pulls live AbokiFX cash spreads and Binance P2P USDT order books for emerging pairs.</td>
                                </tr>
                                <tr>
                                    <td class="py-3.5 px-4 font-bold text-zinc-900 dark:text-white">rates:sync-latest</td>
                                    <td class="py-3.5 px-4 text-blue-600 dark:text-blue-400 font-sans">Every 3 Hours (Weekdays)</td>
                                    <td class="py-3.5 px-4 font-sans text-zinc-500">Pulls European Central Bank (ECB) benchmark exchange rates via Frankfurter.</td>
                                </tr>
                                <tr>
                                    <td class="py-3.5 px-4 font-bold text-zinc-900 dark:text-white">rates:sync-currencies</td>
                                    <td class="py-3.5 px-4 font-sans">Daily at Midnight</td>
                                    <td class="py-3.5 px-4 font-sans text-zinc-500">Audits 165+ global ISO currency symbols, flags, and decimal minor units.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        Fail-Safe Outage Policy & Stale Fallbacks
                    </h3>

                    <p class="text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        If an external API experiences downtime or network errors, LemonWaresEX intercepts the exception, records the failure in <code class="font-mono text-xs">rate_sync_runs</code>, and automatically serves the latest verified database snapshot with an <code class="font-mono text-xs">is_stale: true</code> flag. Client applications continue operating without experiencing HTTP 500 downtime.
                    </p>
                </section>

                <!-- SECTION 8: Admin Operations Playbook -->
                <section id="playbook" class="scroll-mt-24 space-y-6 border-t border-zinc-200 pt-10 dark:border-zinc-800">
                    <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        Part 6: Administrator Operations Playbook
                    </h2>

                    <div class="space-y-6 text-[15px] leading-7 text-zinc-700 dark:text-zinc-300">
                        <div>
                            <h3 class="text-base font-bold text-zinc-900 dark:text-white">
                                1. Emergency Manual Rate Override
                            </h3>
                            <p class="text-zinc-600 dark:text-zinc-400 text-sm">
                                Navigate to <strong>Live Rates Matrix</strong> (<code class="font-mono text-xs">/dashboard?tab=rates</code>), locate the pair (e.g. <code class="font-mono text-xs">USD/NGN</code>), click <strong>"Edit Rate"</strong>, and enter custom Midpoint, Buy (Bid), and Sell (Ask) values. Saving flushes cache keys and activates the override globally in real time.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-base font-bold text-zinc-900 dark:text-white">
                                2. API Key Management & Security
                            </h3>
                            <p class="text-zinc-600 dark:text-zinc-400 text-sm">
                                Navigate to <strong>API Keys</strong> (<code class="font-mono text-xs">/dashboard?tab=keys</code>). Issued tokens are stored exclusively as SHA-256 hashes. Administrators can select tier limits (Free: 60/m, Pro: 1,000/m, Enterprise: 5,000/m) and click <strong>"Revoke"</strong> to terminate abusive developer keys immediately.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-base font-bold text-zinc-900 dark:text-white">
                                3. Global Currency Activation
                            </h3>
                            <p class="text-zinc-600 dark:text-zinc-400 text-sm">
                                Navigate to <strong>Currencies</strong> (<code class="font-mono text-xs">/dashboard?tab=currencies</code>). Toggling <strong>"Deactivate"</strong> instantly excludes that currency from public converter dropdowns and restricts it from developer API requests.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- SECTION 9: Technical Glossary -->
                <section id="glossary" class="scroll-mt-24 space-y-6 border-t border-zinc-200 pt-10 dark:border-zinc-800">
                    <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        Part 7: Technical Reference & Glossary
                    </h2>

                    <div class="overflow-x-auto my-6">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-zinc-300 dark:border-zinc-700">
                                    <th class="py-3 px-4 font-semibold text-zinc-900 dark:text-white">Term</th>
                                    <th class="py-3 px-4 font-semibold text-zinc-900 dark:text-white">Definition & Operational Context</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 text-zinc-600 dark:text-zinc-400">
                                <tr>
                                    <td class="py-3.5 px-4 font-mono font-bold text-zinc-900 dark:text-white">ISO 4217</td>
                                    <td class="py-3.5 px-4">International standard three-letter currency coding system (USD, EUR, NGN, GBP, JPY).</td>
                                </tr>
                                <tr>
                                    <td class="py-3.5 px-4 font-mono font-bold text-zinc-900 dark:text-white">Minor Units</td>
                                    <td class="py-3.5 px-4">The number of decimal places defined for a currency (e.g. 2 for USD, 0 for JPY, 3 for BHD). Guides math rounding.</td>
                                </tr>
                                <tr>
                                    <td class="py-3.5 px-4 font-mono font-bold text-zinc-900 dark:text-white">Base Currency</td>
                                    <td class="py-3.5 px-4">The first currency listed in a currency pair (e.g. USD in USD/NGN). It represents the fixed single unit being evaluated.</td>
                                </tr>
                                <tr>
                                    <td class="py-3.5 px-4 font-mono font-bold text-zinc-900 dark:text-white">Quote Currency</td>
                                    <td class="py-3.5 px-4">The second currency listed in a pair (e.g. NGN in USD/NGN). Represents the price in local money needed to buy one unit of the base.</td>
                                </tr>
                                <tr>
                                    <td class="py-3.5 px-4 font-mono font-bold text-zinc-900 dark:text-white">Dealer Spread</td>
                                    <td class="py-3.5 px-4">The difference between the Ask (Sell) and Bid (Buy) price. Represents the gross profit margin of the currency dealer.</td>
                                </tr>
                                <tr>
                                    <td class="py-3.5 px-4 font-mono font-bold text-zinc-900 dark:text-white">Arbitrary Precision</td>
                                    <td class="py-3.5 px-4">Software-driven decimal arithmetic (BigDecimal) that prevents floating-point rounding errors and lost cents.</td>
                                </tr>
                                <tr>
                                    <td class="py-3.5 px-4 font-mono font-bold text-zinc-900 dark:text-white">TTL Invalidation</td>
                                    <td class="py-3.5 px-4">Automatic cache eviction triggered upon new rate ingestion to guarantee real-time conversion fresh lookups.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>

            <!-- Sticky Right-Hand Index (Laravel Docs Style) -->
            <aside class="hidden lg:col-span-3 lg:block">
                <div class="sticky top-20 space-y-4 pt-2">
                    <div class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                        On This Page
                    </div>
                    <nav class="space-y-2 text-xs font-medium border-l border-zinc-200 dark:border-zinc-800 pl-3">
                        <a href="#architecture" class="block text-zinc-600 hover:text-red-600 dark:text-zinc-400 dark:hover:text-red-400 transition">
                            System Architecture & Pipeline
                        </a>
                        <a href="#source-selection" class="block text-zinc-600 hover:text-red-600 dark:text-zinc-400 dark:hover:text-red-400 transition">
                            How System Chooses Sources
                        </a>
                        <a href="#p2p-rate" class="block text-zinc-600 hover:text-red-600 dark:text-zinc-400 dark:hover:text-red-400 transition">
                            1. Ingesting P2P Street Rates
                        </a>
                        <a href="#math-derivation" class="block text-zinc-600 hover:text-red-600 dark:text-zinc-400 dark:hover:text-red-400 transition">
                            2. Deriving All Currencies (USD Bridge)
                        </a>
                        <a href="#spread-calculation" class="block text-zinc-600 hover:text-red-600 dark:text-zinc-400 dark:hover:text-red-400 transition">
                            3. Spread & Premium Formula
                        </a>
                        <a href="#float-math" class="block text-zinc-600 hover:text-red-600 dark:text-zinc-400 dark:hover:text-red-400 transition">
                            4. Zero Float Math & Spreads
                        </a>
                        <a href="#automation" class="block text-zinc-600 hover:text-red-600 dark:text-zinc-400 dark:hover:text-red-400 transition">
                            5. Ingestion Schedulers
                        </a>
                        <a href="#playbook" class="block text-zinc-600 hover:text-red-600 dark:text-zinc-400 dark:hover:text-red-400 transition">
                            6. Operations Playbook
                        </a>
                        <a href="#glossary" class="block text-zinc-600 hover:text-red-600 dark:text-zinc-400 dark:hover:text-red-400 transition">
                            7. Technical Glossary
                        </a>
                    </nav>
                </div>
            </aside>
        </div>
    </div>
</x-layouts::app>
