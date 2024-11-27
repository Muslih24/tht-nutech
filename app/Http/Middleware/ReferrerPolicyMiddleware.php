<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ReferrerPolicyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request)->header('Referrer-Policy', 'no-referrer-when-downgrade');
    }
}
