<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Factories;

use App\Services\Elasticsearch\Entities\BulkIndexItem;
use App\Services\Elasticsearch\Entities\BulkIndexResult;
use Illuminate\Support\Collection;

class BulkIndexResultFactory
{
    /* TODO kpstya - у меня нигде нет проверок на то, что приходит от Elasticsearch,
        вдруг данные окажутся некорректными, это надо исправить, так же надо наверное обновить под это тесты */

    /**
     * @param  array<string, mixed>  $data
     */
    public function make(array $data): BulkIndexResult
    {
        return new BulkIndexResult(
            took: $data['took'],
            errors: $data['errors'],
            items: (new Collection($data['items']))
                ->map(static fn (array $item): BulkIndexItem => BulkIndexItem::fromArray($item['index']))
        );
    }
}
