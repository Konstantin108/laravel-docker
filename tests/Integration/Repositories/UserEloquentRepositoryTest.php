<?php

namespace Tests\Integration\Repositories;

use App\Models\User;
use App\Repositories\User\UserEloquentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
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
        User::factory()->count(3)->contact()->create();
        $perPage = 2;

        $result = $this->repository->getUsersPagination($perPage);

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
        User::factory()->count($count)->contact()->create();

        $result = $this->repository->getAllUsers();

        $this->assertCount($count, $result);

        foreach ($result as $elem) {
            $this->assertTrue($elem->relationLoaded('contact'));
        }
    }

    #[Test]
    public function it_returns_all_users_when_limit_param_is_given(): void
    {
        User::factory()->count(5)->contact()->create();
        $limit = 3;

        $result = $this->repository->getAllUsers($limit);

        $this->assertCount($limit, $result);
    }
}
