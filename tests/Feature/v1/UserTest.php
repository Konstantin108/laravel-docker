<?php

namespace Tests\Feature\v1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private const INDEX_ROUTE = 'api.v1.user.index';

    public function test_index_v1_no_param()
    {
        $count = 3;
        User::factory()->count($count)->withContact()->create();

        $response = $this->getJson(route(self::INDEX_ROUTE))
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
        $this->assertIsInt($response->json('meta.total'));
    }

    public function test_index_v1_page_param(): void
    {
        User::factory()->count(3)->withContact()->create();
        $page = 2;

        $this->getJson(route(self::INDEX_ROUTE, [
            'page' => $page,
        ]))
            ->assertOk()
            ->assertJsonPath('meta.current_page', $page);
    }

    public function test_index_v1_per_page_param(): void
    {
        User::factory()->count(3)->withContact()->create();
        $perPage = 1;

        $response = $this->getJson(route(self::INDEX_ROUTE, [
            'per_page' => $perPage,
        ]))
            ->assertOk()
            ->assertJsonPath('meta.per_page', $perPage);

        $this->assertCount($perPage, $response->json('data'));
    }

    public function test_index_v1_search_param(): void
    {
        User::factory()->withContact()->create(['email' => 'user.first@mail.ru']);
        User::factory()->withContact()->create(['name' => 'find abc']);
        User::factory()->withContact()->create(['email' => 'thirdfind@mail.ru']);

        $search = 'find';
        $count = 2;

        $response = $this->getJson(route(self::INDEX_ROUTE, [
            'search' => $search,
        ]))
            ->assertOk()
            ->assertJsonPath('meta.total', $count);

        $this->assertCount($count, $response->json('data'));
    }
}
