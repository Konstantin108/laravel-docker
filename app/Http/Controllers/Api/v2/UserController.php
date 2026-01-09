<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Requests\v2\User\IndexRequest;
use App\Http\Resources\User\IndexResource;
use App\Services\Elasticsearch\UsersIndexElasticsearchService;
use App\Services\User\Dto\IndexDto;
use App\Services\User\UserService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function index(
        IndexRequest $request,
        UsersIndexElasticsearchService $searchService,
        UserService $userService,
    ): AnonymousResourceCollection {
        $paginationRequestDto = $userService->getPaginationDataForSearchIndex(
            IndexDto::from($request->validated())
        );

        $searchResult = $searchService->findInSearchIndex($paginationRequestDto);

        return IndexResource::collection($searchResult->hits);
    }
}
