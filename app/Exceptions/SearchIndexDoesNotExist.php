<?php

namespace App\Exceptions;

use Exception;

class SearchIndexDoesNotExist extends Exception
{
    public static function buildMessage(string $indexName): SearchIndexDoesNotExist
    {
        return new self(sprintf(
            'Search index with name [%s] does not exist in Elasticsearch cluster',
            $indexName
        ));
    }
}
