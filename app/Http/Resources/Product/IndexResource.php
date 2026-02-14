<?php

// TODO kpstya возможно настроить swagger

namespace App\Http\Resources\Product;

use App\Services\Product\Entities\ProductEnriched;
use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ProductEnriched $resource
 */
#[SchemaName(name: 'Product\IndexResource')]
class IndexResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            /** @example 1 */
            'id' => $this->resource->id,

            /** @example 57" Monitor Samsung Odyssey Neo G9 G95NC S57CG952NI */
            'name' => $this->resource->name,

            /** @example Monitors */
            'category_name' => $this->resource->categoryName,

            /** @example 9000000 */
            'price' => $this->resource->price,

            /** @example 1 */
            'category_id' => $this->resource->categoryId,

            /** @example Great monitor */
            'description' => $this->resource->description,

            /** @example Visual information output devices */
            'category_description' => $this->resource->categoryDescription,

            /**
             * @var string|null $created_at
             *
             * @format Y-m-d
             *
             * @example 1970-01-01
             */
            'created_at' => $this->resource->createdAt?->format('Y-m-d'),

            /**
             * @var string|null $updated_at
             *
             * @format Y-m-d
             *
             * @example 1970-01-01
             */
            'updated_at' => $this->resource->updatedAt?->format('Y-m-d'),
        ];
    }
}
