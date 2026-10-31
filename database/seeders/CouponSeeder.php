<?php

namespace Database\Seeders;

use App\Models\Blogger;
use App\Models\Category;
use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        // The coupons need bloggers and categories.
        $this->call(BloggerSeeder::class);

        $bloggers = Blogger::pluck('id', 'email');
        $categories = Category::whereNull('parent_id')->orderBy('id')->pluck('id')->values();

        if ($categories->isEmpty()) {
            $this->command?->warn('No categories found. Create categories first, then run this seeder again.');

            return;
        }

        // "categories" = positions in the list of main categories (0 = first category).
        // A position that does not exist falls back to the first category.
        $coupons = [
            // ----- General coupons -----
            [
                'code' => 'WELCOME10', 'type' => 'percentage', 'value' => 10,
                'max_discount' => 100, 'min_order_amount' => 200,
                'categories' => [0],
            ],
            [
                'code' => 'SAVE50', 'type' => 'fixed', 'value' => 50,
                'min_order_amount' => 300, 'usage_limit' => 100,
                'categories' => [0, 1],
            ],

            // ----- Blogger coupons -----
            [
                'code' => 'SARA15', 'type' => 'percentage', 'value' => 15, 'max_discount' => 150,
                'blogger' => 'sara@example.com', 'commission_type' => 'percentage', 'commission_value' => 5,
                'categories' => [0],
            ],
            [
                'code' => 'OMAR100', 'type' => 'fixed', 'value' => 100, 'usage_limit' => 50,
                'blogger' => 'omar@example.com', 'commission_type' => 'fixed', 'commission_value' => 20,
                'categories' => [1],
            ],

            // ----- Coupons that must be rejected (to test every error message) -----
            [
                'code' => 'EXPIRED20', 'type' => 'percentage', 'value' => 20,
                'expires_at' => now()->subDay(),
                'categories' => [0],
            ],
            [
                'code' => 'SOON25', 'type' => 'percentage', 'value' => 25,
                'starts_at' => now()->addWeek(),
                'categories' => [0],
            ],
            [
                'code' => 'OFF30', 'type' => 'percentage', 'value' => 30, 'status' => false,
                'categories' => [0],
            ],
            [
                'code' => 'NOUR25', 'type' => 'percentage', 'value' => 25,   // its blogger is inactive
                'blogger' => 'nour@example.com', 'commission_type' => 'percentage', 'commission_value' => 10,
                'categories' => [0],
            ],
        ];

        foreach ($coupons as $data) {
            $categoryIds = collect($data['categories'])
                ->map(fn ($position) => $categories[$position] ?? $categories[0])
                ->unique()
                ->all();

            $email = $data['blogger'] ?? null;

            $attributes = [
                'type' => $data['type'],
                'value' => $data['value'],
                'max_discount' => $data['max_discount'] ?? null,
                'min_order_amount' => $data['min_order_amount'] ?? null,
                'usage_limit' => $data['usage_limit'] ?? null,
                'starts_at' => $data['starts_at'] ?? null,
                'expires_at' => $data['expires_at'] ?? null,
                'status' => $data['status'] ?? true,
                'blogger_id' => $email ? $bloggers[$email] : null,
                'commission_type' => $data['commission_type'] ?? null,
                'commission_value' => $data['commission_value'] ?? null,
            ];

            // updateOrCreate by code: running the seeder again does not duplicate the coupons.
            $coupon = Coupon::updateOrCreate(['code' => $data['code']], $attributes);
            $coupon->categories()->sync($categoryIds);
        }
    }
}
