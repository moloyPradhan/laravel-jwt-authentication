<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\RazorpayPaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['web', 'redirectIfAuthenticatedCookie'])
    ->get('/login', [Controller::class, 'loginPage'])
    ->name('loginPage');


// public routes
Route::get('/', [Controller::class, 'homePage'])->name('homePage');


// Protected routes here
Route::middleware(['web', 'authGuard'])->group(function () {
    Route::get('profile', [Controller::class, 'profilePage'])->name('profilePage');


    Route::get('chat', [Controller::class, 'listChatUser'])->name('userChatList');
    Route::get('chat/{uid}', [Controller::class, 'userChat'])->name('userChat');

    Route::get('seller/dashboard', [Controller::class, 'sellerDashboardPage'])->name('sellerDashboardPage');
    Route::get('seller/restaurant', [Controller::class, 'sellerAddRestaurantPage'])->name('sellerAddRestaurantPage');
    Route::get('seller/restaurant/{uid}', [Controller::class, 'sellerRestaurantPage'])->name('sellerRestaurantPage');
    Route::get('seller/restaurant/{uid}/profile', [Controller::class, 'sellerRestaurantProfilePage'])->name('sellerRestaurantProfilePage');
    Route::get('seller/restaurant/{uid}/images', [Controller::class, 'sellerRestaurantImagePage'])->name('sellerRestaurantImagePage');
    Route::get('seller/restaurant/{uid}/address', [Controller::class, 'sellerRestaurantAddressPage'])->name('sellerRestaurantAddressPage');
    Route::get('seller/restaurant/{uid}/documents', [Controller::class, 'sellerRestaurantDocumentPage'])->name('sellerRestaurantDocumentPage');
});

Route::get('razorpay-payment', [RazorpayPaymentController::class, 'index']);
Route::post('razorpay-payment', [RazorpayPaymentController::class, 'store'])->name('razorpay.payment.store');



Route::get('/welcome', function () {
    return view('welcome');
});

