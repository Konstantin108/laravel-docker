<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Models\User;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\User\Dto\FilterDto;
use App\Services\User\Entities\UserEnriched;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserService
{
    public function __construct(private readonly UserRepositoryContract $repository) {}

    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function getPagination(FilterDto $filterDto): LengthAwarePaginator
    {
        $paginator = $this->repository->getPagination(
            $filterDto->sortedBy,
            $filterDto->perPage,
            $filterDto->search
        );

        return $paginator->through(function (User $user): UserEnriched {
            return $this->enrich($user);
        });
    }

    /**
     * @return Collection<int, UserEnriched>
     */
    public function getList(?int $limit = null): Collection
    {
        return $this->repository->getList($limit)
            ->map(fn (User $user): UserEnriched => $this->enrich($user));
    }

    public function enrich(User $user): UserEnriched
    {
        return new UserEnriched(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            reserveEmail: $user->contact?->email,
            phone: $user->contact?->phone,
            telegram: $user->contact?->telegram,
            emailVerifiedAt: $user->email_verified_at,
            createdAt: $user->created_at,
            updatedAt: $user->updated_at,
        );
    }

    /**
     * @return list<string>
     */
    public function relations(): array
    {
        return ['contact'];
    }
}
