<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        $role = strtolower((string) (Auth::user()->user_role ?? ''));
        if (!in_array($role, ['admin', 'super_admin'], true)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
