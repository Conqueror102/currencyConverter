<?php

namespace App\Domain\ExchangeRates\Exceptions;

use Exception;
use Throwable;

class RateProviderException extends Exception
{
    public function __construct(
        string $message = 'Exchange rate provider communication error.',
        int $code = 0,
        ?Throwable $previous = null,
        public ?string $provider = null,
        public ?int $statusCode = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
