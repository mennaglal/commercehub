<?php

namespace App\Http\Resources;

use App\Models\OrderReturn;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// A return request as the customer sees it.
class ReturnResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'return_number' => $this->return_number,
            'order_number' => $this->whenLoaded('order', fn () => $this->order?->order_number),
            'status' => $this->status,
            'reason' => $this->reason,
            'customer_note' => $this->customer_note,

            // The admin note is shown only when the return was rejected (it is the reason of the rejection).
            'rejection_reason' => $this->status === OrderReturn::STATUS_REJECTED ? $this->admin_note : null,

            'refund_amount' => (float) $this->refund_amount,
            'refunded_at' => $this->refunded_at,

            'items_count' => $this->whenCounted('items'),
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'order_item_id' => $item->order_item_id,
                'product_name' => $item->orderItem?->product_name,
                'variant_label' => $item->orderItem?->variant_label,
                'quantity' => (int) $item->quantity,
                'refund_amount' => (float) $item->refund_amount,
            ])->values()),

            'created_at' => $this->created_at,
        ];
    }
}
