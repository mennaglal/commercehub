<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\BloggerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderReturnController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VariantTypeController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\AreaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->controller(AdminLoginController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');

    Route::post('/login', 'login')
        ->middleware('throttle:5,1')
        ->name('login.submit');
});

/*
|--------------------------------------------------------------------------
| Admin Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:super-admin|admin|moderator|support'])->group(function () {

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard');
        Route::post('/bulk-delete', 'bulkDelete')->name('bulk.delete');
    });

    Route::post('/logout', [AdminLoginController::class, 'logout'])
        ->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Admins
    |--------------------------------------------------------------------------
    */

    Route::prefix('admins')
        ->as('admins.')
        ->controller(AdminController::class)
        ->group(function () {

            Route::get('/', 'index')->middleware('permission:admins.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:admins.create')->name('create');
            Route::post('/', 'store')->middleware('permission:admins.create')->name('store');
            Route::get('/{admin}/edit', 'edit')->middleware('permission:admins.update')->name('edit');
            Route::put('/{admin}', 'update')->middleware('permission:admins.update')->name('update');
            Route::delete('/{admin}', 'destroy')->middleware('permission:admins.delete')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    Route::prefix('users')
        ->as('users.')
        ->controller(UserController::class)
        ->group(function () {

            Route::get('/', 'index')->middleware('permission:users.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:users.create')->name('create');
            Route::post('/', 'store')->middleware('permission:users.create')->name('store');
            Route::get('/{user}/edit', 'edit')->middleware('permission:users.update')->name('edit');
            Route::put('/{user}', 'update')->middleware('permission:users.update')->name('update');
            Route::delete('/{user}', 'destroy')->middleware('permission:users.delete')->name('destroy');
            Route::post('/{user}/block', 'block')->middleware('permission:users.suspend')->name('block');
            Route::post('/{user}/unblock', 'unblock')->middleware('permission:users.suspend')->name('unblock');
        });

    /*
    |--------------------------------------------------------------------------
    | Vendors
    |--------------------------------------------------------------------------
    */

    Route::prefix('vendors')
        ->as('vendors.')
        ->controller(VendorController::class)
        ->group(function () {

            Route::get('/', 'index')->middleware('permission:vendors.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:vendors.create')->name('create');
            Route::post('/', 'store')->middleware('permission:vendors.create')->name('store');
            Route::get('/{vendor}/edit', 'edit')->middleware('permission:vendors.update')->name('edit');
            Route::put('/{vendor}', 'update')->middleware('permission:vendors.update')->name('update');
            Route::delete('/{vendor}', 'destroy')->middleware('permission:vendors.delete')->name('destroy');
            Route::post('/{vendor}/approve', 'approve')->middleware('permission:vendors.approve')->name('approve');
            Route::post('/{vendor}/reject', 'reject')->middleware('permission:vendors.reject')->name('reject');
            Route::post('/{vendor}/suspend', 'suspend')->middleware('permission:vendors.suspend')->name('suspend');
        });

    /*
    |--------------------------------------------------------------------------
    | Currencies
    |--------------------------------------------------------------------------
    */

    Route::prefix('currencies')
        ->as('currencies.')
        ->controller(CurrencyController::class)
        ->group(function () {

            Route::get('/', 'index')->middleware('permission:currencies.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:currencies.create')->name('create');
            Route::post('/', 'store')->middleware('permission:currencies.create')->name('store');
            Route::get('/{currency}/edit', 'edit')->middleware('permission:currencies.update')->name('edit');
            Route::put('/{currency}', 'update')->middleware('permission:currencies.update')->name('update');
            Route::delete('/{currency}', 'destroy')->middleware('permission:currencies.delete')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Countries
    |--------------------------------------------------------------------------
    */

    Route::prefix('countries')
        ->as('countries.')
        ->controller(CountryController::class)
        ->group(function () {

            Route::get('/', 'index')->middleware('permission:countries.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:countries.create')->name('create');
            Route::post('/', 'store')->middleware('permission:countries.create')->name('store');
            Route::get('/{country}/edit', 'edit')->middleware('permission:countries.update')->name('edit');
            Route::put('/{country}', 'update')->middleware('permission:countries.update')->name('update');
            Route::delete('/{country}', 'destroy')->middleware('permission:countries.delete')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Cities
    |--------------------------------------------------------------------------
    */

    Route::prefix('cities')
        ->as('cities.')
        ->controller(CityController::class)
        ->group(function () {

            Route::get('/', 'index')->middleware('permission:cities.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:cities.create')->name('create');
            Route::post('/', 'store')->middleware('permission:cities.create')->name('store');
            Route::get('/{city}/edit', 'edit')->middleware('permission:cities.update')->name('edit');
            Route::put('/{city}', 'update')->middleware('permission:cities.update')->name('update');
            Route::delete('/{city}', 'destroy')->middleware('permission:cities.delete')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Areas
    |--------------------------------------------------------------------------
    */

    Route::prefix('areas')
        ->as('areas.')
        ->controller(AreaController::class)
        ->group(function () {

            Route::get('/', 'index')->middleware('permission:areas.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:areas.create')->name('create');
            Route::post('/', 'store')->middleware('permission:areas.create')->name('store');
            Route::get('/{area}/edit', 'edit')->middleware('permission:areas.update')->name('edit');
            Route::put('/{area}', 'update')->middleware('permission:areas.update')->name('update');
            Route::delete('/{area}', 'destroy')->middleware('permission:areas.delete')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */

    Route::prefix('categories')
        ->as('categories.')
        ->controller(CategoryController::class)
        ->group(function () {

            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{category}/edit', 'edit')->name('edit');
            Route::put('/{category}', 'update')->name('update');
            Route::delete('/{category}', 'destroy')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Sub Categories
    |--------------------------------------------------------------------------
    */

    Route::prefix('sub-categories')
        ->as('sub-categories.')
        ->controller(SubCategoryController::class)
        ->group(function () {

            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{sub_category}/edit', 'edit')->name('edit');
            Route::put('/{sub_category}', 'update')->name('update');
            Route::delete('/{sub_category}', 'destroy')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Variant Types
    |--------------------------------------------------------------------------
    */

    Route::prefix('variant-types')
        ->as('variant-types.')
        ->controller(VariantTypeController::class)
        ->group(function () {

            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{variantType}/edit', 'edit')->name('edit');
            Route::put('/{variantType}', 'update')->name('update');
            Route::delete('/{variantType}', 'destroy')->name('destroy');

            Route::post('/{variantType}/values', 'storeValue')->name('values.store');
            Route::put('/{variantType}/values/{variantValue}', 'updateValue')->name('values.update');
            Route::delete('/{variantType}/values/{variantValue}', 'destroyValue')->name('values.destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */

    Route::prefix('products')
        ->as('products.')
        ->controller(ProductController::class)
        ->group(function () {

            Route::delete('/bulk-delete', 'bulkDestroy')->name('bulk-destroy');

            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{product}', 'show')->name('show');
            Route::get('/{product}/edit', 'edit')->name('edit');
            Route::put('/{product}', 'update')->name('update');
            Route::patch('/{product}', 'update')->name('update.patch');
            Route::delete('/{product}', 'destroy')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    */

    Route::prefix('orders')
        ->as('orders.')
        ->controller(OrderController::class)
        ->group(function () {

            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::get('/export', 'export')->middleware('permission:orders.view')->name('export');
            Route::post('/', 'store')->name('store');

            Route::get('/{order}', 'show')->name('show');
            Route::patch('/{order}/status', 'updateStatus')->name('status');
            Route::patch('/{order}/delivery', 'updateDelivery')->name('delivery');
            Route::patch('/{order}/payment', 'updatePayment')->name('payment');
            Route::delete('/{order}', 'destroy')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Returns
    |--------------------------------------------------------------------------
    */

    Route::prefix('returns')
        ->as('returns.')
        ->controller(OrderReturnController::class)
        ->group(function () {

            Route::get('/', 'index')->name('index');
            Route::get('/{orderReturn}', 'show')->name('show');
            Route::patch('/{orderReturn}/status', 'updateStatus')->name('status');
        });

    Route::prefix('orders/{order}/returns')
        ->as('orders.returns.')
        ->controller(OrderReturnController::class)
        ->group(function () {

            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
        });

    /*
    |--------------------------------------------------------------------------
    | Coupons
    |--------------------------------------------------------------------------
    */

    Route::resource('coupons', CouponController::class)->except(['show']);

    /*
    |--------------------------------------------------------------------------
    | Bloggers
    |--------------------------------------------------------------------------
    */

    Route::resource('bloggers', BloggerController::class);

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    Route::resource('roles', RoleController::class)->except(['show']);

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    */

    Route::prefix('permissions')
        ->as('permissions.')
        ->controller(PermissionController::class)
        ->group(function () {

            Route::get('/', 'index')->middleware('permission:permissions.view')->name('index');
        });

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    */

    Route::prefix('settings')
        ->as('settings.')
        ->controller(SettingController::class)
        ->group(function () {

            Route::get('/', 'index')->name('index');
            Route::put('/', 'update')->name('update');
        });

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    Route::prefix('notifications')
        ->as('notifications.')
        ->controller(NotificationController::class)
        ->group(function () {

            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
        });

    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    */

    Route::prefix('pages')
        ->as('pages.')
        ->controller(PageController::class)
        ->group(function () {

            Route::get('/', 'index')->name('index');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
        });

    /*
    |--------------------------------------------------------------------------
    | Contact Messages
    |--------------------------------------------------------------------------
    */

    Route::prefix('contact-messages')
        ->as('contact-messages.')
        ->controller(ContactMessageController::class)
        ->group(function () {

            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::put('/{id}', 'update')->name('update');
        });
});
