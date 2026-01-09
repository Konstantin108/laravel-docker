<?php

declare(strict_types=1);

namespace App\Factories;

use App\Factories\Contracts\SourceDtoFactoryContract;
use App\Services\Contracts\SearchableSourceContract;
use App\Services\User\Entities\UserEnriched;
use Illuminate\Support\Carbon;

class UserSourceDtoFactory implements SourceDtoFactoryContract
{
    /**
     * @param  array<string, mixed>  $source
     */
    public function createFromArray(array $source): SearchableSourceContract
    {
        return new UserEnriched(
            id: $source['id'],
            name: $source['name'],
            email: $source['email'],
            reserveEmail: $source['reserve_email'],
            phone: $source['phone'],
            telegram: $source['telegram'],
            emailVerifiedAt: Carbon::parse($source['email_verified_at']),
            createdAt: Carbon::parse($source['created_at']),
            updatedAt: Carbon::parse($source['updated_at'])
        );
    }
}
