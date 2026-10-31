<?php

namespace Database\Seeders;

use App\Models\Blogger;
use Illuminate\Database\Seeder;

class BloggerSeeder extends Seeder
{
    public function run(): void
    {
        $bloggers = [
            ['name' => 'Sara Ahmed', 'phone' => '01000000001', 'email' => 'sara@example.com', 'status' => true],
            ['name' => 'Omar Hassan', 'phone' => '01000000002', 'email' => 'omar@example.com', 'status' => true],
            // Inactive blogger: its coupons must be rejected at checkout (good for testing).
            ['name' => 'Nour Ali', 'phone' => '01000000003', 'email' => 'nour@example.com', 'status' => false],
        ];

        // updateOrCreate by email: running the seeder again does not duplicate the bloggers.
        foreach ($bloggers as $data) {
            Blogger::updateOrCreate(['email' => $data['email']], $data);
        }
    }
}
