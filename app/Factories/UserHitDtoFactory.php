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
     * @param  array<string, mixed>  $hit
     */
    public function createFromArray(array $hit): HitDtoContract
    {
        return new UserEnrichedDto(
            id: $hit['_source']['id'],
            name: $hit['_source']['name'],
            email: $hit['_source']['email'],
            reserveEmail: $hit['_source']['reserve_email'],
            phone: $hit['_source']['phone'],
            telegram: $hit['_source']['telegram'],
            emailVerifiedAt: Carbon::make($hit['_source']['email_verified_at']),
            createdAt: Carbon::make($hit['_source']['created_at']),
            updatedAt: Carbon::make($hit['_source']['updated_at'])
        );
    }
}
