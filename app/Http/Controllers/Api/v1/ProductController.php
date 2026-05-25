<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\RouteGroupEnum;
use App\Enums\SortedByEnum;
use App\Http\Requests\v1\Product\IndexRequest;
use App\Http\Resources\Product\IndexResource;
use App\Services\Product\Dto\FilterDto;
use App\Services\Product\ProductService;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    #[Group(
        name: RouteGroupEnum::PRODUCT->value,
        description: RouteGroupEnum::DESCRIPTIONS[RouteGroupEnum::PRODUCT->value]
    )]
    #[Endpoint(title: 'Получить список продуктов')]
    public function index(IndexRequest $request, ProductService $productService): AnonymousResourceCollection
    {
        return IndexResource::collection(
            $productService->getList(new FilterDto(
                sortedBy: SortedByEnum::from($request->validated('sorted_by', 'desc')),
                search: $request->validated('search'),
                limit: $request->validated('limit'),
            ))
        );
    }
}
