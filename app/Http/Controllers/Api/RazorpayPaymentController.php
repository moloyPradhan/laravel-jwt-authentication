<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Traits\ApiResponse;

use App\Models\Cart;
use App\Models\PaymentGateway;
use App\Models\Order;
use App\Models\OrderItems;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        $validated = $request->validate([
            'address_id' => 'required',
        ]);

        $address_id = $validated['address_id'];

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
                'address_id' => $address_id
            ],
            'clear_cart'   => [
                'user_uid' => $user_uid
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

    public function verifyPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id'    => 'required|string',
            'payment_id'  => 'required|string',
            'signature'   => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $orderId   = $validated['order_id'];
        $paymentId = $validated['payment_id'];
        $signature = $validated['signature'];

        $payment = PaymentGateway::where('order_id', $orderId)->first();

        if (!$payment) {
            return $this->errorResponse(404, 'Payment record not found');
        }

        try {
            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

                // $api->utility->verifyPaymentSignature([
                //     'razorpay_order_id'   => $orderId,
                //     'razorpay_payment_id' => $paymentId,
                //     'razorpay_signature'  => $signature,
                // ])
            ;
        } catch (\Exception $e) {
            $payment->update([
                'payment_id' => $paymentId,
                'status'     => 'failed',
                'response'   => json_encode([
                    'error' => $e->getMessage()
                ]),
            ]);

            return $this->errorResponse(400, 'Payment verification failed');
        }

        $payment->update([
            'payment_id' => $paymentId,
            'status'     => 'success',
            'response'   => json_encode($request->all()),
        ]);

        // success action

        $successAction = json_decode($payment->success_action, true);

        if (!is_array($successAction)) {
            return $this->errorResponse(500, 'Invalid success action format');
        }

        foreach ($successAction as $action => $data) {

            switch ($action) {

                case 'create_order':
                    $this->createOrderAfterPayment($data);
                    break;

                case 'clear_cart':
                    $this->clearUserCart($data);
                    break;

                default:
                    break;
            }
        }

        return $this->successResponse(200, 'Payment verified successfully', ['successAction' => $successAction]);
    }



    protected function createOrderAfterPayment($data)
    {
        $user_uid   = $data['user_uid'];
        // $address_id = $data['address_id'];

        $items = Cart::with('food')
            ->where('user_uid', $user_uid)
            ->get();

        if ($items->isEmpty()) {
            return $this->errorResponse(400, 'Cart is empty');
        }

        DB::beginTransaction();

        try {
            $order_uid = Str::upper(Str::random(8));
            $totalAmount = 0;
            $orderItems = [];

            foreach ($items as $item) {
                $price = $item->food->discount_price ?? $item->food->price;
                $lineTotal = $price * $item->quantity;

                $orderItems[] = [
                    'uid'       => Str::upper(Str::random(8)),
                    'order_uid' => $order_uid,
                    'food_uid'  => $item->food->uid,
                    'quantity'  => $item->quantity,
                    'price'     => $price,
                    'total'     => $lineTotal,
                ];

                $totalAmount += $lineTotal;
            }

            // Create Order
            Order::create([
                'uid'         => $order_uid,
                'user_uid'    => $user_uid,
                'address_uid' => 'ZWJllCR8',
                'amount'      => $totalAmount,
            ]);

            OrderItems::insert($orderItems);
            DB::commit();

            return $this->successResponse(200, "Order created successfully", [
                'order_uid' => $order_uid,
                'amount'    => $totalAmount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(500, $e->getMessage());
        }
    }


    protected function clearUserCart(array $data)
    {
        Cart::where('user_uid', $data['user_uid'])->delete();
    }
}
