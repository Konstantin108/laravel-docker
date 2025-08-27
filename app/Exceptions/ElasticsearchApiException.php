<?php

namespace App\Exceptions;

use Exception;

class ElasticsearchApiException extends Exception
{
    public static function buildMessage(string $errorText, int $status = 500): ElasticsearchApiException
    {
        return new self($errorText, $status);
    }
}
