<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use ReflectionException;
use Tests\TestCase;

class DeleteUsersSearchIndexTest extends TestCase
{
    private const COMMAND = 'app:search:delete-users-search-index';

    public function test_delete_users_search_index_success(): void
    {
        $this->artisan(self::COMMAND)
            ->assertSuccessful()
            ->expectsOutput(json_encode([
                'acknowledged' => true,
            ]));
    }

    /**
     * @throws ReflectionException
     */
    public function test_delete_users_search_index_failed(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function (): ElasticsearchClientContract {
            return new ElasticsearchClientErrorStub;
        });

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage('Index deleting error.');

        $this->artisan(self::COMMAND);
    }
}
