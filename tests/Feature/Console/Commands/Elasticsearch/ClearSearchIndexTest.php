<?php

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use App\Services\Elasticsearch\Enums\SearchIndexEnum;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use ReflectionException;
use Tests\Feature\Console\Commands\Elasticsearch\Abstract\SearchIndexCommandTest;

final class ClearSearchIndexTest extends SearchIndexCommandTest
{
    use RefreshDatabase;

    private const COMMAND = 'app:elasticsearch:clear-index';

    /**
     * @throws SearchIndexException
     */
    #[Test]
    #[DataProvider(methodName: 'indexNameProvider')]
    public function it_successfully_clears_search_index(string $indexName): void
    {
        $model = SearchIndexEnum::from($indexName)->getModel();

        $count = 2;
        $model::factory()->count($count)->create();

        $this->executeCommand(['index_name' => $indexName])
            ->expectsOutputToContain(sprintf('"deleted": %d', $count))
            ->assertSuccessful();
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    #[DataProvider(methodName: 'indexNameProvider')]
    public function it_returns_error_when_clearing_search_index_fails(string $indexName): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function (): ElasticsearchClientContract {
            return new ElasticsearchClientErrorStub;
        });

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage('Index clearing error.');

        $this->executeCommand(['index_name' => $indexName]);
    }

    #[Test]
    public function it_returns_error_when_invalid_search_index_name_is_given(): void
    {
        $this->exceptInvalidSearchIndexName('usdrs');
    }

    #[Test]
    #[DataProvider(methodName: 'indexNameProvider')]
    public function it_returns_questions_for_given_index(string $indexName): void
    {
        $this->expectsPrompts($indexName)->assertSuccessful();
    }

    protected function command(): string
    {
        return self::COMMAND;
    }
}
