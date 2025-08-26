<?php

declare(strict_types=1);

namespace App\Factories;

use App\Dto\Contracts\HitDtoContract;
use App\Dto\User\UserEnrichedDto;
use App\Factories\Contracts\HitDtoFactoryContract;
use Illuminate\Support\Carbon;

class UserHitDtoFactory implements HitDtoFactoryContract
{
    /**
     * @param  array<string, mixed>  $source
     */
    public function createFromArray(array $source): HitDtoContract
    {
        return new UserEnrichedDto(
            id: $source['id'],
            name: $source['name'],
            email: $source['email'],
            reserveEmail: $source['reserve_email'],
            phone: $source['phone'],
            telegram: $source['telegram'],
            emailVerifiedAt: Carbon::make($source['email_verified_at']),
            createdAt: Carbon::make($source['created_at']),
            updatedAt: Carbon::make($source['updated_at'])
        );
    }
}
