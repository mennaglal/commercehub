<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// Every /api request is treated as JSON, even if the app forgot the "Accept: application/json" header.
// (Without it Laravel would REDIRECT on a validation error instead of answering with JSON.)
class ForceJson
{
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
