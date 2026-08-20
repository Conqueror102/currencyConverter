<?php

namespace App\Domain\ExchangeRates\Data;

use Carbon\CarbonImmutable;

readonly class ConversionResult
{
    public function __construct(
        public string $from,
        public string $to,
        public string $amount,
        public string $rate,
        public string $inverseRate,
        public string $result,
        public CarbonImmutable $rateDate,
        public string $provider,
        public string $marketType = 'official',
        public ?string $officialRate = null,
        public ?string $spreadPercentage = null,
        public ?string $buyRate = null,
        public ?string $sellRate = null,
        public string $tradeAction = 'mid',
        public ?string $bidAskSpread = null,
        public bool $isDerived = false,
        public bool $isStale = false,
        public ?CarbonImmutable $fetchedAt = null,
        public ?int $ageDays = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $meta = [
            'rate_date' => $this->rateDate->toDateString(),
            'market_type' => $this->marketType,
            'trade_action' => $this->tradeAction,
            'provider' => $this->provider,
            'is_derived' => $this->isDerived,
            'is_stale' => $this->isStale,
            'age_days' => $this->ageDays ?? (int) $this->rateDate->diffInDays(CarbonImmutable::today()),
            'fetched_at' => ($this->fetchedAt ?? CarbonImmutable::now())->toIso8601String(),
        ];

        if ($this->officialRate !== null) {
            $meta['official_rate'] = $this->officialRate;
        }

        if ($this->spreadPercentage !== null) {
            $meta['spread_percentage'] = $this->spreadPercentage;
        }

        if ($this->buyRate !== null) {
            $meta['buy_rate'] = $this->buyRate;
        }

        if ($this->sellRate !== null) {
            $meta['sell_rate'] = $this->sellRate;
        }

        if ($this->bidAskSpread !== null) {
            $meta['bid_ask_spread'] = $this->bidAskSpread;
        }

        return [
            'data' => [
                'from' => $this->from,
                'to' => $this->to,
                'amount' => $this->amount,
                'rate' => $this->rate,
                'inverse_rate' => $this->inverseRate,
                'result' => $this->result,
            ],
            'meta' => $meta,
        ];
    }
}
