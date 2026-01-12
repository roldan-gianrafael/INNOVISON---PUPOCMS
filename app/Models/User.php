<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', // Keep this for legacy support
        'first_name', // New
        'last_name',  // New
        'email',
        'password',
        'student_id',
        'is_admin'
    ];

    // --- MAGIC ACCESSOR ---
    // This allows you to still use {{ $user->name }} in your blade files!
    public function getNameAttribute($value)
    {
        // If first_name exists, combine them. Otherwise return the old 'name' column.
        if ($this->first_name) {
            return "{$this->first_name} {$this->last_name}";
        }
        return $value;
    }
}
