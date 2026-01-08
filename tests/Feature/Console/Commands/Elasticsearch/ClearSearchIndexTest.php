<?php

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionException;
use Tests\TestCase;

class ClearSearchIndexTest extends TestCase
{
    use RefreshDatabase;

    private const COMMAND = 'app:elasticsearch:clear-index';

    public function test_clear_users_search_index_success(): void
    {
        $count = 2;
        User::factory()->count($count)->create();

        $this->artisan(self::COMMAND)
            ->assertSuccessful()
            ->expectsOutputToContain(sprintf('"deleted": %d', $count));
    }

    /**
     * @throws ReflectionException
     */
    public function test_clear_users_search_index_failed(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function (): ElasticsearchClientContract {
            return new ElasticsearchClientErrorStub;
        });

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage('Index clearing error.');

        $this->artisan(self::COMMAND);
    }
}
