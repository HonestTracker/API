<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CrawlController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//Authenticatie groep
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::get('users', [AuthController::class, 'users'])->name('users');
    Route::post('login', [AuthController::class, 'login'])->name('api.login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('userdetails', [AuthController::class, 'user_details'])->name('userdetails');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::middleware('jwt.auth')->get('user', [AuthController::class, 'user'])->name('user');
    Route::middleware('jwt.auth')->get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::middleware('jwt.auth')->get('user/favorites', [FavoriteController::class, 'index_web'])->name('favorites');
    Route::middleware('jwt.auth')->post('user/favorites/store', [FavoriteController::class, 'store'])->name('favorites');
    Route::middleware('jwt.auth')->post('edit', [AuthController::class, 'edit'])->name('edit');
    Route::middleware('jwt.auth')->post('edit/password', [AuthController::class, 'edit_password'])->name('password');

});
Route::get('auth/google', [AuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);


Route::group(['prefix' => 'mobile'], function ($router) {
    Route::group(['middleware' => 'api'], function ($router) {
        Route::middleware('jwt.auth')->get('home', [ProductController::class, 'homepage'])->name('home');
        Route::middleware('jwt.auth')->get('products', [ProductController::class, 'product_page'])->name('product_page');
        Route::middleware('jwt.auth')->get('products/view', [ProductController::class, 'product_page_single'])->name('product_page_single');
        Route::middleware('jwt.auth')->get('products/search', [ProductController::class, 'search_products'])->name('search_products');
        Route::middleware('jwt.auth')->get('products/filter', [ProductController::class, 'filter_products'])->name('filter_products');
        Route::group(['prefix' => 'categories'], function ($router) {
        });
    });
});
//Site route group
Route::get('home', [ProductController::class, 'homepage_web'])->name('home_web');
Route::get('about', [ProductController::class, 'about_web'])->name('about_web');
Route::get('products', [ProductController::class, 'product_page_web'])->name('product_page_web');
Route::get('products/search', [ProductController::class, 'search_product_web'])->name('search_products_web');
Route::post('products/filter', [ProductController::class, 'filter_products_web'])->name('filter_products_web');
Route::get('products/{id}', [ProductController::class, 'get_product_by_id'])->name('product_by_id');
Route::post('products/comments/post', [CommentController::class, 'store'])->name('store');
Route::middleware('jwt.auth')->post('products/comments/delete', [CommentController::class, 'delete'])->name('delete');
Route::group(['prefix' => 'categories'], function ($router) {
});

Route::get('crawl', [CrawlController::class, 'crawl'])->name('crawl');
Route::get('test', [ProductController::class, 'test'])->name('test');
