<?php

// TODO kpstya moonshine как делать документацию и как тестировать

namespace App\Http\Controllers\Api\v2;

use App\Enums\RouteGroupEnum;
use App\Http\Requests\v2\User\IndexRequest;
use App\Http\Resources\User\UserResource;
use App\Services\Elasticsearch\PaginationRequestMapper;
use App\Services\Elasticsearch\Repositories\Contracts\ElasticsearchRepositoryContract;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    public function __construct(private readonly ElasticsearchRepositoryContract $repository) {}

    #[Group(
        name: RouteGroupEnum::USER->value,
        description: RouteGroupEnum::DESCRIPTIONS[RouteGroupEnum::USER->value]
    )]
    #[Endpoint(title: 'api.v2.users.index')]
    public function index(IndexRequest $request, PaginationRequestMapper $mapper): AnonymousResourceCollection
    {
        $inputData = $request->validated();

        $searchResult = $this->repository->findInSearchIndex($mapper->map(
            Arr::get($inputData, 'search'),
            Arr::get($inputData, 'per_page'),
            Arr::get($inputData, 'sorted_by'),
            Arr::get($inputData, 'page'),
        ));

        return UserResource::collection($searchResult->hits);
    }
}
