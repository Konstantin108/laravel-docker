<?php

use App\Http\Controllers\Api\v2\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->name('api.v2.')->group(function (): void {
    Route::get('user', [UserController::class, 'index'])->name('user.index');
});
