<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'user_id',      // Link to User table
        'student_id',   // Student ID
        'name',         // Name of student
        'email',        // Email
        'problem',      // Issue / reason for consultation
        'service',      // Optional service type
        'date',         // Appointment date
        'time',         // Appointment time
        'status',       // Pending / Completed / Cancelled
        'type',         // Source: online or walkin
        'user_type',    // Role enum: Student/Faculty/Admin/Dependent
        'notes',        // Stored DB field for notes
        'remarks',      // Backward-compatible virtual alias to notes
    ];

    /**
     * Normalize role value to appointments.user_type enum.
     */
    public static function normalizeUserType(?string $value): string
    {
        $key = strtolower(trim((string) $value));

        $map = [
            'student' => 'Student',
            'faculty' => 'Faculty',
            'admin' => 'Admin',
            'dependent' => 'Dependent',
            'dependents' => 'Dependent',
        ];

        return $map[$key] ?? 'Student';
    }

    /**
     * Backward compatibility:
     * old code reads $appointment->remarks, but DB column is `notes`.
     */
    public function getRemarksAttribute()
    {
        return $this->attributes['notes'] ?? null;
    }

    /**
     * Backward compatibility:
     * old code writes $appointment->remarks = '...'; save into `notes`.
     */
    public function setRemarksAttribute($value)
    {
        $this->attributes['notes'] = $value;
    }

    /**
     * Relationship: Appointment belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Only walk-in appointments
     */
    public function scopeWalkIn($query)
    {
        return $query->whereIn('type', ['walkin', 'walk-in']);
    }

    /**
     * Scope: Only online appointments
     */
    public function scopeOnline($query)
    {
        return $query->where('type', 'online');
    }

    /**
     * Helper: Check if appointment is completed
     */
    public function isCompleted()
    {
        return $this->status === 'Completed';
    }
}
