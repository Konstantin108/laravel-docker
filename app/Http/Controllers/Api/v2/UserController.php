<?php

namespace App\Http\Controllers\Api\v2;

use App\Dto\User\IndexDto;
use App\Entities\Elasticsearch\SearchResponse;
use App\Exceptions\SearchIndexDoesNotExist;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\IndexRequest;
use App\Http\Resources\User\IndexResource;
use App\Services\Elasticsearch\UsersIndexElasticsearchService;
use App\Services\SourceDtoCollectionService;
use App\Services\UserService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(
        private readonly UsersIndexElasticsearchService $searchService,
        private readonly UserService $userService,
    ) {}

    /**
     * @throws SearchIndexDoesNotExist
     */
    public function index(
        IndexRequest $request,
        SourceDtoCollectionService $collectionService
    ): AnonymousResourceCollection {
        $paginationRequestDto = $this
            ->userService
            ->getPaginationDataForSearchIndex(
                IndexDto::from($request->validated())
            );

        return IndexResource::collection(
            SearchResponse::fromArray(
                $this->searchService->findInSearchIndex($paginationRequestDto),
                $collectionService
            )->hits
        );
    }
}
