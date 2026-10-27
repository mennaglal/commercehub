<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Models\VariantType;
use App\Models\VariantValue;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        // Count customer accounts only.
        $usersCount = User::role('customer')->count();

        // Count stores linked to vendor accounts.
        $vendorsQuery = Store::whereHas('user', function ($query) {
            $query->role('vendor');
        });

        $vendorsCount = (clone $vendorsQuery)->count();

        // Count all products.
        $productsCount = Product::count();

        // Get the latest customers.
        $latestUsers = User::role('customer')
            ->latest()
            ->take(5)
            ->get();

        // Get the latest vendor stores.
        $latestVendors = (clone $vendorsQuery)
            ->latest()
            ->take(5)
            ->get();

        // Get the latest products.
        $latestProducts = Product::latest()
            ->take(5)
            ->get();

        // Count stores awaiting approval.
        $pendingVendors = (clone $vendorsQuery)
            ->where('approval_status', 'pending')
            ->count();

        // Count approved and active stores.
        $activeVendors = (clone $vendorsQuery)
            ->where('approval_status', 'approved')
            ->where('status', true)
            ->count();

        // Count inactive products.
        $inactiveProducts = Product::where('status', false)->count();

        return view('admin.pages.dashboard.index', compact(
            'usersCount',
            'vendorsCount',
            'productsCount',
            'latestUsers',
            'latestProducts',
            'latestVendors',
            'pendingVendors',
            'activeVendors',
            'inactiveProducts',
        ));
    }

    private const DELETABLE = [
        'User'         => [User::class, 'admins.delete'],
        'User'          => [User::class, 'users.delete'],
        'Store'        => [User::class, 'vendors.delete'],
        'Category'     => [Category::class, 'categories.delete'],
        'Product'       => [Product::class, 'products.delete'],
        'Rule'          => [Role::class, 'roles.delete'],
        'Currency'     => [Currency::class, 'currencies.delete'],
        'Country'      => [Country::class, 'countries.delete'],
        'City'         => [City::class, 'cities.delete'],
        'Area'          => [Area::class, 'areas.delete'],
        'VariantType'  => [VariantType::class, 'variant-types.delete'],
        'VariantValue' => [VariantValue::class, 'variant-values.delete'],
    ];

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'model' => ['required', Rule::in(array_keys(self::DELETABLE))],
            'ids'   => ['required', 'string'],
        ]);

        [$class, $permission] = self::DELETABLE[$request->model];
        abort_unless($request->user()->can($permission), 403);

        $ids = array_filter(explode(',', $request->ids), 'ctype_digit');
        $class::whereIn('id', $ids)->delete();

        return back()->with('success', __('messages.deleted'));
    }
}
