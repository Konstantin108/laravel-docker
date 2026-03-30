<?php

declare(strict_types=1);

namespace App\Clients\Elasticsearch\Exceptions;

use Exception;
use Throwable;

final class ElasticsearchApiException extends Exception
{
    public static function create(Throwable $previous): self
    {
        return new self($previous->getMessage(), $previous->getCode(), $previous);
    }
}
