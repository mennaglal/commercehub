<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'name_ar' => 'إلكترونيات', 'slug' => 'electronics', 'image' => 'images/demo/categories/electronics.jpg', 'children' => [
                ['name' => 'Phones & Accessories', 'name_ar' => 'الهواتف وملحقاتها', 'slug' => 'phones-accessories'],
                ['name' => 'Computers & Laptops', 'name_ar' => 'الكمبيوتر واللابتوب', 'slug' => 'computers-laptops'],
                ['name' => 'Audio', 'name_ar' => 'الصوتيات', 'slug' => 'audio'],
                ['name' => 'Gaming', 'name_ar' => 'الألعاب الإلكترونية', 'slug' => 'gaming'],
                ['name' => 'Smart Devices', 'name_ar' => 'الأجهزة الذكية', 'slug' => 'smart-devices'],
            ]],
            ['name' => 'Fashion', 'name_ar' => 'الأزياء', 'slug' => 'fashion', 'image' => 'images/demo/categories/fashion.jpg', 'children' => [
                ['name' => 'Men Clothing', 'name_ar' => 'ملابس رجالية', 'slug' => 'men-clothing'],
                ['name' => 'Women Clothing', 'name_ar' => 'ملابس نسائية', 'slug' => 'women-clothing'],
                ['name' => 'Shoes', 'name_ar' => 'الأحذية', 'slug' => 'shoes'],
                ['name' => 'Bags', 'name_ar' => 'الحقائب', 'slug' => 'bags'],
                ['name' => 'Accessories', 'name_ar' => 'الإكسسوارات', 'slug' => 'fashion-accessories'],
            ]],
            ['name' => 'Home & Living', 'name_ar' => 'المنزل والمعيشة', 'slug' => 'home-living', 'image' => 'images/demo/categories/home-living.jpg', 'children' => [
                ['name' => 'Furniture', 'name_ar' => 'الأثاث', 'slug' => 'furniture'],
                ['name' => 'Kitchen & Dining', 'name_ar' => 'المطبخ والسفرة', 'slug' => 'kitchen-dining'],
                ['name' => 'Home Decor', 'name_ar' => 'ديكور المنزل', 'slug' => 'home-decor'],
                ['name' => 'Bedding', 'name_ar' => 'المفروشات', 'slug' => 'bedding'],
                ['name' => 'Lighting', 'name_ar' => 'الإضاءة', 'slug' => 'lighting'],
            ]],
            ['name' => 'Beauty', 'name_ar' => 'الجمال والعناية', 'slug' => 'beauty', 'image' => 'images/demo/categories/beauty.jpg', 'children' => [
                ['name' => 'Skincare', 'name_ar' => 'العناية بالبشرة', 'slug' => 'skincare'],
                ['name' => 'Haircare', 'name_ar' => 'العناية بالشعر', 'slug' => 'haircare'],
                ['name' => 'Makeup', 'name_ar' => 'المكياج', 'slug' => 'makeup'],
                ['name' => 'Perfumes', 'name_ar' => 'العطور', 'slug' => 'perfumes'],
                ['name' => 'Personal Care', 'name_ar' => 'العناية الشخصية', 'slug' => 'personal-care'],
            ]],
            ['name' => 'Sports', 'name_ar' => 'الرياضة', 'slug' => 'sports', 'image' => 'images/demo/categories/sports.jpg', 'children' => [
                ['name' => 'Fitness Equipment', 'name_ar' => 'معدات اللياقة', 'slug' => 'fitness-equipment'],
                ['name' => 'Sportswear', 'name_ar' => 'ملابس رياضية', 'slug' => 'sportswear'],
                ['name' => 'Outdoor', 'name_ar' => 'الأنشطة الخارجية', 'slug' => 'outdoor'],
                ['name' => 'Team Sports', 'name_ar' => 'رياضات جماعية', 'slug' => 'team-sports'],
                ['name' => 'Yoga', 'name_ar' => 'اليوغا', 'slug' => 'yoga'],
            ]],
            ['name' => 'Kids & Baby', 'name_ar' => 'الأطفال والرضع', 'slug' => 'kids-baby', 'image' => 'images/demo/categories/kids-baby.jpg', 'children' => [
                ['name' => 'Toys', 'name_ar' => 'الألعاب', 'slug' => 'toys'],
                ['name' => 'Kids Clothing', 'name_ar' => 'ملابس الأطفال', 'slug' => 'kids-clothing'],
                ['name' => 'Baby Care', 'name_ar' => 'العناية بالرضع', 'slug' => 'baby-care'],
                ['name' => 'School Supplies', 'name_ar' => 'مستلزمات المدرسة', 'slug' => 'school-supplies'],
                ['name' => 'Strollers', 'name_ar' => 'عربات الأطفال', 'slug' => 'strollers'],
            ]],
            ['name' => 'Watches & Jewelry', 'name_ar' => 'الساعات والمجوهرات', 'slug' => 'watches-jewelry', 'image' => 'images/demo/categories/watches-jewelry.jpg', 'children' => [
                ['name' => 'Watches', 'name_ar' => 'الساعات', 'slug' => 'watches'],
                ['name' => 'Jewelry', 'name_ar' => 'المجوهرات', 'slug' => 'jewelry'],
                ['name' => 'Sunglasses', 'name_ar' => 'النظارات الشمسية', 'slug' => 'sunglasses'],
                ['name' => 'Fine Accessories', 'name_ar' => 'إكسسوارات فاخرة', 'slug' => 'fine-accessories'],
            ]],
            ['name' => 'Office & Stationery', 'name_ar' => 'المكتب والقرطاسية', 'slug' => 'office-stationery', 'image' => 'images/demo/categories/office-stationery.jpg', 'children' => [
                ['name' => 'Office Supplies', 'name_ar' => 'مستلزمات المكتب', 'slug' => 'office-supplies'],
                ['name' => 'Notebooks', 'name_ar' => 'الكراسات والدفاتر', 'slug' => 'notebooks'],
                ['name' => 'Writing Tools', 'name_ar' => 'أدوات الكتابة', 'slug' => 'writing-tools'],
                ['name' => 'Desk Accessories', 'name_ar' => 'إكسسوارات المكتب', 'slug' => 'desk-accessories'],
            ]],
            ['name' => 'Pets', 'name_ar' => 'الحيوانات الأليفة', 'slug' => 'pets', 'image' => 'images/demo/categories/pets.jpg', 'children' => [
                ['name' => 'Pet Food', 'name_ar' => 'طعام الحيوانات', 'slug' => 'pet-food'],
                ['name' => 'Pet Toys', 'name_ar' => 'ألعاب الحيوانات', 'slug' => 'pet-toys'],
                ['name' => 'Beds & Furniture', 'name_ar' => 'الأسرّة والأثاث', 'slug' => 'pet-beds'],
                ['name' => 'Pet Accessories', 'name_ar' => 'إكسسوارات الحيوانات', 'slug' => 'pet-accessories'],
            ]],
            ['name' => 'Automotive', 'name_ar' => 'السيارات', 'slug' => 'automotive', 'image' => 'images/demo/categories/automotive.jpg', 'children' => [
                ['name' => 'Car Accessories', 'name_ar' => 'إكسسوارات السيارات', 'slug' => 'car-accessories'],
                ['name' => 'Car Care', 'name_ar' => 'العناية بالسيارة', 'slug' => 'car-care'],
                ['name' => 'Tools', 'name_ar' => 'الأدوات', 'slug' => 'auto-tools'],
                ['name' => 'Interior Accessories', 'name_ar' => 'إكسسوارات داخلية', 'slug' => 'interior-accessories'],
            ]],
            ['name' => 'Food & Beverages', 'name_ar' => 'الأطعمة والمشروبات', 'slug' => 'food-beverages', 'image' => 'images/demo/categories/food-beverages.jpg', 'children' => [
                ['name' => 'Coffee & Tea', 'name_ar' => 'القهوة والشاي', 'slug' => 'coffee-tea'],
                ['name' => 'Snacks', 'name_ar' => 'الوجبات الخفيفة', 'slug' => 'snacks'],
                ['name' => 'Pantry', 'name_ar' => 'منتجات البقالة', 'slug' => 'pantry'],
                ['name' => 'Healthy Food', 'name_ar' => 'الأطعمة الصحية', 'slug' => 'healthy-food'],
            ]],
            ['name' => 'Travel', 'name_ar' => 'السفر', 'slug' => 'travel', 'image' => 'images/demo/categories/travel.jpg', 'children' => [
                ['name' => 'Luggage', 'name_ar' => 'حقائب السفر', 'slug' => 'luggage'],
                ['name' => 'Travel Bags', 'name_ar' => 'حقائب الرحلات', 'slug' => 'travel-bags'],
                ['name' => 'Travel Accessories', 'name_ar' => 'إكسسوارات السفر', 'slug' => 'travel-accessories'],
                ['name' => 'Organizers', 'name_ar' => 'منظمات السفر', 'slug' => 'travel-organizers'],
            ]],
        ];

        foreach ($categories as $data) {
            $parent = Category::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'name_ar' => $data['name_ar'],
                    'parent_id' => null,
                    'image' => $data['image'],
                ]
            );

            foreach ($data['children'] as $child) {
                Category::updateOrCreate(
                    ['slug' => $child['slug']],
                    [
                        'name' => $child['name'],
                        'name_ar' => $child['name_ar'],
                        'parent_id' => $parent->id,
                        'image' => $data['image'],
                    ]
                );
            }
        }

        $this->command->info('12 main categories and their subcategories seeded successfully.');
    }
}
