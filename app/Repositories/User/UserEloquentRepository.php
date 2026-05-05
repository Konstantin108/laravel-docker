<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Enums\SortedByEnum;
use App\Models\User;
use App\Repositories\Scopes\LimitScope;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Repositories\User\Scopes\SearchScope;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserEloquentRepository implements UserRepositoryContract
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function getPagination(
        SortedByEnum $sortedByEnum = SortedByEnum::DESC,
        ?int $perPage = null,
        ?string $search = null
    ): LengthAwarePaginator {
        return User::query()
            ->with(['contact'])
            ->tap(new SearchScope($search))
            ->orderBy('id', $sortedByEnum->value)
            ->paginate($perPage ?? 10);
    }

    /**
     * @return Collection<int, User>
     */
    public function getList(?int $limit = null): Collection
    {
        return User::query()
            ->with(['contact'])
            ->tap(new LimitScope($limit))
            ->get();
    }
}
