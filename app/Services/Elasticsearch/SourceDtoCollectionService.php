<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch;

use App\Factories\Contracts\SourceDtoFactoryContract;
use App\Services\Contracts\SearchableSourceContract;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use Illuminate\Support\Collection;

class SourceDtoCollectionService
{
    /**
     * @var array<string, SourceDtoFactoryContract>
     */
    private readonly array $factories;

    public function __construct(SourceDtoFactoryContract ...$factories)
    {
        $this->factories = $factories;
    }

    /**
     * @param  array<string, mixed>  $hits
     * @return Collection<string, SearchableSourceContract>
     *
     * @throws SearchIndexException
     */
    public function execute(array $hits): Collection
    {
        return new Collection(array_map(function (array $hit): SearchableSourceContract {
            $indexName = $hit['_index'];
            if (isset($this->factories[$indexName])) {
                return $this->factories[$indexName]->createFromArray($hit['_source']);
            }

            throw SearchIndexException::doesNotExist($indexName);
        }, $hits));
    }
}
