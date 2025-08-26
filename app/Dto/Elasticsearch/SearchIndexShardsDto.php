<?php

declare(strict_types=1);

namespace App\Dto\Elasticsearch;

use Spatie\LaravelData\Data;

final class SearchIndexShardsDto extends Data
{
    public function __construct(
        public int $total,
        public int $successful,
        public int $skipped,
        public int $failed,
    ) {}
}
