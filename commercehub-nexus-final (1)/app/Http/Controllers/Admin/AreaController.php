<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AreaRequest;
use App\Models\Area;
use App\Models\City;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AreaController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:areas.view', only: ['index']),
            new Middleware('permission:areas.create', only: ['create', 'store']),
            new Middleware('permission:areas.update', only: ['edit', 'update']),
            new Middleware('permission:areas.delete', only: ['destroy']),
        ];
    }

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

    public function store(AreaRequest $request)
    {
        $data = $request->validated();
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

    public function update(AreaRequest $request, Area $area)
    {
        $data = $request->validated();
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
