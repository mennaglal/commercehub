<?php

use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Web\SiteController;
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

    /* ---------- the vendor area ---------- */
    Route::view('/become-vendor', 'web.vendor.become')->name('become-vendor');

    Route::prefix('vendor')->group(function () {
        Route::view('/', 'web.vendor.dashboard')->name('vendor');
        Route::view('/products', 'web.vendor.products')->name('vendor.products');
        Route::get('/products/create', [SiteController::class, 'vendorProductForm'])->name('vendor.products.create');
        Route::get('/products/{id}/edit', [SiteController::class, 'vendorProductForm'])->whereNumber('id')->name('vendor.products.edit');
        Route::view('/orders', 'web.vendor.orders')->name('vendor.orders');
        Route::view('/store', 'web.vendor.store')->name('vendor.store');
    });

    /* ---------- information ---------- */
    Route::view('/contact', 'web.contact')->name('contact');
    Route::get('/pages/{slug}', [SiteController::class, 'page'])->name('page');
});
