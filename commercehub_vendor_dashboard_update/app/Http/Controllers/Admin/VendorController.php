<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VendorRequest;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\Store;
use App\Models\User;
use App\Services\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

class VendorController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:vendors.view', only: ['index']),
            new Middleware('permission:vendors.create', only: ['create', 'store']),
            new Middleware('permission:vendors.update', only: ['edit', 'update']),
            new Middleware('permission:vendors.delete', only: ['destroy']),
            new Middleware('permission:vendors.approve', only: ['approve']),
            new Middleware('permission:vendors.reject', only: ['reject']),
            new Middleware('permission:vendors.suspend', only: ['suspend']),
        ];
    }

    public function __construct(private ImageUploader $images) {}

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
                        ->orWhereHas('store', function ($store) use ($search) {
                            $store->where('name', 'like', "%{$search}%");
                        });
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


    public function store(VendorRequest $request)
    {
        $data = $request->validated();

        $logoPath = null;
        $bannerPath = null;

        try {
            if ($request->hasFile('logo')) {
                $logoPath = $this->images->upload(
                    $request->file('logo'),
                    'stores'
                );
            }

            if ($request->hasFile('banner')) {
                $bannerPath = $this->images->upload(
                    $request->file('banner'),
                    'stores'
                );
            }

            DB::transaction(function () use ($data, $logoPath, $bannerPath) {
                $vendor = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'password' => Hash::make($data['password']),
                    'status' => true,
                    'country_id' => $data['country_id'] ?? null,
                    'city_id' => $data['city_id'] ?? null,
                    'area_id' => $data['area_id'] ?? null,
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
        } catch (Throwable $e) {
            if ($logoPath) {
                $this->images->delete($logoPath);
            }

            if ($bannerPath) {
                $this->images->delete($bannerPath);
            }

            throw $e;
        }

        return redirect()
            ->route('admin.vendors.index')
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

    public function update(VendorRequest $request, User $vendor)
    {
        abort_unless($vendor->hasRole('vendor'), 404);

        $vendor->load('store');

        $data = $request->validated();

        $uploadPath = public_path('uploads/stores');

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $store = $vendor->store;

        $logoPath = $store?->logo;
        $bannerPath = $store?->banner;

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');

            $logoName = time() . '_logo_' . Str::random(8) . '.'
                . $logo->getClientOriginalExtension();

            $logo->move($uploadPath, $logoName);

            $logoPath = 'uploads/stores/' . $logoName;
        }

        if ($request->hasFile('banner')) {
            $banner = $request->file('banner');

            $bannerName = time() . '_banner_' . Str::random(8) . '.'
                . $banner->getClientOriginalExtension();

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
            // Update the vendor user and authentication data.
            $vendorData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'country_id' => $data['country_id'] ?? null,
                'city_id' => $data['city_id'] ?? null,
                'area_id' => $data['area_id'] ?? null,
            ];

            if (!empty($data['password'])) {
                $vendorData['password'] = Hash::make($data['password']);
            }

            $vendor->update($vendorData);

            // Update or create the store with the same location data.
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
            if (
                $request->hasFile('logo')
                && $store?->logo
                && $store->logo !== $logoPath
                && File::exists(public_path($store->logo))
            ) {
                File::delete(public_path($store->logo));
            }

            if (
                $request->hasFile('banner')
                && $store?->banner
                && $store->banner !== $bannerPath
                && File::exists(public_path($store->banner))
            ) {
                File::delete(public_path($store->banner));
            }
        });

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', __('messages.vendor_updated'));
    }

    public function destroy(User $vendor)
    {
        abort_unless($vendor->hasRole('vendor'), 404);

        $store = $vendor->store;
        $paths = [$store?->logo, $store?->banner];

        DB::transaction(function () use ($vendor) {
            $vendor->tokens()->delete();
            $vendor->delete();
        });

        foreach ($paths as $path) {
            $this->images->delete($path);
        }

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', __('messages.vendor_deleted'));
    }

    public function approve(User $vendor)
    {
        abort_unless($vendor->hasRole('vendor') && $vendor->store, 404);

        $vendor->store->update([
            'approval_status' => 'approved',
            'status' => true,
        ]);

        return back()->with('success', __('messages.vendor_approved'));
    }

    public function reject(User $vendor)
    {
        abort_unless($vendor->hasRole('vendor') && $vendor->store, 404);

        $vendor->store->update([
            'approval_status' => 'rejected',
            'status' => false,
        ]);

        return back()->with('success', __('messages.vendor_rejected'));
    }

    public function suspend(User $vendor)
    {
        abort_unless($vendor->hasRole('vendor') && $vendor->store, 404);

        $vendor->store->update(['status' => false]);

        return back()->with('success', __('messages.vendor_suspended'));
    }
}
