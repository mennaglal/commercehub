<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // categories.name is the English name, name_ar the Arabic one.
            'name' => app()->getLocale() === 'ar' ? ($this->name_ar ?: $this->name) : $this->name,
            'slug' => $this->slug,
            'image_url' => $this->image ? asset($this->image) : null,
            'parent_id' => $this->parent_id,

            // Subcategories (only when the controller loaded them).
            'children' => CategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}
