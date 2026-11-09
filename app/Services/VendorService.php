<?php

namespace App\Services;

use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class VendorService
{
    public function __construct(private ImageUploader $images)
    {
    }

    /**
     * A customer asks to become a vendor.
     * The store is created as "pending": it works only after the admin approves it
     * (the vendor cannot add products or see orders before that).
     * To approve automatically, change 'pending' to 'approved' below.
     */
    public function becomeVendor(User $user, array $data)
    {
        if ($user->hasRole('vendor') || $user->store) {
            return null;
        }

        $uploaded = [];

        try {
            $store = DB::transaction(function () use ($user, $data, &$uploaded) {
                $logo = isset($data['logo']) ? $this->images->upload($data['logo'], 'stores') : null;
                $logo && $uploaded[] = $logo;

                $banner = isset($data['banner']) ? $this->images->upload($data['banner'], 'stores') : null;
                $banner && $uploaded[] = $banner;

                $user->assignRole('vendor');

                return Store::create([
                    'user_id' => $user->id,
                    'name' => $data['store_name'],
                    'slug' => $this->generateUniqueSlug($data['store_name']),
                    'description' => $data['description'] ?? null,
                    'logo' => $logo,
                    'banner' => $banner,
                    'status' => true,
                    'approval_status' => 'pending',

                    // The location of the store starts as the location of the user.
                    'country_id' => $user->country_id,
                    'city_id' => $user->city_id,
                    'area_id' => $user->area_id,
                ]);
            });
        } catch (Throwable $e) {
            // The database rolled back: remove the files we just uploaded.
            foreach ($uploaded as $path) {
                $this->images->delete($path);
            }

            throw $e;
        }

        return [
            'user' => $user->load('store'),
            'store' => $store,
        ];
    }

    // The vendor edits his own store. A new logo / banner replaces the old file.
    public function updateStore(Store $store, array $data): Store
    {
        $old = [];
        $new = [];

        foreach (['logo', 'banner'] as $field) {
            if (isset($data[$field])) {
                $old[] = $store->{$field};
                $new[$field] = $this->images->upload($data[$field], 'stores');
            }
        }

        try {
            $store->update(Arr::only($data, ['name', 'description', 'country_id', 'city_id', 'area_id']) + $new);
        } catch (Throwable $e) {
            foreach ($new as $path) {
                $this->images->delete($path);
            }

            throw $e;
        }

        foreach ($old as $path) {
            $this->images->delete($path);
        }

        return $store->fresh();
    }

    private function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'store';
        $slug = $base;
        $counter = 1;

        while (Store::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}
