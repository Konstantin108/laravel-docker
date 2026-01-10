<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Models\User;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\Dto\IndexDto;
use App\Services\User\Entities\UserEnriched;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserService
{
    private const DEFAULT_PER_PAGE = 10;

    public function __construct(
        private readonly UserRepositoryContract $userRepository
    ) {}

    /**
     * @return LengthAwarePaginator<int, UserEnriched>
     */
    public function getPagination(IndexDto $indexDto): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<User> $paginator */
        $paginator = $this->userRepository->getUsersPagination(
            $indexDto->perPage ?? self::DEFAULT_PER_PAGE,
            $indexDto->search
        );

        $userEnrichedCollection = $paginator->getCollection()
            ->map(fn (User $user): UserEnriched => $this->enrich($user));

        return $paginator->setCollection($userEnrichedCollection);
    }

    /**
     * @return Collection<int, UserEnriched>
     */
    public function getUsers(?int $count = null): Collection
    {
        return $this->userRepository->getAllUsers($count)
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
}
