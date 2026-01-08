<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use App\Services\Elasticsearch\Enums\SearchIndexEnum;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use Illuminate\Testing\PendingCommand;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionException;
use Tests\TestCase;

class CreateSearchIndexTest extends TestCase
{
    private const COMMAND = 'app:elasticsearch:create-index';

    // TODO kpstya подумать как поступить с Enum и конфигами различных индексов

    #[DataProvider('indexNameProvider')]
    public function test_create_search_index_success(string $indexName): void
    {
        $this->executeCommand(['index_name' => $indexName])
            ->assertSuccessful()
            ->expectsOutput(json_encode([
                'acknowledged' => true,
                'shards_acknowledged' => true,
                'index' => $indexName,
            ], JSON_PRETTY_PRINT));
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
        $indexName = 'usdrs';

        $this->expectException(SearchIndexException::class);
        $this->expectExceptionMessage(sprintf(
            'The mapping does not contain a search index with name [%s].',
            $indexName
        ));
        $this->expectExceptionCode(1);

        $this->executeCommand(['index_name' => $indexName]);
    }

    #[DataProvider('indexNameProvider')]
    public function test_expects_questions(string $indexName): void
    {
        $this->executeCommand()
            ->expectsChoice(
                'Имя индекса в Elasticsearch',
                $indexName,
                array_column(SearchIndexEnum::cases(), 'value')
            )
            ->assertSuccessful();
    }

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

    /**
     * @param  string[]  $arguments
     */
    private function executeCommand(array $arguments = []): PendingCommand
    {
        return $this->artisan(self::COMMAND, $arguments);
    }
}
