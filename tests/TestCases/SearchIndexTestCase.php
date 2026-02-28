<?php

namespace Tests\TestCases;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use App\Services\Elasticsearch\Enums\SearchIndexEnum;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use Illuminate\Testing\PendingCommand;
use Mockery\MockInterface;
use Tests\TestCase;

abstract class SearchIndexTestCase extends TestCase
{
    abstract protected function command(): string;

    /**
     * @return array<int, array<int, string>>
     */
    public static function indexNameProvider(): array
    {
        return array_map(
            static fn (SearchIndexEnum $case): array => [$case->value],
            SearchIndexEnum::cases()
        );
    }

    protected function exceptInvalidSearchIndexName(string $indexName): void
    {
        $this->expectException(SearchIndexException::class);
        $this->expectExceptionMessage(sprintf(
            'The mapping does not contain a search index with name [%s].',
            $indexName
        ));
        $this->expectExceptionCode(1);

        $this->executeCommand(['index_name' => $indexName]);
    }

    protected function expectsPrompts(string $indexName): PendingCommand
    {
        return $this->executeCommand()
            ->expectsChoice(
                'Имя индекса в Elasticsearch',
                $indexName,
                array_column(SearchIndexEnum::cases(), 'value')
            );
    }

    protected function callMethodWithException(string $methodName, string $exceptionMessage): void
    {
        $this->mock(
            ElasticsearchClientContract::class,
            static function (MockInterface $client) use ($methodName, $exceptionMessage): void {
                $client->shouldReceive($methodName)
                    ->once()
                    ->andThrow(new ElasticsearchApiException($exceptionMessage));
            });
    }

    /**
     * @param  string[]  $arguments
     */
    protected function executeCommand(array $arguments = []): PendingCommand
    {
        return $this->artisan($this->command(), $arguments);
    }
}
