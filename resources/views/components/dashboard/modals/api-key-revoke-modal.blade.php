@if($revokingApiKeyId)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm animate-in fade-in duration-200"
        role="dialog"
        aria-modal="true"
    >
        <div class="relative w-full max-w-md rounded-2xl border border-rose-200 bg-white p-6 shadow-2xl dark:border-rose-900/50 dark:bg-zinc-900 space-y-5">
            <!-- Header -->
            <div class="flex items-start gap-3.5">
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-rose-500/10 text-rose-500 border border-rose-500/20">
                    <flux:icon icon="trash" class="size-5" />
                </div>
                <div>
                    <h3 class="text-base font-bold tracking-tight text-zinc-900 dark:text-white">
                        Revoke Developer API Key
                    </h3>
                    <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                        Are you sure you want to permanently revoke this API key?
                    </p>
                </div>
            </div>

            <!-- Target Key Box -->
            <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-800 dark:bg-zinc-950 space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Key Name</span>
                <p class="text-xs font-bold font-mono text-zinc-900 dark:text-white truncate">{{ $revokingApiKeyName }}</p>
            </div>

            <!-- Warning Notice -->
            <div class="rounded-xl border border-rose-200 bg-rose-50/70 p-3.5 dark:border-rose-900/40 dark:bg-rose-950/30 flex items-start gap-2.5">
                <flux:icon icon="exclamation-triangle" class="size-4 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5" />
                <p class="text-xs leading-relaxed text-rose-800 dark:text-rose-300">
                    Any live apps, bots, or integrations using this token will immediately receive <code class="font-mono font-bold text-[11px]">401 Unauthorized</code> errors. This action cannot be undone.
                </p>
            </div>

            <!-- Footer Actions -->
            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                <button
                    type="button"
                    wire:click="cancelRevokeApiKey"
                    class="rounded-xl border border-zinc-200 bg-white px-4 py-2 text-xs font-semibold text-zinc-700 transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 dark:hover:text-white"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    wire:click="deleteApiKey"
                    class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white shadow-2xs transition hover:bg-rose-700 active:scale-95"
                >
                    Revoke Key Immediately
                </button>
            </div>
        </div>
    </div>
@endif
