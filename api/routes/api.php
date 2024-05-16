<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CrawlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//Authenticatie groep
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::get('users', [AuthController::class, 'users'])->name('users');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

//Site groep
Route::group(['middleware' => 'api'], function ($router) {
    Route::group(['prefix' => 'categories'], function ($router) {
        Route::get('products', [CategoryController::class, 'products'])->name('products');
    });
});

Route::get('category', [CrawlController::class, 'get_category_products'])->name('get_category_products');
Route::get('crawl', [CrawlController::class, 'crawl'])->name('crawl');
