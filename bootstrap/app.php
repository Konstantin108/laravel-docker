<?php

use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;

// TODO kpstya можно ли тут сразу настроить префиксы для роутов

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: [
            __DIR__.'/../routes/api/v1.php',
            __DIR__.'/../routes/api/v2.php',
        ],
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (SearchIndexException $exception): ?JsonResponse {
            if (config('app.debug')) {
                return null;
            }

            // TODO kpstya проверить работу

            return new JsonResponse([
                'error' => [
                    'status' => 500,
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ],
            ], 500);
        });
    })->create();
