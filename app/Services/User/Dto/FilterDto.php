<?php

declare(strict_types=1);

namespace App\Services\User\Dto;

use App\Enums\SortedByEnum;

final readonly class FilterDto
{
    public function __construct(
        public SortedByEnum $sortedBy,
        public ?string $search = null,
        public ?int $perPage = null
    ) {}
}
