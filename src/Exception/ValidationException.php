<?php

namespace App\Exception;

class ValidationException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}