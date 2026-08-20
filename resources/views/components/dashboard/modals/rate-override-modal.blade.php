@if($editingRateId)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
        <div class="w-full max-w-md rounded-2xl border border-zinc-200 bg-white p-6 shadow-2xl dark:border-zinc-700 dark:bg-zinc-900 space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-3 dark:border-zinc-800">
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Manual Rate Override</h3>
                <button type="button" wire:click="cancelEditRate" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-white">&times;</button>
            </div>

            <p class="text-xs text-zinc-500">
                Manually set exchange rate for <strong class="text-zinc-900 dark:text-white">{{ $editingPairLabel }}</strong>.
            </p>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block font-medium text-zinc-600 dark:text-zinc-400 mb-1">Midpoint Rate</label>
                    <input
                        type="number"
                        step="any"
                        wire:model="editingRateVal"
                        class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 font-mono font-semibold text-zinc-900 outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    />
                </div>

                <div>
                    <label class="block font-medium text-zinc-600 dark:text-zinc-400 mb-1">Buy Rate (Bid)</label>
                    <input
                        type="number"
                        step="any"
                        wire:model="editingBuyRateVal"
                        class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 font-mono font-semibold text-zinc-900 outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    />
                </div>

                <div>
                    <label class="block font-medium text-zinc-600 dark:text-zinc-400 mb-1">Sell Rate (Ask)</label>
                    <input
                        type="number"
                        step="any"
                        wire:model="editingSellRateVal"
                        class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 font-mono font-semibold text-zinc-900 outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    />
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button
                    type="button"
                    wire:click="cancelEditRate"
                    class="rounded-xl border border-zinc-200 px-4 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    wire:click="saveRateOverride"
                    class="rounded-xl bg-zinc-900 px-4 py-2 text-xs font-semibold text-white shadow-2xs hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200"
                >
                    Save Override
                </button>
            </div>
        </div>
    </div>
@endif
