<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <div class="bg-muted relative hidden h-full flex-col p-10 text-white lg:flex dark:border-e dark:border-neutral-800">
                <div class="absolute inset-0 bg-neutral-950"></div>
                <a href="{{ route('home') }}" class="relative z-20 flex items-center gap-2.5 text-lg font-bold tracking-tight" wire:navigate>
                    <span class="flex size-9 items-center justify-center rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">
                        <x-app-logo-icon class="size-6" />
                    </span>
                    <span>{{ config('app.name', 'LemonWaresEX') }}</span>
                </a>

                <div class="relative z-20 mt-auto space-y-2">
                    <p class="text-lg font-semibold tracking-tight text-white">
                        Multi-Market Foreign Exchange & Developer Infrastructure
                    </p>
                    <p class="text-xs text-zinc-400 leading-relaxed max-w-md">
                        Real-time official central bank benchmarks, crypto P2P liquidity spreads, and high-precision REST APIs for modern fintechs.
                    </p>
                </div>
            </div>
            <div class="w-full lg:p-8">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                    <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden" wire:navigate>
                        <span class="flex size-10 items-center justify-center rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">
                            <x-app-logo-icon class="size-7" />
                        </span>

                        <span class="text-base font-bold text-zinc-900 dark:text-white">{{ config('app.name', 'LemonWaresEX') }}</span>
                    </a>
                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
