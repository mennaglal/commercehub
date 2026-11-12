<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\VendorOrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private VendorOrderService $orders) {}

    public function index(Request $request)
    {
        $orders = $this->orders->orders($request->user()->store, 15);
        return view('vendor-dashboard.orders.index', compact('orders'));
    }

    public function show(Request $request, string $orderNumber)
    {
        $order = $this->orders->order($request->user()->store, $orderNumber);
        $vendorSubtotal = $order->items->sum(fn ($item) => (float) $item->line_total - (float) $item->discount_amount);
        return view('vendor-dashboard.orders.show', compact('order', 'vendorSubtotal'));
    }
}
