<?php

use App\Http\Controllers\Api\v2\ProductController;
use App\Http\Controllers\Api\v2\UserController;
use Illuminate\Support\Facades\Route;

/* TODO kpstya
    - стоит ли избавиться от эндпоинта storage/{path}
    - как это сделано в наших рабочих проектах? */

Route::prefix('v2')->name('api.v2.')->group(function (): void {
    Route::get('user', [UserController::class, 'index'])->name('user.index');

    Route::get('product', [ProductController::class, 'index'])->name('product.index');
});
