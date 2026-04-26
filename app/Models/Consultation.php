<?php

// app/Models/Consultation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consultation extends Model
{
    use SoftDeletes;

    protected $fillable = [
    'user_id', 'name', 'consultation_date', 'user_type', 'user_role', 'consultation_source', 'service', 
    'medical_condition_id', 'temperature', 'blood_pressure', 'pulse_rate',
    'respiratory_rate', 'covid_status', 'reason_for_visit', 'certificate_type', 'medicine', 
    'medicine_quantity', 'comments'
];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicalCondition(): BelongsTo
    {
        return $this->belongsTo(MedicalConditions::class, 'medical_condition_id');
    }
}
