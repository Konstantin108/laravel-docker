<?php

declare(strict_types=1);

namespace App\Entities\Elasticsearch;

use App\Dto\Elasticsearch\SearchIndexHitsDto;
use App\Dto\Elasticsearch\SearchIndexShardsDto;
use App\Entities\User\UserEnriched;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class SearchResponse extends Data
{
    public function __construct(
        public readonly int $took,
        public readonly bool $timedOut,
        public readonly SearchIndexShardsDto $shardsDto,
        public readonly SearchIndexHitsDto $hitsDto,
        /** @var Collection<string, UserEnriched> */
        public readonly Collection $hits
    ) {}
}
