<?php

declare(strict_types=1);

namespace App\Factories;

use App\Services\Elasticsearch\Entities\BulkIndexItem;
use App\Services\Elasticsearch\Entities\BulkIndexResult;
use Illuminate\Support\Collection;

class BulkIndexResultFactory
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createFromArray(array $data): BulkIndexResult
    {
        return new BulkIndexResult(
            took: $data['took'],
            errors: $data['errors'],
            items: new Collection($data['items'])
                ->map(static fn (array $item): BulkIndexItem => BulkIndexItem::fromArray($item['index']))
        );
    }
}
