<?php

declare(strict_types=1);

namespace App\Dto\Elasticsearch;

use Spatie\LaravelData\Data;

// TODO kpstya возможно сделать слой для создания Dto? что-то типо DtoFactories

final class SearchIndexShardsDto extends Data
{
    public function __construct(
        public int $total,
        public int $successful,
        public int $skipped,
        public int $failed,
    ) {}
}
