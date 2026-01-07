<?php

declare(strict_types=1);

namespace App\Console\Commands\Elasticsearch\User;

use App\Services\Elasticsearch\UsersIndexElasticsearchService;
use Illuminate\Console\Command;

/* TODO kpstya
    команды и весь код должен быть универсальным для всех индексов,
    так же надо будет переработать тесты */

final class ClearUsersSearchIndexCommand extends Command
{
    protected $signature = 'app:search:clear-users-search-index';

    protected $description = 'Очистить индекс users в Elasticsearch';

    public function handle(UsersIndexElasticsearchService $service): int
    {
        $result = $service->clearSearchIndex();
        $this->info(json_encode($result, JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }
}
