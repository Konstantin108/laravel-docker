<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch;

use App\Dto\Elasticsearch\SearchIndexHitsDto;
use App\Dto\Elasticsearch\SearchIndexShardsDto;
use App\Entities\Elasticsearch\SearchResponse;
use App\Exceptions\SearchIndexDoesNotExist;
use App\Services\SourceDtoCollectionService;

class SearchResponseService
{
    public function __construct(
        private readonly SourceDtoCollectionService $collectionService
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
     *
     * @throws SearchIndexDoesNotExist
     */
    public function execute(array $response): SearchResponse
    {
        return new SearchResponse(
            took: $response['took'],
            timedOut: $response['timed_out'],
            shardsDto: SearchIndexShardsDto::from($response['_shards']),
            hitsDto: new SearchIndexHitsDto(
                total: $response['hits']['total']['value'],
                relation: $response['hits']['total']['relation'],
                maxScore: $response['hits']['max_score'],
            ),
            hits: $this->collectionService->create($response['hits']['hits'])
        );
    }
}
