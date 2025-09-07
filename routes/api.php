<?php

use Illuminate\Support\Facades\Route;

// TODO kpstya наверно разделить на 2 файла

Route::name('api.')->group(function (): void {
    Route::prefix('v1')->name('v1.')->group(function (): void {
        Route::get('user', [\App\Http\Controllers\Api\v1\UserController::class, 'index'])
            ->name('user.index');
    });

    Route::prefix('v2')->name('v2.')->group(function (): void {
        Route::get('user', [\App\Http\Controllers\Api\v2\UserController::class, 'index'])
            ->name('user.index');
    });
});
