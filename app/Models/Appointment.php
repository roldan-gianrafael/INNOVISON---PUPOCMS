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
        'user_type',    // 'online' or 'walk-in'
        'notes',        // Optional notes
    ];

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
        return $query->where('type', 'walk-in');
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
