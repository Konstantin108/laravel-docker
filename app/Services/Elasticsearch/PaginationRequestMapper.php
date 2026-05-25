<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch;

use App\Enums\SortedByEnum;
use App\Services\Elasticsearch\Dto\PaginationRequestDto;

class PaginationRequestMapper
{
    private const FIRST_PAGE = 1;

    private const DEFAULT_PER_PAGE = 15;

    private const DEFAULT_SORTED_BY = 'desc';

    public function map(
        ?string $search = null,
        ?int $perPage = null,
        ?string $sortedBy = null,
        ?int $page = null,
    ): PaginationRequestDto {
        $page = $page ?? self::FIRST_PAGE;
        $size = $perPage ?? self::DEFAULT_PER_PAGE;
        $from = ($page - 1) * $size;

        return new PaginationRequestDto(
            size: $size,
            from: $from,
            sort: SortedByEnum::from($sortedBy ?? self::DEFAULT_SORTED_BY),
            search: $search,
        );
    }
}
