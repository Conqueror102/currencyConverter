<?php

namespace App\Domain\ExchangeRates\Data;

use Carbon\CarbonImmutable;

readonly class RateQuery
{
    /**
     * @param  array<int, string>  $quoteCurrencies
     */
    public function __construct(
        public string $baseCurrency = 'EUR',
        public array $quoteCurrencies = [],
        public ?CarbonImmutable $date = null,
        public ?CarbonImmutable $from = null,
        public ?CarbonImmutable $to = null,
    ) {}

    public static function make(string $base = 'EUR'): self
    {
        return new self(baseCurrency: strtoupper($base));
    }
}
