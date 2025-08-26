<?php

declare(strict_types=1);

namespace App\Dto\Elasticsearch;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class SearchIndexHitsDto extends Data
{
    public function __construct(
        public int $total,
        public string $relation,
        public ?float $maxScore,
    ) {}
}
