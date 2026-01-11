<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\RazorpayPaymentController;
use App\Http\Controllers\Api\OrderController;

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
        Route::get('others', [UserController::class, 'listOtherUsers']);
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

    Route::get('{uid}/foods', [FoodController::class, 'listRestaurantFood']);
    Route::get('{uid}/menus', [RestaurantController::class, 'listMenu']);

    Route::middleware('auth:api')->group(function () {

        Route::post('{restaurantId}/foods/{foodId}/images', [FoodController::class, 'addFoodImage']);
        Route::post('{uid}/foods', [FoodController::class, 'addFood']);

        // Route::patch('{restaurantId}/menus/{menuId}', [RestaurantController::class, 'updateMenu']);
        // Route::delete('{restaurantId}/menus/{menuId}', [RestaurantController::class, 'softDeleteMenu']);
        // Route::patch('{restaurantId}/menus/{menuId}/restore', [RestaurantController::class, 'restoreMenu']);

        Route::get('{uid}/basic-details', [RestaurantController::class, 'restaurantBasicDetails']);
        Route::get('{uid}/images', [RestaurantController::class, 'restaurantImages']);

        Route::post('{uid}/menus', [RestaurantController::class, 'createMenu']);

        Route::patch('{restaurantId}/menus/{menuId}', [RestaurantController::class, 'updateMenu']);
        Route::delete('{restaurantId}/menus/{menuId}', [RestaurantController::class, 'softDeleteMenu']);
        Route::patch('{restaurantId}/menus/{menuId}/restore', [RestaurantController::class, 'restoreMenu']);

        Route::post('/', [RestaurantController::class, 'addRestaurant']);
        Route::post('{uid}/documents', [RestaurantController::class, 'addRestaurantDocuments']);
        Route::post('{uid}/images', [RestaurantController::class, 'addRestaurantImages']);
        Route::post('{uid}/addresses', [AddressController::class, 'addRestaurantAddress']);
    });
});

Route::middleware('auth.optional')->group(function () {

    Route::post(
        'restaurants/{restaurantId}/foods/{foodId}/cart',
        [CartController::class, 'addFoodToCart']
    );

    Route::get(
        'cart-items',
        [CartController::class, 'getCartItems']
    );

    Route::delete(
        'cart-items/{uid}',
        [CartController::class, 'removeFoodItemFromCart']
    );
});


Route::middleware('auth:api')->group(function () {
    Route::get('orders', [OrderController::class, 'getOrders']);
    Route::post('orders/create', [RazorpayPaymentController::class, 'createOrder']);
    Route::post('orders/verify-payment', [RazorpayPaymentController::class, 'verifyPayment']);

    Route::get('/messages/{roomId}', [MessageController::class, 'getMessages']);
    Route::post('/send-message', [MessageController::class, 'sendMessage']);
});
