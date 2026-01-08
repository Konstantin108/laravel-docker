<?php

declare(strict_types=1);

namespace App\Entities\Elasticsearch;

use App\Actions\Elasticsearch\Dto\SearchIndexHitsDto;
use App\Actions\Elasticsearch\Dto\SearchIndexShardsDto;
use App\Entities\User\Contracts\SearchableSourceContract;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class SearchResponse extends Data
{
    public function __construct(
        public readonly int $took,
        public readonly bool $timedOut,
        public readonly SearchIndexShardsDto $shardsDto,
        public readonly SearchIndexHitsDto $hitsDto,
        /** @var Collection<string, SearchableSourceContract> */
        public readonly Collection $hits
    ) {}
}
