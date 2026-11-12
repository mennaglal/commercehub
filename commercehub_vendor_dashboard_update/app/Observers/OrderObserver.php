<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

/**
 * Sends the order notifications WITHOUT touching OrderService.
 * "ShouldHandleEventsAfterCommit": the code runs only after the order transaction is saved,
 * so the order items already exist, and nothing is sent if the order fails.
 */
class OrderObserver implements ShouldHandleEventsAfterCommit
{
    public function __construct(private NotificationService $notifications)
    {
    }

    public function created(Order $order): void
    {
        $replace = [
            'number' => $order->order_number,
            'customer' => $order->customer_name,
            'total' => number_format((float) $order->total, 2),
        ];

        // The admins who can see orders.
        $this->notifications->toAdmins(
            'orders.view',
            $this->notifications->payload('order', 'new_order', $replace, route('admin.orders.show', $order))
        );

        // The vendors who have products in this order.
        $this->notifyVendors($order, 'new_order_vendor', $replace);
    }

    public function updated(Order $order): void
    {
        if (! $order->wasChanged(['status', 'delivery_status'])) {
            return;
        }

        $this->notifyVendors($order, 'order_updated_vendor', [
            'number' => $order->order_number,
            'status_ar' => $this->notifications->statusLabel('statuses', $order->status, 'ar'),
            'status_en' => $this->notifications->statusLabel('statuses', $order->status, 'en'),
            'delivery_ar' => $this->notifications->statusLabel('delivery_statuses', $order->delivery_status, 'ar'),
            'delivery_en' => $this->notifications->statusLabel('delivery_statuses', $order->delivery_status, 'en'),
        ]);
    }

    private function notifyVendors(Order $order, string $event, array $replace): void
    {
        $storeIds = $order->items()->whereNotNull('store_id')->distinct()->pluck('store_id');

        if ($storeIds->isEmpty()) {
            return;
        }

        $vendors = User::whereIn('id', Store::whereIn('id', $storeIds)->pluck('user_id'))
            ->where('status', true)
            ->get();

        // The vendor texts have separate :status_ar / :status_en placeholders, so the language is respected.
        $this->notifications->toUsers($vendors, $this->notifications->payload('order', $event, $replace));
    }
}
