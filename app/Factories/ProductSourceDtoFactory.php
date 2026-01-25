<?php

declare(strict_types=1);

namespace App\Factories;

use App\Factories\Contracts\SourceDtoFactoryContract;
use App\Services\Contracts\SearchableSourceContract;
use App\Services\Product\Entities\ProductEnriched;
use Illuminate\Support\Carbon;

class ProductSourceDtoFactory implements SourceDtoFactoryContract
{
    /**
     * @param array{
     *     id: int,
     *     name: string,
     *     category_name: string|null,
     *     price: int,
     *     category_id: int,
     *     description: string|null,
     *     category_description: string|null,
     *     created_at: string,
     *     updated_at: string,
     * } $source
     */
    public function createFromArray(array $source): SearchableSourceContract
    {
        return new ProductEnriched(
            id: $source['id'],
            name: $source['name'],
            categoryName: $source['category_name'],
            price: $source['price'],
            categoryId: $source['category_id'],
            description: $source['description'],
            categoryDescription: $source['category_description'],
            createdAt: ! empty($source['created_at'])
                ? Carbon::parse($source['created_at'])
                : null,
            updatedAt: ! empty($source['updated_at'])
                ? Carbon::parse($source['updated_at'])
                : null
        );
    }
}
