<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,

            'logo' => $this->logo,
            'logo_url' => $this->logo_url,

            'banner' => $this->banner,
            'banner_url' => $this->banner_url,

            'status' => $this->status,

            // Owner (user)
            'owner' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),

            'created_at' => $this->created_at,
        ];
    }
}
