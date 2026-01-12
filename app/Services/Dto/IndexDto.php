<?php

declare(strict_types=1);

namespace App\Services\Dto;

/* TODO kpstya
    наверное я зря убирал toArray(), скорее всего IndexDto должен быть для index запроса
    каждой модели, а уже для elasticsearch надо будет просто черз new создавать свой dto */

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class IndexDto extends Data
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?int $perPage = null,
        public readonly ?int $page = null,
    ) {}
}
