<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendorOrderResource;
use App\Http\Responses\ApiResponse;
use App\Services\VendorOrderService;
use Illuminate\Http\Request;

// The orders that contain items of the vendor (read only: the platform manages the delivery).
class OrderController extends Controller
{
    public function __construct(private VendorOrderService $orders)
    {
    }

    // GET /vendor/orders?per_page=
    public function index(Request $request)
    {
        $request->validate(['per_page' => ['nullable', 'integer', 'min:1', 'max:50']]);

        return ApiResponse::paginated(
            $this->orders->orders($request->user()->store, $request->integer('per_page', 15)),
            VendorOrderResource::class,
            'Orders fetched successfully'
        );
    }

    // GET /vendor/orders/{orderNumber}
    public function show(Request $request, string $orderNumber)
    {
        return ApiResponse::success(
            new VendorOrderResource($this->orders->order($request->user()->store, $orderNumber)),
            'Order fetched successfully'
        );
    }
}
