<?php

declare(strict_types=1);

namespace App\Events\Elasticsearch;

use App\Services\Contracts\SearchableSourceContract;
use Illuminate\Support\Collection;

final readonly class SearchIndexFilledEvent
{
    /**
     * @param  Collection<int, SearchableSourceContract>  $items
     */
    public function __construct(
        public Collection $items,
        public string $indexName
    ) {}
}
