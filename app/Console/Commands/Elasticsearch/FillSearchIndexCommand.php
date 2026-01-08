<?php

declare(strict_types=1);

namespace App\Console\Commands\Elasticsearch;

use App\Console\Commands\Elasticsearch\Concerns\PromptForSearchIndexTrait;
use App\Console\Commands\Elasticsearch\Entities\SearchIndexResolver;
use App\Factories\ElasticsearchServiceFactory;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\text;

// TODO kpstya нужны тесты для этой команды

final class FillSearchIndexCommand extends Command implements PromptsForMissingInput
{
    use PromptForSearchIndexTrait;

    private const LIMIT = 1000;

    protected $signature = 'app:elasticsearch:fill-index {index_name} {--limit=}';

    protected $description = 'Заполнить документами индекс в Elasticsearch';

    /**
     * @throws SearchIndexException
     */
    public function handle(
        ElasticsearchServiceFactory $factory,
        SearchIndexResolver $resolver,
        LoggerInterface $logger
    ): int {
        $searchIndexEnum = $resolver->fromString($this->argument('index_name'));

        $limit = $this->option('limit') !== null
            ? (int) $this->option('limit')
            : self::LIMIT;

        $result = $factory->make($searchIndexEnum->value)->fillSearchIndex($limit);

        $result !== null
            ? $this->formattedOutput($result, $searchIndexEnum->value)
            : $this->info(json_encode($result, JSON_PRETTY_PRINT));

        $logger->info(json_encode($result, JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }

    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output): void
    {
        $input->setOption('limit', text('Указать лимит отправялемых записей?'));
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
