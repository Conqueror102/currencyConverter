@php
    $userKeys = \App\Models\ApiKey::where('user_id', auth()->id())->where('is_active', true)->get();
    $selectedKey = $userKeys->firstWhere('id', $playgroundKeyId) ?? $userKeys->first();
    $tokenPlaceholder = $selectedKey ? $selectedKey->secret_prefix . '••••••••••••••••' : 'cc_live_sample_token_xxxxxx';
    
    $currencies = \App\Models\Currency::where('is_active', true)->orderBy('code')->get();
    if ($currencies->isEmpty()) {
        $currencies = collect([
            (object)['code' => 'USD', 'name' => 'US Dollar'],
            (object)['code' => 'NGN', 'name' => 'Nigerian Naira'],
            (object)['code' => 'EUR', 'name' => 'Euro'],
            (object)['code' => 'GBP', 'name' => 'British Pound'],
            (object)['code' => 'CAD', 'name' => 'Canadian Dollar'],
            (object)['code' => 'AUD', 'name' => 'Australian Dollar'],
            (object)['code' => 'JPY', 'name' => 'Japanese Yen'],
            (object)['code' => 'GHS', 'name' => 'Ghanaian Cedi'],
            (object)['code' => 'KES', 'name' => 'Kenyan Shilling'],
            (object)['code' => 'ZAR', 'name' => 'South African Rand'],
            (object)['code' => 'CNY', 'name' => 'Chinese Yuan'],
            (object)['code' => 'AED', 'name' => 'UAE Dirham'],
        ]);
    }

    $queryArray = [];
    if ($playgroundEndpoint === '/api/v1/convert') {
        $queryArray = [
            'from' => strtoupper(trim($playgroundFrom)),
            'to' => strtoupper(trim($playgroundTo)),
            'amount' => $playgroundAmount,
            'market' => $playgroundMarket,
        ];
    } elseif ($playgroundEndpoint === '/api/v1/rates/latest') {
        $queryArray = [
            'base' => strtoupper(trim($playgroundFrom)),
            'market' => $playgroundMarket,
        ];
    }
    $queryString = !empty($queryArray) ? '?' . http_build_query($queryArray) : '';
    $fullUrl = url($playgroundEndpoint) . $queryString;
@endphp

<div class="space-y-6">
    <!-- Page Banner -->
    <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-base font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                <flux:icon icon="code-bracket-square" class="size-5 text-emerald-500" />
                <span>Interactive API Playground</span>
            </h3>
            <p class="text-xs text-zinc-500 mt-0.5">
                Execute real-time API requests directly in your browser, test dual-market conversion algorithms, and export ready-to-run code.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                HTTPS REST API v1
            </span>
        </div>
    </div>

    <!-- 2-Column Grid: Left Request Builder | Right Live Response & SDKs -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <!-- Left: Request Builder (5 cols) -->
        <div class="lg:col-span-5 rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-4">
            <div class="border-b border-zinc-100 pb-3 dark:border-zinc-800">
                <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Request Parameters</h4>
            </div>

            <!-- API Key Selector -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Target API Key</label>
                    <button type="button" wire:click="$set('activeTab', 'keys')" class="text-[11px] font-semibold text-emerald-600 hover:underline dark:text-emerald-400">
                        + New Key
                    </button>
                </div>
                @if($userKeys->count() > 0)
                    <select
                        wire:model.live="playgroundKeyId"
                        class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    >
                        @foreach($userKeys as $k)
                            <option value="{{ $k->id }}">
                                {{ $k->name }} ({{ $k->secret_prefix }}••• — {{ strtoupper($k->tier) }})
                            </option>
                        @endforeach
                    </select>
                @else
                    <div class="rounded-xl border border-amber-200 bg-amber-50/70 p-2.5 text-xs text-amber-800 dark:border-amber-800/80 dark:bg-amber-950/40 dark:text-amber-300 flex items-center justify-between">
                        <span>No active API key found.</span>
                        <button type="button" wire:click="$set('activeTab', 'keys')" class="font-bold underline">Generate Key</button>
                    </div>
                @endif
            </div>

            <!-- Endpoint Selector -->
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Endpoint</label>
                <select
                    wire:model.live="playgroundEndpoint"
                    class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs font-mono font-semibold outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                >
                    <option value="/api/v1/convert">GET /api/v1/convert (Currency Conversion)</option>
                    <option value="/api/v1/rates/latest">GET /api/v1/rates/latest (Rates Matrix)</option>
                    <option value="/api/v1/currencies">GET /api/v1/currencies (Supported Currencies)</option>
                </select>
            </div>

            @if($playgroundEndpoint === '/api/v1/convert')
                <!-- From & To Currency Dropdowns with Flags -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">From Currency</label>
                        <select
                            wire:model.live="playgroundFrom"
                            class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                        >
                            @foreach($currencies as $c)
                                <option value="{{ $c->code }}">
                                    {{ \App\Domain\ExchangeRates\Support\CurrencyHelper::flag($c->code) }} {{ $c->code }} ({{ $c->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">To Currency</label>
                        <select
                            wire:model.live="playgroundTo"
                            class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                        >
                            @foreach($currencies as $c)
                                <option value="{{ $c->code }}">
                                    {{ \App\Domain\ExchangeRates\Support\CurrencyHelper::flag($c->code) }} {{ $c->code }} ({{ $c->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Amount -->
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Amount</label>
                    <input
                        type="number"
                        step="any"
                        wire:model.live.debounce.300ms="playgroundAmount"
                        placeholder="100"
                        class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs font-mono font-semibold outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    />
                </div>

                <!-- Market Source -->
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Market Rate Source</label>
                    <select
                        wire:model.live="playgroundMarket"
                        class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    >
                        <option value="parallel">Parallel / Black Market (AbokiFX + USDT P2P)</option>
                        <option value="official">Official Central Bank (ECB / CBN)</option>
                    </select>
                </div>
            @elseif($playgroundEndpoint === '/api/v1/rates/latest')
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Base Currency</label>
                    <select
                        wire:model.live="playgroundFrom"
                        class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    >
                        @foreach($currencies as $c)
                            <option value="{{ $c->code }}">
                                {{ \App\Domain\ExchangeRates\Support\CurrencyHelper::flag($c->code) }} {{ $c->code }} ({{ $c->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Market Rate Source</label>
                    <select
                        wire:model.live="playgroundMarket"
                        class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    >
                        <option value="parallel">Parallel Market</option>
                        <option value="official">Official Central Bank</option>
                    </select>
                </div>
            @endif

            <!-- Authentication Preview -->
            <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-800 dark:bg-zinc-950 space-y-1">
                <div class="flex items-center justify-between text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                    <span>Authentication Header</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-normal">
                        {{ $selectedKey ? $selectedKey->name : 'Sample Key' }}
                    </span>
                </div>
                <div class="font-mono text-[11px] text-zinc-700 dark:text-zinc-300 truncate">
                    Authorization: Bearer {{ $tokenPlaceholder }}
                </div>
            </div>

            <!-- Send Button -->
            <button
                type="button"
                wire:click="executePlayground"
                wire:loading.attr="disabled"
                class="w-full flex items-center justify-center gap-2 rounded-xl bg-zinc-900 py-2.5 text-xs font-semibold text-white shadow-2xs transition hover:bg-zinc-800 active:scale-95 disabled:opacity-50 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200"
            >
                <span wire:loading.remove wire:target="executePlayground" class="flex items-center gap-2">
                    <flux:icon icon="paper-airplane" class="size-3.5" />
                    <span>Send API Request</span>
                </span>
                <span wire:loading wire:target="executePlayground" class="flex items-center gap-2">
                    <flux:icon icon="arrow-path" class="size-3.5 animate-spin" />
                    <span>Executing Request...</span>
                </span>
            </button>
        </div>

        <!-- Right: Live Response & SDK Studio (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Live Response Card -->
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-3">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-3 dark:border-zinc-800">
                    <div class="flex items-center gap-2">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Live JSON Response</h4>
                        @if($playgroundResponseStatus)
                            <span class="rounded px-2 py-0.5 text-[10px] font-bold font-mono {{ $playgroundResponseStatus >= 200 && $playgroundResponseStatus < 300 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' }}">
                                HTTP {{ $playgroundResponseStatus }}
                            </span>
                        @endif
                    </div>

                    @if($playgroundResponseLatencyMs)
                        <div class="flex items-center gap-1.5 text-xs font-mono text-zinc-500">
                            <flux:icon icon="bolt" class="size-3.5 text-amber-500" />
                            <span>{{ $playgroundResponseLatencyMs }}ms</span>
                        </div>
                    @endif
                </div>

                @if($playgroundResponseJson)
                    <div class="relative rounded-xl bg-zinc-950 p-4 font-mono text-xs text-zinc-200 border border-zinc-800 max-h-80 overflow-y-auto" x-data="{ copiedJson: false }">
                        <button
                            type="button"
                            @click="
                                navigator.clipboard.writeText(`{{ addslashes($playgroundResponseJson) }}`);
                                copiedJson = true;
                                setTimeout(() => copiedJson = false, 1800);
                            "
                            class="absolute top-3 right-3 rounded-lg bg-zinc-800 px-2.5 py-1 text-[10px] font-sans font-semibold text-zinc-300 transition hover:bg-zinc-700 active:scale-95"
                        >
                            <span x-text="copiedJson ? 'Copied!' : 'Copy JSON'"></span>
                        </button>
                        <pre class="leading-relaxed whitespace-pre-wrap">{{ $playgroundResponseJson }}</pre>
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-zinc-200 p-8 text-center text-xs text-zinc-400 dark:border-zinc-800">
                        Click <strong class="text-zinc-700 dark:text-zinc-300">"Send API Request"</strong> on the left to execute this query against the live engine and view the returned JSON.
                    </div>
                @endif
            </div>

            <!-- Multi-Language Code Snippets Card -->
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 space-y-3">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-3 dark:border-zinc-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Generated Integration Code</h4>
                    <!-- Language Tabs -->
                    <div class="flex items-center gap-1 bg-zinc-100 p-1 rounded-xl dark:bg-zinc-800">
                        <button
                            type="button"
                            wire:click="$set('playgroundSdkTab', 'curl')"
                            class="rounded-lg px-2.5 py-1 text-[11px] font-semibold transition {{ $playgroundSdkTab === 'curl' ? 'bg-white text-zinc-900 shadow-2xs dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' }}"
                        >
                            cURL
                        </button>
                        <button
                            type="button"
                            wire:click="$set('playgroundSdkTab', 'js')"
                            class="rounded-lg px-2.5 py-1 text-[11px] font-semibold transition {{ $playgroundSdkTab === 'js' ? 'bg-white text-zinc-900 shadow-2xs dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' }}"
                        >
                            JavaScript
                        </button>
                        <button
                            type="button"
                            wire:click="$set('playgroundSdkTab', 'python')"
                            class="rounded-lg px-2.5 py-1 text-[11px] font-semibold transition {{ $playgroundSdkTab === 'python' ? 'bg-white text-zinc-900 shadow-2xs dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' }}"
                        >
                            Python
                        </button>
                        <button
                            type="button"
                            wire:click="$set('playgroundSdkTab', 'php')"
                            class="rounded-lg px-2.5 py-1 text-[11px] font-semibold transition {{ $playgroundSdkTab === 'php' ? 'bg-white text-zinc-900 shadow-2xs dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' }}"
                        >
                            PHP
                        </button>
                    </div>
                </div>

                <!-- Snippets Container -->
                <div class="relative rounded-xl bg-zinc-950 p-4 font-mono text-xs text-zinc-200 border border-zinc-800 overflow-x-auto">
                    @if($playgroundSdkTab === 'curl')
                        <pre class="leading-relaxed"><code>curl -X GET "{{ $fullUrl }}" \
  -H "Authorization: Bearer <span class="text-emerald-400">{{ $tokenPlaceholder }}</span>" \
  -H "Accept: application/json"</code></pre>
                    @elseif($playgroundSdkTab === 'js')
                        <pre class="leading-relaxed"><code>const response = await fetch("{{ $fullUrl }}", {
  method: "GET",
  headers: {
    "Authorization": "Bearer <span class="text-emerald-400">{{ $tokenPlaceholder }}</span>",
    "Accept": "application/json"
  }
});

const data = await response.json();
console.log(data);</code></pre>
                    @elseif($playgroundSdkTab === 'python')
                        <pre class="leading-relaxed"><code>import requests

url = "{{ url($playgroundEndpoint) }}"
headers = {
    "Authorization": "Bearer <span class="text-emerald-400">{{ $tokenPlaceholder }}</span>",
    "Accept": "application/json"
}
params = @json($queryArray)

response = requests.get(url, headers=headers, params=params)
print(response.json())</code></pre>
                    @elseif($playgroundSdkTab === 'php')
                        <pre class="leading-relaxed"><code>use Illuminate\Support\Facades\Http;

$response = Http::withToken('<span class="text-emerald-400">{{ $tokenPlaceholder }}</span>')
    ->acceptJson()
    ->get('{{ url($playgroundEndpoint) }}', @json($queryArray));

$data = $response->json();</code></pre>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
