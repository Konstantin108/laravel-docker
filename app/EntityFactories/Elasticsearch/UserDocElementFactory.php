<?php

declare(strict_types=1);

namespace App\EntityFactories\Elasticsearch;

use App\Entities\Elasticsearch\UserDocElement;
use App\Entities\User\UserEnriched;

class UserDocElementFactory
{
    public function make(UserEnriched $user): UserDocElement
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
