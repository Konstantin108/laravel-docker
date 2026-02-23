<?php

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Console\Commands\Elasticsearch\Abstract\SearchIndexCommandTest;

final class CreateSearchIndexTest extends SearchIndexCommandTest
{
    private const COMMAND = 'app:elasticsearch:create-index';

    #[Test]
    #[DataProvider(methodName: 'indexNameProvider')]
    public function it_successfully_creates_search_index(string $indexName): void
    {
        $this->executeCommand(['index_name' => $indexName])
            ->expectsOutputToContain(sprintf('"index": "%s"', $indexName))
            ->assertSuccessful();
    }

    #[Test]
    #[DataProvider(methodName: 'indexNameProvider')]
    public function it_returns_error_when_creating_search_index_fails(string $indexName): void
    {
        $exceptionMessage = 'An error occurred while creating the index.';

        $this->mock(
            ElasticsearchClientContract::class,
            static function (MockInterface $client) use ($exceptionMessage): void {
                $client->shouldReceive('createIndex')
                    ->once()
                    ->andThrow(new ElasticsearchApiException($exceptionMessage));
            });

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
