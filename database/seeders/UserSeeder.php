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
        // Make sure the vendor role exists.
        Role::findOrCreate('vendor', 'web');

        $vendors = [
            [
                'name' => 'Ahmed Mohamed',
                'email' => 'vendor1@example.com',
                'phone' => '01000000001',
            ],
            [
                'name' => 'Mohamed Ali',
                'email' => 'vendor2@example.com',
                'phone' => '01000000002',
            ],
            [
                'name' => 'Omar Hassan',
                'email' => 'vendor3@example.com',
                'phone' => '01000000003',
            ],
            [
                'name' => 'Sara Ahmed',
                'email' => 'vendor4@example.com',
                'phone' => '01000000004',
            ],
            [
                'name' => 'Mona Mahmoud',
                'email' => 'vendor5@example.com',
                'phone' => '01000000005',
            ],
            [
                'name' => 'Youssef Ibrahim',
                'email' => 'vendor6@example.com',
                'phone' => '01000000006',
            ],
            [
                'name' => 'Nour Khaled',
                'email' => 'vendor7@example.com',
                'phone' => '01000000007',
            ],
            [
                'name' => 'Hassan Mostafa',
                'email' => 'vendor8@example.com',
                'phone' => '01000000008',
            ],
            [
                'name' => 'Mariam Adel',
                'email' => 'vendor9@example.com',
                'phone' => '01000000009',
            ],
            [
                'name' => 'Ali Samir',
                'email' => 'vendor10@example.com',
                'phone' => '01000000010',
            ],
        ];

        foreach ($vendors as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => Hash::make('password123'),
                    'status' => true,
                ]
            );

            if (!$user->hasRole('vendor')) {
                $user->assignRole('vendor');
            }
        }

        $this->command->info('10 vendor users created successfully.');
    }
}
