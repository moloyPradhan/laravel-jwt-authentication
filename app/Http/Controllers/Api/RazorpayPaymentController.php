<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Traits\ApiResponse;

use App\Models\Cart;
use App\Models\PaymentGateway;

class RazorpayPaymentController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $orderData = [
            'receipt'         => 'order_rcptid_' . rand(),
            'amount'          => 50000,
            'currency'        => 'INR',
            'payment_capture' => 1
        ];

        $razorpayOrder = $api->order->create($orderData);

        return view('payment', [
            'key'      => env('RAZORPAY_KEY'),
            'order_id' => $razorpayOrder['id'],
            'amount'   => $razorpayOrder['amount']
        ]);
    }


    public function store(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $payment = $api->payment->fetch($request->razorpay_payment_id);

        print_r($payment);
        die;
    }

    public function verify(Request $request)
    {
        $payload = json_encode([]);
        $expectedSignature = hash_hmac('sha256', $payload, env('RAZORPAY_SECRET'));
        // if ($sigHeader === $expectedSignature) {
        //     // Payment verified
        // }
    }


    public function createOrder(Request $request)
    {
        $user = $request->user();
        $user_uid = $user->uid;

        $items = Cart::with('food')
            ->where('user_uid', $user_uid)
            ->get();

        if ($items->isEmpty()) {
            return $this->errorResponse(400, 'Cart is empty');
        }


        $totalAmount = 0;
        foreach ($items as $item) {
            $totalAmount += $item->food->discount_price ?? $item->food->price;
        }



        // Razorpay amount in paise
        $amountInPaise = $totalAmount * 100;


        $orderData = [
            'receipt'         => 'rcpt_' . uniqid(),
            'amount'          => $amountInPaise,
            'currency'        => 'INR',
            'payment_capture' => 1
        ];

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $razorpayOrder = $api->order->create($orderData);

        $successAction = [
            'create_order' => [
                'user_uid' => $user_uid,
                'amount'   => $totalAmount,
            ]
        ];

        PaymentGateway::create([
            'order_id'       => $razorpayOrder['id'],
            'request'        => json_encode($orderData),
            'success_action' => json_encode($successAction),
            'status'         => 'created',
        ]);

        return $this->successResponse(200, 'Payment initialized successfully', [
            'payload' => [
                'key'      => env('RAZORPAY_KEY'),
                'order_id' => $razorpayOrder['id'],
                'amount'   => $amountInPaise,
                'currency' => 'INR',
            ]
        ]);
    }
}
