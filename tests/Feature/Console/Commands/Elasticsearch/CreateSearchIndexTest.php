<?php

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionException;
use Tests\Feature\Console\Commands\Elasticsearch\Abstract\SearchIndexCommandTest;

class CreateSearchIndexTest extends SearchIndexCommandTest
{
    private const COMMAND = 'app:elasticsearch:create-index';

    // TODO kpstya подумать как поступить с кейсами в SearchIndexEnum и c классами сервисов в config/elasticsearch.php

    #[DataProvider('indexNameProvider')]
    public function test_create_search_index_success(string $indexName): void
    {
        $this->executeCommand(['index_name' => $indexName])
            ->assertSuccessful()
            ->expectsOutputToContain(sprintf('"index": "%s"', $indexName));
    }

    /**
     * @throws ReflectionException
     */
    #[DataProvider('indexNameProvider')]
    public function test_create_search_index_failed(string $indexName): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function (): ElasticsearchClientContract {
            return new ElasticsearchClientErrorStub;
        });

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage('An error occurred while creating the index.');

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
