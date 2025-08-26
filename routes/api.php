<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('user', [\App\Http\Controllers\Api\v1\UserController::class, 'index']);
});

Route::prefix('v2')->group(function (): void {
    Route::get('user', [\App\Http\Controllers\Api\v2\UserController::class, 'index']);
});
