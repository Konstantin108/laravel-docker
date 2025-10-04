<?php

declare(strict_types=1);

namespace App\Console\Commands\Elasticsearch;

use App\Services\Elasticsearch\UsersIndexElasticsearchService;
use Illuminate\Console\Command;

class FillUsersSearchIndexCommand extends Command
{
    private const LIMIT = 1000;

    protected $signature = 'app:search:fill-users-search-index {limit:int?}';

    protected $description = 'Заполнить документами индекс users в Elasticsearch';

    public function handle(UsersIndexElasticsearchService $service): int
    {
        $limit = $this->argument('limit:int') !== null
            ? (int) $this->argument('limit:int')
            : self::LIMIT;

        $result = $service->fillSearchIndex($limit);

        $result !== null
            ? $this->formattedOutput($result)
            : $this->info(json_encode($result));

        return self::SUCCESS;
    }

    /**
     * @param  array<string, int|string>  $result
     */
    private function formattedOutput(array $result): void
    {
        $columnNames = ['_id', '_seq_no', '_type', '_version', 'result', '_primary_term', 'status'];

        $rows = array_map(
            static function (array $item) use ($columnNames): array {
                return array_map(
                    static fn (string $columnName): int|string => $item['index'][$columnName],
                    $columnNames
                );
            },
            (array) $result['items']
        );

        $this->alert(strtoupper('raw json result'));
        $this->info(json_encode($result));
        $this->newLine();
        $this->alert(strtoupper('formatted result'));
        $this->info(sprintf('took: %d', $result['took']));
        $this->info(sprintf('errors: %s', json_encode($result['errors'])));
        $this->info(sprintf('total: %d', count((array) $result['items'])));
        $this->info('index: users');
        $this->table($columnNames, $rows);
    }
}
