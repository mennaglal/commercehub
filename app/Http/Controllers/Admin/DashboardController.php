<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Blogger;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Services\BulkDeleteService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class DashboardController extends Controller
{
    public function index()
    {
        // The dashboard view only displays these three numbers, so only these are queried.
        $usersCount = User::role('customer')->count();

        $vendorsCount = Store::whereHas('user', function ($query) {
            $query->role('vendor');
        })->count();

        $productsCount = Product::count();

        return view('admin.pages.dashboard.index', compact(
            'usersCount',
            'vendorsCount',
            'productsCount',
        ));
    }


    private const RESOURCES = [
        // key            => [model, permission, roles (users only)]
        'customers'      => [User::class,     'users.delete',      ['customer']],
        'vendors'        => [User::class,     'vendors.delete',    ['vendor']],
        'admins'         => [User::class,     'admins.delete',     ['admin', 'moderator', 'support']],
        'categories'     => [Category::class, 'categories.delete'],
        'sub-categories' => [Category::class, 'categories.delete'],
        'countries'      => [Country::class,  'countries.delete'],
        'cities'         => [City::class,     'cities.delete'],
        'areas'          => [Area::class,     'areas.delete'],
        'currencies'     => [Currency::class, 'currencies.delete'],
        'products'       => [Product::class,     'products.delete'],
        'orders'         => [Order::class,     'orders.delete'],
        'bloggers'       => [Blogger::class,     'bloggers.delete'],
        'coupons'       => [Coupon::class,     'coupons.delete'],
    ];

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'model' => ['required', Rule::in(array_keys(self::RESOURCES))],
            'ids'   => ['required', 'string'],
        ]);

        [$class, $permission, $roles] = self::RESOURCES[$request->model] + [2 => null];

        abort_unless($request->user()->can($permission), 403);

        $ids = array_filter(array_map('trim', explode(',', $request->ids)), 'ctype_digit');

        $query = $class::whereIn('id', $ids);

        if ($roles) {
            // users only: limit to these roles (super-admin is not in any list) and never yourself
            $query->role($roles)->where('id', '!=', $request->user()->id);
        }

        $query->get()->each(function ($model) {
            try {
                $model->delete();
            } catch (QueryException) {
                // a foreign key still points to it: skip it
            }
        });

        return back()->with('success', __('messages.deleted'));
    }

}
