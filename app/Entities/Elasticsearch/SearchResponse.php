<?php

declare(strict_types=1);

namespace App\Entities\Elasticsearch;

use App\Dto\Elasticsearch\SearchIndexHitsDto;
use App\Dto\Elasticsearch\SearchIndexShardsDto;
use App\Dto\User\UserEnrichedDto;
use Illuminate\Support\Carbon;
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

    /**
     * @param  array<string, mixed>  $hits
     * @return Collection<string, UserEnrichedDto>
     */
    private static function hits(array $hits): Collection
    {
        return collect($hits)
            ->map(fn (array $hit) => new UserEnrichedDto(
                id: $hit['_source']['id'],
                name: $hit['_source']['name'],
                email: $hit['_source']['email'],
                reserveEmail: $hit['_source']['reserve_email'],
                phone: $hit['_source']['phone'],
                telegram: $hit['_source']['telegram'],
                emailVerifiedAt: Carbon::make($hit['_source']['email_verified_at']),
                createdAt: Carbon::make($hit['_source']['created_at']),
                updatedAt: Carbon::make($hit['_source']['updated_at'])
            ));
    }
}
