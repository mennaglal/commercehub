<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// The language of the API answers comes from the header  Accept-Language: ar  (or en).
// (SetLocale reads the session, and an API has no session.)
class ApiLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = strtolower(substr((string) $request->header('Accept-Language'), 0, 2));

        app()->setLocale(in_array($locale, ['ar', 'en'], true) ? $locale : config('app.locale'));

        return $next($request);
    }
}
