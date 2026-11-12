<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    public function run(): void
    {
        $subCategories = [
            // Electronics
            [
                'name' => 'Mobile Phones',
                'name_ar' => 'الهواتف المحمولة',
                'slug' => 'mobile-phones',
                'parent_slug' => 'electronics',
            ],
            [
                'name' => 'Laptops & Computers',
                'name_ar' => 'اللابتوب وأجهزة الكمبيوتر',
                'slug' => 'laptops-computers',
                'parent_slug' => 'electronics',
            ],
            [
                'name' => 'Televisions',
                'name_ar' => 'التلفزيونات',
                'slug' => 'televisions',
                'parent_slug' => 'electronics',
            ],
            [
                'name' => 'Headphones & Speakers',
                'name_ar' => 'السماعات ومكبرات الصوت',
                'slug' => 'headphones-speakers',
                'parent_slug' => 'electronics',
            ],
            [
                'name' => 'Cameras',
                'name_ar' => 'الكاميرات',
                'slug' => 'cameras',
                'parent_slug' => 'electronics',
            ],

            // Fashion
            [
                'name' => 'Men Clothing',
                'name_ar' => 'ملابس رجالي',
                'slug' => 'men-clothing',
                'parent_slug' => 'fashion',
            ],
            [
                'name' => 'Women Clothing',
                'name_ar' => 'ملابس حريمي',
                'slug' => 'women-clothing',
                'parent_slug' => 'fashion',
            ],
            [
                'name' => 'Shoes',
                'name_ar' => 'الأحذية',
                'slug' => 'shoes',
                'parent_slug' => 'fashion',
            ],
            [
                'name' => 'Bags',
                'name_ar' => 'الحقائب',
                'slug' => 'bags',
                'parent_slug' => 'fashion',
            ],
            [
                'name' => 'Watches & Accessories',
                'name_ar' => 'الساعات والإكسسوارات',
                'slug' => 'watches-accessories',
                'parent_slug' => 'fashion',
            ],

            // Home & Living
            [
                'name' => 'Furniture',
                'name_ar' => 'الأثاث',
                'slug' => 'furniture',
                'parent_slug' => 'home-living',
            ],
            [
                'name' => 'Home Decor',
                'name_ar' => 'ديكورات المنزل',
                'slug' => 'home-decor',
                'parent_slug' => 'home-living',
            ],
            [
                'name' => 'Kitchen & Dining',
                'name_ar' => 'المطبخ وأدوات الطعام',
                'slug' => 'kitchen-dining',
                'parent_slug' => 'home-living',
            ],
            [
                'name' => 'Bedding & Bath',
                'name_ar' => 'المفروشات ومستلزمات الحمام',
                'slug' => 'bedding-bath',
                'parent_slug' => 'home-living',
            ],

            // Beauty
            [
                'name' => 'Skincare',
                'name_ar' => 'العناية بالبشرة',
                'slug' => 'skincare',
                'parent_slug' => 'beauty',
            ],
            [
                'name' => 'Makeup',
                'name_ar' => 'مستحضرات التجميل',
                'slug' => 'makeup',
                'parent_slug' => 'beauty',
            ],
            [
                'name' => 'Perfumes',
                'name_ar' => 'العطور',
                'slug' => 'perfumes',
                'parent_slug' => 'beauty',
            ],
            [
                'name' => 'Hair Care',
                'name_ar' => 'العناية بالشعر',
                'slug' => 'hair-care',
                'parent_slug' => 'beauty',
            ],

            // Sports
            [
                'name' => 'Fitness Equipment',
                'name_ar' => 'معدات اللياقة البدنية',
                'slug' => 'fitness-equipment',
                'parent_slug' => 'sports',
            ],
            [
                'name' => 'Sportswear',
                'name_ar' => 'ملابس رياضية',
                'slug' => 'sportswear',
                'parent_slug' => 'sports',
            ],
            [
                'name' => 'Outdoor & Camping',
                'name_ar' => 'التخييم والأنشطة الخارجية',
                'slug' => 'outdoor-camping',
                'parent_slug' => 'sports',
            ],
        ];

        foreach ($subCategories as $data) {
            $parent = Category::where('slug', $data['parent_slug'])
                ->whereNull('parent_id')
                ->first();

            if (!$parent) {
                throw new \RuntimeException(
                    "Parent category '{$data['parent_slug']}' was not found. Run CategorySeeder first."
                );
            }

            Category::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'name_ar' => $data['name_ar'],
                    'parent_id' => $parent->id,
                    'image' => null,
                ]
            );
        }
    }
}
