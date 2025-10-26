<?php

namespace Tests\Feature\v2;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Exceptions\ElasticsearchApiException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\RequestException;
use ReflectionException;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private const INDEX_ROUTE = 'api.v2.user.index';

    public function test_index_v2_no_param(): void
    {
        $count = 3;
        User::factory()->count($count)->withContact()->create();

        $response = $this->getJson(route(self::INDEX_ROUTE))
            ->assertOk()
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
            ]);

        $this->assertCount($count, $response->json('data'));
    }

    public function test_index_v2_page_param(): void
    {
        $count = 13;
        User::factory()->count($count)->withContact()->create();

        $page = 2;
        $perPage = 9;

        $response = $this->getJson(route(self::INDEX_ROUTE, [
            'page' => $page,
            'per_page' => $perPage,
        ]))
            ->assertOk();

        $data = $response->json('data');

        foreach ($data as $elem) {
            $this->assertGreaterThan($count - $perPage, $elem['id']);
        }

        $this->assertCount($count - $perPage, $data);
    }

    public function test_index_v2_per_page_param(): void
    {
        User::factory()->count(3)->withContact()->create();
        $perPage = 1;

        $response = $this->getJson(route(self::INDEX_ROUTE, [
            'per_page' => $perPage,
        ]))
            ->assertOk();

        $this->assertCount($perPage, $response->json('data'));
    }

    /**
     * @throws ReflectionException
     */
    public function test_index_v2_failed(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function (): ElasticsearchClientContract {
            return new ElasticsearchClientErrorStub;
        });

        User::factory()->count(3)->withContact()->create();

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage('Index search error.');

        $this->withoutExceptionHandling()
            ->getJson(route(self::INDEX_ROUTE))
            ->assertInternalServerError();

        $this->expectException(RequestException::class);
    }
}
