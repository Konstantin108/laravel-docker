<?php

declare(strict_types=1);

namespace App\EntityFactories\Elasticsearch;

use App\Dto\User\UserEnrichedDto;
use App\Entities\Elasticsearch\UserDocElement;

class UserDocElementFactory
{
    public function make(UserEnrichedDto $user): UserDocElement
    {
        return new UserDocElement(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            emailVerifiedAt: $user->emailVerifiedAt,
            reserveEmail: $user->reserveEmail,
            phone: $user->phone,
            telegram: $user->telegram,
            createdAt: $user->createdAt,
            updatedAt: $user->updatedAt,
        );
    }
}
