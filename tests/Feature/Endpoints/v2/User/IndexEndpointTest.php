<?php

namespace Tests\Feature\Endpoints\v2\User;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

final class IndexEndpointTest extends TestCase
{
    use RefreshDatabase;

    private const ROUTE = 'api.v2.users.index';

    #[Test]
    public function it_returns_users_list_when_no_params_provided(): void
    {
        $count = 3;
        User::factory()->count($count)->hasContact()->create();

        $response = $this->getJson(route(self::ROUTE))
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
        User::factory()->count(3)->hasContact()->create();

        $this->getJson(route(self::ROUTE, [
            $param => $value,
        ]))
            ->assertInvalid([$param])
            ->assertUnprocessable();
    }

    #[Test]
    public function it_paginates_users_when_page_param_is_given(): void
    {
        $count = 13;
        User::factory()->count($count)->hasContact()->create();

        $page = 2;
        $perPage = 9;

        $response = $this->getJson(route(self::ROUTE, [
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
        User::factory()->count(3)->hasContact()->create();
        $perPage = 1;

        $response = $this->getJson(route(self::ROUTE, [
            'per_page' => $perPage,
        ]))
            ->assertOk();

        $this->assertCount($perPage, $response->json('data'));
    }

    #[Test]
    public function it_throws_exception_with_stack_trace_when_elasticsearch_fails_in_development_environment(): void
    {
        User::factory()->count(3)->hasContact()->create();

        $exceptionMessage = 'Index search error.';

        $this->mock(
            ElasticsearchClientContract::class,
            static function (MockInterface $client) use ($exceptionMessage): void {
                $client->shouldReceive('search')
                    ->once()
                    ->andThrow(new ElasticsearchApiException($exceptionMessage));
            });

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->withoutExceptionHandling()
            ->getJson(route(self::ROUTE))
            ->assertInternalServerError();
    }

    #[Test]
    public function it_returns_json_error_when_elasticsearch_fails_in_production_environment(): void
    {
        // TODO kpstya надо заменить на работу с массивом config(['app.debug' => false]);
        config()->set('app.debug', false);

        User::factory()->count(3)->hasContact()->create();
        $exceptionMessage = 'Index search error.';

        $this->mock(
            ElasticsearchClientContract::class,
            static function (MockInterface $client) use ($exceptionMessage): void {
                $client->shouldReceive('search')
                    ->once()
                    ->andThrow(new ElasticsearchApiException($exceptionMessage));
            });

        $this->getJson(route(self::ROUTE))
            ->assertJson(['message' => 'Server Error'])
            ->assertHeader('Content-Type', 'application/json')
            ->assertInternalServerError();
    }
}
