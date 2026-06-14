<?php

declare(strict_types=1);

namespace App\Services\Product;

use App\Models\Product;
use App\Repositories\Product\Contracts\ProductRepositoryContract;
use App\Services\Product\Dto\FilterDto;
use App\Services\Product\Entities\ProductEnriched;
use Illuminate\Support\Collection;

class ProductService
{
    public function __construct(private readonly ProductRepositoryContract $repository) {}

    /**
     * @return Collection<int, ProductEnriched>
     */
    public function getList(FilterDto $filterDto): Collection
    {
        return $this->repository->getList(
            $filterDto->sortedBy,
            $filterDto->search,
            $filterDto->limit,
        )
            ->map(fn (Product $product): ProductEnriched => $this->enrich($product));
    }

    public function enrich(Product $product): ProductEnriched
    {
        return new ProductEnriched(
            id: $product->id,
            name: $product->name,
            categoryName: $product->category->name,
            price: $product->price,
            categoryId: $product->category_id,
            description: $product->description,
            categoryDescription: $product->category->description,
            createdAt: $product->created_at,
            updatedAt: $product->updated_at,
        );
    }

    /**
     * @return list<string>
     */
    public function relations(): array
    {
        return ['category'];
    }
}
