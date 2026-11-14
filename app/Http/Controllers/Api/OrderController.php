<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Http\Responses\ApiResponse;
use App\Services\CustomerOrderService;
use Illuminate\Http\Request;

// The orders of the logged-in customer. All the rules (stock, price, coupon, shipping) are in OrderService.
class OrderController extends Controller
{
    public function __construct(private CustomerOrderService $orders)
    {
    }

    // POST /checkout/preview : the cart summary. Nothing is saved.
    public function preview(CheckoutRequest $request)
    {
        $summary = $this->orders->preview($request->validated());

        return ApiResponse::success([
            'items' => collect($summary['lines'])->map(fn ($line) => [
                'product_id' => $line['product']->id,
                'variant_id' => $line['variant']?->id,
                'product_name' => $line['product_name'],
                'variant_label' => $line['variant_label'],
                'unit_price' => $line['unit_price'],
                'quantity' => $line['quantity'],
                'line_total' => $line['line_total'],
            ])->values(),
            'subtotal' => $summary['subtotal'],
            'discount' => $summary['discount_total'],
            'shipping' => $summary['shipping_total'],
            'total' => $summary['total'],
            'coupon_code' => $summary['coupon_code'],
        ], 'Cart summary calculated');
    }

    // POST /orders
    public function store(CheckoutRequest $request)
    {
        $order = $this->orders->place($request->user(), $request->validated());

        return ApiResponse::success(new OrderResource($order), 'Order placed successfully', 201);
    }

    public function updatePayment(Request $request, string $orderNumber)
    {
        $data = $request->validate([
            'payment_status' => ['required', 'in:paid,failed'],
            'payment_id' => ['required', 'string', 'max:255'],
            'invoice_id' => ['nullable', 'string', 'max:255'],
            'invoice_url' => ['required', 'url', 'max:2048'],
            'payment_data' => ['nullable', 'array'],
        ]);

        $order = $this->orders->updatePayment(
            $request->user(),
            $orderNumber,
            $data
        );

        return ApiResponse::success(
            new OrderResource($order),
            'Payment updated successfully'
        );
    }

    // GET /orders?per_page=
    public function index(Request $request)
    {
        $request->validate(['per_page' => ['nullable', 'integer', 'min:1', 'max:50']]);

        return ApiResponse::paginated(
            $this->orders->orders($request->user(), $request->integer('per_page', 15)),
            OrderResource::class,
            'Orders fetched successfully'
        );
    }

    // GET /orders/{orderNumber}
    public function show(Request $request, string $orderNumber)
    {
        return ApiResponse::success(
            new OrderResource($this->orders->order($request->user(), $orderNumber)),
            'Order fetched successfully'
        );
    }

    // POST /orders/{orderNumber}/cancel
    public function cancel(Request $request, string $orderNumber)
    {
        return ApiResponse::success(
            new OrderResource($this->orders->cancel($request->user(), $orderNumber)),
            'Order cancelled successfully'
        );
    }
}
