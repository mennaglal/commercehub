<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;

// A suspended user (status = false) cannot use the API, even with a token he already has.
class EnsureActiveUser
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && ! $request->user()->status) {
            return ApiResponse::error('Your account is suspended', null, 403);
        }

        return $next($request);
    }
}
