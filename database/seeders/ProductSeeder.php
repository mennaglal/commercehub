<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $stores = DB::table('stores')->pluck('id');
        $categories = DB::table('categories')->pluck('id');

        if ($stores->isEmpty()) {
            throw new RuntimeException(
                'No stores found. Please seed stores first.'
            );
        }

        if ($categories->isEmpty()) {
            throw new RuntimeException(
                'No categories found. Please seed categories first.'
            );
        }

        $products = [
            ['name_ar' => 'تيشيرت قطني رجالي', 'name_en' => 'Men Cotton T-Shirt', 'price' => 350],
            ['name_ar' => 'قميص رجالي كلاسيك', 'name_en' => 'Classic Men Shirt', 'price' => 650],
            ['name_ar' => 'بنطلون جينز', 'name_en' => 'Jeans Pants', 'price' => 800],
            ['name_ar' => 'فستان صيفي', 'name_en' => 'Summer Dress', 'price' => 950],
            ['name_ar' => 'جاكيت شتوي', 'name_en' => 'Winter Jacket', 'price' => 1500],
            ['name_ar' => 'حذاء رياضي', 'name_en' => 'Sports Shoes', 'price' => 1200],
            ['name_ar' => 'حقيبة يد نسائية', 'name_en' => 'Women Handbag', 'price' => 900],
            ['name_ar' => 'ساعة يد', 'name_en' => 'Wrist Watch', 'price' => 1100],
            ['name_ar' => 'نظارة شمسية', 'name_en' => 'Sunglasses', 'price' => 450],
            ['name_ar' => 'محفظة جلد', 'name_en' => 'Leather Wallet', 'price' => 300],
            ['name_ar' => 'سماعة بلوتوث', 'name_en' => 'Bluetooth Headphones', 'price' => 750],
            ['name_ar' => 'ماوس لاسلكي', 'name_en' => 'Wireless Mouse', 'price' => 400],
            ['name_ar' => 'كيبورد ميكانيكي', 'name_en' => 'Mechanical Keyboard', 'price' => 1800],
            ['name_ar' => 'حامل لابتوب', 'name_en' => 'Laptop Stand', 'price' => 550],
            ['name_ar' => 'شاحن سريع', 'name_en' => 'Fast Charger', 'price' => 250],
            ['name_ar' => 'كوب قهوة سيراميك', 'name_en' => 'Ceramic Coffee Mug', 'price' => 180],
            ['name_ar' => 'زجاجة مياه', 'name_en' => 'Water Bottle', 'price' => 220],
            ['name_ar' => 'طقم ملايات', 'name_en' => 'Bed Sheet Set', 'price' => 850],
            ['name_ar' => 'وسادة نوم', 'name_en' => 'Sleeping Pillow', 'price' => 400],
            ['name_ar' => 'مصباح مكتبي', 'name_en' => 'Desk Lamp', 'price' => 600],
            ['name_ar' => 'طقم عناية بالبشرة', 'name_en' => 'Skin Care Set', 'price' => 700],
            ['name_ar' => 'مرطب وجه', 'name_en' => 'Face Moisturizer', 'price' => 320],
            ['name_ar' => 'شامبو للشعر', 'name_en' => 'Hair Shampoo', 'price' => 190],
            ['name_ar' => 'عطر نسائي', 'name_en' => 'Women Perfume', 'price' => 1300],
            ['name_ar' => 'عطر رجالي', 'name_en' => 'Men Perfume', 'price' => 1450],
            ['name_ar' => 'لعبة أطفال تعليمية', 'name_en' => 'Educational Toy', 'price' => 500],
            ['name_ar' => 'دبدوب أطفال', 'name_en' => 'Teddy Bear', 'price' => 350],
            ['name_ar' => 'حقيبة ظهر', 'name_en' => 'Backpack', 'price' => 650],
            ['name_ar' => 'حافظة موبايل', 'name_en' => 'Phone Case', 'price' => 150],
            ['name_ar' => 'سجادة ديكور', 'name_en' => 'Decorative Rug', 'price' => 1000],
        ];

        DB::transaction(function () use ($products, $stores, $categories) {
            foreach ($products as $data) {
                $price = $data['price'];

                $hasSale = fake()->boolean(35);
                $salePrice = $hasSale
                    ? round($price * fake()->randomFloat(2, 0.70, 0.90), 2)
                    : null;

                $hasVariants = fake()->boolean(40);

                $productId = DB::table('products')->insertGetId([
                    'store_id' => $stores->random(),
                    'category_id' => $categories->random(),

                    'name' => $data['name_en'],
                    'name_ar' => $data['name_ar'],
                    'name_en' => $data['name_en'],

                    'slug' => Str::slug($data['name_en'])
                        . '-' . Str::lower(Str::random(6)),

                    'description' => fake()->paragraph(),
                    'description_ar' => 'وصف تجريبي للمنتج ' . $data['name_ar'],
                    'description_en' => 'Sample description for ' . $data['name_en'],

                    'price' => $price,
                    'sale_price' => $salePrice,
                    'is_sale' => $hasSale,

                    'stock' => $hasVariants ? 0 : rand(5, 100),
                    'sku' => 'SKU-' . Str::upper(Str::random(10)),

                    'image' => null,
                    'status' => 1,
                    'has_variants' => $hasVariants,

                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($hasVariants) {
                    $variantCount = rand(2, 4);

                    for ($i = 1; $i <= $variantCount; $i++) {
                        $variantPrice = $price + (($i - 1) * 50);
                        $variantDiscount = fake()->boolean(25)
                            ? round($variantPrice * 0.85, 2)
                            : null;

                        DB::table('product_variants')->insert([
                            'product_id' => $productId,
                            'sku' => 'VAR-' . Str::upper(Str::random(10)),
                            'price' => $variantPrice,
                            'discount_price' => $variantDiscount,
                            'quantity' => rand(5, 50),
                            'status' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        });

        $this->command->info('30 sample products created successfully.');
    }
}
