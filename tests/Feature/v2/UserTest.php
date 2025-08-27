<?php

namespace Tests\Feature\v2;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientStub;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->bind(ElasticsearchClientContract::class, static function () {
            return new ElasticsearchClientStub;
        });
    }

    public function test_index()
    {
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
}
