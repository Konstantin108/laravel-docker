<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ElasticsearchService;
use App\Ship\Exceptions\ElasticsearchApiException;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;

class FillUsersSearchIndexCommand extends Command
{
    private const int LIMIT = 1000;

    protected $signature = 'search:fill-users-search-index-command {limit?}';

    protected $description = 'Заполнить документами индекс users в Elasticsearch';

    /**
     * @throws ElasticsearchApiException
     * @throws ConnectionException
     */
    public function handle(ElasticsearchService $service): int
    {
        $limit = $this->argument('limit') !== null
            ? (int) $this->argument('limit')
            : self::LIMIT;

        $service->fillUsersSearchIndex($limit);

        return self::SUCCESS;
    }
}
