<?php

namespace Tests\Feature\v2;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\ElasticsearchClientStub;
use App\Exceptions\ElasticsearchApiException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws \ReflectionException
     */
    public function test_index(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function () {
            return new ElasticsearchClientStub;
        });

        $count = 3;
        User::factory()->count($count)->withContact()->create();

        $response = $this
            ->get('api/v2/user/')
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
    public function test_index_failed(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function () {
            return new ElasticsearchClientErrorStub;
        });

        User::factory()->count(3)->withContact()->create();

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage('Index search error');

        $this
            ->withoutExceptionHandling()
            ->get('api/v2/user/')
            ->assertInternalServerError();
    }
}
