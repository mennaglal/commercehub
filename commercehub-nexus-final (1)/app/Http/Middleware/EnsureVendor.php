<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;

/**
 * Vendor routes.
 *   EnsureVendor::class              => the user is a vendor and has a store (can see his store even while pending)
 *   EnsureVendor::class . ':approved' => ALSO the store is approved by the admin and not suspended
 *                                        (needed to add products and to see orders)
 */
class EnsureVendor
{
    public function handle(Request $request, Closure $next, string $mode = 'any')
    {
        $user = $request->user();
        $store = $user?->hasRole('vendor') ? $user->store : null;

        if (! $store) {
            return ApiResponse::error('You are not a vendor', null, 403);
        }

        if ($mode === 'approved') {
            if ($store->approval_status !== 'approved') {
                return ApiResponse::error(
                    'Your store is not approved yet',
                    ['approval_status' => $store->approval_status],
                    403
                );
            }

            if (! $store->status) {
                return ApiResponse::error('Your store is suspended', null, 403);
            }
        }

        return $next($request);
    }
}
