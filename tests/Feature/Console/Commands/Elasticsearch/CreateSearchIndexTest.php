<?php

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionException;
use Tests\Feature\Console\Commands\Elasticsearch\Abstract\SearchIndexCommandTest;

final class CreateSearchIndexTest extends SearchIndexCommandTest
{
    private const COMMAND = 'app:elasticsearch:create-index';

    #[DataProvider('indexNameProvider')]
    public function test_it_successfully_creates_search_index(string $indexName): void
    {
        $this->executeCommand(['index_name' => $indexName])
            ->expectsOutputToContain(sprintf('"index": "%s"', $indexName))
            ->assertSuccessful();
    }

    /**
     * @throws ReflectionException
     */
    #[DataProvider('indexNameProvider')]
    public function test_it_returns_error_when_creating_search_index_fails(string $indexName): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function (): ElasticsearchClientContract {
            return new ElasticsearchClientErrorStub;
        });

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage('An error occurred while creating the index.');

        $this->executeCommand(['index_name' => $indexName]);
    }

    public function test_it_returns_error_when_invalid_search_index_name_is_given(): void
    {
        $this->exceptInvalidSearchIndexName('usdrs');
    }

    #[DataProvider('indexNameProvider')]
    public function test_it_returns_questions_for_given_index(string $indexName): void
    {
        $this->expectsPrompts($indexName)->assertSuccessful();
    }

    protected function command(): string
    {
        return self::COMMAND;
    }
}
