<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Scopes\User\SearchScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function getUsersPagination(int $perPage, ?string $search = null): LengthAwarePaginator
    {
        return User::query()
            ->with('contact')
            ->tap(new SearchScope($search))
            ->paginate($perPage);
    }

    /**
     * @return Collection<int, User>
     */
    public function getAllUsers(?int $limit = null): Collection
    {
        return User::query()
            ->with('contact')
            ->when($limit, fn (Builder $builder, int $limit) => $builder->limit($limit))
            ->get();
    }
}
