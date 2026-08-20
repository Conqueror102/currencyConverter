<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-950">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                @if(auth()->user()?->isAdmin())
                    <flux:sidebar.group :heading="__('Admin Modules')" class="grid">
                        <flux:sidebar.item icon="chart-bar" :href="route('dashboard', ['tab' => 'analytics'])" :current="request()->routeIs('dashboard') && (request('tab') === 'analytics' || !request('tab'))" wire:navigate>
                            {{ __('Analytics & Traffic') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="currency-dollar" :href="route('dashboard', ['tab' => 'rates'])" :current="request()->routeIs('dashboard') && request('tab') === 'rates'" wire:navigate>
                            {{ __('Live Rates Matrix') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="key" :href="route('dashboard', ['tab' => 'keys'])" :current="request()->routeIs('dashboard') && request('tab') === 'keys'" wire:navigate>
                            {{ __('API Keys (All)') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="globe-alt" :href="route('dashboard', ['tab' => 'currencies'])" :current="request()->routeIs('dashboard') && request('tab') === 'currencies'" wire:navigate>
                            {{ __('Currencies') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="queue-list" :href="route('dashboard', ['tab' => 'logs'])" :current="request()->routeIs('dashboard') && request('tab') === 'logs'" wire:navigate>
                            {{ __('Ingestion Logs') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="book-open" :href="route('admin.guide')" :current="request()->routeIs('admin.guide')" wire:navigate>
                            {{ __('System Guide') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @else
                    <flux:sidebar.group :heading="__('Developer Portal')" class="grid">
                        <flux:sidebar.item icon="key" :href="route('dashboard', ['tab' => 'keys'])" :current="request()->routeIs('dashboard') && (request('tab') === 'keys' || !request('tab'))" wire:navigate>
                            {{ __('My API Keys') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="queue-list" :href="route('dashboard', ['tab' => 'logs'])" :current="request()->routeIs('dashboard') && request('tab') === 'logs'" wire:navigate>
                            {{ __('Request Logs') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="code-bracket-square" :href="route('dashboard', ['tab' => 'playground'])" :current="request()->routeIs('dashboard') && request('tab') === 'playground'" wire:navigate>
                            {{ __('API Playground & SDKs') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endif

                <flux:sidebar.group :heading="__('Platform Links')" class="grid mt-4">
                    <flux:sidebar.item icon="arrows-right-left" :href="route('home')" wire:navigate>
                        {{ __('Live Converter') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="book-open-text" :href="route('docs')" wire:navigate>
                        {{ __('API Documentation') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <div class="px-2 py-3 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                <button
                    type="button"
                    x-data
                    x-on:click="$flux.appearance = $flux.appearance === 'dark' ? 'light' : 'dark'"
                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700"
                    aria-label="Toggle theme"
                >
                    <flux:icon icon="sun" class="size-3.5 hidden dark:block" />
                    <flux:icon icon="moon" class="size-3.5 block dark:hidden" />
                </button>
                <span class="text-[11px] text-zinc-400 font-mono">v1.0.0</span>
            </div>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog-6-tooth" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full" data-test="logout-button">
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
