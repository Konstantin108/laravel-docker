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
        $this->info(json_encode($result));

        return self::SUCCESS;
    }
}
