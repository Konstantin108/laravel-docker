<?php

namespace App\Http\Resources\Product;

use App\Services\Product\Entities\ProductEnriched;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexResource extends JsonResource
{
    /** @var ProductEnriched */
    public $resource;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'category_name' => $this->resource->categoryName,
            'price' => $this->resource->price,
            'category_id' => $this->resource->categoryId,
            'description' => $this->resource->description,
            'category_description' => $this->resource->categoryDescription,
            'created_at' => $this->resource->createdAt?->format('Y-m-d'),
            'updated_at' => $this->resource->updatedAt?->format('Y-m-d'),
        ];
    }
}
