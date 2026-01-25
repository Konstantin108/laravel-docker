<?php

declare(strict_types=1);

namespace App\Services\Product\Dto;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

// TODO kpstya возможно переработать это для совместимости с командой FillSearchIndexCommand

#[MapInputName(SnakeCaseMapper::class)]
final class IndexDto extends Data
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?int $limit = null,
    ) {}
}
