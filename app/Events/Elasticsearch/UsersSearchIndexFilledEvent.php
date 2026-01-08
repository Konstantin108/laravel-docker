<?php

declare(strict_types=1);

namespace App\Events\Elasticsearch;

use App\Entities\User\UserEnriched;
use Illuminate\Support\Collection;

final readonly class UsersSearchIndexFilledEvent
{
    public function __construct(
        /** @var Collection<int, UserEnriched> */
        public Collection $users,
        public string $indexName
    ) {}
}
