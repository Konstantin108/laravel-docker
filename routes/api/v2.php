<?php

use App\Http\Controllers\Api\v2\ProductController;
use App\Http\Controllers\Api\v2\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->name('api.v2.')->group(static function (): void {

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
