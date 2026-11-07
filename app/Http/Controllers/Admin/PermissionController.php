<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:permissions.view', only: ['index']),
        ];
    }

    public function index()
    {
        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->withCount('roles')
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Permission $permission) => str_contains($permission->name, '.')
                ? explode('.', $permission->name, 2)[0]
                : 'general');


        return view('admin.pages.permissions.index', compact('permissions'));
    }
}
