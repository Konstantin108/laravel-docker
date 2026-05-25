<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Dto;

use App\Enums\SortedByEnum;

final readonly class PaginationRequestDto
{
    public function __construct(
        public int $size,
        public int $from,
        public SortedByEnum $sort,
        public ?string $search = null,
    ) {}
}
