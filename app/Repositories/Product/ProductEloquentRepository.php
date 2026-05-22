<?php

declare(strict_types=1);

namespace App\Repositories\Product;

use App\Enums\SortedByEnum;
use App\Models\Product;
use App\Repositories\Product\Contracts\ProductRepositoryContract;
use App\Repositories\Product\Scopes\SearchScope;
use App\Repositories\Scopes\LimitScope;
use Illuminate\Database\Eloquent\Collection;

class ProductEloquentRepository implements ProductRepositoryContract
{
    /**
     * @return Collection<int, Product>
     */
    public function getList(
        SortedByEnum $sortedByEnum = SortedByEnum::DESC,
        ?string $search = null,
        ?int $limit = null
    ): Collection {
        return Product::query()
            ->with(['category'])
            ->tap(new SearchScope($search))
            ->tap(new LimitScope($limit))
            ->orderBy('id', $sortedByEnum->value)
            ->get();
    }
}
