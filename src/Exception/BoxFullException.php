<?php

namespace App\Exception;

class BoxFullException extends \Exception
{
    public function __construct(string $message = "Votre box est pleine !", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}