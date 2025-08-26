<?php

declare(strict_types=1);

namespace App\Entities\Elasticsearch;

use App\Dto\Contracts\HitDtoContract;
use App\Dto\Elasticsearch\SearchIndexHitsDto;
use App\Dto\Elasticsearch\SearchIndexShardsDto;
use App\Dto\User\UserEnrichedDto;
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
     */
    public static function fromArray(array $data): SearchResponse
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
            hits: self::hits($data['hits']['hits'])
        );
    }

    // TODO kpstya предусмотреть, если ответ пустой

    /**
     * @param  array<string, mixed>  $hits
     * @return Collection<string, HitDtoContract>
     */
    private static function hits(array $hits): Collection
    {
        // TODO kpstya возможно заменить на DI

        /** @var HitDtoCollectionService $collectionService */
        $collectionService = app(HitDtoCollectionService::class);

        return collect($collectionService->create($hits));
    }
}
