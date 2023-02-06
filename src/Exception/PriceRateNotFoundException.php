<?php

namespace App\Exception;

class PriceRateNotFoundException extends \Exception
{
    public function __construct(string $message = "Price rate not found", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}