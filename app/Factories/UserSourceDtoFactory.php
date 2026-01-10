<?php

declare(strict_types=1);

namespace App\Factories;

use App\Factories\Contracts\SourceDtoFactoryContract;
use App\Services\Contracts\SearchableSourceContract;
use App\Services\User\Entities\UserEnriched;
use Illuminate\Support\Carbon;

class UserSourceDtoFactory implements SourceDtoFactoryContract
{
    // TODO kpstya указать все типы значений в массиве $source

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
            emailVerifiedAt: ! empty($source['email_verified_at'])
                ? Carbon::parse($source['email_verified_at'])
                : null,
            createdAt: ! empty($source['created_at'])
                ? Carbon::parse($source['created_at'])
                : null,
            updatedAt: ! empty($source['updated_at'])
                ? Carbon::parse($source['updated_at'])
                : null
        );
    }
}
