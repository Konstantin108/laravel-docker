<?php

namespace App\Http\Controllers\Api\v2;

use App\Actions\Elasticsearch\SearchResponseTransformAction;
use App\Http\Requests\v2\User\IndexRequest;
use App\Http\Resources\User\IndexResource;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use App\Services\Elasticsearch\UsersIndexElasticsearchService;
use App\Services\User\Dto\IndexDto;
use App\Services\User\UserService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    /**
     * @throws SearchIndexException
     */
    public function index(
        IndexRequest $request,
        SearchResponseTransformAction $action,
        UsersIndexElasticsearchService $searchService,
        UserService $userService,
    ): AnonymousResourceCollection {
        $paginationRequestDto = $userService->getPaginationDataForSearchIndex(
            IndexDto::from($request->validated())
        );

        $result = $searchService->findInSearchIndex($paginationRequestDto);
        $searchResponse = $action->handle($result);

        return IndexResource::collection($searchResponse->hits);
    }
}
