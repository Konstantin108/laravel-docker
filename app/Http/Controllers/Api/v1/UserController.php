<?php

namespace App\Http\Controllers\Api\v1;

use App\Dto\User\IndexDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\IndexRequest;
use App\Http\Resources\User\IndexResource;
use App\Services\UserService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $data = $request->validated();

        return IndexResource::collection(
            $this->userService->getPagination(IndexDto::from($data))
                ->appends($data)
        );
    }
}
