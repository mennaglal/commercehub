<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
class VendorController extends Controller
{
    private function vendorsQuery()
    {
        return User::role('vendor')
            ->with([
                'store.country',
                'store.city',
                'store.area',
                'roles',
            ]);
    }

    public function index(Request $request)
    {
        $vendors = $this->vendorsQuery()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhereHas('store', fn ($store) => $store->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.vendors.index', compact('vendors'));
    }

    public function create()
    {
        $countries = Country::orderBy('name_en')->get();
        $cities = City::orderBy('name_en')->get();
        $areas = Area::orderBy('name_en')->get();

        return view('admin.pages.vendors.create', compact(
            'countries',
            'cities',
            'areas'
        ));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'store_name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:stores,slug'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],

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
        ]);

        $uploadPath = public_path('uploads/stores');

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $logoPath = null;
        $bannerPath = null;

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '_logo.' . $logo->getClientOriginalExtension();
            $logo->move($uploadPath, $logoName);
            $logoPath = 'uploads/stores/' . $logoName;
        }

        if ($request->hasFile('banner')) {
            $banner = $request->file('banner');
            $bannerName = time() . '_banner.' . $banner->getClientOriginalExtension();
            $banner->move($uploadPath, $bannerName);
            $bannerPath = 'uploads/stores/' . $bannerName;
        }

        DB::transaction(function () use ($data, $logoPath, $bannerPath) {
            $vendor = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
                'status' => true,
            ]);

            $vendor->assignRole('vendor');

            $slug = $data['slug'] ?? Str::slug($data['store_name']);

            if (Store::where('slug', $slug)->exists()) {
                $slug .= '-' . Str::lower(Str::random(5));
            }

            $vendor->store()->create([
                'name' => $data['store_name'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
                'logo' => $logoPath,
                'banner' => $bannerPath,
                'status' => true,
                'approval_status' => 'pending',
                'country_id' => $data['country_id'] ?? null,
                'city_id' => $data['city_id'] ?? null,
                'area_id' => $data['area_id'] ?? null,
            ]);
        });

        return redirect()->route('admin.vendors.index')
            ->with('success', __('messages.vendor_created'));
    }

    public function edit(User $vendor)
    {
        abort_unless($vendor->hasRole('vendor'), 404);

        $vendor->load('store');

        $countries = Country::orderBy('name_en')->get();
        $cities = City::orderBy('name_en')->get();
        $areas = Area::orderBy('name_en')->get();

        return view('admin.pages.vendors.edit', compact(
            'vendor',
            'countries',
            'cities',
            'areas'
        ));
    }
    public function update(Request $request, User $vendor)
    {
        abort_unless($vendor->hasRole('vendor'), 404);

        $vendor->load('store');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($vendor->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'store_name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('stores', 'slug')->ignore($vendor->store?->id),
            ],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],

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
        ]);

        $uploadPath = public_path('uploads/stores');

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $store = $vendor->store;

        $logoPath = $store?->logo;
        $bannerPath = $store?->banner;

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '_logo_' . Str::random(8) . '.' . $logo->getClientOriginalExtension();
            $logo->move($uploadPath, $logoName);

            $logoPath = 'uploads/stores/' . $logoName;
        }

        if ($request->hasFile('banner')) {
            $banner = $request->file('banner');
            $bannerName = time() . '_banner_' . Str::random(8) . '.' . $banner->getClientOriginalExtension();
            $banner->move($uploadPath, $bannerName);

            $bannerPath = 'uploads/stores/' . $bannerName;
        }

        DB::transaction(function () use (
            $data,
            $vendor,
            $store,
            $logoPath,
            $bannerPath,
            $request
        ) {
            $vendorData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
            ];

            if (!empty($data['password'])) {
                $vendorData['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
            }

            $vendor->update($vendorData);

            $vendor->store()->updateOrCreate(
                ['user_id' => $vendor->id],
                [
                    'name' => $data['store_name'],
                    'slug' => $data['slug'],
                    'description' => $data['description'] ?? null,
                    'logo' => $logoPath,
                    'banner' => $bannerPath,
                    'country_id' => $data['country_id'] ?? null,
                    'city_id' => $data['city_id'] ?? null,
                    'area_id' => $data['area_id'] ?? null,
                ]
            );

            // Delete replaced images after updating the store.
            if ($request->hasFile('logo') && $store?->logo
                && $store->logo !== $logoPath
                && File::exists(public_path($store->logo))) {
                File::delete(public_path($store->logo));
            }

            if ($request->hasFile('banner') && $store?->banner
                && $store->banner !== $bannerPath
                && File::exists(public_path($store->banner))) {
                File::delete(public_path($store->banner));
            }
        });

        return redirect()->route('admin.vendors.index')
            ->with('success', __('messages.vendor_updated'));
    }

    public function destroy(User $vendor)
    {
        abort_unless($vendor->hasRole('vendor'), 404);

        DB::transaction(function () use ($vendor) {
            $store = $vendor->store;
            if ($store) {
                foreach ([$store->logo, $store->banner] as $path) {
                    if ($path && File::exists(public_path($path))) {
                        File::delete(public_path($path));
                    }
                }
            }
            $vendor->delete();
        });

        return redirect()->route('admin.vendors.index')
            ->with('success', __('messages.vendor_deleted'));
    }

    public function approve(User $vendor)
    {
        abort_unless($vendor->hasRole('vendor') && $vendor->store, 404);
        $vendor->store->update(['approval_status' => 'approved', 'status' => true]);

        return back()->with('success', __('messages.vendor_approved'));
    }

    public function reject(User $vendor)
    {
        abort_unless($vendor->hasRole('vendor') && $vendor->store, 404);
        $vendor->store->update(['approval_status' => 'rejected', 'status' => false]);

        return back()->with('success', __('messages.vendor_rejected'));
    }

    public function suspend(User $vendor)
    {
        abort_unless($vendor->hasRole('vendor') && $vendor->store, 404);
        $vendor->store->update(['status' => false]);

        return back()->with('success', __('messages.vendor_suspended'));
    }
}
