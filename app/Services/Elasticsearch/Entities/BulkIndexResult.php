<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Entities;

use Illuminate\Support\Collection;

final readonly class BulkIndexResult
{
    /**
     * @param  Collection<int, BulkIndexItem>  $items
     */
    public function __construct(
        public int $took,
        public bool $errors,
        public Collection $items
    ) {}
}
