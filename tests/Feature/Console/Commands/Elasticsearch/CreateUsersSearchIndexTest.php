<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use ReflectionException;
use Tests\TestCase;

class CreateUsersSearchIndexTest extends TestCase
{
    private const COMMAND = 'app:search:create-users-search-index';

    public function test_create_users_search_index_success(): void
    {
        $this->artisan(self::COMMAND)
            ->assertSuccessful()
            ->expectsOutput(json_encode([
                'acknowledged' => true,
                'shards_acknowledged' => true,
                'index' => 'users',
            ]));
    }

    /**
     * @throws ReflectionException
     */
    public function test_create_users_search_index_failed(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function (): ElasticsearchClientContract {
            return new ElasticsearchClientErrorStub;
        });

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage('An error occurred while creating the index.');

        $this->artisan(self::COMMAND);
    }
}
