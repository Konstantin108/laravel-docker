<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\v1\User\IndexRequest;
use App\Http\Resources\User\IndexResource;
use App\Services\Dto\IndexDto;
use App\Services\User\UserService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function index(IndexRequest $request, UserService $userService): AnonymousResourceCollection
    {
        $data = $request->validated();

        // TODO kpstya тут возможно не нужен appends()

        return IndexResource::collection(
            $userService->getPagination(IndexDto::from($data))->appends($data)
        );
    }
}
