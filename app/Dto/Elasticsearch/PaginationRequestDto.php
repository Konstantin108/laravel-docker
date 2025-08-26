<?php

declare(strict_types=1);

namespace App\Dto\Elasticsearch;

final class PaginationRequestDto
{
    public function __construct(
        public int $size,
        public int $from,
        public ?string $search = null,
    ) {}
}
