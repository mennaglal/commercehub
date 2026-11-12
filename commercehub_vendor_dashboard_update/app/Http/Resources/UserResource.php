<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar_url' => $this->avatar ? asset($this->avatar) : null,
            'roles' => $this->getRoleNames(),

            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'area_id' => $this->area_id,

            // Only when the controller loaded it: the vendor store of this user.
            'store' => $this->whenLoaded('store', fn () => new VendorStoreResource($this->store)),

            'created_at' => $this->created_at,
        ];
    }
}
