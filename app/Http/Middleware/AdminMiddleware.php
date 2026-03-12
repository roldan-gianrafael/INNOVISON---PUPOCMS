<?php

namespace App\Http\Middleware;

use App\Models\User;
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

        $role = User::normalizeRole(Auth::user()->user_role ?? '');
        if ($role !== User::ROLE_SUPERADMIN) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
