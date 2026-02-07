<?php

namespace App\Http\Resources\User;

use App\Services\User\Entities\UserEnriched;
use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property UserEnriched $resource
 */
#[SchemaName('User\IndexResource')]
class IndexResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            /** @example 1 */
            'id' => $this->resource->id,

            /** @example Ivan */
            'name' => $this->resource->name,

            /** @example ivan@mail.ru */
            'email' => $this->resource->email,

            /** @example ivan@gmail.com */
            'reserve_email' => $this->resource->reserveEmail,

            /** @example 79091234567 */
            'phone' => $this->resource->phone,

            /** @example '@ivan' */
            'telegram' => $this->resource->telegram,

            /**
             * @var string|null $email_verified_at
             *
             * @format Y-m-d
             *
             * @example 1970-01-01
             */
            'email_verified_at' => $this->resource->emailVerifiedAt?->format('Y-m-d'),

            /**
             * @var string|null $created_at
             *
             * @format Y-m-d
             *
             * @example 1970-01-01
             */
            'created_at' => $this->resource->createdAt?->format('Y-m-d'),

            /**
             * @var string|null $updated_at
             *
             * @format Y-m-d
             *
             * @example 1970-01-01
             */
            'updated_at' => $this->resource->updatedAt?->format('Y-m-d'),
        ];
    }
}
