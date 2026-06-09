<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiErrorLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'system_name',
        'endpoint',
        'error_code',
        'error_message',
        'request_payload',
        'response_payload',
        'http_status',
        'error_type',
        'response_time_ms',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'response_time_ms' => 'integer',
    ];

    public static function logError(string $systemName, array $data): self
    {
        return static::create(array_merge([
            'system_name' => $systemName,
            'created_at' => now(),
        ], $data));
    }

    public static function getRecentErrors(int $hours = 24, string $system = null)
    {
        $query = static::query()
            ->where('created_at', '>=', now()->subHours($hours))
            ->orderByDesc('created_at');

        if ($system) {
            $query->where('system_name', $system);
        }

        return $query->get();
    }

    public static function getErrorStats(int $hours = 24)
    {
        return static::query()
            ->where('created_at', '>=', now()->subHours($hours))
            ->selectRaw('system_name, COUNT(*) as error_count, MAX(created_at) as last_error')
            ->groupBy('system_name')
            ->get()
            ->keyBy('system_name');
    }
}
