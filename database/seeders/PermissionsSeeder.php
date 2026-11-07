<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $modules = [
            'admins' => ['view', 'create', 'update', 'delete'],
            'users' => ['view', 'create', 'update', 'delete', 'suspend'],
            'vendors' => ['view', 'create', 'update', 'delete', 'approve', 'reject', 'suspend'],
            'categories' => ['view', 'create', 'update', 'delete'],
            'products' => ['view', 'create', 'update', 'delete', 'approve', 'feature'],
            'notifications' => ['view', 'send'],
            'settings' => ['view', 'update'],
            'roles' => ['view', 'create', 'update', 'delete'],
            'permissions' => ['view', 'assign'],
            'currencies' => ['view', 'create', 'update', 'delete'],
            'countries' => ['view', 'create', 'update', 'delete'],
            'cities' => ['view', 'create', 'update', 'delete'],
            'areas' => ['view', 'create', 'update', 'delete'],
            'variant-types' => ['view', 'create', 'update', 'delete'],
            'variant-values' => ['view', 'create', 'update', 'delete'],
            'orders' => ['view', 'create', 'update', 'delete', 'refund'],
            'coupons' => ['view', 'create', 'update', 'delete'],
            'bloggers' => ['view', 'create', 'update','delete'],
            'returns' => ['view', 'create', 'update', 'refund'],
            'pages' => ['view', 'index', 'update'],
            'contact-messages' => ['view', 'update', 'delete'],

        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$module}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // Ensure all roles referenced by the application exist.
        foreach (['super-admin', 'admin', 'moderator', 'support', 'vendor', 'customer'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // Give super-admin all permissions.
        $superAdmin = Role::where('name', 'super-admin')->where('guard_name', 'web')->first();
        $superAdmin->syncPermissions(Permission::where('guard_name', 'web')->get());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
