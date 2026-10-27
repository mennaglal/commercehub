<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Services\ImageUploader;
use Illuminate\Validation\Rule;
use Throwable;
use App\Http\Requests\Admin\AdminRequest;
class AdminController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:admins.view', only: ['index']),
            new Middleware('permission:admins.create', only: ['create', 'store']),
            new Middleware('permission:admins.update', only: ['edit', 'update']),
            new Middleware('permission:admins.delete', only: ['destroy']),
        ];
    }

    private const ADMIN_ROLES = ['super-admin', 'admin', 'moderator', 'support'];

    public function __construct(private ImageUploader $images) {}

    // Roles the current admin is allowed to hand out.
    private function assignableRoles()
    {
        return Role::whereNotIn('name', ['vendor', 'customer'])
            ->when(
                ! auth()->user()->hasRole('super-admin'),
                fn ($query) => $query->where('name', '!=', 'super-admin')
            )
            ->get();
    }

    // Stops touching customers/vendors through this controller, and protects super-admins.
    private function ensureManageable(User $admin): void
    {
        abort_unless($admin->hasAnyRole(self::ADMIN_ROLES), 404);

        if ($admin->hasRole('super-admin')) {
            abort_unless(auth()->user()->hasRole('super-admin'), 403);
        }
    }
    /**
     * Display a listing of the admins.
     */
    public function index(Request $request)
    {
        $admins = User::role([
            'super-admin',
            'admin',
            'moderator',
            'support',
        ])
            ->with('roles')
            ->when($request->search, function ($q) use ($request) {

                $q->where(function ($query) use ($request) {

                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');

                });

            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.admins.index', compact('admins'));
    }
    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        $roles = $this->assignableRoles();

        return view('admin.pages.admins.create', compact('roles'));
    }


    /**
     * Show the form for editing the admin.
     */
    public function edit(User $admin)
    {
        $this->ensureManageable($admin);
        $roles = $this->assignableRoles();

        return view('admin.pages.admins.edit', compact('admin', 'roles'));
    }

    public function store(AdminRequest $request)
    {
        $data = $request->validated();

        $avatarPath = $request->hasFile('avatar')
            ? $this->images->upload($request->file('avatar'), 'admins')
            : null;

        try {
            DB::transaction(function () use ($data, $avatarPath) {
                $admin = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'avatar' => $avatarPath,
                ]);

                $admin->assignRole($data['role']);
            });
        } catch (Throwable $e) {
            $this->images->delete($avatarPath);   // do not leave an orphan file behind
            throw $e;
        }

        return redirect()
            ->route('admin.admins.index')
            ->with('success', __('messages.created_successfully'));
    }

    public function update(AdminRequest $request, User $admin)
    {
        $this->ensureManageable($admin);

        $data = $request->validated();

        $oldAvatar = $admin->avatar;
        $newAvatar = $request->hasFile('avatar')
            ? $this->images->upload($request->file('avatar'), 'admins')
            : null;

        try {
            DB::transaction(function () use ($data, $admin, $newAvatar) {
                $payload = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'avatar' => $newAvatar ?? $admin->avatar,
                ];

                if (! empty($data['password'])) {
                    $payload['password'] = Hash::make($data['password']);
                }

                $admin->update($payload);
                $admin->syncRoles([$data['role']]);
            });
        } catch (Throwable $e) {
            $this->images->delete($newAvatar);
            throw $e;
        }

        if ($newAvatar) {
            $this->images->delete($oldAvatar);    // delete the old one only after success
        }

        return redirect()
            ->route('admin.admins.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(User $admin)
    {
        $this->ensureManageable($admin);

        if ($admin->hasRole('super-admin')) {
            return back()->with('error', __('messages.cannot_delete_super_admin'));
        }

        abort_if($admin->is(auth()->user()), 403);   // an admin cannot delete themselves

        $avatar = $admin->avatar;

        $admin->tokens()->delete();
        $admin->delete();
        $this->images->delete($avatar);

        return back()->with('success', __('messages.deleted_successfully'));
    }
}
