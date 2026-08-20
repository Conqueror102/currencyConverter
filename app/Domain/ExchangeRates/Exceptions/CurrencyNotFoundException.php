<?php

namespace App\Domain\ExchangeRates\Exceptions;

use Exception;

class CurrencyNotFoundException extends Exception
{
    public function __construct(string $currencyCode)
    {
        parent::__construct("Currency [{$currencyCode}] is not supported or not found.");
    }
}
