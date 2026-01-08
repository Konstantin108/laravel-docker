<?php

declare(strict_types=1);

namespace App\Console\Commands\Elasticsearch\Entities;

use App\Services\Elasticsearch\Enums\SearchIndexEnum;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;

final readonly class SearchIndexResolver
{
    /**
     * @throws SearchIndexException
     */
    public function fromString(string $indexName): SearchIndexEnum
    {
        $searchIndexEnum = SearchIndexEnum::tryFrom($indexName);
        if ($searchIndexEnum === null) {
            throw SearchIndexException::doesNotExist($indexName);
        }

        return $searchIndexEnum;
    }
}
