<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A store as the customers see it. Public: no owner, no status, no approval.
 * (The vendor sees his own store with VendorStoreResource.)
 */
class StoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'logo_url' => $this->logo_url,
            'banner_url' => $this->banner_url,

            'country' => $this->whenLoaded('country', fn () => $this->country?->name),
            'city' => $this->whenLoaded('city', fn () => $this->city?->name),
            'area' => $this->whenLoaded('area', fn () => $this->area?->name),

            'products_count' => $this->whenCounted('products'),
        ];
    }
}
