<?php

declare(strict_types=1);

namespace App\Services\Product\Entities;

use App\Services\Contracts\SearchableSourceContract;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
final class ProductEnriched extends Data implements SearchableSourceContract
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $categoryName,
        public readonly int $price,
        public readonly int $categoryId,
        public readonly ?string $description = null,
        public readonly ?string $categoryDescription = null,
        public readonly ?Carbon $createdAt = null,
        public readonly ?Carbon $updatedAt = null,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }
}
