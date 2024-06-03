<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::group(['prefix' => 'auth'], function ($router) {
        Route::get('login', [UserController::class, 'login'])->name('login');
    });
    Route::get('', [AdminController::class, 'index'])->name('index');

    Route::group(['prefix' => 'products', 'as' => 'products.'], function () {
        Route::get('', [AdminController::class, 'index_products'])->name('index');
    });
});
