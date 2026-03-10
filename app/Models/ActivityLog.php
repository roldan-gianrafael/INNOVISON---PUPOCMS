<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'module',
        'event_type',
        'description',
        'route_name',
        'http_method',
        'request_path',
        'status_code',
        'subject_type',
        'subject_id',
        'metadata',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'metadata' => 'array',
        'status_code' => 'integer',
    ];

    // Relationship para makuha ang buong info ng user kung kailangan
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
