<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\Elasticsearch\PaginationRequestDto;
use App\Dto\User\IndexDto;
use App\Dto\User\UserEnrichedDto;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\Elasticsearch\PaginationService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserService
{
    private const PER_PAGE = 10;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PaginationService $elasticsearchPaginationService
    ) {}

    /**
     * @return LengthAwarePaginator<int, UserEnrichedDto>
     */
    public function getPagination(IndexDto $indexDto): LengthAwarePaginator
    {
        $paginator = $this->userRepository->getUsersPagination(
            $indexDto->perPage ?? self::PER_PAGE,
            $indexDto->search
        );

        /** @var LengthAwarePaginator<User> $paginator */
        $userEnrichedCollection = $paginator
            ->getCollection()
            ->map(fn (User $user): UserEnrichedDto => $this->enrich($user));

        return $paginator->setCollection($userEnrichedCollection);
    }

    /**
     * @return Collection<int, UserEnrichedDto>
     */
    public function getUsers(?int $count = null): Collection
    {
        return $this->userRepository
            ->getAllUsers($count)
            ->map(fn (User $user): UserEnrichedDto => $this->enrich($user));
    }

    public function getPaginationDataForSearchIndex(IndexDto $indexDto): PaginationRequestDto
    {
        return $this
            ->elasticsearchPaginationService
            ->getPaginationRequestData($indexDto->toArray(), self::PER_PAGE);
    }

    public function enrich(User $user): UserEnrichedDto
    {
        return new UserEnrichedDto(
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
