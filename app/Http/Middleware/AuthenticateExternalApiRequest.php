<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticateExternalApiRequest
{
    public function handle(Request $request, Closure $next)
    {
        $expectedKey = trim((string) config('services.external_admin_profile.api_key', ''));
        $headerName = trim((string) config('services.external_admin_profile.header', 'X-External-Api-Key'));

        if ($expectedKey === '') {
            return new JsonResponse([
                'message' => 'External admin profile API key is not configured.',
            ], 403);
        }

        $providedKey = trim((string) $request->header($headerName, ''));

        if ($providedKey === '') {
            $providedKey = trim((string) $request->bearerToken());
        }

        if ($providedKey === '') {
            return new JsonResponse([
                'message' => 'Authentication credentials were not provided.',
            ], 401);
        }

        if (!hash_equals($expectedKey, $providedKey)) {
            return new JsonResponse([
                'message' => 'Forbidden.',
            ], 403);
        }

        return $next($request);
    }
}
