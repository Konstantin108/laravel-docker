<?php

declare(strict_types=1);

namespace App\Console\Commands\Elasticsearch;

use App\Services\UsersIndexElasticsearchService;
use Illuminate\Console\Command;

class FillUsersSearchIndexCommand extends Command
{
    private const LIMIT = 1000;

    protected $signature = 'search:fill-users-search-index-command {limit?}';

    protected $description = 'Заполнить документами индекс users в Elasticsearch';

    public function handle(UsersIndexElasticsearchService $service): int
    {
        $limit = $this->argument('limit') !== null
            ? (int) $this->argument('limit')
            : self::LIMIT;

        $result = $service->fillSearchIndex($limit);
        $this->info(json_encode($result));

        return self::SUCCESS;
    }
}
