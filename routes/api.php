<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReturnController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\Vendor;
use App\Http\Controllers\Api\VendorController;
use App\Http\Middleware\EnsureActiveUser;
use App\Http\Middleware\EnsureVendor;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API routes
|--------------------------------------------------------------------------
| 1) Public           : anybody (the catalog, the locations, the pages, contact us, register, login)
| 2) Logged-in user   : profile, become a vendor
| 3) Customer         : cart summary, orders, returns
| 4) Vendor           : his store, his products, his orders
|
| Every answer has the same shape (ApiResponse): { success, message, data, errors }.
| The language comes from the header  Accept-Language: ar | en.
*/

/* ===================== 1) PUBLIC ===================== */

Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

// Locations: country -> city -> area (the area carries the shipping price)
Route::get('/countries', [LocationController::class, 'countries']);
Route::get('/countries/{country}/cities', [LocationController::class, 'cities'])->whereNumber('country');
Route::get('/cities/{city}/areas', [LocationController::class, 'areas'])->whereNumber('city');

Route::get('/currencies', [CurrencyController::class, 'index']);

// Catalog
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/stores', [StoreController::class, 'index']);
Route::get('/stores/{id}', [StoreController::class, 'show']);

// Pages (about us, privacy policy, return policy ...) and the contact form
Route::get('/pages', [PageController::class, 'index']);
Route::get('/pages/{slug}', [PageController::class, 'show']);
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1');

/* ===================== LOGGED IN (token) ===================== */

Route::middleware(['auth:sanctum', EnsureActiveUser::class])->group(function () {

    /* ---------- 2) Account ---------- */
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/avatar', [ProfileController::class, 'avatar']);
    Route::put('/profile/password', [ProfileController::class, 'password'])->middleware('throttle:5,1');

    Route::post('/become-vendor', [VendorController::class, 'becomeVendor'])->middleware('throttle:5,1');

    /* ---------- 3) Customer: cart summary, orders, returns ---------- */
    Route::post('/checkout/preview', [OrderController::class, 'preview'])->name('checkout.preview')->middleware('throttle:60,1');

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store'])->middleware('throttle:10,1');
    Route::post('/orders/{orderNumber}/payment', [OrderController::class, 'updatePayment']);

    Route::prefix('/orders/{orderNumber}')->where(['orderNumber' => '[A-Za-z0-9\-]+'])->group(function () {
        Route::get('/', [OrderController::class, 'show']);
        Route::post('/cancel', [OrderController::class, 'cancel']);
        Route::get('/returnable-items', [ReturnController::class, 'returnable']);
        Route::post('/returns', [ReturnController::class, 'store'])->middleware('throttle:10,1');
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::post(
            '/orders/{orderNumber}/verify-payment',
            [PaymentController::class, 'verify']
        );
    });
    Route::get('/returns', [ReturnController::class, 'index']);
    Route::get('/returns/{returnNumber}', [ReturnController::class, 'show'])->where('returnNumber', '[A-Za-z0-9\-]+');

    /* ---------- 4) Vendor ---------- */
    Route::prefix('vendor')->middleware(EnsureVendor::class)->group(function () {

        // His store: also available while the store is still waiting for the approval.
        // PUT and POST both work: an app that uploads a logo must use POST (PHP does not read files sent with PUT).
        Route::get('/store', [Vendor\StoreController::class, 'show']);
        Route::match(['put', 'post'], '/store', [Vendor\StoreController::class, 'update']);

        // Everything below needs an APPROVED and active store.
        Route::middleware(EnsureVendor::class . ':approved')->group(function () {
            Route::get('/dashboard', [Vendor\DashboardController::class, 'index']);
            Route::get('/variant-types', [Vendor\VariantTypeController::class, 'index']);

            Route::get('/products', [Vendor\ProductController::class, 'index']);
            Route::post('/products', [Vendor\ProductController::class, 'store']);
            Route::get('/products/{product}', [Vendor\ProductController::class, 'show'])->whereNumber('product');
            // Update: PUT, or POST when the image is uploaded (see above).
            Route::match(['put', 'post'], '/products/{product}', [Vendor\ProductController::class, 'update'])->whereNumber('product');
            Route::patch('/products/{product}/status', [Vendor\ProductController::class, 'toggleStatus'])->whereNumber('product');
            Route::delete('/products/{product}', [Vendor\ProductController::class, 'destroy'])->whereNumber('product');

            Route::get('/orders', [Vendor\OrderController::class, 'index']);
            Route::get('/orders/{orderNumber}', [Vendor\OrderController::class, 'show'])->where('orderNumber', '[A-Za-z0-9\-]+');
        });
    });
});
