<?php

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use App\Models\Contracts\SearchableContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionException;
use Tests\Feature\Console\Commands\Elasticsearch\Abstract\SearchIndexCommandTest;

class ClearSearchIndexTest extends SearchIndexCommandTest
{
    use RefreshDatabase;

    private const COMMAND = 'app:elasticsearch:clear-index';

    #[DataProvider('indexNameProvider')]
    public function test_clear_search_index_success(string $indexName): void
    {
        /** @var SearchableContract $modelName */
        $modelName = config('elasticsearch.search_index_models.'.$indexName);

        $count = 2;
        $modelName::factory()->count($count)->create();

        $this->executeCommand(['index_name' => $indexName])
            ->assertSuccessful()
            ->expectsOutputToContain(sprintf('"deleted": %d', $count));
    }

    /**
     * @throws ReflectionException
     */
    #[DataProvider('indexNameProvider')]
    public function test_clear_search_index_failed(string $indexName): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function (): ElasticsearchClientContract {
            return new ElasticsearchClientErrorStub;
        });

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage('Index clearing error.');

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
