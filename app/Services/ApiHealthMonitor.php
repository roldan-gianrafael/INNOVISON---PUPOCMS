<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ApiHealthMonitor
{
    protected static $systems = [
        'pupt' => [
            'name' => 'PUPT',
            'endpoint' => 'api_url',
            'health_path' => '/api/health',
            'timeout' => 30,
            'type' => 'internal',
        ],
        'dental' => [
            'name' => 'Dental',
            'endpoint' => 'api_url',
            'health_path' => '/api/status',
            'timeout' => 30,
            'type' => 'internal',
        ],
        'sis' => [
            'name' => 'SIS',
            'endpoint' => 'api_url',
            'health_path' => '/api/ping',
            'timeout' => 45,
            'type' => 'internal',
        ],
        'puptas' => [
            'name' => 'PUPTAS',
            'endpoint' => 'api_url',
            'health_path' => '/api/health',
            'timeout' => 30,
            'type' => 'external',
        ],
        'guisis' => [
            'name' => 'GuiSIS',
            'endpoint' => 'api_url',
            'health_path' => '/api/health',
            'timeout' => 30,
            'type' => 'external',
        ],
        'one_portal' => [
            'name' => 'One Portal (IdP)',
            'endpoint' => 'url',
            'health_path' => '/.well-known/openid-configuration',
            'timeout' => 20,
            'type' => 'idp',
        ],
    ];

    public static function checkAllSystems(): array
    {
        return Cache::remember('api_health_check', 60, function () {
            $results = [];
            foreach (static::$systems as $key => $system) {
                $results[$key] = static::checkSystem($key, $system);
            }
            return $results;
        });
    }

    public static function checkSystem(string $key, ?array $system = null): array
    {
        $system = $system ?? static::$systems[$key] ?? null;
        if (!$system) {
            return [
                'status' => 'unknown',
                'message' => 'System not found',
                'response_time' => 0,
            ];
        }

        $startTime = microtime(true);
        $url = config("services.{$key}.{$system['endpoint']}") ?? null;

        if (!$url) {
            return [
                'status' => 'unconfigured',
                'message' => 'No endpoint configured',
                'response_time' => 0,
            ];
        }

        try {
            $fullUrl = rtrim($url, '/') . $system['health_path'];
            $response = Http::timeout($system['timeout'])->get($fullUrl);
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'status' => $response->status() === 200 ? 'healthy' : 'unhealthy',
                'message' => "HTTP {$response->status()}",
                'response_time' => $responseTime,
                'http_status' => $response->status(),
                'last_check' => now()->toDateTimeString(),
            ];
        } catch (\Exception $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            return [
                'status' => 'down',
                'message' => $e->getMessage(),
                'response_time' => $responseTime,
                'http_status' => null,
                'last_check' => now()->toDateTimeString(),
            ];
        }
    }

    public static function clearCache(): void
    {
        Cache::forget('api_health_check');
    }
}
