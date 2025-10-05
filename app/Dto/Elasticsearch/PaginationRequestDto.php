<?php

declare(strict_types=1);

namespace App\Dto\Elasticsearch;

final readonly class PaginationRequestDto
{
    public function __construct(
        public int $size,
        public int $from,
        public ?string $search = null,
    ) {}
}
