<?php

use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\CustomerController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\InvoiceController;
use App\Http\Controllers\Api\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\SliderController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Api\Customer\LoginController as CustomerLoginController;
use App\Http\Controllers\Api\Customer\RegisterController;
use App\Http\Controllers\Api\Customer\ReviewController;
use App\Http\Controllers\Api\Web\CategoryController as WebCategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    // API for admin
    Route::group([ 'prefix' => 'admin', 'as' => 'admin.'], function () {
        Route::post('/login', [ AdminLoginController::class, 'index'])->name('login');
        Route::group( [ 'middleware' => 'auth:api_admin' ], function() {
            Route::get('/me', [ AdminLoginController::class, 'getUser'])->name('profil');
            Route::post('/logout', [ AdminLoginController::class, 'logout'])->name('logout');
            Route::get('/refresh-token', [ AdminLoginController::class, 'refreshToken'])->name('refres_token');
            Route::get('/dashboard', [ AdminDashboardController::class, 'index'])->name('dahboard');
            Route::apiResource('/categories', AdminCategoryController::class, ['except' => ['create', 'edit']]);
            Route::apiResource('/products', ProductController::class, ['except' => ['create', 'edit']]);
            Route::apiResource('/invoices', InvoiceController::class, ['except' => ['index', 'show']]);
            Route::get('/customers', [ CustomerController::class, 'index'])->name('customers.index');
            Route::apiResource('/sliders', SliderController::class, ['except' => ['create', 'show', 'edit', 'update']]);
            Route::apiResource('/users', UserController::class, ['except' => ['create', 'edit']]);
        });
    });

    // Api for customer
    Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {
        Route::post('/register', [ RegisterController::class, 'store'])->name('register');
        Route::post('/login', [ CustomerLoginController::class, 'index'])->name('login');
        Route::group([ 'middleware' => 'auth:api_customer' ], function() {
            Route::get('/me', [ CustomerLoginController::class, 'getUser'])->name('profil');
            Route::post('/logout', [ CustomerLoginController::class, 'logout'])->name('logout');
            Route::get('/refresh-token', [ CustomerLoginController::class, 'refreshToken'])->name('refres_token');
            Route::get('/dashboard', [ CustomerDashboardController::class, 'index'])->name('dahboard');
            Route::apiResource('/invoices', InvoiceController::class, ['only' => ['index', 'show']]);
            Route::post('/reviews', [ ReviewController::class, 'store' ])->name('reviews.store');
        });
    });

    //API for web
    Route::group([ 'prefix' => 'web', 'as' => 'web.' ], function () {
        Route::apiResource('/categories', WebCategoryController::class, ['only' => ['index', 'show' ]]);
    });
});
