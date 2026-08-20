<?php

namespace App\Infrastructure\ExchangeRates\Fake;

use App\Domain\ExchangeRates\Contracts\ExchangeRateProvider;
use App\Domain\ExchangeRates\Data\CurrencyData;
use App\Domain\ExchangeRates\Data\RateData;
use App\Domain\ExchangeRates\Data\RateQuery;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class FakeExchangeRateProvider implements ExchangeRateProvider
{
    /**
     * @var array<string, array<string, string>>
     */
    private array $mockRates = [
        'USD' => '1.085000000000000',
        'GBP' => '0.852000000000000',
        'JPY' => '165.420000000000000',
        'CHF' => '0.965000000000000',
        'CAD' => '1.482000000000000',
        'AUD' => '1.638000000000000',
        'NGN' => '1650.000000000000000',
    ];

    public function name(): string
    {
        return 'fake';
    }

    /**
     * Set custom mock rates for testing.
     *
     * @param  array<string, string>  $rates
     */
    public function setRates(array $rates): self
    {
        $this->mockRates = $rates;

        return $this;
    }

    public function currencies(): Collection
    {
        return collect([
            new CurrencyData(code: 'EUR', name: 'Euro', numericCode: '978', symbol: '€', minorUnit: 2),
            new CurrencyData(code: 'USD', name: 'US Dollar', numericCode: '840', symbol: '$', minorUnit: 2),
            new CurrencyData(code: 'GBP', name: 'British Pound', numericCode: '826', symbol: '£', minorUnit: 2),
            new CurrencyData(code: 'JPY', name: 'Japanese Yen', numericCode: '392', symbol: '¥', minorUnit: 0),
            new CurrencyData(code: 'CHF', name: 'Swiss Franc', numericCode: '756', symbol: 'CHF', minorUnit: 2),
            new CurrencyData(code: 'CAD', name: 'Canadian Dollar', numericCode: '124', symbol: 'CA$', minorUnit: 2),
            new CurrencyData(code: 'AUD', name: 'Australian Dollar', numericCode: '036', symbol: 'A$', minorUnit: 2),
            new CurrencyData(code: 'NGN', name: 'Nigerian Naira', numericCode: '566', symbol: '₦', minorUnit: 2),
        ]);
    }

    public function latest(RateQuery $query): Collection
    {
        return $this->buildRateCollection($query, CarbonImmutable::today());
    }

    public function historical(RateQuery $query, CarbonImmutable $date): Collection
    {
        return $this->buildRateCollection($query, $date);
    }

    public function timeSeries(
        RateQuery $query,
        CarbonImmutable $from,
        CarbonImmutable $to,
    ): Collection {
        $rates = collect();
        $current = $from->copy();

        while ($current->lessThanOrEqualTo($to)) {
            $rates = $rates->merge($this->buildRateCollection($query, $current));
            $current = $current->addDay();
        }

        return $rates;
    }

    /**
     * @return Collection<int, RateData>
     */
    private function buildRateCollection(RateQuery $query, CarbonImmutable $date): Collection
    {
        $results = collect();
        $now = CarbonImmutable::now();

        foreach ($this->mockRates as $quote => $rate) {
            if (! empty($query->quoteCurrencies) && ! in_array($quote, $query->quoteCurrencies, true)) {
                continue;
            }

            $results->push(new RateData(
                rateDate: $date,
                baseCurrency: $query->baseCurrency,
                quoteCurrency: $quote,
                rate: $rate,
                provider: $this->name(),
                providerReference: 'ECB',
                fetchedAt: $now,
            ));
        }

        return $results;
    }
}
