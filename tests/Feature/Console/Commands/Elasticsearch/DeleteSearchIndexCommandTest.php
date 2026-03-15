<?php

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCases\SearchIndexCommandTestCase;

final class DeleteSearchIndexCommandTest extends SearchIndexCommandTestCase
{
    private const COMMAND = 'app:elasticsearch:delete-index';

    #[Test]
    #[DataProvider(methodName: 'indexNameProvider')]
    public function it_successfully_deletes_search_index(string $indexName): void
    {
        $this->executeCommand(['index_name' => $indexName])
            ->expectsOutputToContain('deleting is successful')
            ->assertSuccessful();
    }

    #[Test]
    #[DataProvider(methodName: 'indexNameProvider')]
    public function it_prints_pretty_json_in_verbose_mode_when_deleting_search_index(string $indexName): void
    {
        $this->executeCommand([
            'index_name' => $indexName,
            '-v' => true,
        ])
            ->expectsOutputToContain('"acknowledged": true')
            ->assertSuccessful();
    }

    #[Test]
    #[DataProvider(methodName: 'indexNameProvider')]
    public function it_returns_error_when_deleting_search_index_fails(string $indexName): void
    {
        $exceptionMessage = 'Index deleting error.';

        $this->callMethodWithException('deleteIndex', $exceptionMessage);

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
