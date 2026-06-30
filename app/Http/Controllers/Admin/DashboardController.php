<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $usersCount = User::count();

        $vendorsCount = User::role('vendor')->count();

        $productsCount = Product::count();

        $latestUsers = User::latest()->take(5)->get();

        $latestProducts = Product::latest()->take(5)->get();

        $latestVendors = Store::latest()->take(5)->get();

        $pendingVendors = Store::where('status', 0)->count();

        $activeVendors = Store::where('status', 1)->count();

        $inactiveProducts = Product::where('status', 0)->count();

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

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'model' => 'required|string',
            'ids'   => 'required|string'
        ]);

        $modelName = "App\\Models\\" . $request->model;

        if (!class_exists($modelName)) {
            abort(404);
        }

        $ids = explode(',', $request->ids);

        $modelName::whereIn('id', $ids)->delete();

        return redirect()->back()
            ->with('success', __('dashboard.deleted-successfully'));
    }

}
