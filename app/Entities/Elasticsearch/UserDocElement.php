<?php

declare(strict_types=1);

namespace App\Entities\Elasticsearch;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
final class UserDocElement extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?Carbon $emailVerifiedAt = null,
        public ?string $reserveEmail = null,
        public ?string $phone = null,
        public ?string $telegram = null,
        public ?Carbon $createdAt = null,
        public ?Carbon $updatedAt = null,
    ) {}
}
