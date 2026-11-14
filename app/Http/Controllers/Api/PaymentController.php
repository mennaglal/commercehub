<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;
use App\Http\Responses\ApiResponse;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function verify(
        Request $request,
        string $orderNumber
    ) {
        $data = $request->validate([
            'gateway' => [
                'required',
                'string',
                'in:' . implode(',', Order::PAYMENT_GATEWAYS),
            ],

            'payment_id' => [
                'required',
                'string',
                'max:255',
            ],

            'idempotency_key' => [
                'required',
                'uuid',
            ],
        ]);

        $order = $this->paymentService->verifyPayment(
            $request->user(),
            $orderNumber,
            $data
        );

        return ApiResponse::success(
            new OrderResource($order),
            'Payment verification completed.'
        );
    }
}
