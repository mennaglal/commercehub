<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            // The content is shown only in the show() answer, not in the list.
            'content' => $this->when($request->route('slug') !== null, fn () => $this->content),
            'updated_at' => $this->updated_at,
        ];
    }
}
