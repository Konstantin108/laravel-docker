<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Exceptions;

use Exception;

// TODO kpstya после рефакторинга возможно это исключение уже будет не нужно

final class SearchIndexException extends Exception
{
    public static function doesNotExist(string $indexName): self
    {
        return new self(sprintf('The mapping does not contain a search index with name [%s].', $indexName));
    }
}
