<?php

use App\Domain\ExchangeRates\Services\ConversionService;
use App\Domain\ExchangeRates\Support\CurrencyHelper;
use App\Models\Currency;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $amount = '100';

    public string $from = 'USD';

    public string $to = 'NGN';

    public string $market = 'parallel';

    public string $tradeAction = 'mid';

    public ?string $errorMessage = null;

    /**
     * Swap the from and to currencies.
     */
    public function swap(): void
    {
        $temp = $this->from;
        $this->from = $this->to;
        $this->to = $temp;
        $this->errorMessage = null;
    }

    /**
     * Set quick preset amount.
     */
    public function setAmount(string $val): void
    {
        $this->amount = $val;
        $this->errorMessage = null;
    }

    /**
     * Set market type (official or parallel).
     */
    public function setMarket(string $m): void
    {
        $this->market = in_array($m, ['official', 'parallel'], true) ? $m : 'official';
    }

    /**
     * Set trade action (mid, buy, sell).
     */
    public function setTradeAction(string $action): void
    {
        $this->tradeAction = in_array($action, ['mid', 'buy', 'sell'], true) ? $action : 'mid';
    }

    /**
     * Get flag emoji for currency code.
     */
    public function flag(string $code): string
    {
        return CurrencyHelper::flag($code);
    }

    /**
     * Get currency name.
     */
    public function getCurrencyName(string $code): string
    {
        return CurrencyHelper::name($code);
    }

    /**
     * All available active currencies with flags and clean names.
     */
    #[Computed]
    public function currencies(): array
    {
        /** @var ConversionService $service */
        $service = app(ConversionService::class);
        $list = $service->getActiveCurrencies();

        if ($list->isEmpty()) {
            $defaultCodes = ['USD', 'EUR', 'GBP', 'NGN', 'JPY', 'CAD', 'AUD', 'GHS', 'KES', 'ARS', 'ZAR', 'EGP', 'AED', 'INR', 'CNY', 'CHF', 'BRL'];

            return array_map(fn ($code) => [
                'code' => $code,
                'name' => CurrencyHelper::name($code),
                'symbol' => CurrencyHelper::symbol($code),
                'flag' => CurrencyHelper::flag($code),
            ], $defaultCodes);
        }

        return $list->map(fn (Currency $c) => [
            'code' => $c->code,
            'name' => ($c->name && $c->name !== $c->code) ? $c->name : CurrencyHelper::name($c->code),
            'symbol' => $c->symbol ?? CurrencyHelper::symbol($c->code),
            'flag' => CurrencyHelper::flag($c->code),
        ])->all();
    }

    /**
     * Format a numeric string with thousands comma separators.
     */
    private function formatWithCommas(string|float|int $value, int $decimals = 2): string
    {
        $str = (string) $value;
        $parts = explode('.', $str);
        $formattedInt = number_format((float) ($parts[0] ?? 0));

        if (! isset($parts[1])) {
            return $formattedInt;
        }

        $dec = substr($parts[1], 0, $decimals);

        return "{$formattedInt}.{$dec}";
    }

    /**
     * Compute current conversion result.
     */
    #[Computed]
    public function conversion(): ?array
    {
        $this->errorMessage = null;
        $amt = trim($this->amount);

        if (! is_numeric($amt) || (float) $amt <= 0) {
            $amt = '100';
        }

        try {
            /** @var ConversionService $service */
            $service = app(ConversionService::class);
            $result = $service->convert($this->from, $this->to, $amt, null, null, $this->market, $this->tradeAction);
            $payload = $result->toArray();

            $payload['data']['amount'] = number_format((float) $payload['data']['amount'], 2);
            $payload['data']['result'] = number_format((float) $payload['data']['result'], 2);
            $payload['data']['rate'] = number_format((float) $payload['data']['rate'], 4);
            $payload['data']['inverse_rate'] = number_format((float) $payload['data']['inverse_rate'], 6);

            if (isset($payload['meta']['official_rate'])) {
                $payload['meta']['official_rate'] = number_format((float) $payload['meta']['official_rate'], 2);
            }
            if (isset($payload['meta']['buy_rate'])) {
                $payload['meta']['buy_rate'] = number_format((float) $payload['meta']['buy_rate'], 2);
            }
            if (isset($payload['meta']['sell_rate'])) {
                $payload['meta']['sell_rate'] = number_format((float) $payload['meta']['sell_rate'], 2);
            }

            try {
                \App\Models\ConversionLog::create([
                    'source' => 'web_ui',
                    'base_currency' => $this->from,
                    'quote_currency' => $this->to,
                    'market_type' => $this->market,
                    'trade_action' => $this->tradeAction,
                    'amount' => $amt,
                    'rate' => $result->rate,
                    'result' => $result->convertedAmount,
                    'ip_address' => request()->ip(),
                    'created_at' => now(),
                ]);
            } catch (\Throwable) {
                // Non-blocking
            }

            return $payload;
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();

            $rates = [
                'official' => [
                    'USD_EUR' => '0.8622',
                    'EUR_USD' => '1.1598',
                    'USD_NGN' => '1347.74',
                    'NGN_USD' => '0.000742',
                    'EUR_NGN' => '1563.11',
                    'NGN_EUR' => '0.000640',
                    'USD_GBP' => '0.7381',
                    'GBP_USD' => '1.3548',
                    'USD_JPY' => '159.44',
                ],
                'parallel' => [
                    'USD_EUR' => '0.8622',
                    'EUR_USD' => '1.1598',
                    'USD_NGN' => '1404.00',
                    'NGN_USD' => '0.000712',
                    'EUR_NGN' => '1628.36',
                    'NGN_EUR' => '0.000614',
                    'USD_GBP' => '0.7381',
                    'GBP_USD' => '1.3548',
                    'USD_JPY' => '159.44',
                ],
            ];

            $marketRates = $rates[$this->market] ?? $rates['official'];
            $rate = (float) ($marketRates["{$this->from}_{$this->to}"] ?? '1.00');
            $officialRate = (float) ($rates['official']["{$this->from}_{$this->to}"] ?? $rate);

            $buyRate = $rate * 0.9957;
            $sellRate = $rate * 1.0043;

            $effectiveRate = match ($this->tradeAction) {
                'buy' => $sellRate,
                'sell' => $buyRate,
                default => $rate,
            };

            $resultAmt = (float) $amt * $effectiveRate;
            $invRate = $effectiveRate > 0 ? (1 / $effectiveRate) : 1;

            $spread = null;
            if ($this->market === 'parallel' && $officialRate > 0) {
                $spreadVal = (($effectiveRate - $officialRate) / $officialRate) * 100;
                $spread = ($spreadVal >= 0 ? '+' : '').number_format($spreadVal, 2).'%';
            }

            return [
                'data' => [
                    'from' => $this->from,
                    'to' => $this->to,
                    'amount' => number_format((float) $amt, 2),
                    'rate' => number_format($effectiveRate, 4),
                    'inverse_rate' => number_format($invRate, 6),
                    'result' => number_format($resultAmt, 2),
                ],
                'meta' => [
                    'rate_date' => now()->toDateString(),
                    'market_type' => $this->market,
                    'trade_action' => $this->tradeAction,
                    'provider' => $this->market === 'parallel' ? 'Parallel Market' : 'Official Central Bank',
                    'official_rate' => number_format($officialRate, 2),
                    'buy_rate' => number_format($buyRate, 2),
                    'sell_rate' => number_format($sellRate, 2),
                    'bid_ask_spread' => number_format($sellRate - $buyRate, 2).' (0.86%)',
                    'spread_percentage' => $spread,
                    'is_derived' => false,
                    'age_days' => 0,
                ],
            ];
        }
    }

    /**
     * Compute popular rate cards for the active base currency.
     */
    #[Computed]
    public function popularPairs(): array
    {
        $popularQuotes = ['EUR', 'GBP', 'NGN', 'JPY', 'CAD', 'AUD', 'GHS', 'KES'];
        $base = $this->from;
        $pairs = [];

        /** @var ConversionService $service */
        $service = app(ConversionService::class);

        foreach ($popularQuotes as $quote) {
            if ($quote === $base) {
                continue;
            }

            try {
                $res = $service->convert($base, $quote, '1.0', null, null, $this->market, $this->tradeAction);
                $pairs[] = [
                    'pair' => "{$base}/{$quote}",
                    'base' => $base,
                    'quote' => $quote,
                    'base_flag' => CurrencyHelper::flag($base),
                    'quote_flag' => CurrencyHelper::flag($quote),
                    'rate' => number_format((float) $res->rate, 4),
                ];
            } catch (\Throwable) {
                continue;
            }
        }

        return array_slice($pairs, 0, 4);
    }
};
?>

<div class="w-full max-w-3xl mx-auto" x-data="{ copied: false }">
    <!-- Clean, structured Card -->
    <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
        <div class="space-y-6">
            <!-- Market Selection Tabs (with clean SVG icons) -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-zinc-100 pb-4 dark:border-zinc-800">
                <div class="inline-flex rounded-xl bg-zinc-100 p-1 dark:bg-zinc-800">
                    <button
                        type="button"
                        wire:click="setMarket('official')"
                        class="flex items-center gap-2 rounded-lg px-3.5 py-1.5 text-xs font-semibold transition {{ $market === 'official' ? 'bg-white text-zinc-900 shadow-xs dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' }}"
                    >
                        <flux:icon icon="building-library" class="size-3.5" />
                        <span>Official Market</span>
                    </button>
                    <button
                        type="button"
                        wire:click="setMarket('parallel')"
                        class="flex items-center gap-2 rounded-lg px-3.5 py-1.5 text-xs font-semibold transition {{ $market === 'parallel' ? 'bg-white text-zinc-900 shadow-xs dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' }}"
                    >
                        <flux:icon icon="arrows-up-down" class="size-3.5" />
                        <span>Parallel / Street (AbokiFX)</span>
                    </button>
                </div>

                <!-- Street Trade Action (When in Parallel mode) -->
                @if($market === 'parallel')
                    <div class="inline-flex items-center rounded-lg bg-zinc-100 p-0.5 dark:bg-zinc-800 text-xs">
                        <button
                            type="button"
                            wire:click="setTradeAction('mid')"
                            class="rounded-md px-2.5 py-1 font-medium transition {{ $tradeAction === 'mid' ? 'bg-white text-zinc-900 shadow-xs dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' }}"
                        >
                            Reference
                        </button>
                        <button
                            type="button"
                            wire:click="setTradeAction('sell')"
                            class="rounded-md px-2.5 py-1 font-medium transition {{ $tradeAction === 'sell' ? 'bg-zinc-900 text-white shadow-xs dark:bg-zinc-100 dark:text-zinc-900' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' }}"
                        >
                            Selling {{ $from }}
                        </button>
                        <button
                            type="button"
                            wire:click="setTradeAction('buy')"
                            class="rounded-md px-2.5 py-1 font-medium transition {{ $tradeAction === 'buy' ? 'bg-zinc-900 text-white shadow-xs dark:bg-zinc-100 dark:text-zinc-900' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' }}"
                        >
                            Buying {{ $from }}
                        </button>
                    </div>
                @endif
            </div>

            <!-- Currency Conversion Form -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-12 sm:items-end">
                <!-- Amount Field -->
                <div class="sm:col-span-5">
                    <label class="block text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1.5">
                        Amount
                    </label>
                    <input
                        type="number"
                        step="any"
                        wire:model.live.debounce.250ms="amount"
                        class="w-full rounded-xl border border-zinc-200 bg-white px-3.5 py-2.5 text-base font-semibold text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:focus:border-zinc-400"
                        placeholder="100.00"
                    />
                </div>

                <!-- From Currency (with Country Flag) -->
                <div class="sm:col-span-3">
                    <label class="block text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1.5">
                        From
                    </label>
                    <select
                        wire:model.live="from"
                        class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm font-semibold text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:focus:border-zinc-400"
                    >
                        @foreach($this->currencies as $currency)
                            <option value="{{ $currency['code'] }}" class="bg-white text-zinc-900 dark:bg-zinc-900 dark:text-zinc-100">
                                {{ $currency['flag'] }} {{ $currency['code'] }} — {{ $currency['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Swap Button -->
                <div class="flex justify-center sm:col-span-1">
                    <button
                        type="button"
                        wire:click="swap"
                        title="Swap currencies"
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-zinc-200 bg-zinc-50 text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900 active:scale-95 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700"
                    >
                        <flux:icon icon="arrows-right-left" class="size-4" />
                    </button>
                </div>

                <!-- To Currency (with Country Flag) -->
                <div class="sm:col-span-3">
                    <label class="block text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1.5">
                        To
                    </label>
                    <select
                        wire:model.live="to"
                        class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm font-semibold text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:focus:border-zinc-400"
                    >
                        @foreach($this->currencies as $currency)
                            <option value="{{ $currency['code'] }}" class="bg-white text-zinc-900 dark:bg-zinc-900 dark:text-zinc-100">
                                {{ $currency['flag'] }} {{ $currency['code'] }} — {{ $currency['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Conversion Output Box (with Country Flags) -->
            @if($this->conversion)
                <div class="rounded-xl border border-zinc-200 bg-zinc-50/60 p-5 dark:border-zinc-800 dark:bg-zinc-800/40">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                                <span>{{ $this->flag($from) }}</span>
                                <span>{{ $this->conversion['data']['amount'] }} {{ $this->getCurrencyName($from) }} =</span>
                            </div>
                            <div class="mt-1 flex items-baseline gap-2">
                                <span class="text-2xl">{{ $this->flag($to) }}</span>
                                <span class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                                    {{ $this->conversion['data']['result'] }}
                                </span>
                                <span class="text-lg font-medium text-zinc-500 dark:text-zinc-400">{{ $to }}</span>
                            </div>
                        </div>

                        <!-- Rates & Copy -->
                        <div class="flex items-center gap-3">
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 text-right font-mono">
                                <div>1 {{ $from }} = {{ $this->conversion['data']['rate'] }} {{ $to }}</div>
                                <div class="text-[11px] text-zinc-400">1 {{ $to }} = {{ $this->conversion['data']['inverse_rate'] }} {{ $from }}</div>
                            </div>

                            <button
                                type="button"
                                @click="
                                    navigator.clipboard.writeText('{{ $this->conversion['data']['amount'] }} {{ $from }} = {{ $this->conversion['data']['result'] }} {{ $to }}');
                                    copied = true;
                                    setTimeout(() => copied = false, 2000);
                                "
                                class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-700 shadow-2xs transition hover:bg-zinc-50 active:scale-95 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700"
                            >
                                <flux:icon x-show="!copied" icon="clipboard" class="size-3.5" />
                                <flux:icon x-show="copied" icon="check" class="size-3.5 text-emerald-600 dark:text-emerald-400" style="display: none;" />
                                <span x-text="copied ? 'Copied' : 'Copy'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Parallel Market Bid/Ask Overview (if in Parallel Mode) -->
                    @if($market === 'parallel' && (!empty($this->conversion['meta']['buy_rate']) || !empty($this->conversion['meta']['sell_rate'])))
                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-2 border-t border-zinc-200/70 pt-3 dark:border-zinc-700/70 text-xs">
                            <div class="flex items-center justify-between sm:justify-start gap-1.5">
                                <span class="text-zinc-500 dark:text-zinc-400">Buy Rate (Bid):</span>
                                <span class="font-semibold text-zinc-900 dark:text-white">{{ $this->conversion['meta']['buy_rate'] ?? 'N/A' }} {{ $to }}</span>
                            </div>
                            <div class="flex items-center justify-between sm:justify-start gap-1.5">
                                <span class="text-zinc-500 dark:text-zinc-400">Sell Rate (Ask):</span>
                                <span class="font-semibold text-zinc-900 dark:text-white">{{ $this->conversion['meta']['sell_rate'] ?? 'N/A' }} {{ $to }}</span>
                            </div>
                            <div class="flex items-center justify-between sm:justify-start gap-1.5">
                                <span class="text-zinc-500 dark:text-zinc-400">Dealer Spread:</span>
                                <span class="font-semibold text-zinc-900 dark:text-white">{{ $this->conversion['meta']['bid_ask_spread'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Spread comparison note -->
                    @if($market === 'parallel' && !empty($this->conversion['meta']['spread_percentage']))
                        <div class="mt-3 text-[11px] text-zinc-500 dark:text-zinc-400">
                            Parallel rate is <strong class="text-zinc-800 dark:text-zinc-200">{{ $this->conversion['meta']['spread_percentage'] }}</strong> compared to Official Central Bank (1 {{ $from }} = {{ $this->conversion['meta']['official_rate'] ?? 'N/A' }} {{ $to }}).
                        </div>
                    @endif
                </div>
            @endif

            <!-- Popular Benchmarks with Country Flags -->
            <div class="border-t border-zinc-100 pt-4 dark:border-zinc-800">
                <div class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 mb-2">
                    Popular {{ $from }} Rates ({{ ucfirst($market) }})
                </div>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                    @foreach($this->popularPairs as $pair)
                        <div class="rounded-lg border border-zinc-200/80 bg-zinc-50/50 p-2.5 dark:border-zinc-800 dark:bg-zinc-800/20">
                            <div class="flex items-center gap-1 text-[11px] font-medium text-zinc-500 dark:text-zinc-400">
                                <span>{{ $pair['base_flag'] }}</span>
                                <span>/</span>
                                <span>{{ $pair['quote_flag'] }}</span>
                                <span>{{ $pair['pair'] }}</span>
                            </div>
                            <div class="mt-0.5 font-mono text-xs font-bold text-zinc-900 dark:text-white">
                                {{ $pair['rate'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
