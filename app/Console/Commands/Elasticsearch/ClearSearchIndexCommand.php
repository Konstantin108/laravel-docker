<?php

declare(strict_types=1);

namespace App\Console\Commands\Elasticsearch;

use App\Console\Commands\Elasticsearch\Concerns\PromptForSearchIndexTrait;
use App\Console\Commands\Elasticsearch\Entities\SearchIndexResolver;
use App\Factories\ElasticsearchServiceFactory;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

final class ClearSearchIndexCommand extends Command implements PromptsForMissingInput
{
    use PromptForSearchIndexTrait;

    protected $signature = 'app:elasticsearch:clear-index {index_name}';

    protected $description = 'Очистить индекс в Elasticsearch';

    /**
     * @throws SearchIndexException
     */
    public function handle(ElasticsearchServiceFactory $factory, SearchIndexResolver $resolver): int
    {
        $searchIndexEnum = $resolver->fromString($this->argument('index_name'));

        $result = $factory->make($searchIndexEnum)->clearSearchIndex();
        $this->info(json_encode($result, JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }
}
