<?php

declare(strict_types=1);

namespace App\Services\Product\Dto;

use App\Enums\SortedByEnum;

final readonly class FilterDto
{
    public function __construct(
        public SortedByEnum $sortedBy,
        public ?string $search = null,
        public ?int $limit = null,
    ) {}
}
