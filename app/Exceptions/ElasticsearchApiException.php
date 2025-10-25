<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ElasticsearchApiException extends Exception
{
    public static function buildMessage(string $errorText, int $code = 500, ?Throwable $previous = null): ElasticsearchApiException
    {
        return new self($errorText, $code, $previous);
    }
}
