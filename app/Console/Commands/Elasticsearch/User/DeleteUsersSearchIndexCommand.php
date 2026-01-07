<?php

declare(strict_types=1);

namespace App\Console\Commands\Elasticsearch\User;

// TODO kpstya разобраться с поиском в elasticsearch, там нужно подкидывать класс расширяющий абстракцию

use App\Services\Elasticsearch\UsersIndexElasticsearchService;
use Illuminate\Console\Command;

final class DeleteUsersSearchIndexCommand extends Command
{
    protected $signature = 'app:search:delete-users-search-index';

    protected $description = 'Удалить индекс users в Elasticsearch';

    public function handle(UsersIndexElasticsearchService $service): int
    {
        $result = $service->deleteSearchIndex();
        $this->info(json_encode($result, JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }
}
