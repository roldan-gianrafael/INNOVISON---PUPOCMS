<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthProfile extends Model
{
    protected $fillable = [
        'user_id', 
        'student_id', 'student_number',
        'school_year', 'home_address', 'zipcode', 'birthday', 'student_photo', 
        'height', 'weight',
        'age', 'sex', 'civil_status', 'course_college', 
        'blood_type', 'guardian_name', 'landline', 'cellphone',
        'has_illness', 'medical_history', 'other_illness', 
        'chest_xray_result',
        'has_disability', 'disability_type',
        'pwd_id_proof',
        'food_allergies', 'no_allergies', 'medicine_allergies', 'other_med_allergies',
        'medical_certificate', 'medical_certificate_issued_by',
        'is_smoker', 'is_drinker', 'vaccine_history', 'digital_signature', 'clearance_status',
        'clearance_signature_snapshot_path',
        'pending_reason',
        'verified_at',
        'puptas_sync_status',
        'puptas_synced_at',
        'puptas_sync_message',
        
    ];

    // NAPAKA-IMPORTANTE: Para kusa nang maging array ang JSON columns mo
    protected $casts = [
        'medical_history' => 'array',
        'medicine_allergies' => 'array',
        'vaccine_history' => 'array',
        'no_allergies' => 'boolean',
        'puptas_synced_at' => 'datetime',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getEffectiveClearanceSignaturePathAttribute(): string
    {
        $snapshotPath = trim((string) ($this->clearance_signature_snapshot_path ?? ''));
        if ($snapshotPath !== '') {
            return $snapshotPath;
        }

        $settingsPath = trim((string) optional(Setting::query()->first())->clearance_signature_path);

        return $settingsPath !== '' ? $settingsPath : 'health_profiles/signatures/nurse-sign.png';
    }
}
