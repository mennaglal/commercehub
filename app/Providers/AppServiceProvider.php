<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('admin.layouts.sidebar', function ($view) {

            $counts = [
                'admins' => User::role(['super-admin', 'admin', 'moderator', 'support'])->count(),
                'users' => User::role('customer')->count(),
                'vendors' => User::role('vendor')->count(),

                'roles' => DB::table('roles')->count(),
                'permissions' => DB::table('permissions')->count(),

                'categories' => DB::table('categories')->whereNull('parent_id')->count(),
                'sub-categories' => DB::table('categories')->whereNotNull('parent_id')->count(),
                'variant-types' => DB::table('variant_types')->count(),

                'products' => DB::table('products')->count(),
                'orders' => DB::table('orders')->count(),
                'returns' => DB::table('order_returns')->count(),

                'coupons' => DB::table('coupons')->count(),
                'bloggers' => DB::table('bloggers')->count(),

                'currencies' => DB::table('currencies')->count(),
                'countries' => DB::table('countries')->count(),
                'cities' => DB::table('cities')->count(),
                'areas' => DB::table('areas')->count(),

                'pages' => DB::table('pages')->count(),
                'contact-messages' => DB::table('contact_messages')->count(),
            ];

            $view->with('sidebarCounts', $counts);
        });
    }
}
