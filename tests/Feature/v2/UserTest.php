<?php

namespace Tests\Feature\v2;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use ReflectionException;
use Tests\TestCase;

final class UserTest extends TestCase
{
    use RefreshDatabase;

    private const INDEX_ROUTE = 'api.v2.user.index';

    public function test_user_index_v2_without_params(): void
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

    #[TestWith(['page', '2s'])]
    #[TestWith(['per_page', 's'])]
    public function test_user_index_v2_with_params_failed(string $param, string $value): void
    {
        User::factory()->count(3)->withContact()->create();

        $this->getJson(route(self::INDEX_ROUTE, [
            $param => $value,
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$param]);
    }

    public function test_user_index_v2_with_page_param(): void
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

    public function test_user_index_v2_with_per_page_param(): void
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
    public function test_user_index_v2_elasticsearch_failed_in_development_environment(): void
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
    }

    /**
     * @throws ReflectionException
     */
    public function test_user_index_v2_elasticsearch_failed_in_production_environment(): void
    {
        config()->set('app.debug', false);

        $this->app->bind(ElasticsearchClientContract::class, static function (): ElasticsearchClientContract {
            return new ElasticsearchClientErrorStub;
        });

        User::factory()->count(3)->withContact()->create();

        $this->getJson(route(self::INDEX_ROUTE))
            ->assertInternalServerError()
            ->assertJson(['message' => 'Server Error']);
    }
}
