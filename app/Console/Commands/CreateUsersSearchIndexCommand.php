<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ElasticsearchService;
use App\Ship\Exceptions\ElasticsearchApiException;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;

class CreateUsersSearchIndexCommand extends Command
{
    protected $signature = 'search:create-users-search-index-command';

    protected $description = 'Создать индекс users в Elasticsearch';

    /**
     * @throws ElasticsearchApiException
     * @throws ConnectionException
     */
    public function handle(ElasticsearchService $service): int
    {
        $service->createUsersSearchIndex();

        return self::SUCCESS;
    }
}
