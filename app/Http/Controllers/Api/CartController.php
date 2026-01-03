<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

use App\Models\Restaurant;
use App\Models\Cart;
use App\Models\RestaurantFood;
use App\Services\AuthCookieService;
use Illuminate\Support\Str;

class CartController extends Controller
{
    use ApiResponse;

    /* =====================================================
       ADD / UPDATE CART ITEM
    ===================================================== */

    public function addFoodToCart(Request $request, $restaurantId, $foodId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
        ]);

        $quantity = (int) $validated['quantity'];

        // Validate restaurant
        $restaurant = Restaurant::where('uid', $restaurantId)->first();
        if (!$restaurant) {
            return $this->errorResponse(404, 'Restaurant not found');
        }

        // Validate food
        $food = RestaurantFood::where('uid', $foodId)
            ->where('restaurant_uid', $restaurantId)
            ->first();

        if (!$food) {
            return $this->errorResponse(404, 'Food not found');
        }

        $user = $request->user();

        /* =========================
        LOGGED-IN USER
        ========================= */
        if ($user) {

            // BLOCK other restaurant items
            $existingRestaurant = Cart::where('user_uid', $user->uid)
                ->where('restaurant_uid', '!=', $restaurantId)
                ->exists();

            if ($existingRestaurant) {
                return $this->errorResponse(
                    409,
                    'Your cart contains items from another restaurant. Please clear the cart to continue.'
                );
            }

            if ($quantity <= 0) {
                Cart::where('user_uid', $user->uid)
                    ->where('food_uid', $foodId)
                    ->delete();

                return $this->successResponse(200, 'Item removed from cart');
            }

            Cart::updateOrCreate(
                [
                    'user_uid' => $user->uid,
                    'food_uid' => $foodId,
                ],
                [
                    'restaurant_uid' => $restaurantId,
                    'quantity'       => $quantity,
                ]
            );

            return $this->successResponse(200, 'Added to cart');
        }

        /* =========================
        GUEST USER
        ========================= */
        $authCookie = app(AuthCookieService::class);
        $guestUid   = $request->cookie('guest_uid');

        if (!$guestUid) {
            $guestUid = Str::lower(Str::random(8));
            $authCookie->setGuestUid($guestUid);
        }

        // BLOCK other restaurant items
        $existingRestaurant = Cart::where('guest_uid', $guestUid)
            ->where('restaurant_uid', '!=', $restaurantId)
            ->exists();

        if ($existingRestaurant) {
            return $this->errorResponse(
                409,
                'Your cart contains items from another restaurant. Please clear the cart to continue.'
            );
        }

        if ($quantity <= 0) {
            Cart::where('guest_uid', $guestUid)
                ->where('food_uid', $foodId)
                ->delete();

            return $this->successResponse(200, 'Item removed from cart (guest)');
        }

        Cart::updateOrCreate(
            [
                'guest_uid' => $guestUid,
                'food_uid'  => $foodId,
            ],
            [
                'restaurant_uid' => $restaurantId,
                'quantity'       => $quantity,
            ]
        );

        $response = $this->successResponse(200, 'Added to cart (guest)');

        return $response->withCookie(cookie(
            'guest_uid',
            $guestUid,
            60 * 24 * 30,
            '/',
            null,
            false,
            false,
            false,
            'Lax'
        ));
    }


    /* =====================================================
       DELETE CART ITEM BY CART UID
    ===================================================== */
    public function removeFoodItemFromCart(Request $request, $uid)
    {
        $user = $request->user();

        /* =========================
           LOGGED-IN USER
        ========================= */
        if ($user) {
            $deleted = Cart::where('uid', $uid)
                ->where('user_uid', $user->uid)
                ->delete();

            if (!$deleted) {
                return $this->errorResponse(404, 'Cart item not found');
            }

            return $this->successResponse(200, 'Cart item removed');
        }

        /* =========================
           GUEST USER
        ========================= */
        $guestUid = $request->cookie('guest_uid');

        if (!$guestUid) {
            return $this->errorResponse(404, 'Cart item not found');
        }

        $deleted = Cart::where('uid', $uid)
            ->where('guest_uid', $guestUid)
            ->delete();

        if (!$deleted) {
            return $this->errorResponse(404, 'Cart item not found');
        }

        return $this->successResponse(200, 'Cart item removed (guest)');
    }

    /* =====================================================
       GET CART ITEMS FOR A RESTAURANT
    ===================================================== */

    public function getCartItems(Request $request)
    {
        $user = $request->user();
        $itemsQuery = Cart::with('food');

        if ($user) {
            $itemsQuery->where('user_uid', $user->uid);
        } else {
            $guestUid = $request->cookie('guest_uid');

            if (!$guestUid) {
                return $this->successResponse(200, 'Cart is empty', [
                    'restaurant' => null,
                    'items' => [],
                ]);
            }

            $itemsQuery->where('guest_uid', $guestUid);
        }

        $items = $itemsQuery->get();

        if ($items->isEmpty()) {
            return $this->successResponse(200, 'Cart is empty', [
                'restaurant' => null,
                'items' => [],
            ]);
        }

        $restaurantUid = $items->first()->restaurant_uid;

        $restaurant = Restaurant::with('addresses')
            ->where('uid', $restaurantUid)
            ->first();

        $totalAmount = 0;
        foreach ($items as $item) {
            if (!empty($item['food']['discount_price'])) {
                $totalAmount = $totalAmount + floatval($item['food']['discount_price']);
            } else {
                $totalAmount = $totalAmount + floatval($item['food']['price']);
            }
        }

        return $this->successResponse(200, 'Cart items fetched', [
            'restaurant' => $restaurant,
            'items' => $items,
            'totalAmount' => $totalAmount
        ]);
    }

}
