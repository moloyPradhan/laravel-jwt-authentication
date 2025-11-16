<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


use App\Http\Controllers\Controller;

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

    Route::get('seller/dashboard', [Controller::class, 'sellerDashboard'])->name('sellerDashboardPage');
});



Route::get('/welcome', function () {
    return view('welcome');
});
