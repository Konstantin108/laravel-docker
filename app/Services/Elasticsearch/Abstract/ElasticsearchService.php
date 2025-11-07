<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Abstract;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Services\Elasticsearch\Dto\PaginationRequestDto;
use App\Services\User\UserService;
use stdClass;

abstract class ElasticsearchService
{
    public function __construct(
        protected ElasticsearchClientContract $client,
        protected UserService $userService
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

    abstract public function fillSearchIndex(): mixed;

    /**
     * @return array<string, mixed>
     */
    public function createSearchIndex(): array
    {
        return $this->client->createIndex($this->bodyIndexCreate(), $this->indexName());
    }

    /**
     * @return array<string, bool>
     */
    public function deleteSearchIndex(): array
    {
        return $this->client->deleteIndex($this->indexName());
    }

    /**
     * @return array<string, mixed>
     */
    public function findInSearchIndex(PaginationRequestDto $requestDto): array
    {
        $body = $requestDto->search !== null && mb_strlen($requestDto->search) > 2
            ? $this->searchMultiMatch($requestDto)
            : $this->searchMatchAll($requestDto);

        return $this->client->search($body, $this->indexName());
    }

    /**
     * @return array<string, mixed>
     */
    public function clearSearchIndex(): array
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
    protected function makeDocElement(array $data, string $indexName): string
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
