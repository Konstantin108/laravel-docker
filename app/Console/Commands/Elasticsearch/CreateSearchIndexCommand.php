<?php

declare(strict_types=1);

namespace App\Console\Commands\Elasticsearch;

use App\Console\Commands\Elasticsearch\Concerns\PromptForSearchIndexTrait;
use App\Factories\ElasticsearchServiceFactory;
use App\Services\Elasticsearch\Enums\SearchIndexEnum;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

// TODO kpstya нужны тесты для этой команды

final class CreateSearchIndexCommand extends Command implements PromptsForMissingInput
{
    use PromptForSearchIndexTrait;

    protected $signature = 'app:elasticsearch:create-index {index_name}';

    protected $description = 'Создать индекс в Elasticsearch';

    // TODO kpstya нужно добавить такую же проверку в остальных командах

    /**
     * @throws SearchIndexException
     */
    public function handle(ElasticsearchServiceFactory $factory): int
    {
        $indexName = $this->argument('index_name');

        $searchIndexEnum = SearchIndexEnum::tryFrom($indexName);
        if ($searchIndexEnum === null) {
            throw SearchIndexException::doesNotExist($indexName);
        }

        $result = $factory->make($searchIndexEnum->value)->createSearchIndex();
        $this->info(json_encode($result, JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }
}
