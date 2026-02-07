<?php

use App\Http\Controllers\Api\v2\ProductController;
use App\Http\Controllers\Api\v2\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->name('api.v2.')->group(function (): void {
    Route::get('users', [UserController::class, 'index'])->name('users.index');

    Route::get('products', [ProductController::class, 'index'])->name('products.index');
});
