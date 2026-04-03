<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticateExternalApiRequest
{
    public function handle(Request $request, Closure $next)
    {
        $headerName = trim((string) config('services.external_admin_profile.header', 'X-External-Api-Key'));
        $systemHeaderName = trim((string) config('services.external_admin_profile.system_header', 'X-External-System'));
        $expectedKey = trim((string) config('services.external_admin_profile.api_key', ''));
        $systemKeys = collect(config('services.external_admin_profile.system_keys', []))
            ->mapWithKeys(fn ($value, $key) => [strtolower(trim((string) $key)) => trim((string) $value)])
            ->filter(fn ($value, $key) => $key !== '' && $value !== '');

        if ($expectedKey === '' && $systemKeys->isEmpty()) {
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

        if ($systemKeys->isNotEmpty()) {
            $requestedSystem = strtolower(trim((string) $request->header($systemHeaderName, $request->query('system', ''))));

            if ($requestedSystem !== '') {
                $expectedSystemKey = $systemKeys->get($requestedSystem);

                if ($expectedSystemKey === null || !hash_equals($expectedSystemKey, $providedKey)) {
                    return new JsonResponse([
                        'message' => 'Forbidden.',
                    ], 403);
                }

                $request->attributes->set('external_api_system', $requestedSystem);

                return $next($request);
            }

            $matchedSystem = $systemKeys
                ->keys()
                ->first(fn ($system) => hash_equals((string) $systemKeys->get($system), $providedKey));

            if ($matchedSystem === null) {
                return new JsonResponse([
                    'message' => 'Forbidden.',
                ], 403);
            }

            $request->attributes->set('external_api_system', $matchedSystem);

            return $next($request);
        }

        if (!hash_equals($expectedKey, $providedKey)) {
            return new JsonResponse([
                'message' => 'Forbidden.',
            ], 403);
        }

        return $next($request);
    }
}
