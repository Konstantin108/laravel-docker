<?php

declare(strict_types=1);

namespace App\Repositories\User\Contracts;

use App\Enums\SortedByEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryContract
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function getPagination(
        SortedByEnum $sortedByEnum = SortedByEnum::DESC,
        ?int $perPage = null,
        ?string $search = null
    ): LengthAwarePaginator;

    /**
     * @return Collection<int, User>
     */
    public function getList(?int $limit = null): Collection;
}
