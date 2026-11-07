<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample customers.
        User::factory()
            ->count(20)
            ->create()
            ->each(function ($customer) {
                $customer->assignRole('customer');
            });

        $this->command->info('20 sample customers created successfully.');
    }
}
