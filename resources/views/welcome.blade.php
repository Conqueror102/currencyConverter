<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head', ['title' => 'Universal Currency Converter & High-Precision FX Rates API'])
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
                    <a href="#converter" class="text-sm font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Converter</a>
                    <a href="{{ route('docs') }}" class="text-sm font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">API Documentation</a>
                    <a href="#features" class="text-sm font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Features</a>
                </nav>

                <!-- Actions: Theme Switcher & Auth / CTA -->
                <div class="flex items-center gap-3">
                    <!-- Light / Dark Mode Toggle Button -->
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

        <!-- Main Hero & Converter Section -->
        <main class="pb-24">
            <div class="mx-auto max-w-7xl px-6 pt-12 sm:pt-16">
                <!-- Clean Hero Intro -->
                <div class="mx-auto max-w-3xl text-center">
                    <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-zinc-900 sm:text-5xl dark:text-white">
                        Universal Currency Converter & FX Rates Engine
                    </h1>

                    <p class="mt-4 text-base text-zinc-600 sm:text-lg dark:text-zinc-400">
                        Convert official central bank rates and real-time parallel street market cash rates with high precision and zero floating-point arithmetic error.
                    </p>
                </div>

                <!-- Livewire Converter Widget -->
                <div id="converter" class="mt-10 scroll-mt-24">
                    <livewire:converter />
                </div>
            </div>

            <!-- Developer API Callout Banner -->
            <section class="mx-auto mt-20 max-w-4xl px-6">
                <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center gap-1.5 rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                            <flux:icon icon="code-bracket" class="size-3.5" />
                            <span>Developer API</span>
                        </div>
                        <h2 class="mt-2 text-xl font-bold tracking-tight text-zinc-900 dark:text-white">
                            Integrate Real-Time FX & Street Rates Into Your App
                        </h2>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            Simple REST endpoints for currency conversion, rate series, and dual-market spreads.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <flux:button variant="primary" href="{{ route('docs') }}">
                            Explore Documentation
                        </flux:button>
                    </div>
                </div>
            </section>

            <!-- Features Grid -->
            <section id="features" class="mx-auto mt-20 max-w-7xl px-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-white">
                            <flux:icon icon="scale" class="size-5" />
                        </div>
                        <h3 class="mt-4 text-base font-bold text-zinc-900 dark:text-white">Zero Float Arithmetic</h3>
                        <p class="mt-2 text-xs text-zinc-600 dark:text-zinc-400">
                            Built using Brick Math arbitrary-precision decimal operations. Eliminates classic JavaScript floating-point errors (like 0.1 + 0.2 = 0.30000000000000004).
                        </p>
                    </div>

                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-white">
                            <flux:icon icon="bolt" class="size-5" />
                        </div>
                        <h3 class="mt-4 text-base font-bold text-zinc-900 dark:text-white">Dual Market Precision</h3>
                        <p class="mt-2 text-xs text-zinc-600 dark:text-zinc-400">
                            Switch instantly between Official Central Bank rates and real-world Parallel Market (AbokiFX / BDC Cash / Binance P2P) rates with live spread metrics.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-white">
                            <flux:icon icon="key" class="size-5" />
                        </div>
                        <h3 class="mt-4 text-base font-bold text-zinc-900 dark:text-white">Developer First API</h3>
                        <p class="mt-2 text-xs text-zinc-600 dark:text-zinc-400">
                            Fast cached responses with tiered API key rate limiting, standard HTTP headers, and comprehensive developer documentation.
                        </p>
                    </div>
                </div>
            </section>
        </main>

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
