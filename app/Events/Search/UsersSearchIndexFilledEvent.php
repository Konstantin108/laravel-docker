<?php

declare(strict_types=1);

namespace App\Events\Search;

use App\Dto\User\UserEnrichedDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Collection;

class UsersSearchIndexFilledEvent
{
    use Dispatchable;

    public function __construct(
        /** @var Collection<int, UserEnrichedDto> */
        public readonly Collection $users,
        public readonly string $indexName
    ) {}
}
