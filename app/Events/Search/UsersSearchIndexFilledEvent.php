<?php

declare(strict_types=1);

namespace App\Events\Search;

use App\Entities\User\UserEnriched;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Collection;

final readonly class UsersSearchIndexFilledEvent
{
    use Dispatchable;

    public function __construct(
        /** @var Collection<int, UserEnriched> */
        public Collection $users,
        public string $indexName
    ) {}
}
