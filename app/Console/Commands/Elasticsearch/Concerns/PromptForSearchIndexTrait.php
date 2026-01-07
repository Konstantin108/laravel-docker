<?php

declare(strict_types=1);

namespace App\Console\Commands\Elasticsearch\Concerns;

use App\Services\Elasticsearch\Enums\SearchIndexEnum;

use function Laravel\Prompts\select;

trait PromptForSearchIndexTrait
{
    /**
     * @return array<string, callable>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'index_name' => static fn (): string => select(
                'Имя индекса в Elasticsearch',
                array_column(SearchIndexEnum::cases(), 'value')
            ),
        ];
    }
}
