<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Factories;

use App\Services\Elasticsearch\Dto\SearchIndexHitsDto;
use App\Services\Elasticsearch\Dto\SearchIndexShardsDto;
use App\Services\Elasticsearch\Entities\SearchResult;
use App\Services\Elasticsearch\SourceDtoCollectionService;

final readonly class SearchResultFactory
{
    public function __construct(
        private SourceDtoCollectionService $collectionService
    ) {}

    /**
     * @param array{
     *     took: int,
     *     timed_out: bool,
     *     _shards: array{
     *          total: int,
     *          successful: int,
     *          skipped: int,
     *          failed: int
     *      },
     *     hits: array{
     *          total: array{
     *              value: int,
     *              relation: string
     *          },
     *          max_score: null|float,
     *          hits: array{
     *              _source: mixed
     *          }
     *     }
     * } $response
     */
    public function createFromArray(array $response): SearchResult
    {
        return new SearchResult(
            took: $response['took'],
            timedOut: $response['timed_out'],
            shardsDto: SearchIndexShardsDto::from($response['_shards']),
            hitsDto: new SearchIndexHitsDto(
                total: $response['hits']['total']['value'],
                relation: $response['hits']['total']['relation'],
                maxScore: $response['hits']['max_score'],
            ),
            hits: $this->collectionService->execute($response['hits']['hits'])
        );
    }
}
