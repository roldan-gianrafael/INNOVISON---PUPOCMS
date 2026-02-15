<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Only check that user is logged in
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
