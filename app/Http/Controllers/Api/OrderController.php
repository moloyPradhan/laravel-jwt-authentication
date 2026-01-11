<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

use App\Models\Order;
use App\Models\OrderItems;

class OrderController extends Controller
{
    use ApiResponse;

    public function getOrders(Request $request)
    {
        $user_uid = $request->user()->uid;

        $orders = Order::with([
            'order_items.food' 
        ])->where('user_uid', $user_uid)->get();

        return $this->successResponse(200, 'Orders', [
            'orders' => $orders,
        ]);
    }
}
