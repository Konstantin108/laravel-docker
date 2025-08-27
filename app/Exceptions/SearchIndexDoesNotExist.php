<?php

namespace App\Exceptions;

use Exception;

class SearchIndexDoesNotExist extends Exception
{
    public static function buildMessage(string $indexName): SearchIndexDoesNotExist
    {
        return new self(sprintf(
            'The mapping does not contain a search index with name [%s].',
            $indexName
        ));
    }
}
