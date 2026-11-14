<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\OrderReturnItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Customer returns AFTER delivery (partial returns by item and quantity).
 *
 * Flow:  requested -> approved -> received -> refunded     (or rejected)
 *   requested : nothing changes, the quantities are only reserved so they cannot be returned twice
 *   received  : the good pieces go back to the stock
 *   refunded  : the refund is saved and the blogger commission is reduced
 *
 * (A shipment that never reached the customer is NOT a return: that is delivery "returned" in OrderService.)
 */
class ReturnService
{
    /* ---------------- Create ---------------- */

    /**
     * @param array $data ['reason', 'customer_note', 'items' => [order_item_id => quantity]]
     */
    public function create(Order $order, array $data, ?int $adminId = null): OrderReturn
    {
        return DB::transaction(function () use ($order, $data, $adminId) {
            // The order row is locked: two returns of the same order are handled one after the other,
            // so the same piece can never be returned twice.
            $order = Order::lockForUpdate()->findOrFail($order->id);

            $this->ensureReturnable($order);

            $items = $order->items()->get()->keyBy('id');
            $done = $this->returnedTotals($order);

            $lines = [];

            foreach ($data['items'] as $orderItemId => $quantity) {
                $quantity = (int) $quantity;

                if ($quantity <= 0) {
                    continue;
                }

                // The item is looked up INSIDE this order, so an id of another order is rejected.
                $item = $items->get((int) $orderItemId);

                if (! $item) {
                    $this->fail(__('returns.errors.invalid_item'));
                }

                $totals = $done->get($item->id);
                $remainingQty = $item->quantity - (int) ($totals->qty ?? 0);

                if ($quantity > $remainingQty) {
                    $this->fail(__('returns.errors.too_many', ['product' => $item->product_name, 'available' => $remainingQty]));
                }

                $lines[] = [
                    'order_item_id' => $item->id,
                    'quantity' => $quantity,
                    'refund_amount' => $this->portion($this->netOf($item) - (float) ($totals->refund ?? 0), $remainingQty, $quantity),
                    'commission_amount' => $this->portion((float) $item->commission_amount - (float) ($totals->commission ?? 0), $remainingQty, $quantity),
                ];
            }

            if (empty($lines)) {
                $this->fail(__('returns.errors.no_items'));
            }

            $return = OrderReturn::create([
                'order_id' => $order->id,
                'return_number' => $this->makeReturnNumber(),
                'status' => OrderReturn::STATUS_REQUESTED,
                'reason' => $data['reason'],
                'customer_note' => $data['customer_note'] ?? null,
                'refund_amount' => round(collect($lines)->sum('refund_amount'), 2),
                'processed_by' => $adminId,
            ]);

            $return->items()->createMany($lines);

            return $return;
        });
    }

    /**
     * The order items with how many pieces can still be returned (used by the create form).
     */

    public function returnableItems(Order $order): Collection
    {
        $this->ensureReturnable($order);

        $done = $this->returnedTotals($order);

        return $order->items()->get()->map(function (OrderItem $item) use ($done) {
            $totals = $done->get($item->id);

            $remainingQty = $item->quantity - (int) ($totals->qty ?? 0);

            $remainingMoney = $this->netOf($item) - (float) ($totals->refund ?? 0);

            return (object) [
                'item' => $item,
                'remaining' => $remainingQty,
                'unit_refund' => $remainingQty > 0
                    ? round($remainingMoney / $remainingQty, 2)
                    : 0,
            ];
        });
    }
    /* ---------------- Status changes ---------------- */

    /**
     * @param array $data admin_note, restock [return_item_id => 1], refund_amount, refund_reference
     */
    public function changeStatus(OrderReturn $return, string $status, array $data = [], ?int $adminId = null): void
    {
        DB::transaction(function () use ($return, $status, $data, $adminId) {
            // Same lock order as create() (order first, then the return): no deadlocks, no double processing.
            $order = Order::lockForUpdate()->findOrFail($return->order_id);
            $return = OrderReturn::with('items.orderItem')->lockForUpdate()->findOrFail($return->id);

            if (! in_array($status, OrderReturn::TRANSITIONS[$return->status], true)) {
                $this->fail(__('returns.errors.status_not_allowed'), 'status');
            }

            $changes = ['status' => $status, 'processed_by' => $adminId];

            if (! empty($data['admin_note'])) {
                $changes['admin_note'] = $data['admin_note'];
            }

            if ($status === OrderReturn::STATUS_APPROVED) {
                $changes['approved_at'] = now();
            }

            if ($status === OrderReturn::STATUS_RECEIVED) {
                $changes['received_at'] = now();
                $this->receive($return, $data['restock'] ?? []);
            }

            if ($status === OrderReturn::STATUS_REFUNDED) {
                $changes['refunded_at'] = now();
                $changes['refund_reference'] = $data['refund_reference'] ?? null;
                $changes['refund_amount'] = $this->refundAmount($return, $data);
            }

            $return->update($changes);

            if ($status === OrderReturn::STATUS_REFUNDED) {
                $this->afterRefund($order, $return);
            }
        });
    }

    /* ---------------- Internals ---------------- */

    // The good pieces go back to the stock. A damaged piece does not.
    private function receive(OrderReturn $return, array $restock): void
    {
        foreach ($return->items as $returnItem) {
            $goBack = ! empty($restock[$returnItem->id]);

            $returnItem->update(['restock' => $goBack]);

            if (! $goBack) {
                continue;
            }

            $item = $returnItem->orderItem;

            if ($item->variant_id) {
                ProductVariant::whereKey($item->variant_id)->increment('quantity', $returnItem->quantity);
            } elseif ($item->product_id && $item->variant_label === null) {
                // Simple product only. (An item whose variant was deleted has a label, so it is skipped.)
                Product::whereKey($item->product_id)->increment('stock', $returnItem->quantity);
            }
        }
    }

    // The admin may refund LESS than the calculated amount, never more.
    private function refundAmount(OrderReturn $return, array $data): float
    {
        $max = (float) $return->refund_amount;
        $amount = isset($data['refund_amount']) ? round((float) $data['refund_amount'], 2) : $max;

        if ($amount > $max) {
            $this->fail(__('returns.errors.refund_too_high', ['max' => number_format($max, 2)]), 'refund_amount');
        }

        return $amount;
    }

    // After the refund: take the commission back and mark the payment as refunded if everything came back.
    private function afterRefund(Order $order, OrderReturn $return): void
    {
        $changes = [];

        $reversal = round($return->items->sum('commission_amount'), 2);

        if ($order->blogger_id && $reversal > 0) {
            $commission = max(0, round((float) $order->commission_amount - $reversal, 2));

            $changes['commission_amount'] = $commission;

            if ($commission <= 0) {
                $changes['commission_status'] = Order::COMMISSION_CANCELLED;
            }
        }

        // Every piece of the order is refunded => the payment is refunded.
        $orderedQty = (int) $order->items()->sum('quantity');

        $refundedQty = (int) OrderReturnItem::whereIn(
            'order_return_id',
            $order->returns()->where('status', OrderReturn::STATUS_REFUNDED)->select('id')
        )->sum('quantity');

        if ($orderedQty > 0 && $refundedQty >= $orderedQty && $order->payment_status === Order::PAYMENT_PAID) {
            $changes['payment_status'] = Order::PAYMENT_REFUNDED;
        }

        if ($changes) {
            $order->update($changes);
        }
    }

    // Delivered and still inside the return window.
    private function ensureReturnable(Order $order): void
    {
        if ($order->delivery_status !== Order::DELIVERY_DELIVERED) {
            $this->fail(__('returns.errors.not_delivered'));
        }

        if (! $order->canBeReturned()) {
            $this->fail(__('returns.errors.window_expired', ['days' => (int) config('orders.return_days', 14)]));
        }
    }

    /**
     * What was already requested for each item of the order (rejected returns do not count).
     * One grouped query: quantity, refunded money and commission taken back.
     *
     * @return Collection<int, object{item_id: int, qty: int, refund: float, commission: float}>
     */
    private function returnedTotals(Order $order): Collection
    {
        return OrderReturnItem::query()
            ->join('order_returns', 'order_returns.id', '=', 'order_return_items.order_return_id')
            ->where('order_returns.order_id', $order->id)
            ->where('order_returns.status', '!=', OrderReturn::STATUS_REJECTED)
            ->groupBy('order_return_items.order_item_id')
            ->selectRaw('order_return_items.order_item_id as item_id,
                         SUM(order_return_items.quantity) as qty,
                         SUM(order_return_items.refund_amount) as refund,
                         SUM(order_return_items.commission_amount) as commission')
            ->get()
            ->keyBy('item_id');
    }

    // What the customer really paid for the item: its total minus its share of the coupon discount.
    private function netOf(OrderItem $item): float
    {
        return (float) $item->line_total - (float) $item->discount_amount;
    }

    /**
     * The part of the remaining money that belongs to the pieces returned now.
     * When the LAST pieces are returned they take everything that is left, so no cent is lost.
     * Example: 100.00 for 3 pieces => 33.33, 33.33, then 33.34.
     */
    private function portion(float $remainingMoney, int $remainingQty, int $returning): float
    {
        if ($remainingQty <= 0 || $returning <= 0) {
            return 0.0;
        }

        if ($returning >= $remainingQty) {
            return max(round($remainingMoney, 2), 0.0);
        }

        return round($remainingMoney * $returning / $remainingQty, 2);
    }

    private function makeReturnNumber(): string
    {
        return 'RET-' . now()->format('ymd') . '-' . Str::upper(Str::random(5));
    }

    private function fail(string $message, string $field = 'items'): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}
