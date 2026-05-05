<?php

use App\Http\Controllers\Api\v1\ProductController;
use App\Http\Controllers\Api\v1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(static function (): void {
    // TODO kpstya стоит ли добавить префиксы
    Route::get('users', [UserController::class, 'index'])->name('users.index');

    Route::get('products', [ProductController::class, 'index'])->name('products.index');
});
