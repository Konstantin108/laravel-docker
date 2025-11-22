<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Exceptions;

use Exception;

// TODO kpstya сделать наследование от HttpException или ловить в Handler (надо наследовать от ExceptionHandler)

final class SearchIndexException extends Exception
{
    private const DOES_NOT_EXIST_CODE = 1;

    public static function doesNotExist(string $indexName): self
    {
        return new self(
            sprintf('The mapping does not contain a search index with name [%s].', $indexName),
            self::DOES_NOT_EXIST_CODE
        );
    }
}
