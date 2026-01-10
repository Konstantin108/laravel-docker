<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch;

use App\Services\Dto\IndexDto;
use App\Services\Elasticsearch\Dto\PaginationRequestDto;

class PaginationRequestMapper
{
    private const DEFAULT_PER_PAGE = 10;

    public function map(IndexDto $indexDto): PaginationRequestDto
    {
        $page = $indexDto->page ?? 1;
        $size = $indexDto->perPage ?? self::DEFAULT_PER_PAGE;
        $from = --$page * $size;

        return new PaginationRequestDto(
            size: $size,
            from: $from,
            search: $indexDto->search,
        );
    }
}
