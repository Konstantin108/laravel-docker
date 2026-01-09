<?php

namespace App\Http\Resources\User;

use App\Services\User\Entities\UserEnriched;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexResource extends JsonResource
{
    /** @var UserEnriched */
    public $resource;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'reserve_email' => $this->resource->reserveEmail,
            'phone' => $this->resource->phone,
            'telegram' => $this->resource->telegram,
            'email_verified_at' => $this->resource->emailVerifiedAt?->format('Y-m-d'),
            'created_at' => $this->resource->createdAt?->format('Y-m-d'),
            'updated_at' => $this->resource->updatedAt?->format('Y-m-d'),
        ];
    }
}
