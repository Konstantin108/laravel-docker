<?php

declare(strict_types=1);

namespace App\Console\Commands\Elasticsearch;

use App\Services\Elasticsearch\UsersIndexElasticsearchService;
use Illuminate\Console\Command;

class DeleteUsersSearchIndexCommand extends Command
{
    protected $signature = 'app:search:delete-users-search-index';

    protected $description = 'Удалить индекс users в Elasticsearch';

    public function handle(UsersIndexElasticsearchService $service): int
    {
        $result = $service->deleteSearchIndex();
        $this->info(json_encode($result));

        return self::SUCCESS;
    }
}
