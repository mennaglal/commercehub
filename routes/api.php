<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\VendorController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    Route::post('/become-vendor', [VendorController::class, 'becomeVendor']);
    Route::post('/store-products', [ProductController::class, 'store']);


    Route::middleware('auth:sanctum')->group(function () {
        // The in-app inbox of the logged-in user (customer or vendor)
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('unread-count', [NotificationController::class, 'unreadCount']);
            Route::post('read-all', [NotificationController::class, 'markAllRead']);
            Route::post('{id}/read', [NotificationController::class, 'markRead']);
        });

        // Firebase: the app registers / removes the token of its phone
        Route::post('device-tokens', [DeviceTokenController::class, 'store'])->middleware('throttle:20,1');
        Route::post('device-tokens/unregister', [DeviceTokenController::class, 'destroy'])->middleware('throttle:20,1');
    });

});
