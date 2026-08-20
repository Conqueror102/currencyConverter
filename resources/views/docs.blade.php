<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head', ['title' => 'API Documentation - LemonWaresEX'])
        <style>
            /* Code Theme Styles */
            .code-token-keyword { color: #c678dd; }
            .code-token-string { color: #98c379; }
            .code-token-number { color: #d19a66; }
            .code-token-key { color: #61afef; }
            .code-token-boolean { color: #e5c07b; }
            .code-token-comment { color: #5c6370; font-style: italic; }
            .code-token-method { color: #56b6c2; font-weight: bold; }
        </style>
    </head>
    <body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100 selection:bg-zinc-900 selection:text-white dark:selection:bg-white dark:selection:text-zinc-900">
        <!-- Navigation Header -->
        <header class="sticky top-0 z-50 border-b border-zinc-200 bg-white/90 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-950/90">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-3.5">
                <!-- Brand / Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 shadow-xs">
                        <x-app-logo-icon class="size-5 fill-current" />
                    </span>
                    <div>
                        <span class="text-base font-bold tracking-tight text-zinc-900 dark:text-white">LemonWares<span class="text-emerald-500">EX</span></span>
                        <span class="ml-1.5 rounded-md border border-emerald-200 bg-emerald-50 px-1.5 py-0.5 text-[10px] font-semibold text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">API v1</span>
                    </div>
                </a>

                <!-- Navigation Links -->
                <nav class="hidden items-center gap-6 md:flex">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Converter</a>
                    <a href="{{ route('docs') }}" class="text-sm font-semibold text-zinc-900 dark:text-white">API Documentation</a>
                </nav>

                <!-- Actions: Theme Switcher & Auth / CTA -->
                <div class="flex items-center gap-3">
                    <!-- Light / Dark Mode Toggle -->
                    <button
                        type="button"
                        x-data
                        x-on:click="$flux.appearance = $flux.appearance === 'dark' ? 'light' : 'dark'"
                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-zinc-200 bg-zinc-50 text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700"
                        aria-label="Toggle theme"
                    >
                        <flux:icon icon="sun" class="size-4 hidden dark:block" />
                        <flux:icon icon="moon" class="size-4 block dark:hidden" />
                    </button>

                    @auth
                        <flux:button variant="primary" href="{{ route('dashboard') }}" wire:navigate>
                            Dashboard
                        </flux:button>
                    @else
                        <flux:button variant="subtle" href="{{ route('login') }}" wire:navigate>
                            Log in
                        </flux:button>
                        <flux:button variant="primary" href="{{ route('register') }}" wire:navigate>
                            Get API Key
                        </flux:button>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Documentation Layout -->
        <div class="mx-auto max-w-7xl px-6 py-10">
            <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
                <!-- Sidebar Table of Contents -->
                <aside class="hidden lg:col-span-3 lg:block">
                    <div class="sticky top-24 space-y-6">
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                Getting Started
                            </h3>
                            <ul class="mt-3 space-y-2 text-sm">
                                <li>
                                    <a href="#overview" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition">Overview & Base URL</a>
                                </li>
                                <li>
                                    <a href="#authentication" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition">Authentication & Headers</a>
                                </li>
                                <li>
                                    <a href="#rate-limits" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition">Rate Limits</a>
                                </li>
                                <li>
                                    <a href="#data-freshness" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition">Data Freshness & Sync Cadence</a>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                Endpoints
                            </h3>
                            <ul class="mt-3 space-y-2 text-sm font-mono">
                                <li>
                                    <a href="#convert" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition flex items-center gap-1.5">
                                        <span class="rounded bg-emerald-100 px-1 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">GET</span>
                                        <span>/convert</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#rates-latest" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition flex items-center gap-1.5">
                                        <span class="rounded bg-emerald-100 px-1 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">GET</span>
                                        <span>/rates/latest</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#rates-pair" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition flex items-center gap-1.5">
                                        <span class="rounded bg-emerald-100 px-1 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">GET</span>
                                        <span>/rates/{base}/{quote}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#currencies" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition flex items-center gap-1.5">
                                        <span class="rounded bg-emerald-100 px-1 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">GET</span>
                                        <span>/currencies</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </aside>

                <!-- Main Documentation Content -->
                <main class="lg:col-span-9 space-y-16">
                    <!-- Overview -->
                    <section id="overview" class="scroll-mt-24 space-y-4">
                        <div class="inline-flex items-center gap-2 rounded-lg bg-zinc-100 px-2.5 py-1 text-xs font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                            <span>REST API v1</span>
                        </div>
                        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
                            LemonWaresEX API Reference
                        </h1>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                            The LemonWaresEX API provides high-precision foreign exchange conversion, real-time central bank feeds, and live parallel street market cash rates (including AbokiFX and P2P order books).
                        </p>

                        <!-- Base URL Card -->
                        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
                            <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Base Endpoint</div>
                            <div class="mt-1 font-mono text-sm font-semibold text-zinc-900 dark:text-white">
                                {{ url('/api/v1') }}
                            </div>
                        </div>
                    </section>

                    <!-- Authentication -->
                    <section id="authentication" class="scroll-mt-24 space-y-4 border-t border-zinc-200 pt-10 dark:border-zinc-800">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Authentication</h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            Authenticate your API requests by passing your API Key in the <code class="rounded bg-zinc-100 px-1.5 py-0.5 font-mono text-xs text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">X-API-Key</code> request header. Unauthenticated public requests are limited to 30 requests per minute.
                        </p>

                        <!-- Code Theme Box: Auth Example -->
                        <div class="overflow-hidden rounded-xl border border-zinc-800 bg-[#1e1e24] shadow-md">
                            <div class="flex items-center justify-between border-b border-zinc-800 bg-[#18181c] px-4 py-2 text-xs">
                                <div class="flex items-center gap-1.5">
                                    <span class="h-3 w-3 rounded-full bg-[#ff5f56]"></span>
                                    <span class="h-3 w-3 rounded-full bg-[#ffbd2e]"></span>
                                    <span class="h-3 w-3 rounded-full bg-[#27c93f]"></span>
                                    <span class="ml-2 font-mono text-zinc-400">cURL Header</span>
                                </div>
                            </div>
                            <pre class="p-4 font-mono text-xs leading-relaxed text-zinc-200 overflow-x-auto"><code><span class="code-token-method">curl</span> -X GET "{{ url('/api/v1/convert?from=USD&to=NGN&amount=100&market=parallel') }}" \
  -H <span class="code-token-string">"X-API-Key: rf_live_your_api_key_here"</span> \
                        </div>
                    </section>

                    <!-- Rate Limits -->
                    <section id="rate-limits" class="scroll-mt-24 space-y-4 border-t border-zinc-200 pt-10 dark:border-zinc-800">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Rate Limits & Quotas</h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            Requests are metered per minute based on your developer API tier. Standard rate-limiting headers are included in every HTTP response:
                        </p>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 text-xs">
                            <div class="rounded-xl border border-zinc-200 bg-white p-3.5 dark:border-zinc-800 dark:bg-zinc-900">
                                <div class="font-bold text-zinc-900 dark:text-white">Free Tier</div>
                                <div class="mt-1 font-mono text-zinc-500">60 requests / min</div>
                            </div>
                            <div class="rounded-xl border border-zinc-200 bg-white p-3.5 dark:border-zinc-800 dark:bg-zinc-900">
                                <div class="font-bold text-zinc-900 dark:text-white">Pro Tier</div>
                                <div class="mt-1 font-mono text-zinc-500">1,000 requests / min</div>
                            </div>
                            <div class="rounded-xl border border-zinc-200 bg-white p-3.5 dark:border-zinc-800 dark:bg-zinc-900">
                                <div class="font-bold text-zinc-900 dark:text-white">Enterprise Tier</div>
                                <div class="mt-1 font-mono text-zinc-500">5,000 requests / min</div>
                            </div>
                        </div>
                    </section>

                    <!-- Data Freshness & Sync Cadence -->
                    <section id="data-freshness" class="scroll-mt-24 space-y-4 border-t border-zinc-200 pt-10 dark:border-zinc-800">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Data Freshness & Automated Sync Cadence</h2>
                        <p class="mt-2 text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            LemonWaresEX runs scheduled background workers to maintain real-time accuracy across official and parallel markets:
                        </p>
                        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
                            <table class="w-full text-left text-xs">
                                <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-800/50">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold text-zinc-700 dark:text-zinc-300">Data Stream</th>
                                        <th class="px-4 py-3 font-semibold text-zinc-700 dark:text-zinc-300">Refresh Frequency</th>
                                        <th class="px-4 py-3 font-semibold text-zinc-700 dark:text-zinc-300">Sources</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-white">Parallel / Street Cash & P2P</td>
                                        <td class="px-4 py-3 font-mono text-emerald-600 dark:text-emerald-400 font-bold">Every 30 Minutes</td>
                                        <td class="px-4 py-3 text-zinc-500">AbokiFX Street BDC + Binance P2P Order Book</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-white">Official Central Bank Rates</td>
                                        <td class="px-4 py-3 font-mono text-blue-600 dark:text-blue-400 font-bold">Every 3 Hours (Weekdays)</td>
                                        <td class="px-4 py-3 text-zinc-500">European Central Bank (ECB) via Frankfurter</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-white">ISO Currency Directory</td>
                                        <td class="px-4 py-3 font-mono text-zinc-600 dark:text-zinc-400">Daily at Midnight</td>
                                        <td class="px-4 py-3 text-zinc-500">ISO 4217 Global Currency Registry</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- Endpoint 1: Convert -->
                    <section id="convert" class="scroll-mt-24 space-y-6 border-t border-zinc-200 pt-10 dark:border-zinc-800">
                        <div class="flex items-center gap-2">
                            <span class="rounded-md bg-emerald-600 px-2 py-1 text-xs font-bold text-white">GET</span>
                            <h2 class="font-mono text-lg font-bold text-zinc-900 dark:text-white">/api/v1/convert</h2>
                        </div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            Perform arbitrary-precision monetary conversion between two currencies for official central bank or parallel street markets.
                        </p>

                        <!-- Parameters Table -->
                        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
                            <table class="w-full text-left text-xs">
                                <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-800/50">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold text-zinc-700 dark:text-zinc-300">Parameter</th>
                                        <th class="px-4 py-3 font-semibold text-zinc-700 dark:text-zinc-300">Type</th>
                                        <th class="px-4 py-3 font-semibold text-zinc-700 dark:text-zinc-300">Required</th>
                                        <th class="px-4 py-3 font-semibold text-zinc-700 dark:text-zinc-300">Description</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 font-mono">
                                    <tr>
                                        <td class="px-4 py-3 font-bold text-zinc-900 dark:text-white">from</td>
                                        <td class="px-4 py-3 text-zinc-500">string</td>
                                        <td class="px-4 py-3 text-emerald-600 dark:text-emerald-400 font-sans font-semibold">Yes</td>
                                        <td class="px-4 py-3 font-sans text-zinc-600 dark:text-zinc-400">3-letter ISO code to convert from (e.g. <code class="text-zinc-900 dark:text-zinc-200">USD</code>).</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 font-bold text-zinc-900 dark:text-white">to</td>
                                        <td class="px-4 py-3 text-zinc-500">string</td>
                                        <td class="px-4 py-3 text-emerald-600 dark:text-emerald-400 font-sans font-semibold">Yes</td>
                                        <td class="px-4 py-3 font-sans text-zinc-600 dark:text-zinc-400">3-letter ISO code to convert to (e.g. <code class="text-zinc-900 dark:text-zinc-200">NGN</code>).</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 font-bold text-zinc-900 dark:text-white">amount</td>
                                        <td class="px-4 py-3 text-zinc-500">number</td>
                                        <td class="px-4 py-3 text-emerald-600 dark:text-emerald-400 font-sans font-semibold">Yes</td>
                                        <td class="px-4 py-3 font-sans text-zinc-600 dark:text-zinc-400">Numeric monetary amount greater than 0.</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 font-bold text-zinc-900 dark:text-white">market</td>
                                        <td class="px-4 py-3 text-zinc-500">string</td>
                                        <td class="px-4 py-3 text-zinc-400 font-sans">No</td>
                                        <td class="px-4 py-3 font-sans text-zinc-600 dark:text-zinc-400"><code class="text-zinc-900 dark:text-zinc-200">official</code> (default) or <code class="text-zinc-900 dark:text-zinc-200">parallel</code> (street / AbokiFX / P2P).</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 font-bold text-zinc-900 dark:text-white">action</td>
                                        <td class="px-4 py-3 text-zinc-500">string</td>
                                        <td class="px-4 py-3 text-zinc-400 font-sans">No</td>
                                        <td class="px-4 py-3 font-sans text-zinc-600 dark:text-zinc-400"><code class="text-zinc-900 dark:text-zinc-200">mid</code> (default), <code class="text-zinc-900 dark:text-zinc-200">buy</code> (Ask rate), or <code class="text-zinc-900 dark:text-zinc-200">sell</code> (Bid rate).</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Code Theme Box: Response -->
                        <div class="overflow-hidden rounded-xl border border-zinc-800 bg-[#1e1e24] shadow-md">
                            <div class="flex items-center justify-between border-b border-zinc-800 bg-[#18181c] px-4 py-2 text-xs">
                                <div class="flex items-center gap-1.5">
                                    <span class="h-3 w-3 rounded-full bg-[#ff5f56]"></span>
                                    <span class="h-3 w-3 rounded-full bg-[#ffbd2e]"></span>
                                    <span class="h-3 w-3 rounded-full bg-[#27c93f]"></span>
                                    <span class="ml-2 font-mono text-zinc-400">Response (200 OK)</span>
                                </div>
                                <span class="rounded bg-emerald-500/20 px-2 py-0.5 font-mono text-[11px] text-emerald-400">application/json</span>
                            </div>
                            <pre class="p-4 font-mono text-xs leading-relaxed text-zinc-200 overflow-x-auto"><code>{
  <span class="code-token-key">"data"</span>: {
    <span class="code-token-key">"from"</span>: <span class="code-token-string">"USD"</span>,
    <span class="code-token-key">"to"</span>: <span class="code-token-string">"NGN"</span>,
    <span class="code-token-key">"amount"</span>: <span class="code-token-string">"100.00"</span>,
    <span class="code-token-key">"rate"</span>: <span class="code-token-string">"1398.000000"</span>,
    <span class="code-token-key">"inverse_rate"</span>: <span class="code-token-string">"0.000715"</span>,
    <span class="code-token-key">"result"</span>: <span class="code-token-string">"139800.00"</span>
  },
  <span class="code-token-key">"meta"</span>: {
    <span class="code-token-key">"rate_date"</span>: <span class="code-token-string">"2026-08-20"</span>,
    <span class="code-token-key">"market_type"</span>: <span class="code-token-string">"parallel"</span>,
    <span class="code-token-key">"trade_action"</span>: <span class="code-token-string">"sell"</span>,
    <span class="code-token-key">"provider"</span>: <span class="code-token-string">"parallel_p2p"</span>,
    <span class="code-token-key">"official_rate"</span>: <span class="code-token-string">"1347.740989"</span>,
    <span class="code-token-key">"buy_rate"</span>: <span class="code-token-string">"1398.000000"</span>,
    <span class="code-token-key">"sell_rate"</span>: <span class="code-token-string">"1410.000000"</span>,
    <span class="code-token-key">"bid_ask_spread"</span>: <span class="code-token-string">"12.00 (0.86%)"</span>,
    <span class="code-token-key">"spread_percentage"</span>: <span class="code-token-string">"+3.73%"</span>,
    <span class="code-token-key">"is_derived"</span>: <span class="code-token-boolean">false</span>,
    <span class="code-token-key">"is_stale"</span>: <span class="code-token-boolean">false</span>,
    <span class="code-token-key">"age_days"</span>: <span class="code-token-number">0</span>
  }
}</code></pre>
                        </div>
                    </section>

                    <!-- Endpoint 2: Latest Rates -->
                    <section id="rates-latest" class="scroll-mt-24 space-y-6 border-t border-zinc-200 pt-10 dark:border-zinc-800">
                        <div class="flex items-center gap-2">
                            <span class="rounded-md bg-emerald-600 px-2 py-1 text-xs font-bold text-white">GET</span>
                            <h2 class="font-mono text-lg font-bold text-zinc-900 dark:text-white">/api/v1/rates/latest</h2>
                        </div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            Retrieve a full matrix of exchange rates for a given base currency.
                        </p>

                        <!-- Code Theme Box -->
                        <div class="overflow-hidden rounded-xl border border-zinc-800 bg-[#1e1e24] shadow-md">
                            <div class="flex items-center justify-between border-b border-zinc-800 bg-[#18181c] px-4 py-2 text-xs">
                                <div class="flex items-center gap-1.5">
                                    <span class="h-3 w-3 rounded-full bg-[#ff5f56]"></span>
                                    <span class="h-3 w-3 rounded-full bg-[#ffbd2e]"></span>
                                    <span class="h-3 w-3 rounded-full bg-[#27c93f]"></span>
                                    <span class="ml-2 font-mono text-zinc-400">Response (200 OK)</span>
                                </div>
                            </div>
                            <pre class="p-4 font-mono text-xs leading-relaxed text-zinc-200 overflow-x-auto"><code>{
  <span class="code-token-key">"base"</span>: <span class="code-token-string">"USD"</span>,
  <span class="code-token-key">"date"</span>: <span class="code-token-string">"2026-08-20"</span>,
  <span class="code-token-key">"market"</span>: <span class="code-token-string">"official"</span>,
  <span class="code-token-key">"provider"</span>: <span class="code-token-string">"frankfurter"</span>,
  <span class="code-token-key">"rates"</span>: {
    <span class="code-token-key">"EUR"</span>: <span class="code-token-string">"0.862218"</span>,
    <span class="code-token-key">"GBP"</span>: <span class="code-token-string">"0.738101"</span>,
    <span class="code-token-key">"NGN"</span>: <span class="code-token-string">"1347.740989"</span>,
    <span class="code-token-key">"JPY"</span>: <span class="code-token-string">"159.441283"</span>,
    <span class="code-token-key">"CAD"</span>: <span class="code-token-string">"1.387308"</span>
  }
}</code></pre>
                        </div>
                    </section>

                    <!-- Endpoint 3: Specific Pair -->
                    <section id="rates-pair" class="scroll-mt-24 space-y-6 border-t border-zinc-200 pt-10 dark:border-zinc-800">
                        <div class="flex items-center gap-2">
                            <span class="rounded-md bg-emerald-600 px-2 py-1 text-xs font-bold text-white">GET</span>
                            <h2 class="font-mono text-lg font-bold text-zinc-900 dark:text-white">/api/v1/rates/{base}/{quote}</h2>
                        </div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            Query a direct exchange rate pair between two currencies.
                        </p>

                        <!-- Code Theme Box -->
                        <div class="overflow-hidden rounded-xl border border-zinc-800 bg-[#1e1e24] shadow-md">
                            <div class="flex items-center justify-between border-b border-zinc-800 bg-[#18181c] px-4 py-2 text-xs">
                                <div class="flex items-center gap-1.5">
                                    <span class="h-3 w-3 rounded-full bg-[#ff5f56]"></span>
                                    <span class="h-3 w-3 rounded-full bg-[#ffbd2e]"></span>
                                    <span class="h-3 w-3 rounded-full bg-[#27c93f]"></span>
                                    <span class="ml-2 font-mono text-zinc-400">Response (200 OK)</span>
                                </div>
                            </div>
                            <pre class="p-4 font-mono text-xs leading-relaxed text-zinc-200 overflow-x-auto"><code>{
  <span class="code-token-key">"data"</span>: {
    <span class="code-token-key">"base"</span>: <span class="code-token-string">"USD"</span>,
    <span class="code-token-key">"quote"</span>: <span class="code-token-string">"NGN"</span>,
    <span class="code-token-key">"rate"</span>: <span class="code-token-string">"1404.000000"</span>,
    <span class="code-token-key">"inverse_rate"</span>: <span class="code-token-string">"0.000712"</span>,
    <span class="code-token-key">"buy_rate"</span>: <span class="code-token-string">"1398.000000"</span>,
    <span class="code-token-key">"sell_rate"</span>: <span class="code-token-string">"1410.000000"</span>
  },
  <span class="code-token-key">"meta"</span>: {
    <span class="code-token-key">"rate_date"</span>: <span class="code-token-string">"2026-08-20"</span>,
    <span class="code-token-key">"market_type"</span>: <span class="code-token-string">"parallel"</span>,
    <span class="code-token-key">"provider"</span>: <span class="code-token-string">"parallel_p2p"</span>,
    <span class="code-token-key">"official_rate"</span>: <span class="code-token-string">"1347.740989"</span>,
    <span class="code-token-key">"spread_percentage"</span>: <span class="code-token-string">"+4.17%"</span>
  }
}</code></pre>
                        </div>
                    </section>
                </main>
            </div>
        </div>

        <!-- Footer -->
        <footer class="border-t border-zinc-200 bg-white py-10 dark:border-zinc-800 dark:bg-zinc-950">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-6 sm:flex-row text-xs text-zinc-500">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-zinc-900 dark:text-white">LemonWaresEX</span>
                    <span>•</span>
                    <span>High-Precision Multi-Market Currency Engine</span>
                </div>
                <div>
                    &copy; {{ date('Y') }} LemonWaresEX. All rights reserved.
                </div>
            </div>
        </footer>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
