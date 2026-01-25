<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Factories;

use App\Services\Elasticsearch\Abstract\ElasticsearchService;
use App\Services\Elasticsearch\Enums\SearchIndexEnum;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;

class ElasticsearchServiceFactory
{
    /**
     * @var array<string, ElasticsearchService>
     */
    private readonly array $services;

    public function __construct(ElasticsearchService ...$services)
    {
        $this->services = $services;
    }

    /**
     * @throws SearchIndexException
     */
    public function make(SearchIndexEnum $enum): ElasticsearchService
    {
        $indexName = $enum->value;

        if (isset($this->services[$indexName])) {
            return $this->services[$indexName];
        }

        throw SearchIndexException::doesNotExist($indexName);
    }
}
