<?php

namespace Tests\Feature\v2;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use ReflectionException;
use Tests\TestCase;

final class ProductTest extends TestCase
{
    use RefreshDatabase;

    private const INDEX_ROUTE = 'api.v2.products.index';

    public function test_it_returns_products_list_when_no_params_provided(): void
    {
        $count = 3;
        Product::factory()->count($count)->create();

        $response = $this->getJson(route(self::INDEX_ROUTE))
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'category_name',
                        'price',
                        'category_id',
                        'description',
                        'category_description',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertOk();

        $this->assertCount($count, $response->json('data'));
    }

    #[TestWith(['page', '2s'])]
    #[TestWith(['per_page', 's'])]
    public function test_it_returns_error_when_invalid_params_are_provided(string $param, string $value): void
    {
        Product::factory()->count(3)->create();

        $this->getJson(route(self::INDEX_ROUTE, [
            $param => $value,
        ]))
            ->assertJsonValidationErrors([$param])
            ->assertUnprocessable();
    }

    public function test_it_paginates_products_when_page_param_is_given(): void
    {
        $count = 13;
        Product::factory()->count($count)->create();

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

    public function test_it_limits_products_per_page_when_per_page_param_is_given(): void
    {
        Product::factory()->count(3)->create();
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
    public function test_it_throws_exception_with_stack_trace_when_elasticsearch_fails_in_development_environment(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function (): ElasticsearchClientContract {
            return new ElasticsearchClientErrorStub;
        });

        Product::factory()->count(3)->create();

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage('Index search error.');

        $this->withoutExceptionHandling()
            ->getJson(route(self::INDEX_ROUTE))
            ->assertInternalServerError();
    }

    /**
     * @throws ReflectionException
     */
    public function test_it_returns_json_error_when_elasticsearch_fails_in_production_environment(): void
    {
        config()->set('app.debug', false);

        $this->app->bind(ElasticsearchClientContract::class, static function (): ElasticsearchClientContract {
            return new ElasticsearchClientErrorStub;
        });

        Product::factory()->count(3)->create();

        $this->getJson(route(self::INDEX_ROUTE))
            ->assertJson(['message' => 'Server Error'])
            ->assertInternalServerError();
    }
}
