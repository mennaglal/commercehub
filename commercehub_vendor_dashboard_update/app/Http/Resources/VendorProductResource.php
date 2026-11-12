<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// A product as its OWNER (the vendor) sees it: both languages, SKU, stock, status and the variants rows.
class VendorProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'sku' => $this->sku,

            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,

            'category_id' => $this->category_id,
            'image_url' => $this->image ? asset($this->image) : null,
            'status' => (bool) $this->status,

            'has_variants' => (bool) $this->has_variants,
            'price' => (float) $this->price,
            'is_sale' => (bool) $this->is_sale,
            'sale_price' => $this->sale_price !== null ? (float) $this->sale_price : null,
            'stock' => (int) $this->stock,

            'variants' => $this->whenLoaded('variants', fn () => $this->variants->map(fn ($variant) => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => (float) $variant->price,
                'discount_price' => $variant->discount_price !== null ? (float) $variant->discount_price : null,
                'quantity' => (int) $variant->quantity,
                'values' => $variant->values->pluck('id')->values(),
                'label' => $variant->values->pluck('value')->implode(' / '),
            ])->values()),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
