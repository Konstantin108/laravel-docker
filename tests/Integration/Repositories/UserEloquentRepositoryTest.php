<?php

namespace Tests\Integration\Repositories;

use App\Enums\SortedByEnum;
use App\Models\User;
use App\Repositories\User\UserEloquentRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class UserEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UserEloquentRepository $repository;

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(UserEloquentRepository::class);
    }

    #[Test]
    public function it_returns_paginated_users_when_per_page_param_is_given(): void
    {
        User::factory()->count(3)->hasContact()->create();
        $perPage = 2;

        $result = $this->repository->getPagination(perPage: $perPage);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertInstanceOf(User::class, $result->getCollection()->first());
        $this->assertCount($perPage, $result->items());

        foreach ($result->items() as $elem) {
            $this->assertTrue($elem->relationLoaded('contact'));
        }
    }

    #[Test]
    public function it_returns_all_users_with_contact_relation(): void
    {
        $count = 2;
        User::factory()->count($count)->hasContact()->create();

        $result = $this->repository->getList();

        $this->assertInstanceOf(User::class, $result->first());
        $this->assertCount($count, $result);

        foreach ($result as $elem) {
            $this->assertTrue($elem->relationLoaded('contact'));
        }
    }

    #[Test]
    public function it_returns_all_users_when_limit_param_is_given(): void
    {
        User::factory()->count(5)->hasContact()->create();
        $limit = 3;

        $result = $this->repository->getList($limit);

        $this->assertCount($limit, $result);
    }

    #[test]
    public function it_returns_paginated_users_sorted_by_id_asc(): void
    {
        User::factory()->count(5)->create();

        $ids = $this->repository->getPagination(sortedByEnum: SortedByEnum::ASC)
            ->getCollection()
            ->pluck('id')
            ->values()
            ->all();

        $expected = $ids;
        sort($expected);

        $this->assertSame($expected, $ids);
    }

    #[test]
    public function it_returns_paginated_users_sorted_by_id_desc(): void
    {
        User::factory()->count(5)->create();

        $ids = $this->repository->getPagination()
            ->getCollection()
            ->pluck('id')
            ->values()
            ->all();

        $expected = $ids;
        rsort($expected);

        $this->assertSame($expected, $ids);
    }
}
