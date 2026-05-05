<?php

use App\Http\Controllers\Api\v2\ProductController;
use App\Http\Controllers\Api\v2\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->name('api.v2.')->group(static function (): void {
    Route::prefix('users')->name('users.')->group(static function (): void {
        Route::get('/', [UserController::class, 'index'])->name('index');
    });

    Route::prefix('products')->name('products.')->group(static function (): void {
        Route::get('/', [ProductController::class, 'index'])->name('index');
    });
});
