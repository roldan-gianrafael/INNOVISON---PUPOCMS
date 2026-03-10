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

    public const ROLE_STUDENT = 'student';
    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_STUDENT_ASSISTANT = 'student_assistant';

    public static function normalizeRole(?string $role): string
    {
        $normalizedRole = strtolower(trim((string) $role));

        // Legacy role alias: "admin" now maps to super admin privileges.
        if ($normalizedRole === 'admin') {
            return self::ROLE_SUPER_ADMIN;
        }

        return $normalizedRole;
    }

    /**
     * The attributes that are mass assignable.
     */
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
     'first_name',
    'last_name',
    'name',
    'student_id',
    'DOB',
    'gender',
    'height',
    'weight',
    'email',
    'contact_no',
    'course',
    'year',
    'section',
    'barcode',
    'user_role',
    'password',
    'is_health_profile_completed',

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

    public function hasRole($roles): bool
    {
        $currentRole = self::normalizeRole($this->user_role);
        $roles = is_array($roles) ? $roles : [$roles];
        $roles = array_map(function ($role) {
            return self::normalizeRole((string) $role);
        }, $roles);

        return in_array($currentRole, $roles, true);
    }

    public function isAdminLike(): bool
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    public function isStudentAssistant(): bool
    {
        return $this->hasRole(self::ROLE_STUDENT_ASSISTANT);
    }
    public function healthProfile() {
    return $this->hasOne(HealthProfile::class);
}
}
