<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

final class ElasticsearchApiException extends Exception
{
    // TODO kpstya нужно перенести дто и исключения в папки к сервисам

    public static function buildMessage(Throwable $previous): self
    {
        return new self($previous->getMessage(), $previous->getCode(), $previous);
    }
}
