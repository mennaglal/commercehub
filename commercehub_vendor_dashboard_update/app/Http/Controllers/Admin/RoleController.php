<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:roles.view', only: ['index']),
            new Middleware('permission:roles.create', only: ['create', 'store']),
            new Middleware('permission:roles.update', only: ['edit', 'update']),
            new Middleware('permission:roles.delete', only: ['destroy']),
        ];
    }

    public function index()
    {
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->withCount(['permissions', 'users'])
            ->orderByRaw("CASE WHEN name = 'super-admin' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->paginate(15);

        return view('admin.pages.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = $this->permissionsGrouped();

        return view('admin.pages.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateRole($request);

        DB::transaction(function () use ($validated) {
            $role = Role::create([
                'name' => $validated['name'],
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($validated['permissions'] ?? []);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', __('roles.created'));
    }

    public function edit(Role $role)
    {
        abort_unless($role->guard_name === 'web', 404);

        $permissions = $this->permissionsGrouped();
        $role->load('permissions');
        $selectedPermissions = $role->permissions->pluck('name')->all();

        return view('admin.pages.roles.edit', compact('role', 'permissions', 'selectedPermissions'));
    }

    public function update(Request $request, Role $role)
    {
        abort_unless($role->guard_name === 'web', 404);

        if ($role->name === 'super-admin') {
            return back()->with('error', __('roles.super_admin_protected'));
        }

        $validated = $this->validateRole($request, $role);

        DB::transaction(function () use ($validated, $role) {
            $role->update(['name' => $validated['name']]);
            $role->syncPermissions($validated['permissions'] ?? []);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', __('roles.updated'));
    }

    public function destroy(Role $role)
    {
        abort_unless($role->guard_name === 'web', 404);

        if ($role->name === 'super-admin') {
            return back()->with('error', __('roles.super_admin_protected'));
        }

        if ($role->users()->exists()) {
            return back()->with('error', __('roles.role_in_use'));
        }

        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', __('roles.deleted'));
    }

    private function validateRole(Request $request, ?Role $role = null): array
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('roles', 'name')
                    ->where('guard_name', 'web')
                    ->ignore($role?->id),
            ],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => [
                'string',
                Rule::exists('permissions', 'name')->where('guard_name', 'web'),
            ],
        ]);

        // Prevent a role manager from granting permissions they do not personally have.
        if (! $request->user()->hasRole('super-admin')) {
            $allowedPermissions = $request->user()->getAllPermissions()->pluck('name')->all();
            $requestedPermissions = $validated['permissions'] ?? [];
            $unauthorizedPermissions = array_diff($requestedPermissions, $allowedPermissions);

            if ($unauthorizedPermissions !== []) {
                throw ValidationException::withMessages([
                    'permissions' => __('roles.cannot_assign_unavailable_permissions'),
                ]);
            }
        }

        return $validated;
    }

    private function permissionsGrouped()
    {
        return Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Permission $permission) => str_contains($permission->name, '.')
                ? explode('.', $permission->name, 2)[0]
                : 'general');
    }
}
