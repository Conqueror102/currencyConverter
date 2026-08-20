@if($newlyCreatedPlainToken)
    <div
        x-data="{
            copied: false,
            async copyToken() {
                try {
                    await navigator.clipboard.writeText('{{ $newlyCreatedPlainToken }}');
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                } catch (e) {
                    console.warn('Clipboard copy failed');
                }
            }
        }"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm animate-in fade-in duration-200"
        role="dialog"
        aria-modal="true"
    >
        <div class="relative w-full max-w-lg rounded-2xl border border-zinc-200 bg-white p-6 shadow-2xl dark:border-zinc-800 dark:bg-zinc-900 space-y-5">
            <!-- Header -->
            <div class="flex items-start gap-3.5">
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                    <flux:icon icon="key" class="size-5" />
                </div>
                <div>
                    <h3 class="text-base font-bold tracking-tight text-zinc-900 dark:text-white">
                        API Secret Key Generated
                    </h3>
                    <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                        Please copy your new API token now. For your security, you will not be able to view this token again.
                    </p>
                </div>
            </div>

            <!-- Token Display & Copy Box -->
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                    API Secret Key
                </label>
                <div class="flex items-center rounded-xl border border-zinc-200 bg-zinc-50 p-1.5 dark:border-zinc-700 dark:bg-zinc-950">
                    <input
                        type="text"
                        readonly
                        value="{{ $newlyCreatedPlainToken }}"
                        class="w-full bg-transparent px-3 py-1.5 font-mono text-xs font-semibold text-zinc-900 outline-none dark:text-zinc-100 select-all"
                    />
                    <button
                        type="button"
                        @click="copyToken()"
                        class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-zinc-900 px-3.5 py-1.5 text-xs font-semibold text-white shadow-2xs transition hover:bg-zinc-800 active:scale-95 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200"
                    >
                        <span x-show="!copied" class="flex items-center gap-1">
                            <flux:icon icon="document-duplicate" class="size-3.5" />
                            <span>Copy</span>
                        </span>
                        <span x-show="copied" class="flex items-center gap-1 text-emerald-600 dark:text-emerald-500 font-bold" style="display: none;">
                            <flux:icon icon="check" class="size-3.5" />
                            <span>Copied!</span>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Security Warning Notice -->
            <div class="rounded-xl border border-zinc-200 bg-zinc-50/70 p-3.5 dark:border-zinc-800 dark:bg-zinc-800/40 flex items-start gap-2.5">
                <flux:icon icon="shield-exclamation" class="size-4 text-amber-500 shrink-0 mt-0.5" />
                <p class="text-xs leading-relaxed text-zinc-600 dark:text-zinc-400">
                    Treat this secret token like a password. Do not check it into git or expose it in public client-side JavaScript.
                </p>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                <button
                    type="button"
                    wire:click="$set('newlyCreatedPlainToken', null)"
                    class="rounded-xl bg-zinc-900 px-4 py-2 text-xs font-semibold text-white shadow-2xs transition hover:bg-zinc-800 active:scale-95 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200"
                >
                    I have saved my secret key
                </button>
            </div>
        </div>
    </div>
@endif
