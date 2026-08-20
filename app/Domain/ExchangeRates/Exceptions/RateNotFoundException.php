<?php

namespace App\Domain\ExchangeRates\Exceptions;

use Exception;

class RateNotFoundException extends Exception
{
    public function __construct(string $from, string $to, ?string $date = null)
    {
        $dateStr = $date ? " for date [{$date}]" : '';
        parent::__construct("No exchange rate available from [{$from}] to [{$to}]{$dateStr}.");
    }
}
