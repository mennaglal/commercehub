<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = User::role('vendor')
            ->whereDoesntHave('store')
            ->get();

        if ($vendors->isEmpty()) {
            $this->command->info('All vendors already have stores. Skipping.');

            return;
        }

        $storeNames = [
            'Fashion House',
            'Tech Zone',
            'Home Style',
            'Beauty Corner',
            'Kids World',
            'Smart Gadgets',
            'Elegant Store',
            'Daily Needs',
            'Modern Living',
            'Trend Market',
        ];

        $descriptions = [
            'Your destination for quality products and great prices.',
            'Discover our latest collection of carefully selected products.',
            'We provide quality products with excellent customer service.',
            'Shop your favorite products from our exclusive collection.',
            'Bringing you the best products for your everyday needs.',
        ];

        DB::transaction(function () use (
            $vendors,
            $storeNames,
            $descriptions
        ) {
            foreach ($vendors->take(count($storeNames)) as $index => $vendor) {
                $name = $storeNames[$index];

                DB::table('stores')->insert([
                    'user_id' => $vendor->id,
                    'name' => $name,
                    'slug' => Str::slug($name) . '-' . Str::lower(Str::random(5)),
                    'logo' => null,
                    'banner' => null,
                    'description' => fake()->randomElement($descriptions),
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        $this->command->info(
            min($vendors->count(), count($storeNames))
            . ' sample stores created successfully.'
        );
    }
}
