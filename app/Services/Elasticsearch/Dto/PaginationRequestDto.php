<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Dto;

final readonly class PaginationRequestDto
{
    public function __construct(
        public int $size,
        public int $from,
        public ?string $search = null,
    ) {}
}
