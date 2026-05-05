<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Enums\SortedByEnum;
use App\Models\User;
use App\Repositories\Scopes\LimitScope;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Repositories\User\Scopes\SearchScope;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserEloquentRepository implements UserRepositoryContract
{
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
