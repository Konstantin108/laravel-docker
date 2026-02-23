<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\RouteGroupEnum;
use App\Http\Requests\v1\User\IndexRequest;
use App\Http\Resources\User\IndexResource;
use App\Services\User\Dto\IndexDto;
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
    #[Endpoint(title: 'Получить список пользователей с пагинацией')]
    public function index(IndexRequest $request, UserService $userService): AnonymousResourceCollection
    {
        $data = $request->validated();

        return IndexResource::collection(
            $userService->getPagination(IndexDto::from($data))->appends($data)
        );
    }
}
