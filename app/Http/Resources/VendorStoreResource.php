<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// The store as its OWNER sees it: includes the status and the approval of the admin.
class VendorStoreResource extends JsonResource
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

            'status' => (bool) $this->status,
            'approval_status' => $this->approval_status,   // pending | approved | rejected

            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'area_id' => $this->area_id,

            'created_at' => $this->created_at,
        ];
    }
}
