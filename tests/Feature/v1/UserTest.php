<?php

namespace Tests\Feature\v1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_index()
    {
        $count = 3;
        User::factory()->count($count)->withContact()->create();

        $response = $this
            ->get('api/v1/user/')
            ->assertOk()
            ->assertJsonPath('meta.total', $count)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'reserve_email',
                        'phone',
                        'telegram',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'next',
                    'prev',
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'links' => [
                        '*' => [
                            'active',
                            'label',
                            'url',
                        ],
                    ],
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);

        $this->assertCount($count, $response->json('data'));
    }

    public function test_index_page_param(): void
    {
        User::factory()->count(3)->withContact()->create();
        $page = 2;

        $this
            ->get('api/v1/user?page='.$page)
            ->assertOk()
            ->assertJsonPath('meta.current_page', $page);
    }

    public function test_index_per_page_param(): void
    {
        User::factory()->count(3)->withContact()->create();
        $perPage = 1;

        $response = $this
            ->get('api/v1/user?per_page='.$perPage)
            ->assertOk()
            ->assertJsonPath('meta.per_page', $perPage);

        $this->assertCount($perPage, $response->json('data'));
    }

    public function test_index_search_param(): void
    {
        User::factory()->withContact()->create(['email' => 'user.first@mail.ru']);
        User::factory()->withContact()->create(['name' => 'find abc']);
        User::factory()->withContact()->create(['email' => 'thirdfind@mail.ru']);

        $search = 'find';
        $count = 2;

        $response = $this
            ->get('api/v1/user?search='.$search)
            ->assertOk()
            ->assertJsonPath('meta.total', $count);

        $this->assertCount($count, $response->json('data'));
    }
}
