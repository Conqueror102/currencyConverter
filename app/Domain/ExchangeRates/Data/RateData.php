<?php

namespace App\Domain\ExchangeRates\Data;

use Carbon\CarbonImmutable;

readonly class RateData
{
    /**
     * @param  array<string, mixed>|null  $rawPayload
     */
    public function __construct(
        public CarbonImmutable $rateDate,
        public string $baseCurrency,
        public string $quoteCurrency,
        public string $rate,
        public string $provider,
        public string $marketType = 'official',
        public ?string $buyRate = null,
        public ?string $sellRate = null,
        public ?string $providerReference = null,
        public ?CarbonImmutable $fetchedAt = null,
        public ?array $rawPayload = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'rate_date' => $this->rateDate->toDateString(),
            'base_currency' => $this->baseCurrency,
            'quote_currency' => $this->quoteCurrency,
            'rate' => $this->rate,
            'buy_rate' => $this->buyRate,
            'sell_rate' => $this->sellRate,
            'provider' => $this->provider,
            'market_type' => $this->marketType,
            'provider_reference' => $this->providerReference,
            'fetched_at' => ($this->fetchedAt ?? CarbonImmutable::now())->toIso8601String(),
            'raw_payload' => $this->rawPayload,
        ];
    }
}
