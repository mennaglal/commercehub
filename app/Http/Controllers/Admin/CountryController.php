<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class CountryController extends Controller
{
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('countries', 'name_ar')
                    ->where('currency_id', $request->currency_id),
            ],
            'name_en' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'size:2', 'alpha', 'unique:countries,code'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['code'] = strtoupper($data['code']);
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

    public function update(Request $request, Country $country)
    {
        $data = $request->validate([
            'name_ar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('countries', 'name_ar')
                    ->where('currency_id', $request->currency_id)
                    ->ignore($country->id),
            ],
            'name_en' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'size:2',
                'alpha',
                Rule::unique('countries', 'code')->ignore($country->id),
            ],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['code'] = strtoupper($data['code']);
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
