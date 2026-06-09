<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ApiHealthMonitor
{
    protected static $systems = [
        'pupt' => [
            'name' => 'PUPT (Faculty)',
            'config_key' => 'services.pupt_flss.faculty_profiles_url',
            'timeout' => 30,
            'description' => 'PUPT Faculty Profile Service',
        ],
        'guisis' => [
            'name' => 'GuiSIS',
            'config_key' => 'services.guisis.base_url',
            'timeout' => 20,
            'description' => 'GuiSIS Student Information System',
        ],
        'puptas' => [
            'name' => 'PUPTAS',
            'config_key' => 'services.puptas.api_url',
            'timeout' => 20,
            'description' => 'PUPTAS Medical Assessment System',
        ],
        'one_portal' => [
            'name' => 'One Portal (IdP)',
            'config_key' => 'services.idp.url',
            'timeout' => 20,
            'description' => 'Authentication & Identity Provider',
        ],
    ];

    public static function getSystemsList(): array
    {
        return static::$systems;
    }

    public static function checkAllSystems(): array
    {
        return Cache::remember('api_health_check', 60, function () {
            $results = [];
            foreach (static::$systems as $key => $system) {
                $results[$key] = static::checkSystem($key);
            }
            return $results;
        });
    }

    public static function checkSystem(string $key): array
    {
        $system = static::$systems[$key] ?? null;
        if (!$system) {
            return [
                'status' => 'unknown',
                'message' => 'System not configured',
                'response_time' => 0,
                'name' => 'Unknown',
            ];
        }

        $startTime = microtime(true);
        $baseUrl = config($system['config_key']);

        if (!$baseUrl) {
            return [
                'status' => 'unconfigured',
                'message' => 'No endpoint configured',
                'response_time' => 0,
                'name' => $system['name'],
                'description' => $system['description'] ?? '',
            ];
        }

        try {
            $testUrl = rtrim($baseUrl, '/');
            $response = Http::timeout($system['timeout'])->get($testUrl);
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'status' => $response->status() === 200 ? 'healthy' : 'unhealthy',
                'message' => "HTTP {$response->status()}",
                'response_time' => $responseTime,
                'http_status' => $response->status(),
                'name' => $system['name'],
                'description' => $system['description'] ?? '',
                'last_check' => now()->toDateTimeString(),
            ];
        } catch (\Exception $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            return [
                'status' => 'down',
                'message' => substr($e->getMessage(), 0, 100),
                'response_time' => $responseTime,
                'http_status' => null,
                'name' => $system['name'],
                'description' => $system['description'] ?? '',
                'last_check' => now()->toDateTimeString(),
            ];
        }
    }

    public static function clearCache(): void
    {
        Cache::forget('api_health_check');
    }
}
