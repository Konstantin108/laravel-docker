<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\ElasticsearchClientStub;
use App\Exceptions\ElasticsearchApiException;
use ReflectionException;
use Tests\TestCase;

class CreateUsersSearchIndexTest extends TestCase
{
    private const COMMAND = 'app:search:create-users-search-index';

    /**
     * @throws ReflectionException
     */
    public function test_create_users_search_index_success(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function () {
            return new ElasticsearchClientStub;
        });

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
        $this->app->bind(ElasticsearchClientContract::class, static function () {
            return new ElasticsearchClientErrorStub;
        });

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage('An error occurred while creating the index');

        $this->artisan(self::COMMAND);
    }
}
