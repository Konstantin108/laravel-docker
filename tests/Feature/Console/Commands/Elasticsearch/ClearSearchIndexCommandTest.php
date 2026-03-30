<?php

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use App\Services\Elasticsearch\Enums\SearchIndexEnum;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCases\SearchIndexCommandTestCase;

final class ClearSearchIndexCommandTest extends SearchIndexCommandTestCase
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
            ->expectsOutputToContain('clearing is successful')
            ->assertSuccessful();
    }

    /**
     * @throws SearchIndexException
     */
    #[Test]
    #[DataProvider(methodName: 'indexNameProvider')]
    public function it_prints_pretty_json_in_verbose_mode_when_clearing_search_index(string $indexName): void
    {
        $model = SearchIndexEnum::from($indexName)->getModel();

        $count = 2;
        $model::factory()->count($count)->create();

        $this->executeCommand([
            'index_name' => $indexName,
            '-v' => true,
        ])
            ->expectsOutputToContain(sprintf('"deleted": %d', $count))
            ->assertSuccessful();
    }

    #[Test]
    #[DataProvider(methodName: 'indexNameProvider')]
    public function it_returns_error_when_clearing_search_index_fails(string $indexName): void
    {
        $exceptionMessage = 'Index clearing error.';

        $this->callMethodWithException('clearIndex', $exceptionMessage);

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage($exceptionMessage);

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
