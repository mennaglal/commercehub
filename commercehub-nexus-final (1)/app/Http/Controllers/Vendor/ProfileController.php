<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\VendorService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user()->load(['store.country', 'store.city', 'store.area']);
        return view('vendor-dashboard.profile.edit', ['user' => $user, 'store' => $user->store]);
    }

    public function update(Request $request, VendorService $vendors)
    {
        $userData = $request->validate([
            'user_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($request->user()->id)],
        ]);
        $storeData = validator($request->all(), [
            'store_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'country_id' => ['nullable', 'integer', Rule::exists('countries', 'id')->where('is_active', true)],
            'city_id' => ['nullable', 'integer', Rule::exists('cities', 'id')->where('is_active', true)->where('country_id', $request->input('country_id'))],
            'area_id' => ['nullable', 'integer', Rule::exists('areas', 'id')->where('is_active', true)->where('city_id', $request->input('city_id'))],
        ])->validate();

        $request->user()->update(['name' => $userData['user_name'], 'phone' => $userData['phone'] ?? null, 'email' => $userData['email']]);
        $storeData['name'] = $storeData['store_name'];
        unset($storeData['store_name']);
        $vendors->updateStore($request->user()->store, $storeData);
        return back()->with('success', __('messages.updated_successfully'));
    }
}
