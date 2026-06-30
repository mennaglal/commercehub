<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{

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
        $roles = Role::whereNotIn('name', [
            'vendor',
            'customer',
        ])->get();

        return view('admin.pages.admins.create', compact('roles'));
    }

    /**
     * Store a newly created admin.
     */
    public function store(Request $request)
    {
        $request->validate([

            'name' => 'required|string|max:255',

            'email' => 'required|email|unique:users,email',

            'password' => 'required|min:6|confirmed',

            'role' => 'required|exists:roles,name',

            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',


        ]);

        DB::transaction(function () use ($request) {
            // upload folder
            $uploadPath = public_path('uploads/admins');

            // create folder if not exists
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            // upload avatar
            $avatarPath = null;

            if ($request->hasFile('avatar')) {

                $avatar = $request->file('avatar');

                $avatarName = time() . '_avatar.' . $avatar->getClientOriginalExtension();

                $avatar->move($uploadPath, $avatarName);

                $avatarPath = 'uploads/admins/' . $avatarName;
            }

            $admin = User::create([

                'name' => $request->name,

                'email' => $request->email,

                'password' => Hash::make($request->password),

                'avatar'   => $avatarPath,


            ]);

            $admin->assignRole($request->role);

        });

        return redirect()
            ->route('admin.admins.index')
            ->with('success', __('messages.created_successfully'));
    }

    /**
     * Show the form for editing the admin.
     */
    public function edit(User $admin)
    {
        $roles = Role::whereNotIn('name', [
            'vendor',
            'customer',
        ])->get();

        return view('admin.pages.admins.edit', compact('admin', 'roles'));
    }

    /**
     * Update the specified admin.
     */
    public function update(Request $request, User $admin)
    {
        $request->validate([

            'name' => 'required|string|max:255',

            'email' => 'required|email|unique:users,email,' . $admin->id,

            'password' => 'nullable|min:6|confirmed',

            'role' => 'required|exists:roles,name',

            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',


        ]);

        DB::transaction(function () use ($request, $admin) {

            $avatarPath = $admin->avatar;

            // upload folder
            $uploadPath = public_path('uploads/admins');

            // create folder if not exists
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            // upload new avatar
            if ($request->hasFile('avatar')) {

                // delete old avatar
                if ($admin->avatar && File::exists(public_path($admin->avatar))) {
                    File::delete(public_path($admin->avatar));
                }

                $avatar = $request->file('avatar');

                $avatarName = time() . '_avatar.' . $avatar->getClientOriginalExtension();

                $avatar->move($uploadPath, $avatarName);

                $avatarPath = 'uploads/admins/' . $avatarName;
            }

            $admin->update([

                'name' => $request->name,

                'email' => $request->email,

                'password' => $request->filled('password')
                    ? Hash::make($request->password)
                    : $admin->password,

                'avatar' => $avatarPath,

            ]);

            $admin->syncRoles([$request->role]);

        });

        return redirect()
            ->route('admin.admins.index')
            ->with('success', __('messages.updated_successfully'));
    }

    /**
     * Remove the specified admin.
     */
    public function destroy(User $admin)
    {
        if ($admin->hasRole('super-admin')) {

            return back()->with(
                'error',
                __('messages.cannot_delete_super_admin')
            );
        }

        $admin->delete();

        return back()->with(
            'success',
            __('messages.deleted_successfully')
        );
    }
}
