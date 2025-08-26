<?php

declare(strict_types=1);

namespace App\Dto\User;

use App\Dto\Contracts\HitDtoContract;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

final class UserEnrichedDto extends Data implements HitDtoContract
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?string $reserveEmail = null,
        public ?string $phone = null,
        public ?string $telegram = null,
        public ?Carbon $emailVerifiedAt = null,
        public ?Carbon $createdAt = null,
        public ?Carbon $updatedAt = null,
    ) {}
}
