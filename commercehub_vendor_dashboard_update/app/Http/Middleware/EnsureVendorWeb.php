<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVendorWeb
{
    public function handle(Request $request, Closure $next, string $mode = 'any'): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('vendor.login');
        }

        abort_unless($user->status && $user->hasRole('vendor') && $user->store, 403);

        $store = $user->store;

        if ($mode === 'approved') {
            abort_unless($store->approval_status === 'approved' && $store->status, 403, 'Your store is not approved or is suspended.');
        }

        return $next($request);
    }
}
