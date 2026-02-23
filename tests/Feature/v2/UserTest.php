<?php

namespace Tests\Feature\v2;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

final class UserTest extends TestCase
{
    use RefreshDatabase;

    private const INDEX_ROUTE = 'api.v2.users.index';

    #[Test]
    public function it_returns_users_list_when_no_params_provided(): void
    {
        $count = 3;
        User::factory()->count($count)->contact()->create();

        $response = $this->getJson(route(self::INDEX_ROUTE))
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
            ])
            ->assertOk();

        $this->assertCount($count, $response->json('data'));
    }

    #[Test]
    #[TestWith(data: ['page', '2s'])]
    #[TestWith(data: ['per_page', 's'])]
    public function it_returns_error_when_invalid_params_are_provided(string $param, string $value): void
    {
        User::factory()->count(3)->contact()->create();

        $this->getJson(route(self::INDEX_ROUTE, [
            $param => $value,
        ]))
            ->assertJsonValidationErrors([$param])
            ->assertUnprocessable();
    }

    #[Test]
    public function it_paginates_users_when_page_param_is_given(): void
    {
        $count = 13;
        User::factory()->count($count)->contact()->create();

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

    #[Test]
    public function it_limits_users_per_page_when_per_page_param_is_given(): void
    {
        User::factory()->count(3)->contact()->create();
        $perPage = 1;

        $response = $this->getJson(route(self::INDEX_ROUTE, [
            'per_page' => $perPage,
        ]))
            ->assertOk();

        $this->assertCount($perPage, $response->json('data'));
    }

    #[Test]
    public function it_throws_exception_with_stack_trace_when_elasticsearch_fails_in_development_environment(): void
    {
        User::factory()->count(3)->contact()->create();

        $exceptionMessage = 'Index search error.';

        $this->mock(
            ElasticsearchClientContract::class,
            static function (MockInterface $elasticsearchClient) use ($exceptionMessage): void {
                $elasticsearchClient->shouldReceive('search')
                    ->once()
                    ->andThrow(new ElasticsearchApiException($exceptionMessage));
            });

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage('Index search error.');

        $this->withoutExceptionHandling()
            ->getJson(route(self::INDEX_ROUTE))
            ->assertInternalServerError();
    }

    // TODO kpstya mock с вызовщт ошибки надо вынести

    #[Test]
    public function it_returns_json_error_when_elasticsearch_fails_in_production_environment(): void
    {
        config()->set('app.debug', false);

        User::factory()->count(3)->contact()->create();
        $exceptionMessage = 'Index search error.';

        $this->mock(
            ElasticsearchClientContract::class,
            static function (MockInterface $elasticsearchClient) use ($exceptionMessage): void {
                $elasticsearchClient->shouldReceive('search')
                    ->once()
                    ->andThrow(new ElasticsearchApiException($exceptionMessage));
            });

        $this->getJson(route(self::INDEX_ROUTE))
            ->assertJson(['message' => 'Server Error'])
            ->assertHeader('Content-Type', 'application/json')
            ->assertInternalServerError();
    }
}
