<div class="space-y-6">
    <!-- Create Key Form Card -->
    <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex items-center justify-between border-b border-zinc-100 pb-3 mb-4 dark:border-zinc-800">
            <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white">
                    {{ $this->isAdmin ? 'Generate Developer API Key (Admin Mode)' : 'Generate New API Secret Key' }}
                </h3>
                <p class="text-xs text-zinc-500">
                    {{ $this->isAdmin ? 'Provision API tokens for developers across Free, Starter, Pro, or Enterprise tiers.' : 'Create a secret key to authenticate your REST API requests. Tokens are hashed with SHA-256.' }}
                </p>
            </div>
        </div>

        @if($this->isAdmin)
            <!-- Admin Form with Tier & Limit Selectors -->
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-12 sm:items-end">
                <div class="sm:col-span-5">
                    <label class="block text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1">Key Description / Client Name</label>
                    <input
                        type="text"
                        wire:model="newKeyName"
                        placeholder="e.g. Mobile iOS Client Production"
                        class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    />
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1">Tier</label>
                    <select
                        wire:model="newKeyTier"
                        class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    >
                        <option value="free">Free (60 req/min)</option>
                        <option value="starter">Starter (300 req/min)</option>
                        <option value="pro">Pro (1,000 req/min)</option>
                        <option value="enterprise">Enterprise (5,000 req/min)</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1">Rate Limit / min</label>
                    <input
                        type="number"
                        wire:model="newKeyRateLimit"
                        class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    />
                </div>

                <div class="sm:col-span-2">
                    <button
                        type="button"
                        wire:click="createApiKey"
                        class="w-full rounded-xl bg-zinc-900 px-4 py-2 text-xs font-semibold text-white shadow-2xs transition hover:bg-zinc-800 active:scale-95 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200"
                    >
                        Issue Token
                    </button>
                </div>
            </div>
        @else
            <!-- Standard Developer Form (Free Tier, 60 req/min) -->
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-12 sm:items-end">
                <div class="sm:col-span-9">
                    <label class="block text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1">Application Name / Integration Purpose</label>
                    <input
                        type="text"
                        wire:model="newKeyName"
                        placeholder="e.g. My Next.js E-Commerce Checkout / Production Bot"
                        class="w-full rounded-xl border border-zinc-200 bg-white px-3.5 py-2 text-xs outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    />
                </div>

                <div class="sm:col-span-3">
                    <button
                        type="button"
                        wire:click="createApiKey"
                        class="w-full rounded-xl bg-zinc-900 px-4 py-2 text-xs font-semibold text-white shadow-2xs transition hover:bg-zinc-800 active:scale-95 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200"
                    >
                        Generate API Key
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- API Keys Table -->
    <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex items-center justify-between border-b border-zinc-100 pb-3 mb-3 dark:border-zinc-800">
            <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                {{ $this->isAdmin ? 'System API Keys Registry' : 'My Active API Keys' }}
            </h4>
            <span class="text-xs text-zinc-400 font-mono">{{ count($this->apiKeysList) }} {{ count($this->apiKeysList) === 1 ? 'key' : 'keys' }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="border-b border-zinc-200 bg-zinc-50/70 dark:border-zinc-800 dark:bg-zinc-800/40">
                    <tr>
                        <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Name</th>
                        <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Prefix Identifier</th>
                        <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Tier</th>
                        <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Rate Limit</th>
                        <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Status</th>
                        @if($this->isAdmin)
                            <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Owner</th>
                        @else
                            <th class="px-3.5 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Last Used</th>
                        @endif
                        <th class="px-3.5 py-2.5 text-right font-semibold text-zinc-600 dark:text-zinc-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @forelse($this->apiKeysList as $k)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                            <td class="px-3.5 py-3 font-semibold text-zinc-900 dark:text-white">{{ $k['name'] }}</td>
                            <td class="px-3.5 py-3 font-mono text-zinc-500">{{ $k['prefix'] }}•••</td>
                            <td class="px-3.5 py-3">
                                <span class="rounded bg-zinc-100 px-1.5 py-0.5 text-[10px] font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                    {{ $k['tier'] }}
                                </span>
                            </td>
                            <td class="px-3.5 py-3 font-mono text-zinc-600 dark:text-zinc-400">{{ $k['rate_limit'] }} req/min</td>
                            <td class="px-3.5 py-3">
                                <span class="rounded px-1.5 py-0.5 text-[10px] font-bold {{ $k['is_active'] ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400' }}">
                                    {{ $k['is_active'] ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            @if($this->isAdmin)
                                <td class="px-3.5 py-3 text-zinc-500">{{ $k['user'] }}</td>
                            @else
                                <td class="px-3.5 py-3 text-zinc-500">{{ $k['last_used'] }}</td>
                            @endif
                            <td class="px-3.5 py-3 text-right space-x-2">
                                <button
                                    type="button"
                                    wire:click="toggleApiKey({{ $k['id'] }})"
                                    class="font-semibold text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                                >
                                    {{ $k['is_active'] ? 'Disable' : 'Enable' }}
                                </button>
                                <button
                                    type="button"
                                    wire:click="confirmRevokeApiKey({{ $k['id'] }})"
                                    class="font-semibold text-rose-600 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300"
                                >
                                    Revoke
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3.5 py-6 text-center text-zinc-400">
                                No API keys generated yet. Click "Generate API Key" above to create your first token.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(! $this->isAdmin)
        <!-- Developer Integration Quick-Start Guide -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-3 dark:border-zinc-800">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                        <flux:icon icon="code-bracket" class="size-4 text-emerald-500" />
                        <span>Quick-Start API Integration</span>
                    </h3>
                    <p class="text-xs text-zinc-500">Authenticate requests by passing your key in the HTTP Authorization header.</p>
                </div>
                <a href="{{ route('docs') }}" wire:navigate class="text-xs font-semibold text-zinc-900 hover:underline dark:text-white flex items-center gap-1">
                    <span>Read Full Docs</span>
                    <flux:icon icon="arrow-right" class="size-3" />
                </a>
            </div>

            <div class="space-y-3 font-mono text-xs">
                <div>
                    <span class="text-zinc-500 block mb-1">1. Live Currency Conversion Endpoint (cURL)</span>
                    <div class="rounded-xl bg-zinc-950 p-3.5 text-zinc-200 overflow-x-auto border border-zinc-800">
                        <code>curl -X GET "{{ url('/api/v1/convert') }}?from=USD&to=NGN&amount=100&market=parallel" \<br>
  -H "Authorization: Bearer <span class="text-emerald-400">YOUR_API_KEY</span>" \<br>
  -H "Accept: application/json"</code>
                    </div>
                </div>

                <div>
                    <span class="text-zinc-500 block mb-1">2. JavaScript / TypeScript (Fetch API)</span>
                    <div class="rounded-xl bg-zinc-950 p-3.5 text-zinc-200 overflow-x-auto border border-zinc-800">
                        <code>const res = await fetch("{{ url('/api/v1/convert') }}?from=USD&to=NGN&amount=100&market=parallel", {<br>
  headers: { "Authorization": "Bearer <span class="text-emerald-400">YOUR_API_KEY</span>", "Accept": "application/json" }<br>
});<br>
const data = await res.json();<br>
console.log(data.data.result);</code>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
