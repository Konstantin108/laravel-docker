<?php

namespace Tests\Integration\Repositories;

use App\Models\User;
use App\Repositories\User\UserEloquentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class UserEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UserEloquentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new UserEloquentRepository;
    }

    #[Test]
    public function it_returns_paginated_users_when_per_page_param_is_given(): void
    {
        /* TODO kpstya
            - надо добавить тесты на сортировку для v1\User и v1\Product
            - возможно добавить сортировку для v2\User и v2\Product, тогда добавить и тесты на это */

        User::factory()->count(3)->hasContact()->create();
        $perPage = 2;

        $result = $this->repository->getPagination(perPage: $perPage);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
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
}
