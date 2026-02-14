<?php

namespace App\Http\Controllers\Api\v1;

use App\Dictionaries\RouteGroupDescriptionDictionary;
use App\Dictionaries\RouteGroupDictionary;
use App\Http\Requests\v1\Product\IndexRequest;
use App\Http\Resources\Product\IndexResource;
use App\Services\Product\Dto\IndexDto;
use App\Services\Product\ProductService;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    #[Group(
        name: RouteGroupDictionary::PRODUCTS,
        description: RouteGroupDescriptionDictionary::PRODUCTS
    )]
    #[Endpoint(title: 'Получить список продуктов')]
    public function index(IndexRequest $request, ProductService $productService): AnonymousResourceCollection
    {
        $data = $request->validated();

        return IndexResource::collection(
            $productService->getProducts(IndexDto::from($data))
        );
    }
}
