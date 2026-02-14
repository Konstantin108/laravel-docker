<?php

namespace Tests\Feature\v1;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

final class UserTest extends TestCase
{
    use RefreshDatabase;

    private const INDEX_ROUTE = 'api.v1.users.index';

    public function test_it_returns_users_list_when_no_params_provided()
    {
        $count = 3;
        User::factory()->count($count)->contact()->create();

        $response = $this->getJson(route(self::INDEX_ROUTE))
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
            ])
            ->assertHeader('Content-Type', 'application/json')
            ->assertOk();

        $this->assertCount($count, $response->json('data'));
        $this->assertIsInt($response->json('meta.total'));
    }

    #[TestWith(data: ['page', 'two'])]
    #[TestWith(data: ['per_page', 'one'])]
    public function test_it_returns_error_when_invalid_params_are_provided(string $param, string $value): void
    {
        User::factory()->count(3)->contact()->create();

        $this->getJson(route(self::INDEX_ROUTE, [
            $param => $value,
        ]))
            ->assertJsonValidationErrors([$param])
            ->assertUnprocessable();
    }

    public function test_it_paginates_users_when_page_param_is_given(): void
    {
        User::factory()->count(3)->contact()->create();
        $page = 2;

        $this->getJson(route(self::INDEX_ROUTE, [
            'page' => $page,
        ]))
            ->assertJsonPath('meta.current_page', $page)
            ->assertOk();
    }

    public function test_it_limits_users_per_page_when_per_page_param_is_given(): void
    {
        User::factory()->count(3)->contact()->create();
        $perPage = 1;

        $response = $this->getJson(route(self::INDEX_ROUTE, [
            'per_page' => $perPage,
        ]))
            ->assertJsonPath('meta.per_page', $perPage)
            ->assertOk();

        $this->assertCount($perPage, $response->json('data'));
    }

    #[TestWith(data: ['Иван', 1])]
    #[TestWith(data: ['BK.RU', 2])]
    #[TestWith(data: ['Василий', 0])]
    #[TestWith(data: ['@iva', 1])]
    #[TestWith(data: ['898', 1])]
    #[TestWith(data: ['RESERve.', 3])]
    #[TestWith(data: ['Ив', 3])]
    #[TestWith(data: [null, 3])]
    public function test_it_filters_users_by_search_param(?string $search, int $resultCount): void
    {
        $data = [
            [
                'user' => ['name' => 'Иван', 'email' => 'ivan@bk.ru'],
                'contact' => ['email' => 'ivan@reserve.ru', 'phone' => '79094545533', 'telegram' => '@ivan'],
            ],
            [
                'user' => ['name' => 'Сергей', 'email' => 'sergey@gmail.com'],
                'contact' => ['email' => 'sergey@reserve.com', 'phone' => '8-800-23', 'telegram' => '@ss17'],
            ],
            [
                'user' => ['name' => 'Кирилл', 'email' => 'kirill@bk.ru'],
                'contact' => ['email' => 'kirill@reserve.ru', 'phone' => '7(907)898-22', 'telegram' => '@krevetko'],
            ],
        ];

        User::factory()
            ->count(count($data))
            ->state(new Sequence(...array_map(
                static fn (array $elem): array => $elem['user'],
                $data
            )))
            ->contact(new Sequence(...array_map(
                static fn (array $elem): array => $elem['contact'],
                $data
            )))
            ->create();

        $response = $this->getJson(route(self::INDEX_ROUTE, [
            'search' => $search,
        ]))
            ->assertJsonPath('meta.total', $resultCount)
            ->assertOk();

        $this->assertCount($resultCount, $response->json('data'));
    }
}
