<?php

namespace App\Domain\ExchangeRates\Contracts;

use App\Domain\ExchangeRates\Data\CurrencyData;
use App\Domain\ExchangeRates\Data\RateData;
use App\Domain\ExchangeRates\Data\RateQuery;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

interface ExchangeRateProvider
{
    /**
     * Get the human-readable identifier of the provider.
     */
    public function name(): string;

    /**
     * Fetch all available currencies with metadata from the provider.
     *
     * @return Collection<int, CurrencyData>
     */
    public function currencies(): Collection;

    /**
     * Fetch latest exchange rates matching the given query.
     *
     * @return Collection<int, RateData>
     */
    public function latest(RateQuery $query): Collection;

    /**
     * Fetch historical exchange rates for a specific calendar date.
     *
     * @return Collection<int, RateData>
     */
    public function historical(RateQuery $query, CarbonImmutable $date): Collection;

    /**
     * Fetch timeseries exchange rates across a date range.
     *
     * @return Collection<int, RateData>
     */
    public function timeSeries(
        RateQuery $query,
        CarbonImmutable $from,
        CarbonImmutable $to,
    ): Collection;
}
