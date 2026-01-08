<?php

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionException;
use Tests\Feature\Console\Commands\Elasticsearch\Abstract\SearchIndexCommandTest;

class DeleteSearchIndexTest extends SearchIndexCommandTest
{
    private const COMMAND = 'app:elasticsearch:delete-index';

    #[DataProvider('indexNameProvider')]
    public function test_delete_search_index_success(string $indexName): void
    {
        $this->executeCommand(['index_name' => $indexName])
            ->assertSuccessful()
            ->expectsOutputToContain('"acknowledged": true');
    }

    /**
     * @throws ReflectionException
     */
    #[DataProvider('indexNameProvider')]
    public function test_delete_search_index_failed(string $indexName): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function (): ElasticsearchClientContract {
            return new ElasticsearchClientErrorStub;
        });

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage('Index deleting error.');

        $this->executeCommand(['index_name' => $indexName]);
    }

    public function test_invalid_search_index_name(): void
    {
        $this->exceptInvalidSearchIndexName('usdrs');
    }

    #[DataProvider('indexNameProvider')]
    public function test_expects_questions(string $indexName): void
    {
        $this->expectsPrompts($indexName)->assertSuccessful();
    }

    protected function command(): string
    {
        return self::COMMAND;
    }
}
