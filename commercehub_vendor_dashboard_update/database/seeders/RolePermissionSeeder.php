<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        $superAdmin = Role::findByName('super-admin');

        $superAdmin->syncPermissions(
            Permission::all()
        );

        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        $admin = Role::findByName('admin');

        $admin->syncPermissions([

            'admins.view',
            'admins.create',
            'admins.update',

            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.suspend',

            'vendors.view',
            'vendors.approve',
            'vendors.reject',

            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',

            'products.view',
            'products.create',
            'products.update',
            'products.delete',
            'products.approve',
            'products.feature',

            'orders.view',
            'orders.update',
            'orders.refund',

            'coupons.view',
            'coupons.create',
            'coupons.update',
            'coupons.delete',

            'notifications.view',
            'notifications.send',

            'settings.view',
            'settings.update',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Moderator
        |--------------------------------------------------------------------------
        */

        $moderator = Role::findByName('moderator');

        $moderator->syncPermissions([

            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',

            'products.view',
            'products.create',
            'products.update',
            'products.delete',
            'products.approve',
            'products.feature',

            'vendors.view',

        ]);

        /*
        |--------------------------------------------------------------------------
        | Support
        |--------------------------------------------------------------------------
        */

        $support = Role::findByName('support');

        $support->syncPermissions([

            'users.view',
            'users.suspend',

            'orders.view',
            'orders.update',

            'notifications.view',
            'notifications.send',

        ]);

        /*
       |--------------------------------------------------------------------------
       | Vendor
       |--------------------------------------------------------------------------
       */

        $vendor = Role::findByName('vendor');

        $vendor->syncPermissions([]);

        /*
     |--------------------------------------------------------------------------
     | Customer
     |--------------------------------------------------------------------------
     */

        $customer = Role::findByName('customer');

        $customer->syncPermissions([]);
    }
}
