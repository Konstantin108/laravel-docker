<?php

declare(strict_types=1);

namespace App\Actions;

use App\Entities\Elasticsearch\SearchResponse;
use App\Services\Elasticsearch\Dto\SearchIndexHitsDto;
use App\Services\Elasticsearch\Dto\SearchIndexShardsDto;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use App\Services\Elasticsearch\SourceDtoCollectionService;

class SearchResponseAction
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
     * @throws SearchIndexException
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
