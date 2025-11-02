<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\User\Contracts\SearchableSourceContract;
use App\Exceptions\SearchIndexException;
use App\Factories\Contracts\SourceDtoFactoryContract;
use Illuminate\Support\Collection;

class SourceDtoCollectionService
{
    /**
     * @var List<SourceDtoFactoryContract>
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
    public function create(array $hits): Collection
    {
        return new Collection(array_map(function (array $hit): SearchableSourceContract {
            $indexName = $hit['_index'];
            if (! isset($this->factories[$indexName])) {
                throw SearchIndexException::doesNotExist($indexName);
            }

            return $this->factories[$indexName]->createFromArray($hit['_source']);
        }, $hits));
    }
}
