<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        $currentRole = User::normalizeRole(Auth::user()->user_role ?? '');
        $allowedRoles = array_map(function ($role) {
            return User::normalizeRole((string) $role);
        }, $roles);

        if (!in_array($currentRole, $allowedRoles, true)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
