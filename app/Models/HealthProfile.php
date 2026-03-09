<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthProfile extends Model
{
    protected $fillable = [
        'user_id', 
        'school_year', 'home_address', 'student_photo', 
        'age', 'sex', 'civil_status', 'course_college', 
        'blood_type', 'guardian_name', 'landline', 'cellphone',
        'has_illness', 'medical_history', 'other_illness', 
        'has_disability', 'disability_type',
        'food_allergies', 'no_allergies', 'medicine_allergies', 'other_med_allergies',
        'is_smoker', 'is_drinker', 'vaccine_history', 'digital_signature', 'clearance_status',
    'pending_reason',
    'verified_at',
        
    ];

    // NAPAKA-IMPORTANTE: Para kusa nang maging array ang JSON columns mo
    protected $casts = [
        'medical_history' => 'array',
        'medicine_allergies' => 'array',
        'vaccine_history' => 'array',
        'no_allergies' => 'boolean',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}