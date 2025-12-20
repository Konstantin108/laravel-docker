<?php

declare(strict_types=1);

namespace App\Console\Commands\Elasticsearch\User;

use App\Services\Elasticsearch\UsersIndexElasticsearchService;
use Illuminate\Console\Command;

final class CreateUsersSearchIndexCommand extends Command
{
    protected $signature = 'app:search:create-users-search-index';

    protected $description = 'Создать индекс users в Elasticsearch';

    public function handle(UsersIndexElasticsearchService $service): int
    {
        $result = $service->createSearchIndex();
        $this->info(json_encode($result));

        return self::SUCCESS;
    }
}
