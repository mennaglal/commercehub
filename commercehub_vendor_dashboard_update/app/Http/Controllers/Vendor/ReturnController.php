<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\OrderReturn;
use App\Models\OrderReturnItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReturnController extends Controller
{
    private const VENDOR_STATUSES = ['requested', 'approved', 'rejected', 'received'];

    public function index(Request $request)
    {
        $storeId = $request->user()->store->id;
        $returns = OrderReturn::query()
            ->whereHas('items.orderItem', fn ($q) => $q->where('store_id', $storeId))
            ->with(['order:id,order_number,created_at', 'items' => fn ($q) => $q->whereHas('orderItem', fn ($i) => $i->where('store_id', $storeId))->with('orderItem')])
            ->latest()->paginate(15);
        return view('vendor-dashboard.returns.index', compact('returns'));
    }

    public function show(Request $request, int $return)
    {
        $storeId = $request->user()->store->id;
        $orderReturn = OrderReturn::query()
            ->whereHas('items.orderItem', fn ($q) => $q->where('store_id', $storeId))
            ->with(['order', 'items' => fn ($q) => $q->whereHas('orderItem', fn ($i) => $i->where('store_id', $storeId))->with('orderItem')])
            ->findOrFail($return);
        return view('vendor-dashboard.returns.show', compact('orderReturn'));
    }

    public function updateItemStatus(Request $request, int $return, int $item)
    {
        $data = $request->validate([
            'vendor_status' => ['required', Rule::in(self::VENDOR_STATUSES)],
            'vendor_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $storeId = $request->user()->store->id;
        $returnItem = OrderReturnItem::query()
            ->where('order_return_id', $return)
            ->whereHas('orderItem', fn ($q) => $q->where('store_id', $storeId))
            ->firstOrFail();

        $allowed = [
            'requested' => ['approved', 'rejected'],
            'approved' => ['received', 'rejected'],
            'rejected' => [],
            'received' => [],
        ];

        abort_unless(in_array($data['vendor_status'], $allowed[$returnItem->vendor_status ?? 'requested'] ?? [], true), 422, 'This return-item status transition is not allowed.');

        $returnItem->update([
            'vendor_status' => $data['vendor_status'],
            'vendor_note' => $data['vendor_note'] ?? null,
            'vendor_processed_at' => now(),
        ]);

        return back()->with('success', __('messages.updated_successfully'));
    }
}
