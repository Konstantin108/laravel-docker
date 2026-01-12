<?php

declare(strict_types=1);

namespace App\Console\Commands\Elasticsearch;

use App\Console\Commands\Elasticsearch\Concerns\PromptForSearchIndexTrait;
use App\Console\Commands\Elasticsearch\Entities\SearchIndexResolver;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use App\Services\Elasticsearch\Factories\ElasticsearchServiceFactory;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

final class DeleteSearchIndexCommand extends Command implements PromptsForMissingInput
{
    use PromptForSearchIndexTrait;

    protected $signature = 'app:elasticsearch:delete-index {index_name}';

    protected $description = 'Удалить индекс в Elasticsearch';

    /**
     * @throws SearchIndexException
     */
    public function handle(ElasticsearchServiceFactory $factory, SearchIndexResolver $resolver): int
    {
        $searchIndexEnum = $resolver->fromString($this->argument('index_name'));

        $result = $factory->make($searchIndexEnum)->deleteSearchIndex();
        $this->info(json_encode($result, JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }
}
