<?php

declare(strict_types=1);

namespace App\Entities\Elasticsearch;

use App\Dto\Elasticsearch\SearchIndexHitsDto;
use App\Dto\Elasticsearch\SearchIndexShardsDto;
use App\Dto\User\UserEnrichedDto;
use App\Exceptions\SearchIndexDoesNotExist;
use App\Services\HitDtoCollectionService;
use Illuminate\Support\Collection;

final class SearchResponse
{
    public function __construct(
        public int $took,
        public bool $timedOut,
        public SearchIndexShardsDto $shardsDto,
        public SearchIndexHitsDto $hitsDto,
        /** @var Collection<string, UserEnrichedDto> */
        public Collection $hits
    ) {}

    // TODO kpstya добавить toArray

    /**
     * @param array{
     *     took: int,
     *     timed_out: bool,
     *     _shards: array{
     *         total: int,
     *         successful: int,
     *         skipped: int,
     *         failed: int
     *     },
     *     hits: array{
     *         total: array{
     *             value: int,
     *             relation: string
     *         },
     *         max_score: null|float,
     *         hits: array{
     *             _source: mixed
     *         }
     *     }
     * } $data
     *
     * @throws SearchIndexDoesNotExist
     */
    public static function fromArray(array $data, HitDtoCollectionService $collectionService): SearchResponse
    {
        return new self(
            took: $data['took'],
            timedOut: $data['timed_out'],
            shardsDto: SearchIndexShardsDto::from($data['_shards']),
            hitsDto: new SearchIndexHitsDto(
                total: $data['hits']['total']['value'],
                relation: $data['hits']['total']['relation'],
                maxScore: $data['hits']['max_score'],
            ),
            hits: $collectionService->create($data['hits']['hits'])
        );
    }
}
