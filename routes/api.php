<?php

use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\CustomerController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\InvoiceController;
use App\Http\Controllers\Api\Admin\LoginController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\SliderController;
use App\Http\Controllers\Api\Admin\UserController;
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
    Route::post('/login', [ LoginController::class, 'index'])->name('admin.login');
    // API for admin
    Route::group(['prefix' => 'admin', 'middleware' => 'auth:api_admin'],function () {
        Route::get('/me', [ LoginController::class, 'getUser'])->name('admin.profil');
        Route::post('/logout', [ LoginController::class, 'logout'])->name('admin.logout');
        Route::get('/refresh-token', [ LoginController::class, 'refreshToken'])->name('admin.refres_token');

        Route::get('/dashboard', [ DashboardController::class, 'index'])->name('admin.dahboard');
        Route::apiResource('/categories', CategoryController::class, ['except' => ['create', 'edit']]);
        Route::apiResource('/products', ProductController::class, ['except' => ['create', 'edit']]);
        Route::apiResource('/invoices', InvoiceController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);
        Route::get('/customers', [ CustomerController::class, 'index'])->name('admin.customers.index');
        Route::apiResource('/sliders', SliderController::class, ['except' => ['create', 'show', 'edit', 'update']]);
        Route::apiResource('/users', UserController::class, ['except' => ['create', 'edit']]);
    });

    // Api for customer
    // Route::group(['prefix' => 'user', 'middleware' => 'auth:api_admin'],function () {
        
    // });
});
