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
use App\Http\Controllers\Admin\VendorController;
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

    Route::resource('users',UserController::class);

    Route::post('users/{user}/block',[UserController::class,'block'])
        ->name('users.block');

    Route::post('users/{user}/unblock',[UserController::class,'unblock'])
        ->name('users.unblock');

    Route::resource('vendors',VendorController::class);

    Route::post('vendors/{vendor}/approve',[VendorController::class,'approve'])
        ->name('vendors.approve');

    Route::post('vendors/{vendor}/reject',[VendorController::class,'reject'])
        ->name('vendors.reject');

    Route::post('vendors/{vendor}/suspend',[VendorController::class,'suspend'])
        ->name('vendors.suspend');

    Route::resource('categories',CategoryController::class);

    Route::resource('sub-categories',SubCategoryController::class);

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
