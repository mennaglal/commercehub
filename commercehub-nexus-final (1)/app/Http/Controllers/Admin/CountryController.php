<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CountryRequest;
use App\Models\Country;
use App\Models\Currency;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CountryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:countries.view', only: ['index']),
            new Middleware('permission:countries.create', only: ['create', 'store']),
            new Middleware('permission:countries.update', only: ['edit', 'update']),
            new Middleware('permission:countries.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $countries = Country::with('currency')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name_ar', 'like', "%{$search}%")
                        ->orWhere('name_en', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhereHas('currency', function ($currency) use ($search) {
                            $currency->where('code', 'like', "%{$search}%")
                                ->orWhere('name_ar', 'like', "%{$search}%")
                                ->orWhere('name_en', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.countries.index', compact('countries'));
    }

    public function create()
    {
        $currencies = Currency::orderBy('name_en')->get();

        return view('admin.pages.countries.create', compact('currencies'));
    }

    public function store(CountryRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        Country::create($data);

        return redirect()
            ->route('admin.countries.index')
            ->with('success', __('messages.created_successfully'));
    }

    public function edit(Country $country)
    {
        $currencies = Currency::orderBy('name_en')->get();

        return view('admin.pages.countries.edit', compact('country', 'currencies'));
    }

    public function update(CountryRequest $request, Country $country)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $country->update($data);

        return redirect()
            ->route('admin.countries.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Country $country)
    {
        if ($country->cities()->exists()) {
            return back()->with('error', __('messages.country_has_cities'));
        }

        try {
            $country->delete();
        } catch (QueryException $e) {
            return back()->with('error', __('messages.country_in_use'));
        }

        return back()->with('success', __('messages.deleted_successfully'));
    }
}
