<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_name',
        'clinic_location',
        'open_time',
        'close_time',
        'email_notifications',
        'auto_approve'
    ];
}