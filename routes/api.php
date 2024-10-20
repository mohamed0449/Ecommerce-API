<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\CategroyController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => 'api','prefix' => 'auth'], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});

// Brands routes
Route::group(['prefix' => 'brands'], function ($router) {
    Route::controller(BrandsController::class)->group(function () {
        Route::get('index', 'index')->middleware('is_admin');
        Route::get('show/{id}', 'show')->middleware('is_admin');
        Route::post('store', 'store')->middleware('is_admin');
        Route::put('update_brand/{id}', 'update_brand')->middleware('is_admin');
        Route::delete('delete_brand/{id}', 'delete_brand')->middleware('is_admin');
    });
});

// Categories routes
Route::group(['prefix' => 'category'], function ($router) {
    Route::controller(CategroyController::class)->group(function () {
        Route::get('index', 'index')->middleware('is_admin');
        Route::get('show/{id}', 'show')->middleware('is_admin');
        Route::post('store', 'store')->middleware('is_admin');
        Route::put('update_category/{id}', 'update_category')->middleware('is_admin');
        Route::delete('delete_category/{id}', 'delete_category')->middleware('is_admin');
    });
});

// Location routes
Route::group(['prefix' => 'location'], function ($router) {
    Route::controller(LocationController::class)->group(function () {
        Route::post('store', 'store')->middleware('auth');
        Route::put('update/{id}', 'update')->middleware('auth');
        Route::delete('destroy/{id}', 'destroy')->middleware('auth');
    });
});

// Products routes
Route::group(['prefix' => 'product'], function ($router) {
    Route::controller(ProductController::class)->group(function () {
        Route::get('index', 'index')->middleware('auth');
        Route::get('show/{id}', 'show')->middleware('auth');
        Route::post('store', 'store')->middleware('is_admin');
        Route::put('update/{id}', 'update')->middleware('is_admin');
        Route::delete('destroy/{id}', 'destroy')->middleware('is_admin');
    });
});

// Orders routes
Route::group(['prefix' => 'orders'], function ($router) {
    Route::controller(OrderController::class)->group(function () {
        Route::get('index', 'index')->middleware('is_admin');
        Route::get('show/{id}', 'show')->middleware('is_admin');
        Route::post('store', 'store')->middleware('auth');
        Route::get('get_order_items/{id}', 'get_order_items')->middleware('is_admin');
        Route::get('get_user_orders/{id}', 'get_user_orders')->middleware('is_admin');
        Route::post('change_order_status/{id}', 'change_order_status')->middleware('is_admin');
    });
});