<?php

namespace App\Http\Controllers\Api\v2;

use App\Dto\User\IndexDto;
use App\Entities\Elasticsearch\SearchResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\IndexRequest;
use App\Services\ElasticsearchService;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        private readonly ElasticsearchService $searchService,
        private readonly UserService $userService,
    ) {}

    // TODO kpstya написать тесты на этот метод, добавить вводимые и возврщаемые типы
    public function index(IndexRequest $request): void
    {
        $paginationRequestDto = $this
            ->userService
            ->getPaginationDataForSearchIndex(
                IndexDto::from($request->validated())
            );

        $res = $this->searchService->findUsersInSearchIndex($paginationRequestDto);
        //        dd($res);
        $res = SearchResponse::fromArray($res);
        dd($res);
    }
}
