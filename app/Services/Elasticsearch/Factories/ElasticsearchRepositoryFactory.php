<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Factories;

use App\Services\Elasticsearch\Enums\SearchIndexEnum;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use App\Services\Elasticsearch\Repositories\Contracts\ElasticsearchRepositoryContract;

final class ElasticsearchRepositoryFactory
{
    /**
     * @var array<string, ElasticsearchRepositoryContract>
     */
    private readonly array $repositories;

    public function __construct(ElasticsearchRepositoryContract ...$repositories)
    {
        $this->repositories = $repositories;
    }

    /**
     * @throws SearchIndexException
     */
    public function make(SearchIndexEnum $enum): ElasticsearchRepositoryContract
    {
        $indexName = $enum->value;

        if (isset($this->repositories[$indexName])) {
            return $this->repositories[$indexName];
        }

        throw SearchIndexException::doesNotExist($indexName);
    }
}
