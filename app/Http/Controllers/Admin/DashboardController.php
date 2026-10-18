<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

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

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'model' => 'required|string',
            'ids' => 'required|string',
        ]);

        $modelName = 'App\\Models\\' . $request->model;

        if (!class_exists($modelName)) {
            abort(404);
        }

        $ids = array_filter(
            explode(',', $request->ids),
            fn ($id) => ctype_digit($id)
        );

        $modelName::whereIn('id', $ids)->delete();

        return redirect()->back()
            ->with('success', __('messages.deleted'));
    }
}
