<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\RouteGroupEnum;
use App\Enums\SortedByEnum;
use App\Http\Requests\v1\User\IndexRequest;
use App\Http\Resources\User\UserResource;
use App\Services\User\Dto\FilterDto;
use App\Services\User\UserService;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    #[Group(
        name: RouteGroupEnum::USER->value,
        description: RouteGroupEnum::DESCRIPTIONS[RouteGroupEnum::USER->value]
    )]
    #[Endpoint(title: 'api.v1.users.index')]
    public function index(IndexRequest $request, UserService $userService): AnonymousResourceCollection
    {
        return UserResource::collection(
            $userService->getPagination(new FilterDto(
                sortedBy: SortedByEnum::from($request->validated('sorted_by', 'desc')),
                search: $request->validated('search'),
                perPage: $request->validated('per_page'),
            ))
                ->withQueryString()
        );
    }
}
