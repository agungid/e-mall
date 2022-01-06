<?php

use App\Http\Controllers\Api\Admin\LoginController;
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
        Route::get('/profil', [ LoginController::class, 'getUser'])->name('admin.profil');
    });

    // Api for customer
    // Route::group(['prefix' => 'user', 'middleware' => 'auth:api_admin'],function () {
        
    // });
});
