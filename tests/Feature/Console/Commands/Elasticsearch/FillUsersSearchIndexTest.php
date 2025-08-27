<?php

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\ElasticsearchClientStub;
use App\Exceptions\ElasticsearchApiException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FillUsersSearchIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws \ReflectionException
     */
    public function test_fill_users_search_index(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function () {
            return new ElasticsearchClientStub;
        });

        $users = User::factory()->count(2)->withContact()->create();

        $this
            ->artisan('search:fill-users-search-index-command')
            ->assertSuccessful()
            ->expectsOutputToContain(sprintf('"_id":"%d"', $users->first()->id));
    }

    /**
     * @throws \ReflectionException
     */
    public function test_fill_users_search_index_when_users_table_is_empty(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function () {
            return new ElasticsearchClientStub;
        });

        $this
            ->artisan('search:fill-users-search-index-command')
            ->assertSuccessful()
            ->expectsOutputToContain('null');
    }

    /**
     * @throws \ReflectionException
     */
    public function test_fill_users_search_index_failed(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function () {
            return new ElasticsearchClientErrorStub;
        });

        User::factory()->count(2)->withContact()->create();

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage('Index filling error');

        $this->artisan('search:fill-users-search-index-command');
    }
}
