<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReturnRequest;
use App\Http\Resources\ReturnResource;
use App\Http\Responses\ApiResponse;
use App\Services\CustomerOrderService;
use Illuminate\Http\Request;

// The return requests of the logged-in customer (the admin reviews them from the panel).
class ReturnController extends Controller
{
    public function __construct(private CustomerOrderService $orders)
    {
    }

    // GET /orders/{orderNumber}/returnable-items : what can still be returned, and the refund of each piece
    public function returnable(Request $request, string $orderNumber)
    {
        $lines = $this->orders->returnableItems($request->user(), $orderNumber);

        return ApiResponse::success($lines->map(fn ($line) => [
            'order_item_id' => $line->item->id,
            'product_name' => $line->item->product_name,
            'variant_label' => $line->item->variant_label,
            'ordered_quantity' => (int) $line->item->quantity,
            'remaining_quantity' => (int) $line->remaining,
            'refund_per_piece' => (float) $line->unit_refund,
        ])->values(), 'Returnable items fetched successfully');
    }

    // POST /orders/{orderNumber}/returns
    public function store(ReturnRequest $request, string $orderNumber)
    {
        $return = $this->orders->requestReturn($request->user(), $orderNumber, $request->validated());

        return ApiResponse::success(new ReturnResource($return), 'Return request sent successfully', 201);
    }

    // GET /returns?per_page=
    public function index(Request $request)
    {
        $request->validate(['per_page' => ['nullable', 'integer', 'min:1', 'max:50']]);

        return ApiResponse::paginated(
            $this->orders->returns($request->user(), $request->integer('per_page', 15)),
            ReturnResource::class,
            'Returns fetched successfully'
        );
    }

    // GET /returns/{returnNumber}
    public function show(Request $request, string $returnNumber)
    {
        return ApiResponse::success(
            new ReturnResource($this->orders->orderReturn($request->user(), $returnNumber)),
            'Return fetched successfully'
        );
    }
}
