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

    public function sellerRestaurantPage($restaurantId)
    {
        return view('seller.restaurant', [
            'restaurantId' => $restaurantId,
        ]);
    }

    public function sellerRestaurantProfilePage($restaurantId)
    {
        return view('seller.basicDetail', [
            'restaurantId' => $restaurantId,
        ]);
    }

    public function sellerRestaurantImagePage($restaurantId)
    {
        return view('seller.restaurantImage', [
            'restaurantId' => $restaurantId,
        ]);
    }

    public function sellerRestaurantAddressPage($restaurantId)
    {
        return view('seller.restaurantAddress', [
            'restaurantId' => $restaurantId,
        ]);
    }

    public function sellerRestaurantDocumentPage($restaurantId)
    {
        return view('seller.restaurantDocument', [
            'restaurantId' => $restaurantId,
        ]);
    }

    public function sellerRestaurantMenuPage($restaurantId)
    {
        return view('seller.restaurantMenu', [
            'restaurantId' => $restaurantId,
        ]);
    }

    public function sellerRestaurantFoodPage($restaurantId)
    {
        return view('seller.restaurantFood', [
            'restaurantId' => $restaurantId,
        ]);
    }

    public function sellerRestaurantAddFoodPage($restaurantId)
    {
        return view('seller.addFood', [
            'restaurantId' => $restaurantId,
        ]);
    }

    public function sellerFoodAddImagePage($restaurantId, $foodId)
    {
        return view('seller.addFoodImage', [
            'restaurantId' => $restaurantId,
            'foodId'       => $foodId
        ]);
    }

    public function restaurantFoodsPage($restaurantId)
    {
        return view('restaurantFood', [
            'restaurantId' => $restaurantId,
        ]);
    }

    public function cartItemsPage($restaurantId)
    {
        return view('cart', [
            'restaurantId' => $restaurantId,
        ]);
    }
}
