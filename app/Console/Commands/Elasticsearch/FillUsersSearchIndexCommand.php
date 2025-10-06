<?php

declare(strict_types=1);

namespace App\Console\Commands\Elasticsearch;

use App\Services\Elasticsearch\UsersIndexElasticsearchService;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;

class FillUsersSearchIndexCommand extends Command
{
    private const LIMIT = 1000;

    protected $signature = 'app:search:fill-users-search-index {limit:int?}';

    protected $description = 'Заполнить документами индекс users в Elasticsearch';

    public function handle(UsersIndexElasticsearchService $service, LoggerInterface $logger): int
    {
        $limit = $this->argument('limit:int') !== null
            ? (int) $this->argument('limit:int')
            : self::LIMIT;

        $result = $service->fillSearchIndex($limit);

        $result !== null
            ? $this->formattedOutput($result)
            : $this->info(json_encode($result));

        $logger->info(json_encode($result));

        return self::SUCCESS;
    }

    /**
     * @param  array<string, int|string>  $result
     */
    private function formattedOutput(array $result): void
    {
        /**
         * @var array<int, array<string, array<string, string|int|array<string, string|int>>>> $items
         */
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
        $this->info('index: users');
        $this->info(sprintf('took: %d', $result['took']));
        $this->info(sprintf('errors: %s', json_encode($result['errors'])));
        $this->info(sprintf('created: %d', $createdDocsCount));
        $this->info(sprintf('updated: %d', $updatedDocsCount));
        $this->info(sprintf('total: %d', count($items)));
    }
}
