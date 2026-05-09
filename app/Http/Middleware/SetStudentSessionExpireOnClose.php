<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetStudentSessionExpireOnClose
{
    public function handle(Request $request, Closure $next)
    {
        config([
            'session.expire_on_close' => $request->is('student') || $request->is('student/*'),
        ]);

        return $next($request);
    }
}
