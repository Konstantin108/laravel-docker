<?php

declare(strict_types=1);

namespace App\Entities\Elasticsearch;

use App\Dto\Elasticsearch\SearchIndexHitsDto;
use App\Dto\Elasticsearch\SearchIndexShardsDto;
use App\Dto\User\UserEnrichedDto;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class SearchResponse extends Data
{
    public function __construct(
        public int $took,
        public bool $timedOut,
        public SearchIndexShardsDto $shardsDto,
        public SearchIndexHitsDto $hitsDto,
        /** @var Collection<string, UserEnrichedDto> */
        public Collection $hits
    ) {}
}
