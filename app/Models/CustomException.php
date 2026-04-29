<?php

namespace App\Models;

class CustomException extends \Exception
{
    private int $statusCode;
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->statusCode = $code;
    }

    public function setStatusCode($statusCode): void
    {
        $this->statusCode = $statusCode;
    }
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
