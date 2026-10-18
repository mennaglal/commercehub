<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CurrencyController extends Controller
{
    public function index(Request $request)
    {
        $currencies = Currency::query()
            ->withCount('countries')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name_ar', 'like', "%{$search}%")
                        ->orWhere('name_en', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('admin.pages.currencies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'size:3',
                'alpha',
                'unique:currencies,code',
            ],
            'symbol' => ['required', 'string', 'max:10'],
            'decimal_places' => ['required', 'integer', 'min:0', 'max:9'],
            'rate_to_egp' => ['nullable', 'numeric', 'gt:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['code'] = strtoupper($data['code']);

        if ($data['code'] === 'EGP') {
            $data['rate_to_egp'] = 1;
            $data['is_active'] = true;
        } else {
            if (!isset($data['rate_to_egp'])) {
                return back()
                    ->withErrors([
                        'rate_to_egp' => 'يجب إدخال سعر صرف صحيح للعملة.',
                    ])
                    ->withInput();
            }

            $data['is_active'] = $request->boolean('is_active');
        }

        Currency::create($data);

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', __('messages.created_successfully'));
    }

    public function edit(Currency $currency)
    {
        return view('admin.pages.currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'size:3',
                'alpha',
                Rule::unique('currencies', 'code')->ignore($currency->id),
            ],
            'symbol' => ['required', 'string', 'max:10'],
            'decimal_places' => ['required', 'integer', 'min:0', 'max:9'],
            'rate_to_egp' => ['nullable', 'numeric', 'gt:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['code'] = strtoupper($data['code']);

        if ($currency->code === 'EGP' && $data['code'] !== 'EGP') {
            return back()
                ->withErrors([
                    'code' => 'لا يمكن تغيير كود العملة الأساسية EGP.',
                ])
                ->withInput();
        }

        if ($data['code'] === 'EGP') {
            $data['rate_to_egp'] = 1;
            $data['is_active'] = true;
        } else {
            if (!isset($data['rate_to_egp'])) {
                return back()
                    ->withErrors([
                        'rate_to_egp' => 'يجب إدخال سعر صرف صحيح للعملة.',
                    ])
                    ->withInput();
            }

            $data['is_active'] = $request->boolean('is_active');
        }

        $currency->update($data);

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Currency $currency)
    {
        if ($currency->code === 'EGP') {
            return back()->with(
                'error',
                'لا يمكن حذف العملة الأساسية EGP.'
            );
        }

        if ($currency->countries()->exists()) {
            return back()->with(
                'error',
                __('messages.currency_has_countries')
            );
        }

        try {
            $currency->delete();
        } catch (QueryException $e) {
            return back()->with(
                'error',
                __('messages.currency_in_use')
            );
        }

        return back()->with(
            'success',
            __('messages.deleted_successfully')
        );
    }
}
