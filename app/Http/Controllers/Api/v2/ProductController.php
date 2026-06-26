<?php

namespace App\Http\Controllers\Api\v2;

use App\Enums\RouteGroupEnum;
use App\Http\Requests\v2\Product\IndexRequest;
use App\Http\Resources\Product\ProductResource;
use App\Services\Elasticsearch\PaginationRequestMapper;
use App\Services\Elasticsearch\Repositories\Contracts\ElasticsearchRepositoryContract;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;

final class ProductController extends Controller
{
    public function __construct(private readonly ElasticsearchRepositoryContract $repository) {}

    #[Group(
        name: RouteGroupEnum::PRODUCT->value,
        description: RouteGroupEnum::DESCRIPTIONS[RouteGroupEnum::PRODUCT->value]
    )]
    #[Endpoint(title: 'api.v2.products.index')]
    public function index(IndexRequest $request, PaginationRequestMapper $mapper): AnonymousResourceCollection
    {
        $inputData = $request->validated();

        $searchResult = $this->repository->findInSearchIndex($mapper->map(
            Arr::get($inputData, 'search'),
            Arr::get($inputData, 'per_page'),
            Arr::get($inputData, 'sorted_by'),
            Arr::get($inputData, 'page'),
        ));

        return ProductResource::collection($searchResult->hits);
    }
}
