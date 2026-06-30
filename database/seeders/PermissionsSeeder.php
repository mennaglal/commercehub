<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [

            'admins' => [
                'view',
                'create',
                'update',
                'delete',
            ],

            'users' => [
                'view',
                'create',
                'update',
                'delete',
                'suspend',
            ],

            'vendors' => [
                'view',
                'approve',
                'reject',
                'suspend',
            ],

            'categories' => [
                'view',
                'create',
                'update',
                'delete',
            ],

            'products' => [
                'view',
                'create',
                'update',
                'delete',
                'approve',
                'feature',
            ],

            'orders' => [
                'view',
                'update',
                'refund',
            ],

            'coupons' => [
                'view',
                'create',
                'update',
                'delete',
            ],

            'notifications' => [
                'view',
                'send',
            ],

            'settings' => [
                'view',
                'update',
            ],

            'roles' => [
                'view',
                'create',
                'update',
                'delete',
            ],

            'permissions' => [
                'view',
                'assign',
            ],

        ];

        foreach ($modules as $module => $actions) {

            foreach ($actions as $action) {

                Permission::firstOrCreate([
                    'name' => "{$module}.{$action}",
                    'guard_name' => 'web',
                ]);

            }

        }
    }
}
