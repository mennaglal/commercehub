<?php

namespace Database\Seeders;

use App\Models\VariantType;
use App\Models\VariantValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VariantSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $variants = [
                [
                    'name_ar' => 'المقاس',
                    'name_en' => 'Size',
                    'input_type' => 'text',
                    'values' => [
                        ['value_ar' => 'صغير', 'value_en' => 'S'],
                        ['value_ar' => 'متوسط', 'value_en' => 'M'],
                        ['value_ar' => 'كبير', 'value_en' => 'L'],
                        ['value_ar' => 'كبير جداً', 'value_en' => 'XL'],
                        ['value_ar' => 'كبير جداً جداً', 'value_en' => 'XXL'],
                    ],
                ],
                [
                    'name_ar' => 'اللون',
                    'name_en' => 'Color',
                    'input_type' => 'color',
                    'values' => [
                        [
                            'value_ar' => 'أحمر',
                            'value_en' => 'Red',
                            'color_code' => '#FF0000',
                        ],
                        [
                            'value_ar' => 'أزرق',
                            'value_en' => 'Blue',
                            'color_code' => '#0000FF',
                        ],
                        [
                            'value_ar' => 'أسود',
                            'value_en' => 'Black',
                            'color_code' => '#000000',
                        ],
                        [
                            'value_ar' => 'أبيض',
                            'value_en' => 'White',
                            'color_code' => '#FFFFFF',
                        ],
                        [
                            'value_ar' => 'أخضر',
                            'value_en' => 'Green',
                            'color_code' => '#008000',
                        ],
                    ],
                ],
                [
                    'name_ar' => 'الوزن',
                    'name_en' => 'Weight',
                    'input_type' => 'text',
                    'values' => [
                        ['value_ar' => '250 جرام', 'value_en' => '250g'],
                        ['value_ar' => '500 جرام', 'value_en' => '500g'],
                        ['value_ar' => '1 كيلو', 'value_en' => '1kg'],
                        ['value_ar' => '2 كيلو', 'value_en' => '2kg'],
                        ['value_ar' => '5 كيلو', 'value_en' => '5kg'],
                    ],
                ],
                [
                    'name_ar' => 'الخامة',
                    'name_en' => 'Material',
                    'input_type' => 'text',
                    'values' => [
                        ['value_ar' => 'قطن', 'value_en' => 'Cotton'],
                        ['value_ar' => 'بوليستر', 'value_en' => 'Polyester'],
                        ['value_ar' => 'جلد طبيعي', 'value_en' => 'Genuine Leather'],
                        ['value_ar' => 'جلد صناعي', 'value_en' => 'Faux Leather'],
                        ['value_ar' => 'ستانلس ستيل', 'value_en' => 'Stainless Steel'],
                        ['value_ar' => 'خشب', 'value_en' => 'Wood'],
                        ['value_ar' => 'بلاستيك', 'value_en' => 'Plastic'],
                    ],
                ],
            ];

            foreach ($variants as $variantData) {
                $values = $variantData['values'];

                $variantType = VariantType::updateOrCreate(
                    ['name_en' => $variantData['name_en']],
                    [
                        'name_ar' => $variantData['name_ar'],
                        'input_type' => $variantData['input_type'],
                        'status' => true,
                    ]
                );

                foreach ($values as $valueData) {
                    VariantValue::updateOrCreate(
                        [
                            'variant_type_id' => $variantType->id,
                            'value_en' => $valueData['value_en'],
                        ],
                        [
                            'value_ar' => $valueData['value_ar'],
                            'color_code' => $valueData['color_code'] ?? null,
                            'status' => true,
                        ]
                    );
                }
            }
        });
    }
}
