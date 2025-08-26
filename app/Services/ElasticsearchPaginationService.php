<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\Elasticsearch\PaginationRequestDto;

class ElasticsearchPaginationService
{
    /**
     * @param array{
     *     search: string|null,
     *     perPage: int|null,
     *     page: int|null
     * } $params
     */
    public function getPaginationRequestData(array $params, int $defaultPerPage): PaginationRequestDto
    {
        $page = $params['page'] ?? 1;
        $size = $params['perPage'] ?? $defaultPerPage;
        $from = --$page * $size;

        return new PaginationRequestDto(
            size: $size,
            from: $from,
            search: $params['search']
        );
    }
}
