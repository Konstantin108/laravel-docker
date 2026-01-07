<?php

declare(strict_types=1);

namespace App\Console\Commands\Elasticsearch;

use App\Console\Commands\Elasticsearch\Concerns\PromptForSearchIndexTrait;
use App\Factories\ElasticsearchServiceFactory;
use App\Services\Elasticsearch\Enums\SearchIndexEnum;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Psr\Log\LoggerInterface;

// TODO kpstya нужны тесты для этой команды

final class FillSearchIndexCommand extends Command implements PromptsForMissingInput
{
    use PromptForSearchIndexTrait;

    private const LIMIT = 1000;

    protected $signature = 'app:elasticsearch:fill-index {index_name}';

    protected $description = 'Заполнить документами индекс в Elasticsearch';

    /**
     * @throws SearchIndexException
     */
    public function handle(ElasticsearchServiceFactory $factory, LoggerInterface $logger): int
    {
        $searchIndexEnum = SearchIndexEnum::from($this->argument('index_name'));

        // TODO kpstya передавать в команду limit

        // TODO kpstya к fn надо добавить static

        $result = $factory->make($searchIndexEnum->value)->fillSearchIndex(self::LIMIT);

        $result !== null
            ? $this->formattedOutput($result, $searchIndexEnum->value)
            : $this->info(json_encode($result, JSON_PRETTY_PRINT));

        $logger->info(json_encode($result, JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }

    /**
     * @param  array<string, int|string>  $result
     */
    private function formattedOutput(array $result, string $indexName): void
    {
        /** @var array<int, array<string, array<string, string|int|array<string, string|int>>>> $items */
        $items = $result['items'];
        $columnNames = ['_id', '_seq_no', '_type', '_version', 'result', '_primary_term', 'status'];

        $rows = array_map(static function (array $item) use ($columnNames): array {
            return array_map(
                static fn (string $columnName): int|string => $item['index'][$columnName],
                $columnNames
            );
        }, $items);

        $createdDocsCount = $updatedDocsCount = 0;
        foreach ($items as $item) {
            if ($item['index']['status'] === 201) {
                $createdDocsCount++;
            }
            if ($item['index']['status'] === 200) {
                $updatedDocsCount++;
            }
        }

        $this->table($columnNames, $rows);
        $this->info(sprintf('index: %s', $indexName));
        $this->info(sprintf('took: %d', $result['took']));
        $this->info(sprintf('errors: %s', json_encode($result['errors'])));
        $this->info(sprintf('created: %d', $createdDocsCount));
        $this->info(sprintf('updated: %d', $updatedDocsCount));
        $this->info(sprintf('total: %d', count($items)));
    }
}
