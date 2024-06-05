<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CrawlController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware;

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
        Route::get('', [AdminController::class, 'index'])->name('index');

        Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
            Route::get('', [AdminController::class, 'index_users'])->name('index');
        });
        Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
            Route::get('', [AdminController::class, 'index_categories'])->name('index');
            Route::get('create', [CategoryController::class, 'create_admin'])->name('create');
            Route::post('store', [CategoryController::class, 'store_admin'])->name('store');
            Route::group(['prefix' => '{category}'], function () {
                Route::get('edit', [CategoryController::class, 'edit_admin'])->name('edit');
                Route::post('update', [CategoryController::class, 'update_admin'])->name('update');
                Route::group(['prefix' => 'sites', 'as' => 'sites.'], function () {
                    Route::get('', [AdminController::class, 'index_sites'])->name('index');
                    Route::get('create', [SiteController::class, 'create'])->name('create');
                    Route::post('store', [SiteController::class, 'store'])->name('store');
                    Route::group(['prefix' => '{site}'], function () {
                        Route::get('fetch', [SiteController::class, 'fetch_products'])->name('fetch_products');
                    });
                });
            });
        });
        Route::group(['prefix' => 'products', 'as' => 'products.'], function () {
            Route::get('', [AdminController::class, 'index_products'])->name('index');
        });
    });
});

require __DIR__ . '/auth.php';
