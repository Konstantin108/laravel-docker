<?php

declare(strict_types=1);

namespace App\Dto\User;

use App\Dto\Contracts\SourceDtoContract;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
final class UserEnrichedDto extends Data implements SourceDtoContract
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $reserveEmail = null,
        public readonly ?string $phone = null,
        public readonly ?string $telegram = null,
        public readonly ?Carbon $emailVerifiedAt = null,
        public readonly ?Carbon $createdAt = null,
        public readonly ?Carbon $updatedAt = null,
    ) {}
}
