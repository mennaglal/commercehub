<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PagesSeeder extends Seeder
{
    public function run()
    {
        $pages = [
            [
                'slug' => 'about-us',
                'title_ar' => 'من نحن',
                'title_en' => 'About Us',
            ],
            [
                'slug' => 'privacy-policy',
                'title_ar' => 'سياسة الخصوصية',
                'title_en' => 'Privacy Policy',
            ],
            [
                'slug' => 'return-policy',
                'title_ar' => 'سياسة الاسترجاع',
                'title_en' => 'Return Policy',
            ],
            [
                'slug' => 'terms-and-conditions',
                'title_ar' => 'الشروط والأحكام',
                'title_en' => 'Terms & Conditions',
            ],
            [
                'slug' => 'contact-us',
                'title_ar' => 'تواصل معنا',
                'title_en' => 'Contact Us',
            ],
        ];

        foreach ($pages as $page) {
            Page::firstOrCreate(
                ['slug' => $page['slug']],
                array_merge($page, [
                    'is_active' => true,
                ])
            );
        }

        Permission::firstOrCreate([
            'name' => 'pages.index',
            'guard_name' => 'web',
        ]);

        Permission::firstOrCreate([
            'name' => 'pages.update',
            'guard_name' => 'web',
        ]);
    }
}
