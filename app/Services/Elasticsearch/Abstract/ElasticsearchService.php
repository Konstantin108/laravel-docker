<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Abstract;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Services\Elasticsearch\Dto\PaginationRequestDto;
use App\Services\Elasticsearch\Entities\SearchResult;
use App\Services\Elasticsearch\Factories\SearchResultFactory;
use stdClass;

abstract class ElasticsearchService
{
    public function __construct(
        protected ElasticsearchClientContract $client,
        protected SearchResultFactory $searchResultFactory,
    ) {}

    abstract protected function indexName(): string;

    /**
     * @return array<string, mixed>
     */
    abstract protected function bodyIndexCreate(): array;

    /**
     * @return list<string>
     */
    abstract protected function multiMatchFieldsSettings(): array;

    abstract public function fillSearchIndex(?int $count = null): mixed;

    /**
     * @return array<string, mixed>
     */
    final public function createSearchIndex(): array
    {
        return $this->client->createIndex($this->bodyIndexCreate(), $this->indexName());
    }

    /**
     * @return array<string, bool>
     */
    final public function deleteSearchIndex(): array
    {
        return $this->client->deleteIndex($this->indexName());
    }

    /* TODO kpstya
        - дорабоать state и фабрику UserFactory, с учетом того, что почта может быть неверифицирована
        - доработать тесты для user/v1 и user/v2 для неверифицированных пользователей */

    final public function findInSearchIndex(PaginationRequestDto $requestDto): SearchResult
    {
        $body = $requestDto->search !== null && mb_strlen($requestDto->search) > 2
            ? $this->searchMultiMatch($requestDto)
            : $this->searchMatchAll($requestDto);

        $result = $this->client->search($body, $this->indexName());

        return $this->searchResultFactory->createFromArray($result);
    }

    /**
     * @return array<string, mixed>
     */
    final public function clearSearchIndex(): array
    {
        $body = [
            'query' => [
                'match_all' => new stdClass,
            ],
        ];

        return $this->client->clearIndex($body, $this->indexName());
    }

    /**
     * @param  array<string, int|string>  $data
     */
    final protected function makeDocElement(array $data, string $indexName): string
    {
        return sprintf(
            "%s\n%s\n",
            json_encode([
                'index' => [
                    '_index' => $indexName,
                    '_id' => $data['id'],
                ],
            ]),
            json_encode($data),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function searchMultiMatch(PaginationRequestDto $requestDto): array
    {
        return [
            'size' => $requestDto->size,
            'from' => $requestDto->from,
            'query' => [
                'multi_match' => [
                    'query' => $requestDto->search,
                    'fields' => $this->multiMatchFieldsSettings(),
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function searchMatchAll(PaginationRequestDto $requestDto): array
    {
        return [
            'size' => $requestDto->size,
            'from' => $requestDto->from,
            'query' => [
                'match_all' => new stdClass,
            ],
        ];
    }
}
