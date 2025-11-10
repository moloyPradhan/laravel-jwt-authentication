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

Route::get('/check-cookie', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'access_token' => $request->cookie('access_token'),
        'all' => $request->cookies->all(),
    ]);
});

Route::get('/', [Controller::class, 'homePage'])->name('homePage');

Route::middleware(['web', 'redirectIfAuthenticatedCookie'])
    ->get('/login', [Controller::class, 'loginPage'])
    ->name('loginPage');

Route::middleware('auth:api')->group(function () {
    Route::get('chat', [Controller::class, 'listChatUser'])->name('userChatList');
});

Route::get('/welcome', function () {
    return view('welcome');
});
