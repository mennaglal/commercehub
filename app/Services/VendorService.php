<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class VendorService
{
    public function becomeVendor($user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {

            if ($user->hasRole('vendor')) {
                return null;
            }

            // assign role
            $user->assignRole('vendor');

            // upload folder
            $uploadPath = public_path('uploads/stores');

            // create folder if not exists
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            // upload logo
            $logoPath = null;

            if (isset($data['logo'])) {

                $logo = $data['logo'];

                $logoName = time() . '_logo.' . $logo->getClientOriginalExtension();

                $logo->move($uploadPath, $logoName);

                $logoPath = 'uploads/stores/' . $logoName;
            }

            // upload banner
            $bannerPath = null;

            if (isset($data['banner'])) {

                $banner = $data['banner'];

                $bannerName = time() . '_banner.' . $banner->getClientOriginalExtension();

                $banner->move($uploadPath, $bannerName);

                $bannerPath = 'uploads/stores/' . $bannerName;
            }

            // create store
            $store = Store::create([
                'user_id' => $user->id,
                'name' => $data['store_name'],
                'slug' => $this->generateUniqueSlug($data['store_name']),
                'description' => $data['description'] ?? null,
                'logo' => $logoPath,
                'banner' => $bannerPath,
                'status' => 1,
            ]);

            return [
                'user' => $user,
                'store' => $store,
            ];
        });
    }

    private function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);

        $count = Store::where('slug', 'like', $slug . '%')->count();

        return $count
            ? $slug . '-' . ($count + 1)
            : $slug;
    }
}
