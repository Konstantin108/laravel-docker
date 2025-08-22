<?php

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientStub;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FillUsersSearchIndexTest extends TestCase
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

    public function test_fill_users_search_index(): void
    {
        $users = User::factory()->count(2)->create();

        $this
            ->artisan('search:fill-users-search-index-command')
            ->assertSuccessful()
            ->expectsOutputToContain(sprintf('"_id":"%d"', $users->first()->id));
    }
}
