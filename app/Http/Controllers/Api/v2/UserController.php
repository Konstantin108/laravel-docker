<?php

namespace App\Http\Controllers\Api\v2;

use App\Dictionaries\RouteGroupDescriptionDictionary;
use App\Dictionaries\RouteGroupDictionary;
use App\Http\Requests\v2\User\IndexRequest;
use App\Http\Resources\User\IndexResource;
use App\Services\Elasticsearch\PaginationRequestMapper;
use App\Services\Elasticsearch\UsersIndexElasticsearchService;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    #[Group(
        name: RouteGroupDictionary::USERS,
        description: RouteGroupDescriptionDictionary::USERS
    )]
    #[Endpoint(title: 'Получить список пользователей с пагинацией [v2]')]
    public function index(
        IndexRequest $request,
        UsersIndexElasticsearchService $searchService,
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
