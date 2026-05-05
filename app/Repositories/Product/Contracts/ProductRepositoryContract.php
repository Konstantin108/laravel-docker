<?php

declare(strict_types=1);

namespace App\Repositories\Product\Contracts;

use App\Enums\SortedByEnum;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryContract
{
    /**
     * @return Collection<int, Product>
     */
    public function getList(
        SortedByEnum $sortedByEnum = SortedByEnum::DESC,
        ?string $search = null,
        ?int $limit = null
    ): Collection;
}
