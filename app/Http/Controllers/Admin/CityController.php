<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CityRequest;
use App\Models\City;
use App\Models\Country;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CityController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:cities.view', only: ['index']),
            new Middleware('permission:cities.create', only: ['create', 'store']),
            new Middleware('permission:cities.update', only: ['edit', 'update']),
            new Middleware('permission:cities.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $cities = City::with('country')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name_ar', 'like', "%{$search}%")
                        ->orWhere('name_en', 'like', "%{$search}%")
                        ->orWhereHas('country', function ($country) use ($search) {
                            $country->where('name_ar', 'like', "%{$search}%")
                                ->orWhere('name_en', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.cities.index', compact('cities'));
    }

    public function create()
    {
        $countries = Country::orderBy('name_en')->get();

        return view('admin.pages.cities.create', compact('countries'));
    }

    public function store(CityRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        City::create($data);

        return redirect()
            ->route('admin.cities.index')
            ->with('success', __('messages.created_successfully'));
    }

    public function edit(City $city)
    {
        $countries = Country::orderBy('name_en')->get();

        return view('admin.pages.cities.edit', compact('city', 'countries'));
    }

    public function update(CityRequest $request, City $city)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $city->update($data);

        return redirect()
            ->route('admin.cities.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(City $city)
    {
        if ($city->areas()->exists()) {
            return back()->with('error', __('messages.city_has_areas'));
        }

        try {
            $city->delete();
        } catch (QueryException $e) {
            return back()->with('error', __('messages.city_in_use'));
        }

        return back()->with('success', __('messages.deleted_successfully'));
    }
}
