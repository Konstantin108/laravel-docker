<?php

declare(strict_types=1);

namespace App\Console\Commands\Elasticsearch;

use App\Console\Commands\Elasticsearch\Concerns\PromptForSearchIndexTrait;
use App\Console\Commands\Elasticsearch\Entities\SearchIndexResolver;
use App\Services\Elasticsearch\Entities\BulkIndexItem;
use App\Services\Elasticsearch\Entities\BulkIndexResult;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use App\Services\Elasticsearch\Factories\ElasticsearchServiceFactory;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\text;

final class FillSearchIndexCommand extends Command implements PromptsForMissingInput
{
    use PromptForSearchIndexTrait;

    private const LIMIT = 1000;

    protected $signature = 'app:elasticsearch:fill-index {index_name} {--limit=}';

    protected $description = 'Заполнить документами индекс в Elasticsearch';

    /**
     * @throws SearchIndexException
     */
    public function handle(
        ElasticsearchServiceFactory $factory,
        SearchIndexResolver $resolver,
        LoggerInterface $logger
    ): int {
        $searchIndexEnum = $resolver->fromString($this->argument('index_name'));
        $limit = (int) $this->option('limit') ?: self::LIMIT;

        $result = $factory->make($searchIndexEnum)->fillSearchIndex($limit);

        $result !== null
            ? $this->formattedOutput($result)
            : $this->info(json_encode($result, JSON_PRETTY_PRINT));

        if (config('elasticsearch.fill_index_log')) {
            $logger->info(json_encode($result, JSON_PRETTY_PRINT));
        }

        return self::SUCCESS;
    }

    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output): void
    {
        $input->setOption('limit', text('Указать лимит отправялемых записей?'));
    }

    private function formattedOutput(BulkIndexResult $result): void
    {
        $item = $result->items->first();

        $columnNames = array_keys($item->toArray());
        $rows = $result->items->map(static fn (BulkIndexItem $item): array => [
            $item->id,
            $item->seqNumber,
            $item->index,
            $item->version,
            $item->result,
            $item->primaryTerm,
            $item->status->value,
            $item->type,
        ]);

        $createdDocsCount = $result->items
            ->where(static fn (BulkIndexItem $item): bool => $item->status->isCreated())
            ->count();

        $updatedDocsCount = $result->items
            ->where(static fn (BulkIndexItem $item): bool => $item->status->isUpdated())
            ->count();

        $this->table($columnNames, $rows);
        $this->info(sprintf('index: %s', $item->index));
        $this->info(sprintf('took: %d', $result->took));
        $this->info(sprintf('errors: %s', json_encode($result->errors)));
        $this->info(sprintf('created: %d', $createdDocsCount));
        $this->info(sprintf('updated: %d', $updatedDocsCount));
        $this->info(sprintf('total: %d', $result->items->count()));
    }
}
