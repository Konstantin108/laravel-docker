<?php

namespace Tests\Integration\Repositories;

use App\Models\User;
use App\Repositories\User\UserEloquentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
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

    public function test_get_user_pagination_with_per_page_param(): void
    {
        User::factory()->count(3)->withContact()->create();
        $perPage = 2;

        $result = $this->repository->getUsersPagination($perPage);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount($perPage, $result->items());

        foreach ($result->items() as $elem) {
            $this->assertTrue($elem->relationLoaded('contact'));
        }
    }

    public function test_get_all_users(): void
    {
        $count = 2;
        User::factory()->count($count)->withContact()->create();

        $result = $this->repository->getAllUsers();

        $this->assertCount($count, $result);

        foreach ($result as $elem) {
            $this->assertTrue($elem->relationLoaded('contact'));
        }
    }

    public function test_get_all_users_with_limit_param(): void
    {
        User::factory()->count(5)->withContact()->create();
        $limit = 3;

        $result = $this->repository->getAllUsers($limit);

        $this->assertCount($limit, $result);
    }
}
