<?php

namespace Tests\Feature\Console\Commands\Elasticsearch\Abstract;

use App\Services\Elasticsearch\Enums\SearchIndexEnum;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use Illuminate\Testing\PendingCommand;
use Tests\TestCase;

abstract class SearchIndexCommandTest extends TestCase
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

    /**
     * @param  string[]  $arguments
     */
    protected function executeCommand(array $arguments = []): PendingCommand
    {
        return $this->artisan($this->command(), $arguments);
    }
}
