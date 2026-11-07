<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = User::role('vendor')->get();

        // Create sample vendors if none exist.
        if ($vendors->isEmpty()) {

            $sampleVendors = [
                'Ahmed Mohamed',
                'Sara Ali',
                'Omar Hassan',
                'Mariam Khaled',
                'Youssef Adel',
                'Farida Mohamed',
                'Mostafa Gamal',
                'Mia Anas'
            ];

            foreach ($sampleVendors as $name) {
                $vendor = User::factory()->create([
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]);

                $vendor->assignRole('vendor');

                $vendors->push($vendor);
            }
        }

        $createdStores = 0;

        foreach ($vendors as $vendor) {

            if ($vendor->store()->exists()) {
                continue;
            }

            Store::factory()->create([
                'user_id' => $vendor->id,
            ]);

            $createdStores++;
        }

        $this->command->info(
            "{$createdStores} stores created successfully."
        );
    }
}
