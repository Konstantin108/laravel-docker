<?php

declare(strict_types=1);

namespace App\Factories;

use App\Entities\User\Contracts\SearchableSourceContract;
use App\Entities\User\UserEnriched;
use App\Factories\Contracts\SourceDtoFactoryContract;
use Illuminate\Support\Carbon;

class UserSourceDtoFactory implements SourceDtoFactoryContract
{
    /* TODO kpstya
        можно сильно упростить, больше не подставлять сервисы из конфига, можно просто использовать
        сущности с контрактом SearchableSourceContract. При необходимости метод from() можно переопределить */

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
