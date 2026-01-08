<?php

declare(strict_types=1);

namespace App\Events\Elasticsearch;

use App\Entities\Contracts\SearchableSourceContract;
use Illuminate\Support\Collection;

final readonly class SearchIndexFilledEvent
{
    public function __construct(
        /** @var Collection<int, SearchableSourceContract> */
        public Collection $items,
        public string $indexName
    ) {}
}
