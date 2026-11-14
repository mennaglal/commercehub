<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * The order as a VENDOR sees it. The controller loads only the items of his store, so:
 *  - he sees only his own items and his own total,
 *  - he never sees the phone and the address of the customer (the platform delivers the order).
 */
class VendorOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_number' => $this->order_number,
            'status' => $this->status,
            'delivery_status' => $this->delivery_status,
            'created_at' => $this->created_at,

            'customer_name' => $this->customer_name,

            // What the customer paid for the items of THIS vendor (after the coupon discount).
            'items_total' => round($this->items->sum(fn ($item) => (float) $item->line_total - (float) $item->discount_amount), 2),

            'items' => $this->items->map(fn ($item) => [
                'product_name' => $item->product_name,
                'variant_label' => $item->variant_label,
                'sku' => $item->sku,
                'unit_price' => (float) $item->unit_price,
                'quantity' => (int) $item->quantity,
                'line_total' => (float) $item->line_total,
            ])->values(),
        ];
    }
}
