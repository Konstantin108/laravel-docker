<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Enums;

enum BulkStatusEnum: int
{
    case CREATED = 201;

    case UPDATED = 200;

    public function isCreated(): bool
    {
        return $this === self::CREATED;
    }

    public function isUpdated(): bool
    {
        return $this === self::UPDATED;
    }
}
