<?php

namespace App\Http\Controllers\Api\v2;

use App\Actions\SearchResponseAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\IndexRequest;
use App\Http\Resources\User\IndexResource;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use App\Services\Elasticsearch\UsersIndexElasticsearchService;
use App\Services\User\Dto\IndexDto;
use App\Services\User\UserService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(
        private readonly UsersIndexElasticsearchService $searchService,
        private readonly UserService $userService,
    ) {}

    /**
     * @throws SearchIndexException
     */
    public function index(IndexRequest $request, SearchResponseAction $action): AnonymousResourceCollection
    {
        $paginationRequestDto = $this->userService->getPaginationDataForSearchIndex(
            IndexDto::from($request->validated())
        );

        return IndexResource::collection(
            $action->run(
                $this->searchService->findInSearchIndex($paginationRequestDto)
            )->hits
        );
    }
}
