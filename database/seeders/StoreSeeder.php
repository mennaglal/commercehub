<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\User;
use App\Models\Country;
use App\Models\City;
use App\Models\Area;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $stores = [
            ['slug' => 'tech-zone', 'name' => 'Tech Zone', 'description' => 'Discover quality products at Tech Zone.', 'logo' => 'images/demo/stores/tech-zone-logo.svg', 'banner' => 'images/demo/stores/tech-zone-banner.jpg'],
            ['slug' => 'urban-style', 'name' => 'Urban Style', 'description' => 'Discover quality products at Urban Style.', 'logo' => 'images/demo/stores/urban-style-logo.svg', 'banner' => 'images/demo/stores/urban-style-banner.jpg'],
            ['slug' => 'home-avenue', 'name' => 'Home Avenue', 'description' => 'Discover quality products at Home Avenue.', 'logo' => 'images/demo/stores/home-avenue-logo.svg', 'banner' => 'images/demo/stores/home-avenue-banner.jpg'],
            ['slug' => 'glow-beauty', 'name' => 'Glow Beauty', 'description' => 'Discover quality products at Glow Beauty.', 'logo' => 'images/demo/stores/glow-beauty-logo.svg', 'banner' => 'images/demo/stores/glow-beauty-banner.jpg'],
            ['slug' => 'active-life', 'name' => 'Active Life', 'description' => 'Discover quality products at Active Life.', 'logo' => 'images/demo/stores/active-life-logo.svg', 'banner' => 'images/demo/stores/active-life-banner.jpg'],
            ['slug' => 'little-world', 'name' => 'Little World', 'description' => 'Discover quality products at Little World.', 'logo' => 'images/demo/stores/little-world-logo.svg', 'banner' => 'images/demo/stores/little-world-banner.jpg'],
            ['slug' => 'time-luxe', 'name' => 'Time Luxe', 'description' => 'Discover quality products at Time Luxe.', 'logo' => 'images/demo/stores/time-luxe-logo.svg', 'banner' => 'images/demo/stores/time-luxe-banner.jpg'],
            ['slug' => 'paper-trail', 'name' => 'Paper Trail', 'description' => 'Discover quality products at Paper Trail.', 'logo' => 'images/demo/stores/paper-trail-logo.svg', 'banner' => 'images/demo/stores/paper-trail-banner.jpg'],
            ['slug' => 'pet-corner', 'name' => 'Pet Corner', 'description' => 'Discover quality products at Pet Corner.', 'logo' => 'images/demo/stores/pet-corner-logo.svg', 'banner' => 'images/demo/stores/pet-corner-banner.jpg'],
            ['slug' => 'auto-hub', 'name' => 'Auto Hub', 'description' => 'Discover quality products at Auto Hub.', 'logo' => 'images/demo/stores/auto-hub-logo.svg', 'banner' => 'images/demo/stores/auto-hub-banner.jpg'],
            ['slug' => 'daily-choice', 'name' => 'Daily Choice', 'description' => 'Discover quality products at Daily Choice.', 'logo' => 'images/demo/stores/daily-choice-logo.svg', 'banner' => 'images/demo/stores/daily-choice-banner.jpg'],
            ['slug' => 'travel-mate', 'name' => 'Travel Mate', 'description' => 'Discover quality products at Travel Mate.', 'logo' => 'images/demo/stores/travel-mate-logo.svg', 'banner' => 'images/demo/stores/travel-mate-banner.jpg'],
        ];

        // Get locations with their relationships.
        $areas = Area::with('city.country')->get();

        if ($areas->isEmpty()) {
            $this->command->error('No areas found. Please seed countries, cities and areas first.');
            return;
        }

        $vendors = User::role('vendor')->get();

        // Create enough demo vendor accounts.
        while ($vendors->count() < count($stores)) {
            $number = $vendors->count() + 1;
            $email = "demo.vendor{$number}@example.com";

            $vendor = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => "Demo Vendor {$number}",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            if (! $vendor->hasRole('vendor')) {
                $vendor->assignRole('vendor');
            }

            $vendors->push($vendor);
        }

        foreach ($stores as $index => $data) {
            $vendor = $vendors->values()->get($index);

            // Distribute stores across available areas.
            $area = $areas->get($index % $areas->count());

            Store::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'user_id' => $vendor->id,
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'logo' => $data['logo'],
                    'banner' => $data['banner'],
                    'country_id' => $area->city->country_id,
                    'city_id' => $area->city_id,
                    'area_id' => $area->id,
                    'status' => fake()->boolean(),
                ]
            );
        }

        $this->command->info('12 demo stores seeded successfully.');
    }
}
