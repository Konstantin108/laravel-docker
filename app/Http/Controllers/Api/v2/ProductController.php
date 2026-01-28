<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Requests\v2\Product\IndexRequest;
use App\Http\Resources\Product\IndexResource;
use App\Services\Elasticsearch\PaginationRequestMapper;
use App\Services\Elasticsearch\ProductIndexElasticsearchService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;

class ProductController extends Controller
{
    public function index(
        IndexRequest $request,
        ProductIndexElasticsearchService $searchService,
        PaginationRequestMapper $mapper,
    ): AnonymousResourceCollection {
        $data = $request->validated();

        $searchResult = $searchService->findInSearchIndex($mapper->map(
            Arr::get($data, 'search'),
            Arr::get($data, 'per_page'),
            Arr::get($data, 'page'),
        ));

        return IndexResource::collection($searchResult->hits);
    }
}
