<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\EnsureVendorWeb;

use App\Http\Controllers\Vendor\AuthController as VendorAuthController;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboardController;
use App\Http\Controllers\Vendor\ProductController as VendorProductController;
use App\Http\Controllers\Vendor\OrderController as VendorOrderController;
use App\Http\Controllers\Vendor\ReturnController as VendorReturnController;
use App\Http\Controllers\Vendor\ProfileController as VendorProfileController;

Route::middleware(SetLocale::class)
    ->prefix('vendor')
    ->name('vendor.')
    ->group(function () {

        // Authentication
        Route::get('/login', [VendorAuthController::class, 'showLogin'])
            ->name('login');

        Route::post('/login', [VendorAuthController::class, 'login'])
            ->middleware('throttle:5,1')
            ->name('login.submit');

        // Authenticated Vendor
        Route::middleware(EnsureVendorWeb::class)->group(function () {

            Route::post('/logout', [VendorAuthController::class, 'logout'])
                ->name('logout');

            Route::get('/', [VendorDashboardController::class, 'index'])
                ->name('dashboard');

            // Profile
            Route::get('/profile', [VendorProfileController::class, 'edit'])
                ->name('profile.edit');

            Route::put('/profile', [VendorProfileController::class, 'update'])
                ->name('profile.update');

            // Approved Vendors
            Route::middleware(EnsureVendorWeb::class . ':approved')->group(function () {

                // Products
                Route::resource('products', VendorProductController::class)
                    ->except(['show']);

                Route::patch('/products/{product}/status', [VendorProductController::class, 'toggleStatus'])
                    ->whereNumber('product')
                    ->name('products.status');

                // Orders
                Route::get('/orders', [VendorOrderController::class, 'index'])
                    ->name('orders.index');

                Route::get('/orders/{orderNumber}', [VendorOrderController::class, 'show'])
                    ->name('orders.show');

                // Returns
                Route::get('/returns', [VendorReturnController::class, 'index'])
                    ->name('returns.index');

                Route::get('/returns/{return}', [VendorReturnController::class, 'show'])
                    ->whereNumber('return')
                    ->name('returns.show');

                Route::patch('/returns/{return}/items/{item}/status', [VendorReturnController::class, 'updateItemStatus'])
                    ->whereNumber('return')
                    ->whereNumber('item')
                    ->name('returns.items.status');
            });
        });
    });
