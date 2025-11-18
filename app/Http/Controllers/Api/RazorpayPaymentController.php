<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;


class RazorpayPaymentController extends Controller
{
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
}
