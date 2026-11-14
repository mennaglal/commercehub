<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductService
{
    public function create($user, array $data)
    {
        $store = $user->store;

        if (!$store) {
            return null;
        }

        $imagePath = null;

        if (isset($data['image'])) {

            $image = $data['image'];

            $imageName = time() . '_product.' . $image->getClientOriginalExtension();

            $image->move(public_path('uploads/products'), $imageName);

            $imagePath = 'uploads/products/' . $imageName;
        }

        $product = Product::create([

            'store_id' => $store->id,

            'name' => $data['name'],

            'slug' => Str::slug($data['name']) . '-' . uniqid(),

            'description' => $data['description'] ?? null,

            'price' => $data['price'],

            'stock' => $data['stock'],

            'sku' => $data['sku'],

            'image' => $imagePath,

            'status' => 1,
        ]);

        return $product;
    }
}
