<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(private CouponService $coupons)
    {
    }

    /* ---------------- Create ---------------- */

    /**
     * Create an order with its items, inside one transaction:
     * if anything fails, nothing is saved, no stock and no coupon use is changed.
     */
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $lines = $this->buildLines($data['items']);

            $storeIds = collect($lines)->pluck('store_id')->unique();
            if ($storeIds->count() > 1) {
                $this->fail(__('orders.errors.one_store'));
            }

            $subtotal = round(collect($lines)->sum('line_total'), 2);
            $shipping = round((float) ($data['shipping_total'] ?? 0), 2);

            // Coupon: discount + blogger commission (both calculated here, once).
            $coupon = null;
            $discount = 0.0;
            $commission = 0.0;

            if (! empty($data['coupon_code'])) {
                $applied = $this->coupons->apply($data['coupon_code'], $lines);

                $coupon = $applied['coupon'];
                $discount = $applied['discount'];
                $commission = $applied['commission'];
            }

            $isOnline = $data['payment_method'] === Order::PAYMENT_ONLINE;

            $order = Order::create([
                'order_number' => $this->makeOrderNumber(),
                'store_id' => $storeIds->first(),
                'status' => Order::STATUS_PENDING,

                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'shipping_address' => $data['shipping_address'],
                'notes' => $data['notes'] ?? null,

                'subtotal' => $subtotal,
                'discount_total' => $discount,
                'shipping_total' => $shipping,
                'total' => round($subtotal - $discount + $shipping, 2),

                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon?->code,
                'blogger_id' => $coupon?->blogger_id,
                'commission_amount' => $commission,
                'commission_status' => $coupon?->blogger_id ? Order::COMMISSION_PENDING : null,

                'payment_method' => $data['payment_method'],
                'payment_gateway' => $isOnline ? $data['payment_gateway'] : null,
                'payment_status' => Order::PAYMENT_PENDING,
                'delivery_status' => Order::DELIVERY_PENDING,
            ]);

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'variant_id' => $line['variant']?->id,
                    'product_name' => $line['product_name'],
                    'variant_label' => $line['variant_label'],
                    'sku' => $line['sku'],
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'line_total' => $line['line_total'],
                ]);

                // Take the quantity out of stock (rows are locked, so two orders cannot sell the same piece).
                if ($line['variant']) {
                    $line['variant']->decrement('quantity', $line['quantity']);
                } else {
                    $line['product']->decrement('stock', $line['quantity']);
                }
            }

            $coupon?->increment('used_count');

            return $order;
        });
    }

    /* ---------------- Status changes ---------------- */

    /**
     * Order status: pending -> confirmed -> cancelled.
     * Cancelling is allowed only before shipping, and gives everything back (stock, coupon use, commission).
     */
    public function changeStatus(Order $order, string $status): void
    {
        DB::transaction(function () use ($order, $status) {
            $order = $this->lock($order);

            if (! in_array($status, Order::STATUS_TRANSITIONS[$order->status], true)) {
                $this->fail(__('orders.errors.status_not_allowed'), 'status');
            }

            $changes = ['status' => $status];

            if ($status === Order::STATUS_CANCELLED) {
                if (! in_array($order->delivery_status, [Order::DELIVERY_PENDING, Order::DELIVERY_PREPARING], true)) {
                    $this->fail(__('orders.errors.cannot_cancel'), 'status');
                }

                $changes += $this->release($order);
            }

            $order->update($changes);
        });
    }

    /**
     * Delivery status: pending -> preparing -> shipped -> delivered (or returned).
     */
    public function changeDelivery(Order $order, string $status, ?string $trackingNumber = null): void
    {
        DB::transaction(function () use ($order, $status, $trackingNumber) {
            $order = $this->lock($order);

            if ($order->status !== Order::STATUS_CONFIRMED) {
                $this->fail(__('orders.errors.order_not_confirmed'), 'delivery_status');
            }

            if (! in_array($status, Order::DELIVERY_TRANSITIONS[$order->delivery_status], true)) {
                $this->fail(__('orders.errors.delivery_not_allowed'), 'delivery_status');
            }

            $changes = ['delivery_status' => $status];

            if ($trackingNumber) {
                $changes['tracking_number'] = $trackingNumber;
            }

            if ($status === Order::DELIVERY_SHIPPED) {
                $changes['shipped_at'] = now();
            }

            if ($status === Order::DELIVERY_DELIVERED) {
                $changes['delivered_at'] = now();

                // The blogger earns the commission only when the customer really received the order.
                if ($order->blogger_id) {
                    $changes['commission_status'] = Order::COMMISSION_EARNED;
                }

                // Cash on delivery: the money is collected at the door.
                if ($order->payment_method === Order::PAYMENT_COD && $order->payment_status === Order::PAYMENT_PENDING) {
                    $changes['payment_status'] = Order::PAYMENT_PAID;
                    $changes['paid_at'] = now();
                }
            }

            if ($status === Order::DELIVERY_RETURNED) {
                $changes += $this->release($order);
            }

            $order->update($changes);
        });
    }

    /**
     * Update the payment data. The admin form uses it now,
     * and a gateway webhook (Fatura, Tabby...) can call the same method later.
     */
    public function updatePayment(Order $order, array $data): void
    {
        $changes = Arr::only($data, ['payment_status', 'payment_id', 'invoice_id', 'invoice_url', 'payment_data']);

        if (($changes['payment_status'] ?? null) === Order::PAYMENT_PAID && ! $order->paid_at) {
            $changes['paid_at'] = now();
        }

        $order->update($changes);
    }

    /* ---------------- Prices ---------------- */

    /**
     * The price the customer pays for ONE piece. Single source of truth:
     * the order form and the saved order both use this method.
     */
    public function unitPrice(Product $product, ?ProductVariant $variant = null): float
    {
        if ($variant) {
            $price = (float) $variant->price;
            $discount = (float) $variant->discount_price;

            return $discount > 0 && $discount < $price ? $discount : $price;
        }

        return $product->is_sale && (float) $product->sale_price > 0
            ? (float) $product->sale_price
            : (float) $product->price;
    }

    // "Red / S"
    public function variantLabel(ProductVariant $variant): string
    {
        return $variant->values->pluck('value_en')->implode(' / ');
    }

    /* ---------------- Internals ---------------- */

    /**
     * Turns the submitted rows into trusted lines:
     * merges duplicates, loads products from the DB, checks the rules, computes prices.
     */
    private function buildLines(array $items): array
    {
        // 1) Same product + variant twice => one line with the quantities added.
        $merged = [];
        foreach ($items as $item) {
            $variantId = ! empty($item['variant_id']) ? (int) $item['variant_id'] : null;
            $key = $item['product_id'] . '-' . ($variantId ?? 0);

            $merged[$key] ??= [
                'product_id' => (int) $item['product_id'],
                'variant_id' => $variantId,
                'quantity' => 0,
            ];
            $merged[$key]['quantity'] += (int) $item['quantity'];
        }

        // 2) Load (and lock) everything with two queries, not one query per row.
        $products = Product::whereIn('id', collect($merged)->pluck('product_id'))
            ->lockForUpdate()->get()->keyBy('id');

        $variants = ProductVariant::whereIn('id', collect($merged)->pluck('variant_id')->filter())
            ->with('values')->lockForUpdate()->get()->keyBy('id');

        // 3) Check the rules and build the lines.
        $lines = [];
        foreach ($merged as $row) {
            $product = $products->get($row['product_id']);
            $variant = $row['variant_id'] ? $variants->get($row['variant_id']) : null;

            if (! $product || ! $product->status) {
                $this->fail(__('orders.errors.product_unavailable'));
            }

            $name = $product->name_en;

            if ($product->has_variants && ! $variant) {
                $this->fail(__('orders.errors.variant_required', ['product' => $name]));
            }

            if ($variant && (! $product->has_variants || $variant->product_id !== $product->id)) {
                $this->fail(__('orders.errors.variant_mismatch', ['product' => $name]));
            }

            $available = $variant ? $variant->quantity : $product->stock;
            if ($available < $row['quantity']) {
                $this->fail(__('orders.errors.not_enough_stock', ['product' => $name, 'available' => $available]));
            }

            $unitPrice = $this->unitPrice($product, $variant);

            $lines[] = [
                'product' => $product,
                'variant' => $variant,
                'store_id' => $product->store_id,
                'product_name' => $name,
                'variant_label' => $variant ? $this->variantLabel($variant) : null,
                'sku' => ($variant?->sku) ?: $product->sku,
                'unit_price' => $unitPrice,
                'quantity' => $row['quantity'],
                'line_total' => round($unitPrice * $row['quantity'], 2),
            ];
        }

        return $lines;
    }

    /**
     * Gives back what the order took (used by cancel and return):
     * the stock and one coupon use. Returns the order columns to change for the commission.
     */
    private function release(Order $order): array
    {
        foreach ($order->items as $item) {
            if ($item->variant_id) {
                ProductVariant::whereKey($item->variant_id)->increment('quantity', $item->quantity);
            } elseif ($item->product_id && $item->variant_label === null) {
                // Simple product only. (An item whose variant was deleted has a label, so it is skipped.)
                Product::whereKey($item->product_id)->increment('stock', $item->quantity);
            }
        }

        if ($order->coupon_id) {
            Coupon::whereKey($order->coupon_id)->where('used_count', '>', 0)->decrement('used_count');
        }

        return $order->blogger_id ? ['commission_status' => Order::COMMISSION_CANCELLED] : [];
    }

    private function lock(Order $order): Order
    {
        return Order::lockForUpdate()->findOrFail($order->id);
    }

    private function makeOrderNumber(): string
    {
        return 'ORD-' . now()->format('ymd') . '-' . Str::upper(Str::random(5));
    }

    private function fail(string $message, string $field = 'items'): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}
