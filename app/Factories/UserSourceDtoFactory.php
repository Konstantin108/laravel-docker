<?php

declare(strict_types=1);

namespace App\Factories;

use App\Dto\Contracts\SourceDtoContract;
use App\Dto\User\UserEnrichedDto;
use App\Factories\Contracts\SourceDtoFactoryContract;
use Illuminate\Support\Carbon;

class UserSourceDtoFactory implements SourceDtoFactoryContract
{
    /**
     * @param  array<string, mixed>  $source
     */
    public function createFromArray(array $source): SourceDtoContract
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
