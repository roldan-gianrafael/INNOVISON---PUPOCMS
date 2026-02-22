<?php

// app/Models/Consultation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consultation extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'medical_condition_id', 'consultation_date', 'comments'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicalCondition(): BelongsTo
    {
        return $this->belongsTo(MedicalConditions::class, 'medical_condition_id');
    }
}