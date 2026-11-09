<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

/**
 * The orders of ONE vendor store: only the orders that contain his items, and only his items.
 * (The platform delivers the orders, so the vendor only follows them.)
 */
class VendorOrderService
{
    // A product with 5 pieces or less is "low stock".
    private const LOW_STOCK = 5;

    public function orders(Store $store, int $perPage): LengthAwarePaginator
    {
        return Order::whereHas('items', fn ($items) => $items->where('store_id', $store->id))
            // The constraint keeps ONLY the items of this store in $order->items.
            ->with(['items' => fn ($items) => $items->where('store_id', $store->id)])
            ->latest()
            ->paginate($perPage);
    }

    public function order(Store $store, string $number): Order
    {
        return Order::where('order_number', $number)
            ->whereHas('items', fn ($items) => $items->where('store_id', $store->id))
            ->with(['items' => fn ($items) => $items->where('store_id', $store->id)])
            ->firstOrFail();
    }

    // The numbers of the vendor home screen (cached for one minute).
    public function dashboard(Store $store): array
    {
        return Cache::remember("vendor_dashboard:{$store->id}", 60, function () use ($store) {
            // Sales = the items of the valid orders (not cancelled, not sent back), after the coupon discount.
            $sales = fn () => OrderItem::query()
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('order_items.store_id', $store->id)
                ->where('orders.status', '!=', Order::STATUS_CANCELLED)
                ->where('orders.delivery_status', '!=', Order::DELIVERY_RETURNED)
                ->selectRaw('COUNT(DISTINCT orders.id) AS orders, COALESCE(SUM(order_items.line_total - order_items.discount_amount), 0) AS sales');

            $last30 = $sales()->where('orders.created_at', '>=', now()->subDays(30))->first();
            $all = $sales()->first();

            return [
                'products' => [
                    'total' => $store->products()->count(),
                    'active' => $store->products()->where('status', true)->count(),
                    'low_stock' => $store->products()->where('status', true)->where('stock', '<=', self::LOW_STOCK)->count(),
                ],
                'orders_last_30_days' => (int) $last30->orders,
                'sales_last_30_days' => (float) $last30->sales,
                'orders_total' => (int) $all->orders,
                'sales_total' => (float) $all->sales,
            ];
        });
    }
}
