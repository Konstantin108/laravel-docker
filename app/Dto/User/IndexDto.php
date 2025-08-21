<?php

declare(strict_types=1);

namespace App\Dto\User;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class IndexDto extends Data
{
    public function __construct(
        public ?string $search = null,
        public ?int $perPage = null,
    ) {}
}
