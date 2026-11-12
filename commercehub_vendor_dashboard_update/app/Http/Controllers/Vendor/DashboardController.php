<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\OrderReturnItem;
use App\Services\VendorOrderService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private VendorOrderService $orders) {}

    public function index(Request $request)
    {
        $store = $request->user()->store;
        $stats = $this->orders->dashboard($store);
        $recentOrders = $this->orders->orders($store, 5);
        $recentReturns = OrderReturnItem::query()
            ->whereHas('orderItem', fn ($q) => $q->where('store_id', $store->id))
            ->with(['orderReturn.order', 'orderItem'])
            ->latest('order_return_items.id')->take(5)->get();

        return view('vendor-dashboard.dashboard.index', compact('store', 'stats', 'recentOrders', 'recentReturns'));
    }
}
