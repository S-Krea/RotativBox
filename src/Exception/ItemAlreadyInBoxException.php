<?php

namespace App\Exception;

class ItemAlreadyInBoxException extends \Exception
{
    public function __construct(string $message = "Ce produit est déjà dans votre box !", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}