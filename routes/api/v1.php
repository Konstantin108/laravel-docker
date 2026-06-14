<?php

use App\Http\Controllers\Api\v1\ProductController;
use App\Http\Controllers\Api\v1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(static function (): void {

    Route::prefix('users')
        ->name('users.')
        ->controller(UserController::class)
        ->group(static function (): void {
            Route::get('/', 'index')->name('index');
        });

    Route::prefix('products')
        ->name('products.')
        ->controller(ProductController::class)
        ->group(static function (): void {
            Route::get('/', 'index')->name('index');
        });
});
