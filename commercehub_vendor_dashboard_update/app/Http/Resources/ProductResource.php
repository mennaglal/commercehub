<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * The product card in the lists. Public: nothing internal here (no SKU, no exact stock, no status).
 * For a product with variants "price" is the lowest variant price (price_is_from = true).
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'image_url' => $this->image ? asset($this->image) : null,

            'price' => (float) $this->price,
            'sale_price' => $this->is_sale && $this->sale_price !== null ? (float) $this->sale_price : null,
            'final_price' => (float) $this->final_price,
            'is_sale' => (bool) $this->is_sale,
            'has_variants' => (bool) $this->has_variants,
            'price_is_from' => (bool) $this->has_variants,
            'in_stock' => $this->stock > 0,

            'category' => $this->whenLoaded('category', fn () => new CategoryResource($this->category)),
            'store' => $this->whenLoaded('store', fn () => [
                'id' => $this->store->id,
                'name' => $this->store->name,
                'slug' => $this->store->slug,
            ]),
        ];
    }
}
