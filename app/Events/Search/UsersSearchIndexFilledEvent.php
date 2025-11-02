<?php

declare(strict_types=1);

namespace App\Events\Search;

use App\Entities\User\UserEnriched;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Collection;

final class UsersSearchIndexFilledEvent
{
    use Dispatchable;

    public function __construct(
        /** @var Collection<int, UserEnriched> */
        public readonly Collection $users,
        public readonly string $indexName
    ) {}
}
