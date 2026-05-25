<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Contracts;

use App\Services\Elasticsearch\Dto\PaginationRequestDto;
use App\Services\Elasticsearch\Entities\SearchResult;

interface ElasticsearchServiceContract
{
    public function fillSearchIndex(?int $limit = null): mixed;

    /**
     * @return array<string, mixed>
     */
    public function createSearchIndex(): array;

    /**
     * @return array<string, bool>
     */
    public function deleteSearchIndex(): array;

    public function findInSearchIndex(PaginationRequestDto $requestDto): SearchResult;

    /**
     * @return array<string, mixed>
     */
    public function clearSearchIndex(): array;
}
