@extends('layouts.admin')

@section('title', 'Medical Assessment')

@push('styles')
<style>
    .assessment-wrap { max-width: 980px; margin: 0 auto; display: grid; gap: 16px; }
    .assessment-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06); padding: 18px; }
    .assessment-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
    .assessment-title { margin: 0; font-size: 20px; font-weight: 800; color: #0f172a; }
    .assessment-sub { margin: 6px 0 0; font-size: 13px; color: #64748b; }
    .assessment-back { display: inline-flex; align-items: center; gap: 8px; border-radius: 10px; padding: 10px 14px; font-size: 13px; font-weight: 700; color: #1e293b; background: #e2e8f0; border: 1px solid #cbd5e1; text-decoration: none; }
    .patient-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #8b0000;
        color: #ffffff;
        padding: 15px 18px;
        border-radius: 10px;
        margin-bottom: 16px;
        border-left: 4px solid #8B0000;
    }
    .patient-header-name { margin: 0 0 6px; font-size: 20px; font-weight: 800; text-color: #ffffff; }
    .badge-chip {
        display: inline-flex;
        align-items: center;
        background: #e2e8f0;
        color: #334155;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 800;
        margin-right: 8px;
        margin-bottom: 4px;
    }
    .badge-chip-applicant {
        background: #fef3c7;
        color: #92400e;
    }
    .patient-date-right {
        text-align: right;
        color: #86efac
    }
    .patient-date-label {
        display: block;
        font-size: 12px;
        color: #94a3b8;
    }
    .patient-date-value {
        font-weight: 700;
        color: #334155;
    }
    .assessment-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
    .assessment-field { display: grid; gap: 6px; }
    .assessment-label { font-size: 12px; font-weight: 700; color: #334155; }
    .assessment-control {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
        color: #0f172a;
        padding: 10px 12px;
        font-size: 14px;
    }
    .assessment-control[readonly] {
        background: #f8fafc;
    }
    .assessment-radios { display: flex; gap: 14px; align-items: center; padding-top: 4px; }
    .assessment-radios label { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #1f2937; font-weight: 600; }
    .assessment-full { grid-column: 1 / -1; }
    .assessment-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 4px; }
    .assessment-btn {
        border: 1px solid transparent;
        border-radius: 999px;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
    }
    .assessment-btn-cancel { background: #e2e8f0; color: #334155; border-color: #cbd5e1; text-decoration: none; display: inline-flex; align-items: center; }
    .assessment-btn-save { background: #70131B; color: #ffffff; border-color: #8f2230; }
    .assessment-success { border: 1px solid #86efac; background: #ecfdf3; color: #166534; border-radius: 10px; padding: 10px 12px; font-size: 13px; font-weight: 700; }
    .assessment-error { border: 1px solid #fecaca; background: #fef2f2; color: #991b1b; border-radius: 10px; padding: 10px 12px; font-size: 13px; font-weight: 700; }

    [data-theme="dark"] .assessment-card { background: #0f172a; border-color: #334155; box-shadow: none; }
    [data-theme="dark"] .assessment-title { color: #f8fafc; }
    [data-theme="dark"] .assessment-sub,
    [data-theme="dark"] .assessment-label { color: #cbd5e1; }
    [data-theme="dark"] .assessment-back { background: #1e293b; color: #f8fafc; border-color: #475569; }
    [data-theme="dark"] .assessment-control { background: #111827; color: #f8fafc; border-color: #475569; }
    [data-theme="dark"] .assessment-control[readonly] { background: #1e293b; }
    [data-theme="dark"] .assessment-radios label { color: #f8fafc; }
    [data-theme="dark"] .assessment-btn-cancel { background: #1e293b; color: #f8fafc; border-color: #475569; }
    [data-theme="dark"] .patient-header { background: #111827; border-left-color: #facc15; }
    [data-theme="dark"] .patient-header-name { color: #f8fafc; }
    [data-theme="dark"] .badge-chip { background: #1f2937; color: #e2e8f0; }
    [data-theme="dark"] .badge-chip-applicant { background: #3f2a00; color: #facc15; }
    [data-theme="dark"] .patient-date-label { color: #cbd5e1; }
    [data-theme="dark"] .patient-date-value { color: #f8fafc; }

    @media (max-width: 768px) {
        .assessment-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
@php
    $studentRole = \App\Models\Appointment::normalizeUserType($profile->user->user_role ?? $profile->user->user_type ?? 'Student');
@endphp
<div class="assessment-wrap">
    

    <div class="assessment-card">
        <div class="patient-header">
            <div>
                <h3 class="patient-header-name">{{ $profile->user->name ?? 'N/A' }}</h3>
                <span class="badge-chip">{{ $studentRole }}</span>
                <span class="badge-chip">{{ $profile->user->student_number ?: ($profile->user->student_id ?? 'N/A') }}</span>
                <span class="badge-chip badge-chip-applicant">Applicant</span>
            </div>
            <div class="patient-date-right">
                <span class="patient-date-label">Date Today</span>
                <span class="patient-date-value">{{ now()->format('F d, Y') }}</span>
            </div>
        </div>

        @if(session('success'))
            <div class="assessment-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="assessment-error">
                Please check the form fields and try again.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.medical_assessment.update', $profile->id) }}">
            @csrf
            @method('PUT')

            <div class="assessment-grid">
                <div class="assessment-field">
                    <label class="assessment-label">Date</label>
                    <input type="date" name="assessment_date" class="assessment-control" value="{{ old('assessment_date', optional($profile->assessment_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                </div>

                <div class="assessment-field">
                    <label class="assessment-label">Date of Birth</label>
                    <input type="text" class="assessment-control" value="{{ !empty($profile->user->DOB) ? \Carbon\Carbon::parse($profile->user->DOB)->format('m/d/Y') : 'N/A' }}" readonly>
                </div>

                <div class="assessment-field">
                    <label class="assessment-label">Height</label>
                    <input type="text" name="height" class="assessment-control" value="{{ old('height', $profile->height) }}" placeholder="e.g. 5'4&quot;">
                </div>

                <div class="assessment-field">
                    <label class="assessment-label">Weight</label>
                    <input type="text" name="weight" class="assessment-control" value="{{ old('weight', $profile->weight) }}" placeholder="e.g. 52 kg">
                </div>

                <div class="assessment-field">
                    <label class="assessment-label">BP</label>
                    <input type="text" name="blood_pressure" class="assessment-control" value="{{ old('blood_pressure', $profile->blood_pressure) }}" placeholder="e.g. 120/80">
                </div>

                <div class="assessment-field">
                    <label class="assessment-label">RR</label>
                    <input type="text" name="respiratory_rate" class="assessment-control" value="{{ old('respiratory_rate', $profile->respiratory_rate) }}" placeholder="e.g. 18">
                </div>

                <div class="assessment-field">
                    <label class="assessment-label">Temp</label>
                    <input type="text" name="temperature" class="assessment-control" value="{{ old('temperature', $profile->temperature) }}" placeholder="e.g. 36.8 C">
                </div>

                <div class="assessment-field">
                    <label class="assessment-label">Covid Positive?</label>
                    <div class="assessment-radios">
                        <label>
                            <input type="radio" name="covid_positive" value="Yes" {{ old('covid_positive', $profile->covid_positive) === 'Yes' ? 'checked' : '' }}>
                            Yes
                        </label>
                        <label>
                            <input type="radio" name="covid_positive" value="No" {{ old('covid_positive', $profile->covid_positive) === 'No' ? 'checked' : '' }}>
                            No
                        </label>
                    </div>
                </div>

                <div class="assessment-field">
                    <label class="assessment-label">Medical Cert Issued By</label>
                    <input type="text" name="medical_certificate_issued_by" class="assessment-control" value="{{ old('medical_certificate_issued_by', $profile->medical_certificate_issued_by) }}" placeholder="Doctor name">
                </div>

                <div class="assessment-field">
                    <label class="assessment-label">Date Issued</label>
                    <input type="date" name="medical_certificate_issued_at" class="assessment-control" value="{{ old('medical_certificate_issued_at', optional($profile->medical_certificate_issued_at)->format('Y-m-d')) }}">
                </div>

                <div class="assessment-field">
                    <label class="assessment-label">Chest X-ray Result</label>
                    <input type="text" name="chest_xray_result_text" class="assessment-control" value="{{ old('chest_xray_result_text', $profile->chest_xray_result_text) }}" placeholder="e.g. Normal / With findings">
                </div>

                <div class="assessment-field">
                    <label class="assessment-label">X-ray Date</label>
                    <input type="date" name="chest_xray_date" class="assessment-control" value="{{ old('chest_xray_date', optional($profile->chest_xray_date)->format('Y-m-d')) }}">
                </div>

                <div class="assessment-field assessment-full">
                    <label class="assessment-label">Remarks</label>
                    <textarea name="assessment_remarks" class="assessment-control" rows="4" placeholder="Enter assessment remarks...">{{ old('assessment_remarks', $profile->assessment_remarks) }}</textarea>
                </div>
            </div>

            <div class="assessment-actions">
                <a href="{{ route('admin.show_health', $profile->id) }}" class="assessment-btn assessment-btn-cancel">Cancel</a>
                <button type="submit" class="assessment-btn assessment-btn-save">Save Assessment</button>
            </div>
        </form>
    </div>
</div>
@endsection
