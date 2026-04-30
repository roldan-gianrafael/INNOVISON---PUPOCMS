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
        'chest_xray_result',
        'has_disability', 'disability_type',
        'pwd_id_proof',
        'medical_certificate', 'health_form_upload', 'clearance_status',
        'assessment_date',
        'blood_pressure',
        'respiratory_rate',
        'temperature',
        'covid_positive',
        'medical_certificate_issued_by',
        'medical_certificate_issued_at',
        'chest_xray_result_text',
        'chest_xray_date',
        'assessment_remarks',
        'clearance_signature_snapshot_path',
        'pending_reason',
        'verified_at',
        'puptas_sync_status',
        'puptas_synced_at',
        'puptas_sync_message',
        
    ];

    protected $casts = [
        'puptas_synced_at' => 'datetime',
        'assessment_date' => 'date',
        'medical_certificate_issued_at' => 'date',
        'chest_xray_date' => 'date',

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
