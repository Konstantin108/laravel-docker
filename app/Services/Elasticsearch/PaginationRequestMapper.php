<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch;

use App\Services\Elasticsearch\Dto\PaginationRequestDto;

class PaginationRequestMapper
{
    private const FIRST_PAGE = 1;

    private const DEFAULT_PER_PAGE = 10;

    public function map(
        ?string $search = null,
        ?int $perPage = null,
        ?int $page = null
    ): PaginationRequestDto {
        $page = $page ?? self::FIRST_PAGE;
        $size = $perPage ?? self::DEFAULT_PER_PAGE;
        $from = --$page * $size;

        return new PaginationRequestDto(
            size: $size,
            from: $from,
            search: $search,
        );
    }
}
