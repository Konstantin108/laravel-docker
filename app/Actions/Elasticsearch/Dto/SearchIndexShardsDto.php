<?php

declare(strict_types=1);

namespace App\Actions\Elasticsearch\Dto;

use Spatie\LaravelData\Data;

final class SearchIndexShardsDto extends Data
{
    public function __construct(
        public readonly int $total,
        public readonly int $successful,
        public readonly int $skipped,
        public readonly int $failed,
    ) {}
}
