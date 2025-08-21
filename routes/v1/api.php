<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::middleware('api')->group(function (): void {
        Route::get('user', [UserController::class, 'index']);
    });
});
