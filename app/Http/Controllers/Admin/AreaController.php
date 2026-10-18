<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        $areas = Area::with('city.country')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name_ar', 'like', "%{$search}%")
                        ->orWhere('name_en', 'like', "%{$search}%")
                        ->orWhereHas('city', function ($city) use ($search) {
                            $city->where('name_ar', 'like', "%{$search}%")
                                ->orWhere('name_en', 'like', "%{$search}%")
                                ->orWhereHas('country', function ($country) use ($search) {
                                    $country->where('name_ar', 'like', "%{$search}%")
                                        ->orWhere('name_en', 'like', "%{$search}%");
                                });
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.areas.index', compact('areas'));
    }

    public function create()
    {
        $cities = City::with('country')
            ->orderBy('name_en')
            ->get();

        return view('admin.pages.areas.create', compact('cities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('areas', 'name_ar')
                    ->where('city_id', $request->city_id),
            ],
            'name_en' => ['required', 'string', 'max:255'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        Area::create($data);

        return redirect()
            ->route('admin.areas.index')
            ->with('success', __('messages.created_successfully'));
    }

    public function edit(Area $area)
    {
        $cities = City::with('country')
            ->orderBy('name_en')
            ->get();

        return view('admin.pages.areas.edit', compact('area', 'cities'));
    }

    public function update(Request $request, Area $area)
    {
        $data = $request->validate([
            'name_ar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('areas', 'name_ar')
                    ->where('city_id', $request->city_id)
                    ->ignore($area->id),
            ],
            'name_en' => ['required', 'string', 'max:255'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $area->update($data);

        return redirect()
            ->route('admin.areas.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Area $area)
    {
        try {
            $area->delete();
        } catch (QueryException $e) {
            return back()->with('error', __('messages.area_in_use'));
        }

        return back()->with('success', __('messages.deleted_successfully'));
    }
}
