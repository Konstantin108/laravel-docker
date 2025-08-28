<?php

namespace App\Http\Controllers\Api\v2;

use App\Dto\User\IndexDto;
use App\Exceptions\SearchIndexDoesNotExist;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\IndexRequest;
use App\Http\Resources\User\IndexResource;
use App\Services\Elasticsearch\SearchResponseService;
use App\Services\Elasticsearch\UsersIndexElasticsearchService;
use App\Services\UserService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(
        private readonly UsersIndexElasticsearchService $searchService,
        private readonly UserService $userService,
        private readonly SearchResponseService $searchResponseService
    ) {}

    /**
     * @throws SearchIndexDoesNotExist
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $paginationRequestDto = $this
            ->userService
            ->getPaginationDataForSearchIndex(
                IndexDto::from($request->validated())
            );

        return IndexResource::collection(
            $this->searchResponseService->execute(
                $this->searchService->findInSearchIndex($paginationRequestDto)
            )->hits
        );
    }
}
