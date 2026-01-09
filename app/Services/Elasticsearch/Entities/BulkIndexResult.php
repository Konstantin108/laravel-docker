<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Entities;

use Illuminate\Support\Collection;

final readonly class BulkIndexResult
{
    public function __construct(
        public int $took,
        public bool $errors,
        /** @var Collection<int, BulkIndexItem> */
        public Collection $items
    ) {}
}
