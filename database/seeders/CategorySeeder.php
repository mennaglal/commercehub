<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'name_ar' => 'إلكترونيات',
                'slug' => 'electronics',
            ],
            [
                'name' => 'Fashion',
                'name_ar' => 'الأزياء',
                'slug' => 'fashion',
            ],
            [
                'name' => 'Home & Living',
                'name_ar' => 'المنزل والمعيشة',
                'slug' => 'home-living',
            ],
            [
                'name' => 'Beauty',
                'name_ar' => 'الجمال والعناية',
                'slug' => 'beauty',
            ],
            [
                'name' => 'Sports',
                'name_ar' => 'الرياضة',
                'slug' => 'sports',
            ],
        ];

        foreach ($categories as $data) {
            Category::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'name_ar' => $data['name_ar'],
                    'parent_id' => null,
                    'image' => null,
                ]
            );
        }
    }
}
