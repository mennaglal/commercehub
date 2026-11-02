<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
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

        // Load active variant types and their active values.
        $variantTypes = DB::table('variant_types')
            ->where('status', true)
            ->get()
            ->keyBy('name_en');

        $variantValues = DB::table('variant_values')
            ->where('status', true)
            ->get()
            ->groupBy('variant_type_id');

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

        DB::transaction(function () use (
            $products,
            $stores,
            $categories,
            $variantTypes,
            $variantValues
        ) {
            foreach ($products as $data) {
                $price = $data['price'];

                // Determine whether this product supports variants.
                $typeNames = $this->getVariantTypeNames(
                    $data['name_en']
                );

                $hasVariants = ! empty($typeNames)
                    && fake()->boolean(40);

                $hasSale = fake()->boolean(35);

                $salePrice = $hasSale
                    ? round($price * fake()->randomFloat(2, 0.70, 0.90), 2)
                    : null;

                // Products with variants get their price and stock
                // calculated from their variants below.
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

                    'price' => $hasVariants ? 0 : $price,
                    'sale_price' => $hasVariants ? null : $salePrice,
                    'is_sale' => $hasVariants ? false : $hasSale,

                    'stock' => $hasVariants ? 0 : rand(5, 100),
                    'sku' => 'SKU-' . Str::upper(Str::random(10)),

                    'image' => null,
                    'status' => 1,
                    'has_variants' => $hasVariants,

                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if (! $hasVariants) {
                    continue;
                }

                $product = Product::findOrFail($productId);

                // Resolve the types and their available values.
                $selectedTypes = collect($typeNames)
                    ->map(fn ($name) => $variantTypes->get($name))
                    ->filter()
                    ->filter(fn ($type) => $variantValues->has($type->id))
                    ->filter(fn ($type) => $variantValues->get($type->id)->isNotEmpty())
                    ->values();

                if ($selectedTypes->isEmpty()) {
                    throw new RuntimeException(
                        "No active variant types or values found for {$data['name_en']}."
                    );
                }

                if ($selectedTypes->count() !== count($typeNames)) {
                    throw new RuntimeException(
                        "Some required variant types or values are missing for {$data['name_en']}. "
                        . 'Please run VariantSeeder first.'
                    );
                }

                // Attach the types available for this product.
                $product->variantTypes()->sync(
                    $selectedTypes->pluck('id')->all()
                );

                // Generate every possible combination of selected values.
                $combinations = [[]];

                foreach ($selectedTypes as $type) {
                    $values = $variantValues
                        ->get($type->id)
                        ->pluck('id')
                        ->all();

                    $nextCombinations = [];

                    foreach ($combinations as $combination) {
                        foreach ($values as $valueId) {
                            $nextCombinations[] = array_merge(
                                $combination,
                                [$valueId]
                            );
                        }
                    }

                    $combinations = $nextCombinations;
                }

                // Pick between two and four different combinations.
                shuffle($combinations);

                $variantCount = min(
                    count($combinations),
                    rand(2, 4)
                );

                $combinations = array_slice(
                    $combinations,
                    0,
                    $variantCount
                );

                foreach ($combinations as $index => $valueIds) {
                    $variantPrice = $price + ($index * 50);

                    $variantDiscount = fake()->boolean(25)
                        ? round($variantPrice * 0.85, 2)
                        : null;

                    // Create the product variant.
                    $variant = new ProductVariant();

                    $variant->sku = 'VAR-' . Str::upper(Str::random(10));
                    $variant->price = $variantPrice;
                    $variant->discount_price = $variantDiscount;
                    $variant->quantity = rand(5, 50);
                    $variant->status = true;

                    $product->variants()->save($variant);

                    // Attach the selected values to this exact combination.
                    $variant->values()->sync($valueIds);
                }

                // Keep listing price and stock consistent with the variants.
                $product->update([
                    'price' => $product->variants()->min('price'),
                    'stock' => $product->variants()->sum('quantity'),
                ]);
            }
        });

        $this->command->info(
            '30 sample products created successfully with variant combinations.'
        );
    }

    /**
     * Return the variant types suitable for a product.
     */
    private function getVariantTypeNames(string $name): array
    {
        $name = strtolower($name);

        // Clothing
        if (
            str_contains($name, 't-shirt')
            || str_contains($name, 'shirt')
            || str_contains($name, 'jeans')
            || str_contains($name, 'dress')
            || str_contains($name, 'jacket')
        ) {
            return ['Size', 'Color', 'Material'];
        }

        // Shoes
        if (str_contains($name, 'shoes')) {
            return ['Size', 'Color', 'Material'];
        }

        // Bags and cases
        if (
            str_contains($name, 'handbag')
            || str_contains($name, 'wallet')
            || str_contains($name, 'backpack')
            || str_contains($name, 'phone case')
        ) {
            return ['Color', 'Material'];
        }

        // Accessories
        if (
            str_contains($name, 'watch')
            || str_contains($name, 'sunglasses')
        ) {
            return ['Color'];
        }

        // Electronics
        if (
            str_contains($name, 'headphones')
            || str_contains($name, 'mouse')
            || str_contains($name, 'keyboard')
            || str_contains($name, 'charger')
        ) {
            return ['Color'];
        }

        // Home and living
        if (
            str_contains($name, 'laptop stand')
            || str_contains($name, 'coffee mug')
            || str_contains($name, 'water bottle')
            || str_contains($name, 'bed sheet')
            || str_contains($name, 'pillow')
            || str_contains($name, 'desk lamp')
            || str_contains($name, 'rug')
        ) {
            return ['Color', 'Material'];
        }

        // Other products remain simple products.
        return [];
    }
}
