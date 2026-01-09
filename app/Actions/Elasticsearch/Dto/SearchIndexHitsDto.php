<?php

declare(strict_types=1);

namespace App\Actions\Elasticsearch\Dto;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class SearchIndexHitsDto extends Data
{
    public function __construct(
        public readonly int $total,
        public readonly string $relation,
        public readonly ?float $maxScore,
    ) {}
}
