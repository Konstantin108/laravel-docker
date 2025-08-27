<?php

declare(strict_types=1);

namespace App\Console\Commands\Elasticsearch;

use App\Services\Elasticsearch\UsersIndexElasticsearchService;
use Illuminate\Console\Command;

class CreateUsersSearchIndexCommand extends Command
{
    protected $signature = 'search:create-users-search-index-command';

    protected $description = 'Создать индекс users в Elasticsearch';

    public function handle(UsersIndexElasticsearchService $service): int
    {
        $result = $service->createSearchIndex();
        $this->info(json_encode($result));

        return self::SUCCESS;
    }
}
