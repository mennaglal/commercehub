<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerRequest;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\User;
use App\Services\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Throwable;
class UserController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:users.view', only: ['index']),
            new Middleware('permission:users.create', only: ['create', 'store']),
            new Middleware('permission:users.update', only: ['edit', 'update']),
            new Middleware('permission:users.delete', only: ['destroy']),
            new Middleware('permission:users.block', only: ['block']),
            new Middleware('permission:users.unblock', only: ['unblock']),
        ];
    }
    public function __construct(private ImageUploader $images) {}
    private function customersQuery()
    {
        return User::role('customer')
            ->with(['roles', 'country', 'city', 'area']);
    }

    private function locationData(): array
    {
        return [
            'countries' => Country::orderBy('name_en')->get(),
            'cities' => City::orderBy('name_en')->get(),
            'areas' => Area::orderBy('name_en')->get(),
        ];
    }

    public function index(Request $request)
    {
        $users = $this->customersQuery()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.users.index', compact('users'));
    }

    public function create()
    {
        return view(
            'admin.pages.users.create',
            $this->locationData()
        );
    }

    public function store(CustomerRequest $request)
    {
        $data = $request->validated();

        $data['avatar'] = $request->hasFile('avatar')
            ? $this->images->upload($request->file('avatar'), 'users')
            : null;

        $data['password'] = Hash::make($data['password']);

        try {
            DB::transaction(function () use ($data) {
                $user = User::create($data + ['status' => true]);
                $user->assignRole('customer');
            });
        } catch (Throwable $e) {
            $this->images->delete($data['avatar']);
            throw $e;
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('messages.user_created'));
    }
    public function edit(User $user)
    {
        abort_unless($user->hasRole('customer'), 404);

        return view(
            'admin.pages.users.edit',
            array_merge(['user' => $user], $this->locationData())
        );
    }

    public function update(CustomerRequest $request, User $user)
    {
        abort_unless($user->hasRole('customer'), 404);

        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $oldAvatar = $user->avatar;

        $newAvatar = $request->hasFile('avatar')
            ? $this->images->upload($request->file('avatar'), 'users')
            : null;

        if ($newAvatar) {
            $data['avatar'] = $newAvatar;
        } else {
            unset($data['avatar']);
        }

        try {
            $user->update($data);
        } catch (Throwable $e) {
            $this->images->delete($newAvatar);
            throw $e;
        }

        if ($newAvatar) {
            $this->images->delete($oldAvatar);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('messages.user_updated'));
    }
    public function destroy(User $user)
    {
        abort_unless($user->hasRole('customer'), 404);

        $avatar = $user->avatar;

        DB::transaction(function () use ($user) {
            $user->tokens()->delete();
            $user->delete();
        });

        $this->images->delete($avatar);

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('messages.user_deleted'));
    }
    public function block(User $user)
    {
        abort_unless($user->hasRole('customer'), 404);

        DB::transaction(function () use ($user) {
            $user->update(['status' => false]);
            $user->tokens()->delete();
        });

        return back()->with('success', __('messages.user_blocked'));
    }

    public function unblock(User $user)
    {
        abort_unless($user->hasRole('customer'), 404);

        $user->update(['status' => true]);

        return back()->with('success', __('messages.user_unblocked'));
    }
}
