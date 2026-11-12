<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// An item of an order, from the snapshot saved when the order was placed (not from the live product).
class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_slug' => $this->whenLoaded('product', fn () => $this->product?->slug),
            'image_url' => $this->whenLoaded('product', fn () => $this->product?->image ? asset($this->product->image) : null),

            'product_name' => $this->product_name,
            'variant_label' => $this->variant_label,
            'sku' => $this->sku,
            'store_name' => $this->store_name,

            'unit_price' => (float) $this->unit_price,
            'quantity' => (int) $this->quantity,
            'line_total' => (float) $this->line_total,
            'discount_amount' => (float) $this->discount_amount,
        ];
    }
}
