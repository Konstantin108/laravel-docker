<?php

namespace Tests\Feature\v2;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\ElasticsearchClientStub;
use App\Exceptions\ElasticsearchApiException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\RequestException;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws \ReflectionException
     */
    public function test_index_v2_no_param(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function () {
            return new ElasticsearchClientStub;
        });

        $count = 3;
        User::factory()->count($count)->withContact()->create();

        $response = $this
            ->getJson(route('api.v2.user.index'))
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

    /**
     * @throws \ReflectionException
     */
    public function test_index_v2_page_param(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function () {
            return new ElasticsearchClientStub;
        });

        $count = 13;
        User::factory()->count($count)->withContact()->create();

        $page = 2;
        $perPage = 9;

        $response = $this
            ->getJson(route('api.v2.user.index', [
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

    /**
     * @throws \ReflectionException
     */
    public function test_index_v2_per_page_param(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function () {
            return new ElasticsearchClientStub;
        });

        User::factory()->count(3)->withContact()->create();
        $perPage = 1;

        $response = $this
            ->getJson(route('api.v2.user.index', [
                'per_page' => $perPage,
            ]))
            ->assertOk();

        $this->assertCount($perPage, $response->json('data'));
    }

    /**
     * @throws \ReflectionException
     */
    public function test_index_v2_failed(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function () {
            return new ElasticsearchClientErrorStub;
        });

        User::factory()->count(3)->withContact()->create();

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage('Index search error');

        $this
            ->withoutExceptionHandling()
            ->getJson(route('api.v2.user.index'))
            ->assertInternalServerError();

        $this->expectException(RequestException::class);
    }
}
