<?php

use App\Http\Controllers\Api\v1\ProductController;
use App\Http\Controllers\Api\v1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::get('user', [UserController::class, 'index'])->name('user.index');

    Route::get('product', [ProductController::class, 'index'])->name('product.index');
});
