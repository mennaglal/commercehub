<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
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

    private function locationRules(Request $request): array
    {
        return [
            'country_id' => [
                'nullable',
                'integer',
                'exists:countries,id',
            ],
            'city_id' => [
                'nullable',
                'integer',
                Rule::exists('cities', 'id')
                    ->where('country_id', $request->input('country_id')),
            ],
            'area_id' => [
                'nullable',
                'integer',
                Rule::exists('areas', 'id')
                    ->where('city_id', $request->input('city_id')),
            ],
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

    public function store(Request $request)
    {
        $data = $request->validate(array_merge([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], $this->locationRules($request)));

        $uploadPath = public_path('uploads/users');

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $avatarPath = null;

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = uniqid() . '_avatar.' . $avatar->getClientOriginalExtension();

            $avatar->move($uploadPath, $avatarName);

            $avatarPath = 'uploads/users/' . $avatarName;
        }

        $data['avatar'] = $avatarPath;
        $data['password'] = Hash::make($data['password']);

        DB::transaction(function () use ($data) {
            $user = User::create($data + ['status' => true]);

            $user->assignRole('customer');
        });

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

    public function update(Request $request, User $user)
    {
        abort_unless($user->hasRole('customer'), 404);

        $data = $request->validate(array_merge([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], $this->locationRules($request)));

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $uploadPath = public_path('uploads/users');

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && File::exists(public_path($user->avatar))) {
                File::delete(public_path($user->avatar));
            }

            $avatar = $request->file('avatar');
            $avatarName = uniqid() . '_avatar.' . $avatar->getClientOriginalExtension();

            $avatar->move($uploadPath, $avatarName);

            $data['avatar'] = 'uploads/users/' . $avatarName;
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('messages.user_updated'));
    }

    public function destroy(User $user)
    {
        abort_unless($user->hasRole('customer'), 404);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('messages.user_deleted'));
    }

    public function block(User $user)
    {
        abort_unless($user->hasRole('customer'), 404);

        $user->update(['status' => false]);

        return back()->with('success', __('messages.user_blocked'));
    }

    public function unblock(User $user)
    {
        abort_unless($user->hasRole('customer'), 404);

        $user->update(['status' => true]);

        return back()->with('success', __('messages.user_unblocked'));
    }
}
