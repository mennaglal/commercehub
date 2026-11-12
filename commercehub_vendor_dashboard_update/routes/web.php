<?php

use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Web\SiteController;
use App\Http\Controllers\Vendor\AuthController as VendorAuthController;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboardController;
use App\Http\Controllers\Vendor\ProductController as VendorProductController;
use App\Http\Controllers\Vendor\OrderController as VendorOrderController;
use App\Http\Controllers\Vendor\ReturnController as VendorReturnController;
use App\Http\Controllers\Vendor\ProfileController as VendorProfileController;
use App\Http\Middleware\EnsureVendorWeb;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/change-language/{locale}', [LanguageController::class,'change'])
    ->name('change-language');

Route::middleware(SetLocale::class)->name('site.')->group(function () {

    /* ---------- the shop ---------- */
    Route::view('/', 'web.home')->name('home');
    Route::view('/shop', 'web.shop')->name('shop');
    Route::get('/products/{slug}', [SiteController::class, 'product'])->name('product');
    Route::view('/stores', 'web.stores')->name('stores');
    Route::get('/stores/{slug}', [SiteController::class, 'store'])->name('store');

    /* ---------- the cart and the order ---------- */
    Route::view('/cart', 'web.cart')->name('cart');
    Route::view('/checkout', 'web.checkout')->name('checkout');
    Route::get('/order-success/{orderNumber}', [SiteController::class, 'orderSuccess'])->name('order-success');

    /* ---------- the account ---------- */
    Route::view('/login', 'web.login')->name('login');
    Route::view('/register', 'web.register')->name('register');

    Route::prefix('account')->group(function () {
        Route::view('/', 'web.account.profile')->name('account');
        Route::view('/orders', 'web.account.orders')->name('account.orders');
        Route::get('/orders/{orderNumber}', [SiteController::class, 'order'])->name('account.order');
        Route::get('/orders/{orderNumber}/return', [SiteController::class, 'returnForm'])->name('account.return.new');
        Route::view('/returns', 'web.account.returns')->name('account.returns');
        Route::get('/returns/{returnNumber}', [SiteController::class, 'returnShow'])->name('account.return');
        Route::view('/notifications', 'web.account.notifications')->name('account.notifications');
    });

    /* ---------- vendor dashboard ---------- */
    Route::view('/become-vendor', 'web.vendor.become')->name('become-vendor');



    /* ---------- information ---------- */
    Route::view('/contact', 'web.contact')->name('contact');
    Route::get('/pages/{slug}', [SiteController::class, 'page'])->name('page');
});

Route::middleware(SetLocale::class)->prefix('vendor')->name('vendor.')->group(function () {
        Route::get('/login', [VendorAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [VendorAuthController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');

        Route::middleware(EnsureVendorWeb::class)->group(function () {
            Route::post('/logout', [VendorAuthController::class, 'logout'])->name('logout');
            Route::get('/', [VendorDashboardController::class, 'index'])->name('dashboard');
            Route::get('/profile', [VendorProfileController::class, 'edit'])->name('profile.edit');
            Route::put('/profile', [VendorProfileController::class, 'update'])->name('profile.update');

            Route::middleware(EnsureVendorWeb::class . ':approved')->group(function () {
                Route::resource('products', VendorProductController::class)->except(['show']);
                Route::patch('/products/{product}/status', [VendorProductController::class, 'toggleStatus'])->whereNumber('product')->name('products.status');
                Route::get('/orders', [VendorOrderController::class, 'index'])->name('orders.index');
                Route::get('/orders/{orderNumber}', [VendorOrderController::class, 'show'])->name('orders.show');
                Route::get('/returns', [VendorReturnController::class, 'index'])->name('returns.index');
                Route::get('/returns/{return}', [VendorReturnController::class, 'show'])->whereNumber('return')->name('returns.show');
                Route::patch('/returns/{return}/items/{item}/status', [VendorReturnController::class, 'updateItemStatus'])->whereNumber('return')->whereNumber('item')->name('returns.items.status');
            });
        });
    });
