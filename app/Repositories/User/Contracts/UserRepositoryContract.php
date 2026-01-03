<?php

declare(strict_types=1);

namespace App\Repositories\User\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryContract
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function getUsersPagination(int $perPage, ?string $search = null): LengthAwarePaginator;

    // TODO kpstya возможно после переработки ElasticsearchService можно будет создать UserElasticsearchRepository

    /**
     * @return Collection<int, User>
     */
    public function getAllUsers(?int $limit = null): Collection;
}
