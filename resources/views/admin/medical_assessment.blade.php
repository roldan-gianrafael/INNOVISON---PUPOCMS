@extends('layouts.admin')

@section('title', 'Medical Assessment')

@push('styles')
<style>
    .assessment-page {
        --assessment-maroon: #70131B;
        --assessment-maroon-soft: #8f2230;
        --assessment-yellow: #facc15;
        --assessment-ink: #111827;
        --assessment-muted: #64748b;
        --assessment-card: #ffffff;
        --assessment-line: rgba(127, 29, 45, 0.14);
        max-width: 1320px;
        margin: 0 auto;
        display: grid;
        gap: 18px;
    }

    .assessment-hero {
        position: relative;
        overflow: hidden;
        border-radius: 26px;
        padding: 24px;
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, 0.32), transparent 30%),
            linear-gradient(135deg, #70131B 0%, #8f2230 52%, #4c0f16 100%);
        color: #ffffff;
        box-shadow: 0 22px 46px rgba(112, 19, 27, 0.24);
    }

    .assessment-hero::after {
        content: "";
        position: absolute;
        inset: auto -10% -70% 38%;
        height: 180px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.10);
        transform: rotate(-10deg);
        pointer-events: none;
    }

    .assessment-hero-inner {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        flex-wrap: wrap;
    }

    .assessment-kicker {
        margin: 0 0 8px;
        color: #fde68a;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.10em;
        text-transform: uppercase;
    }

    .assessment-title {
        margin: 0;
        color: #ffffff;
        font-size: clamp(24px, 4vw, 38px);
        font-weight: 950;
        letter-spacing: -0.03em;
    }

    .assessment-sub {
        max-width: 680px;
        margin: 10px 0 0;
        color: rgba(255, 255, 255, 0.86);
        font-size: 14px;
        line-height: 1.65;
    }

    .assessment-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        padding: 0 18px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.22);
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
        text-decoration: none;
        font-size: 13px;
        font-weight: 900;
        backdrop-filter: blur(8px);
        transition: transform .18s ease, background .18s ease, color .18s ease, border-color .18s ease;
    }

    .assessment-back:hover {
        transform: translateY(-1px);
        background: var(--assessment-yellow);
        color: #111827;
        border-color: var(--assessment-yellow);
        text-decoration: none;
    }

    .assessment-hero .assessment-kicker,
    .assessment-hero .assessment-title,
    .assessment-hero .assessment-sub,
    .assessment-hero .assessment-back {
        color: #ffffff !important;
    }

    .assessment-hero .assessment-sub {
        opacity: 0.88;
    }

    .assessment-console {
        display: grid;
        grid-template-columns: minmax(280px, 360px) minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }

    .patient-snapshot {
        position: sticky;
        top: 92px;
        border-radius: 24px;
        overflow: hidden;
        background: var(--assessment-card);
        border: 1px solid var(--assessment-line);
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.08);
    }

    .snapshot-top {
        padding: 22px;
        background:
            radial-gradient(circle at top left, rgba(250, 204, 21, 0.26), transparent 34%),
            linear-gradient(135deg, #fff8f1, #ffffff);
        border-bottom: 1px solid var(--assessment-line);
    }

    .snapshot-avatar {
        width: 68px;
        height: 68px;
        border-radius: 22px;
        display: grid;
        place-items: center;
        margin-bottom: 14px;
        background: linear-gradient(135deg, var(--assessment-maroon), var(--assessment-maroon-soft));
        color: #ffffff;
        font-size: 20px;
        font-weight: 950;
        box-shadow: 0 16px 26px rgba(112, 19, 27, 0.22);
    }

    .snapshot-name {
        margin: 0;
        color: var(--assessment-ink);
        font-size: 22px;
        font-weight: 950;
        letter-spacing: -0.02em;
    }

    .snapshot-meta {
        margin: 6px 0 0;
        color: var(--assessment-muted);
        font-size: 13px;
        line-height: 1.45;
    }

    .snapshot-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 14px;
    }

    .snapshot-chip {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        padding: 0 10px;
        border-radius: 999px;
        background: #fff7ed;
        color: #9a3412;
        border: 1px solid #fed7aa;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .snapshot-chip.is-maroon {
        background: #fff1f2;
        color: var(--assessment-maroon);
        border-color: #fecdd3;
    }

    .snapshot-list {
        display: grid;
        gap: 10px;
        padding: 18px;
    }

    .snapshot-item {
        display: grid;
        gap: 4px;
        padding: 12px 14px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .snapshot-item small {
        color: #64748b;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .snapshot-item strong {
        color: #111827;
        font-size: 14px;
        font-weight: 850;
        word-break: break-word;
    }

    .assessment-form-shell {
        display: grid;
        gap: 16px;
    }

    .assessment-alert {
        border-radius: 18px;
        padding: 13px 16px;
        font-size: 13px;
        font-weight: 800;
    }

    .assessment-success {
        border: 1px solid #86efac;
        background: #ecfdf3;
        color: #166534;
    }

    .assessment-error {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }

    .assessment-section-card {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        background: var(--assessment-card);
        border: 1px solid var(--assessment-line);
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.06);
        padding: 20px;
    }

    .assessment-section-card::before {
        content: "";
        position: absolute;
        left: 20px;
        right: 20px;
        top: 0;
        height: 5px;
        border-radius: 0 0 999px 999px;
        background: linear-gradient(90deg, var(--assessment-maroon), var(--assessment-yellow));
    }

    .section-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
    }

    .section-icon {
        width: 44px;
        height: 44px;
        border-radius: 16px;
        display: inline-grid;
        place-items: center;
        background: #fff1f2;
        color: var(--assessment-maroon);
        border: 1px solid #fecdd3;
        font-weight: 950;
        flex: 0 0 auto;
    }

    .section-title {
        margin: 0;
        color: #111827;
        font-size: 17px;
        font-weight: 950;
    }

    .section-copy {
        margin: 3px 0 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }

    .assessment-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .assessment-grid.is-vitals {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .assessment-field {
        display: grid;
        gap: 7px;
        min-width: 0;
    }

    .assessment-full {
        grid-column: 1 / -1;
    }

    .assessment-label {
        color: #475569;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .assessment-control {
        width: 100%;
        min-height: 48px;
        border: 0;
        border-bottom: 3px solid var(--assessment-maroon-soft);
        border-radius: 0 0 14px 14px;
        background: linear-gradient(180deg, #fff 0%, #fff8fa 100%);
        color: #111827;
        padding: 12px 14px;
        font-size: 14px;
        font-weight: 750;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.84);
        transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease, background .18s ease;
    }

    textarea.assessment-control {
        min-height: 132px;
        resize: vertical;
        line-height: 1.6;
    }

    .assessment-control:focus {
        outline: none;
        border-bottom-color: var(--assessment-yellow);
        background: #ffffff;
        box-shadow: 0 12px 24px rgba(112, 19, 27, 0.10);
        transform: translateY(-1px);
    }

    .assessment-control[readonly] {
        color: #475569;
        background: #f8fafc;
        border-bottom-color: #cbd5e1;
        box-shadow: none;
    }

    .assessment-radios {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .assessment-radio {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 48px;
        padding: 0 16px;
        border-radius: 999px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
        font-size: 13px;
        font-weight: 900;
        cursor: pointer;
    }

    .assessment-radio input {
        accent-color: var(--assessment-maroon);
    }

    .assessment-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        position: sticky;
        bottom: 12px;
        z-index: 5;
        padding: 12px;
        border-radius: 22px;
        background: linear-gradient(135deg, rgba(112, 19, 27, 0.96), rgba(143, 34, 48, 0.94));
        border: 1px solid rgba(250, 204, 21, 0.18);
        backdrop-filter: blur(10px);
        box-shadow: 0 16px 30px rgba(112, 19, 27, 0.22);
    }

    .assessment-btn {
        position: relative;
        overflow: hidden;
        min-height: 46px;
        border: 1px solid transparent;
        border-radius: 999px;
        padding: 0 20px;
        font-size: 13px;
        font-weight: 950;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        z-index: 0;
        transition: transform .18s ease, box-shadow .18s ease, background .18s ease, color .18s ease, border-color .18s ease;
    }

    .assessment-btn::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, transparent 0%, rgba(255, 239, 181, 0.50) 48%, transparent 100%);
        transform: translateX(-140%);
        transition: transform 1.35s ease;
        z-index: -1;
    }

    .assessment-btn:hover::after {
        transform: translateX(140%);
    }

    .assessment-btn-cancel {
        background: #e2e8f0;
        color: #334155;
        border-color: #cbd5e1;
    }

    .assessment-btn-save {
        background: linear-gradient(135deg, var(--assessment-maroon), var(--assessment-maroon-soft));
        color: #ffffff;
        border-color: var(--assessment-maroon-soft);
        box-shadow: 0 12px 24px rgba(112, 19, 27, 0.20);
    }

    .assessment-btn-save:hover,
    .assessment-btn-cancel:hover {
        transform: translateY(-1px);
        background: var(--assessment-yellow);
        color: #111827;
        border-color: var(--assessment-yellow);
        text-decoration: none;
        box-shadow: 0 14px 26px rgba(112, 19, 27, 0.16);
    }

    html[data-theme="dark"] .assessment-page {
        --assessment-card: #0f172a;
        --assessment-line: rgba(250, 204, 21, 0.16);
        --assessment-ink: #f8fafc;
        --assessment-muted: #cbd5e1;
    }

    html[data-theme="dark"] .assessment-hero {
        box-shadow: 0 22px 46px rgba(0, 0, 0, 0.34);
    }

    html[data-theme="dark"] .patient-snapshot,
    html[data-theme="dark"] .assessment-section-card {
        background: rgba(15, 23, 42, 0.96);
        border-color: rgba(250, 204, 21, 0.14);
        box-shadow: 0 18px 34px rgba(0, 0, 0, 0.26);
    }

    html[data-theme="dark"] .snapshot-top {
        background: linear-gradient(135deg, rgba(112, 19, 27, 0.54), rgba(15, 23, 42, 0.96));
        border-color: rgba(250, 204, 21, 0.14);
    }

    html[data-theme="dark"] .snapshot-name,
    html[data-theme="dark"] .snapshot-item strong,
    html[data-theme="dark"] .section-title {
        color: #ffffff;
    }

    html[data-theme="dark"] .snapshot-meta,
    html[data-theme="dark"] .snapshot-item small,
    html[data-theme="dark"] .section-copy,
    html[data-theme="dark"] .assessment-label {
        color: #cbd5e1;
    }

    html[data-theme="dark"] .snapshot-item,
    html[data-theme="dark"] .assessment-radio {
        background: rgba(17, 24, 39, 0.86);
        border-color: rgba(148, 163, 184, 0.18);
    }

    html[data-theme="dark"] .assessment-radio {
        color: #f8fafc;
    }

    html[data-theme="dark"] .section-icon {
        background: rgba(250, 204, 21, 0.14);
        color: #facc15;
        border-color: rgba(250, 204, 21, 0.24);
    }

    html[data-theme="dark"] .assessment-control {
        background: rgba(17, 24, 39, 0.88);
        color: #f8fafc;
        border-bottom-color: rgba(250, 204, 21, 0.34);
        box-shadow: none;
    }

    html[data-theme="dark"] .assessment-control[readonly] {
        background: rgba(30, 41, 59, 0.82);
        color: #cbd5e1;
        border-bottom-color: rgba(148, 163, 184, 0.28);
    }

    html[data-theme="dark"] .assessment-control::placeholder {
        color: rgba(203, 213, 225, 0.68);
    }

    html[data-theme="dark"] .assessment-actions {
        background: linear-gradient(135deg, rgba(112, 19, 27, 0.96), rgba(79, 13, 25, 0.96));
        border-color: rgba(250, 204, 21, 0.18);
        box-shadow: 0 18px 34px rgba(0, 0, 0, 0.32);
    }

    html[data-theme="dark"] .assessment-btn-cancel {
        background: rgba(30, 41, 59, 0.96);
        color: #f8fafc;
        border-color: rgba(148, 163, 184, 0.28);
    }

    @media (max-width: 1080px) {
        .assessment-console {
            grid-template-columns: 1fr;
        }

        .patient-snapshot {
            position: static;
        }

        .snapshot-list {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .assessment-hero {
            padding: 20px;
            border-radius: 22px;
        }

        .assessment-grid,
        .assessment-grid.is-vitals,
        .snapshot-list {
            grid-template-columns: 1fr;
        }

        .assessment-section-card {
            padding: 18px;
            border-radius: 20px;
        }

        .assessment-actions {
            position: static;
            justify-content: stretch;
        }

        .assessment-btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
@php
    $studentRole = \App\Models\Appointment::normalizeUserType($profile->user->user_role ?? $profile->user->user_type ?? 'Student');
    $patientName = $profile->user->name ?? 'N/A';
    $patientInitials = collect(explode(' ', trim($patientName)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->implode('') ?: 'PT';
    $referenceNumber = $profile->reference_number ?: ($profile->student_number ?: ($profile->user->student_number ?: ($profile->user->student_id ?? 'N/A')));
    $dateOfBirth = !empty($profile->user->DOB) ? \Carbon\Carbon::parse($profile->user->DOB)->format('m/d/Y') : 'N/A';
    $ageDisplay = $calculatedAge !== null ? $calculatedAge . ' years old' : 'N/A';
@endphp

<div class="assessment-page">
    <div class="assessment-hero">
        <div class="assessment-hero-inner">
            <div>
                <p class="assessment-kicker">Clinical Console</p>
                <h1 class="assessment-title">Medical Assessment</h1>
                <p class="assessment-sub">Review the applicant snapshot, record vital signs, document certificate details, and save the clinic assessment in one responsive workspace.</p>
            </div>
            <a href="{{ route('admin.show_health', $profile->id) }}" class="assessment-back">Back to Health Profile</a>
        </div>
    </div>

    <div class="assessment-console">
        <aside class="patient-snapshot">
            <div class="snapshot-top">
                <div class="snapshot-avatar">{{ $patientInitials }}</div>
                <h2 class="snapshot-name">{{ $patientName }}</h2>
                <p class="snapshot-meta">Applicant profile prepared for clinic review and clearance assessment.</p>
                <div class="snapshot-chips">
                    <span class="snapshot-chip is-maroon">{{ $studentRole }}</span>
                    <span class="snapshot-chip">Applicant</span>
                </div>
            </div>
            <div class="snapshot-list">
                <div class="snapshot-item">
                    <small>Reference Number</small>
                    <strong>{{ $referenceNumber }}</strong>
                </div>
                <div class="snapshot-item">
                    <small>Date Today</small>
                    <strong>{{ now()->format('F d, Y') }}</strong>
                </div>
                <div class="snapshot-item">
                    <small>Date of Birth</small>
                    <strong>{{ $dateOfBirth }}</strong>
                </div>
                <div class="snapshot-item">
                    <small>Age</small>
                    <strong>{{ $ageDisplay }}</strong>
                </div>
            </div>
        </aside>

        <form method="POST" action="{{ route('admin.medical_assessment.update', $profile->id) }}" class="assessment-form-shell">
            @csrf
            @method('PUT')

            @if(session('success'))
                <div class="assessment-alert assessment-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="assessment-alert assessment-error">Please check the form fields and try again.</div>
            @endif

            <section class="assessment-section-card">
                <div class="section-head">
                    <span class="section-icon">01</span>
                    <div>
                        <h3 class="section-title">Assessment Context</h3>
                        <p class="section-copy">Confirm the assessment date and patient reference details.</p>
                    </div>
                </div>

                <div class="assessment-grid">
                    <div class="assessment-field">
                        <label class="assessment-label">Assessment Date</label>
                        <input type="date" name="assessment_date" class="assessment-control" value="{{ old('assessment_date', optional($profile->assessment_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                    </div>

                    <div class="assessment-field">
                        <label class="assessment-label">Date of Birth</label>
                        <input type="text" class="assessment-control" value="{{ $dateOfBirth }}" readonly>
                    </div>
                </div>
            </section>

            <section class="assessment-section-card">
                <div class="section-head">
                    <span class="section-icon">02</span>
                    <div>
                        <h3 class="section-title">Vitals</h3>
                        <p class="section-copy">Capture core measurements and current health screening values.</p>
                    </div>
                </div>

                <div class="assessment-grid is-vitals">
                    <div class="assessment-field">
                        <label class="assessment-label">Height</label>
                        <input type="text" name="height" class="assessment-control" value="{{ old('height', $profile->height) }}" placeholder="e.g. 164 cm">
                    </div>

                    <div class="assessment-field">
                        <label class="assessment-label">Weight</label>
                        <input type="text" name="weight" class="assessment-control" value="{{ old('weight', $profile->weight) }}" placeholder="e.g. 52 kg">
                    </div>

                    <div class="assessment-field">
                        <label class="assessment-label">Blood Pressure</label>
                        <input type="text" name="blood_pressure" class="assessment-control" value="{{ old('blood_pressure', $profile->blood_pressure) }}" placeholder="e.g. 120/80">
                    </div>

                    <div class="assessment-field">
                        <label class="assessment-label">Respiratory Rate</label>
                        <input type="text" name="respiratory_rate" class="assessment-control" value="{{ old('respiratory_rate', $profile->respiratory_rate) }}" placeholder="e.g. 18">
                    </div>

                    <div class="assessment-field">
                        <label class="assessment-label">Temperature</label>
                        <input type="text" name="temperature" class="assessment-control" value="{{ old('temperature', $profile->temperature) }}" placeholder="e.g. 36.8 C">
                    </div>

                    <div class="assessment-field">
                        <label class="assessment-label">Covid Positive?</label>
                        <div class="assessment-radios">
                            <label class="assessment-radio">
                                <input type="radio" name="covid_positive" value="Yes" {{ old('covid_positive', $profile->covid_positive) === 'Yes' ? 'checked' : '' }}>
                                Yes
                            </label>
                            <label class="assessment-radio">
                                <input type="radio" name="covid_positive" value="No" {{ old('covid_positive', $profile->covid_positive) === 'No' ? 'checked' : '' }}>
                                No
                            </label>
                        </div>
                    </div>
                </div>
            </section>

            <section class="assessment-section-card">
                <div class="section-head">
                    <span class="section-icon">03</span>
                    <div>
                        <h3 class="section-title">Document Review</h3>
                        <p class="section-copy">Record certificate issuer and chest X-ray findings for clearance tracking.</p>
                    </div>
                </div>

                <div class="assessment-grid">
                    <div class="assessment-field">
                        <label class="assessment-label">Medical Certificate Issued By</label>
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
                </div>
            </section>

            <section class="assessment-section-card">
                <div class="section-head">
                    <span class="section-icon">04</span>
                    <div>
                        <h3 class="section-title">Clinical Remarks</h3>
                        <p class="section-copy">Summarize findings, notes, or recommendations from the medical assessment.</p>
                    </div>
                </div>

                <div class="assessment-grid">
                    <div class="assessment-field assessment-full">
                        <label class="assessment-label">Remarks</label>
                        <textarea name="assessment_remarks" class="assessment-control" rows="4" placeholder="Enter assessment remarks...">{{ old('assessment_remarks', $profile->assessment_remarks) }}</textarea>
                    </div>
                </div>
            </section>

            <div class="assessment-actions">
                <a href="{{ route('admin.show_health', $profile->id) }}" class="assessment-btn assessment-btn-cancel">Cancel</a>
                <button type="submit" class="assessment-btn assessment-btn-save">Save Assessment</button>
            </div>
        </form>
    </div>
</div>
@endsection
