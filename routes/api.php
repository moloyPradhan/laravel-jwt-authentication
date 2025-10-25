<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AddressController;

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
        Route::post('addresses', [AddressController::class, 'addUserAddress']);
    });
});
