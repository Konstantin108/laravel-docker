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

    private const INDEX_ROUTE = 'api.v1.user.index';

    public function test_user_index_v1_without_params()
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

    #[TestWith(['page', 'two'])]
    #[TestWith(['per_page', 'one'])]
    public function test_user_index_v1_with_params_failed(string $param, string $value): void
    {
        User::factory()->count(3)->withContact()->create();

        $this->getJson(route(self::INDEX_ROUTE, [
            $param => $value,
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$param]);
    }

    public function test_user_index_v1_with_page_param(): void
    {
        User::factory()->count(3)->withContact()->create();
        $page = 2;

        $this->getJson(route(self::INDEX_ROUTE, [
            'page' => $page,
        ]))
            ->assertOk()
            ->assertJsonPath('meta.current_page', $page);
    }

    public function test_user_index_v1_with_per_page_param(): void
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

    #[TestWith(['Иван', 1])]
    #[TestWith(['BK.RU', 2])]
    #[TestWith(['Василий', 0])]
    #[TestWith(['@iva', 1])]
    #[TestWith(['898', 1])]
    #[TestWith(['RESERve.', 3])]
    #[TestWith(['Ив', 3])]
    #[TestWith([null, 3])]
    public function test_user_index_v1_with_search_param(?string $search, int $resultCount): void
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
            ->withContact(new Sequence(...array_map(
                static fn (array $elem): array => $elem['contact'],
                $data
            )))
            ->create();

        $response = $this->getJson(route(self::INDEX_ROUTE, [
            'search' => $search,
        ]))
            ->assertOk()
            ->assertJsonPath('meta.total', $resultCount);

        $this->assertCount($resultCount, $response->json('data'));
    }
}
