<?php

namespace App\Http\Controllers\Api\v2;

use App\Enums\RouteGroupEnum;
use App\Http\Requests\v2\Product\IndexRequest;
use App\Http\Resources\Product\IndexResource;
use App\Services\Elasticsearch\Contracts\ElasticsearchServiceContract;
use App\Services\Elasticsearch\PaginationRequestMapper;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;

class ProductController extends Controller
{
    public function __construct(private readonly ElasticsearchServiceContract $searchService) {}

    #[Group(
        name: RouteGroupEnum::PRODUCT->value,
        description: RouteGroupEnum::DESCRIPTIONS[RouteGroupEnum::PRODUCT->value]
    )]
    #[Endpoint(title: 'api.v2.products.index')]
    public function index(IndexRequest $request, PaginationRequestMapper $mapper): AnonymousResourceCollection
    {
        $data = $request->validated();

        $searchResult = $this->searchService->findInSearchIndex($mapper->map(
            Arr::get($data, 'search'),
            Arr::get($data, 'per_page'),
            Arr::get($data, 'sorted_by'),
            Arr::get($data, 'page'),
        ));

        return IndexResource::collection($searchResult->hits);
    }
}
