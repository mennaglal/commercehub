<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CurrencyRequest;
use App\Models\Currency;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CurrencyController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:currencies.view', only: ['index']),
            new Middleware('permission:currencies.create', only: ['create', 'store']),
            new Middleware('permission:currencies.update', only: ['edit', 'update']),
            new Middleware('permission:currencies.delete', only: ['destroy']),
        ];
    }

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

    public function store(CurrencyRequest $request)
    {
        Currency::create($this->normalize($request->validated(), $request));

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', __('messages.created_successfully'));
    }

    public function edit(Currency $currency)
    {
        return view('admin.pages.currencies.edit', compact('currency'));
    }

    public function update(CurrencyRequest $request, Currency $currency)
    {
        $currency->update($this->normalize($request->validated(), $request));

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Currency $currency)
    {
        if ($currency->code === 'EGP') {
            return back()->with('error', __('messages.cannot_delete_base_currency'));
        }

        if ($currency->countries()->exists()) {
            return back()->with('error', __('messages.currency_has_countries'));
        }

        try {
            $currency->delete();
        } catch (QueryException $e) {
            return back()->with('error', __('messages.currency_in_use'));
        }

        return back()->with('success', __('messages.deleted_successfully'));
    }

    // The base currency (EGP) is always active and always has a rate of 1.
    private function normalize(array $data, Request $request): array
    {
        if ($data['code'] === 'EGP') {
            $data['rate_to_egp'] = 1;
            $data['is_active'] = true;
        } else {
            $data['is_active'] = $request->boolean('is_active');
        }

        return $data;
    }
}
