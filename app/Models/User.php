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

    /**
     * The attributes that are mass assignable.
     */
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
    'name',         
    'first_name',   
    'last_name',    
    'email',
    'password',
    'student_id',   
    'barcode',      
    'user_type',    
    'is_admin'      
];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    /**
     * MAGIC ACCESSOR for $user->name
     * Returns "First Last" if first_name exists, else old 'name' column.
     */
    public function getNameAttribute($value)
    {
        if ($this->first_name) {
            return "{$this->first_name} {$this->last_name}";
        }
        return $value;
    }

    /**
     * RELATION: User has many appointments
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * SCOPE: Only students
     */
    public function scopeStudents($query)
    {
        return $query->where('is_admin', 0);
    }
}
