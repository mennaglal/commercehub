<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(

            [
                'email' => 'admin@commercehub.com',
            ],

            [
                'name' => 'Super Admin',
                'phone' => '01000000000',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]

        );

        $admin->syncRoles('super-admin');
    }
}
