<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * The order as the CUSTOMER sees it.
 * Internal data is never returned: no blogger, no commission, no raw gateway data.
 */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $invoiceUrl = (string) $this->invoice_url;

        return [
            'order_number' => $this->order_number,
            'status' => $this->status,
            'created_at' => $this->created_at,

            'totals' => [
                'subtotal' => (float) $this->subtotal,
                'discount' => (float) $this->discount_total,
                'shipping' => (float) $this->shipping_total,
                'total' => (float) $this->total,
            ],
            'coupon_code' => $this->coupon_code,

            'payment' => [
                'method' => $this->payment_method,
                'gateway' => $this->payment_gateway,
                'status' => $this->payment_status,
                // Only a real web link is returned.
                'invoice_url' => str_starts_with($invoiceUrl, 'https://') || str_starts_with($invoiceUrl, 'http://') ? $invoiceUrl : null,
                'paid_at' => $this->paid_at,
            ],

            'delivery' => [
                'status' => $this->delivery_status,
                'tracking_number' => $this->tracking_number,
                'shipped_at' => $this->shipped_at,
                'delivered_at' => $this->delivered_at,
            ],

            'shipping_address' => [
                'name' => $this->customer_name,
                'phone' => $this->customer_phone,
                'address' => $this->shipping_address,
                'notes' => $this->shipping_notes,
                'country' => $this->whenLoaded('country', fn () => $this->country?->name),
                'city' => $this->whenLoaded('city', fn () => $this->city?->name),
                'area' => $this->whenLoaded('area', fn () => $this->area?->name),
            ],
            'notes' => $this->notes,

            // What the app can offer to the customer right now.
            'can_cancel' => in_array($this->status, [Order::STATUS_PENDING, Order::STATUS_CONFIRMED], true)
                && in_array($this->delivery_status, [Order::DELIVERY_PENDING, Order::DELIVERY_PREPARING], true),
            'can_return' => $this->canBeReturned(),

            'items_count' => $this->whenCounted('items'),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
