<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use App\Helpers\AuthHelper;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function homePage()
    {
        return view('home');
    }

    public function loginPage()
    {
        return view('login');
    }

    public function profilePage()
    {
        return view('profile');
    }

    public function listChatUser()
    {
        return view('chatList');
    }

    public function userChat(Request $request, $friendId)
    {
        $userId = AuthHelper::getUserId($request);
        return view('chat', compact('friendId', 'userId'));
    }

    public function sellerDashboardPage()
    {
        return view('seller.dashboard');
    }

    public function sellerAddRestaurantPage()
    {
        return view('seller.addRestaurant');
    }
    
    public function sellerRestaurantPage()
    {
        return view('seller.restaurant');
    }
}
