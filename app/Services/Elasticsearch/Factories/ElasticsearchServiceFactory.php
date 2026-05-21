<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Factories;

use App\Services\Elasticsearch\Contracts\ElasticsearchServiceContract;
use App\Services\Elasticsearch\Enums\SearchIndexEnum;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;

class ElasticsearchServiceFactory
{
    /**
     * @var array<string, ElasticsearchServiceContract>
     */
    private readonly array $services;

    public function __construct(ElasticsearchServiceContract ...$services)
    {
        $this->services = $services;
    }

    /**
     * @throws SearchIndexException
     */
    public function make(SearchIndexEnum $enum): ElasticsearchServiceContract
    {
        $indexName = $enum->value;

        if (isset($this->services[$indexName])) {
            return $this->services[$indexName];
        }

        throw SearchIndexException::doesNotExist($indexName);
    }
}
