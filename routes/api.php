<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\RestaurantController;

Route::prefix('auth')->group(function () {
    Route::post('verify', [AuthController::class, 'verifyUser']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'profile']);
    });
});

Route::prefix('users')->group(function () {
    Route::post('/', [AuthController::class, 'register']);

    Route::middleware('auth:api')->group(function () {
        Route::get('addresses', [AddressController::class, 'index']);
        Route::post('addresses', [AddressController::class, 'addUserAddress']);
        Route::get('restaurants', [RestaurantController::class, 'getRestaurants']);
    });
});

Route::middleware('auth:api')->group(function () {
    Route::get('addresses/{uid}', [AddressController::class, 'show']);
    Route::put('addresses/{uid}', [AddressController::class, 'update']);
    Route::delete('addresses/{uid}', [AddressController::class, 'destroy']);
});

Route::prefix('restaurants')->group(function () {
    Route::get('/', [RestaurantController::class, 'index']);
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [RestaurantController::class, 'addRestaurant']);
        Route::post('{uid}/documents', [RestaurantController::class, 'addRestaurantDocuments']);
        Route::post('{uid}/images', [RestaurantController::class, 'addRestaurantImages']);
    });
});
