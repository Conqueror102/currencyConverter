<?php

namespace App\Domain\ExchangeRates\Data;

use Carbon\CarbonImmutable;

readonly class CurrencyData
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public string $code,
        public string $name,
        public ?string $numericCode = null,
        public ?string $symbol = null,
        public int $minorUnit = 2,
        public bool $isActive = true,
        public ?CarbonImmutable $availableFrom = null,
        public ?CarbonImmutable $availableUntil = null,
        public array $metadata = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'numeric_code' => $this->numericCode,
            'symbol' => $this->symbol,
            'minor_unit' => $this->minorUnit,
            'is_active' => $this->isActive,
            'available_from' => $this->availableFrom?->toDateString(),
            'available_until' => $this->availableUntil?->toDateString(),
            'metadata' => $this->metadata,
        ];
    }
}
