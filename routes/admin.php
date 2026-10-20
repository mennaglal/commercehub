<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VariantTypeController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\AreaController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {

    Route::get('/login', [AdminLoginController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AdminLoginController::class, 'login'])
        ->name('login.submit');

});

Route::middleware(['auth', 'role:super-admin|admin|moderator|support'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('bulk-delete', [DashboardController::class, 'bulkDelete'])
        ->name('bulk.delete');

    Route::post('/logout', [AdminLoginController::class, 'logout'])
        ->name('logout');

    Route::prefix('admins')
        ->as('admins.')
        ->controller(AdminController::class)
        ->group(function () {

            Route::get('/', 'index')
                ->middleware('permission:admins.view')
                ->name('index');

            Route::get('/create', 'create')
                ->middleware('permission:admins.create')
                ->name('create');

            Route::post('/', 'store')
                ->middleware('permission:admins.create')
                ->name('store');

            Route::get('/{admin}/edit', 'edit')
                ->middleware('permission:admins.update')
                ->name('edit');

            Route::put('/{admin}', 'update')
                ->middleware('permission:admins.update')
                ->name('update');

            Route::delete('/{admin}', 'destroy')
                ->middleware('permission:admins.delete')
                ->name('destroy');
        });

    Route::prefix('users')->as('users.')->controller(UserController::class)->group(function () {
        Route::get('/', 'index')->middleware('permission:users.view')->name('index');
        Route::get('/create', 'create')->middleware('permission:users.create')->name('create');
        Route::post('/', 'store')->middleware('permission:users.create')->name('store');
        Route::get('/{user}/edit', 'edit')->middleware('permission:users.update')->name('edit');
        Route::put('/{user}', 'update')->middleware('permission:users.update')->name('update');
        Route::delete('/{user}', 'destroy')->middleware('permission:users.delete')->name('destroy');
        Route::post('/{user}/block', 'block')->middleware('permission:users.suspend')->name('block');
        Route::post('/{user}/unblock', 'unblock')->middleware('permission:users.suspend')->name('unblock');
    });

    Route::prefix('vendors')->as('vendors.')->controller(VendorController::class)->group(function () {
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

    // Currencies
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

    // Countries
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

    // Cities
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

    // Areas
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

    Route::resource('categories',CategoryController::class);

    Route::resource('sub-categories',SubCategoryController::class);


    Route::resource('variant-types', VariantTypeController::class)
        ->except(['show'])
        ->names('variant-types');

    Route::post(
        'variant-types/{variantType}/values',
        [VariantTypeController::class, 'storeValue']
    )->name('variant-types.values.store');

    Route::put(
        'variant-types/{variantType}/values/{variantValue}',
        [VariantTypeController::class, 'updateValue']
    )->name('variant-types.values.update');

    Route::delete(
        'variant-types/{variantType}/values/{variantValue}',
        [VariantTypeController::class, 'destroyValue']
    )->name('variant-types.values.destroy');


    Route::resource('products',ProductController::class);

    Route::resource('orders',OrderController::class);

    Route::resource('coupons',CouponController::class);

    Route::resource('roles',RoleController::class);

    Route::resource('permissions',PermissionController::class);

    Route::resource('settings',SettingController::class)
        ->only(['index','update']);

    Route::resource('notifications',NotificationController::class)
        ->only(['index','create','store']);


});
