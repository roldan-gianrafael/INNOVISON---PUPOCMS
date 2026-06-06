@extends('layouts.admin')

@section('title', 'Clinical Consultation')

@push('styles')
<style>
    .consultation-workspace {
        display: flex;
        flex-wrap: nowrap;
        gap: 20px;
        align-items: start;
        width: 100%;
        overflow-x: auto;
        padding-bottom: 8px;
    }
    .consultation-documents {
        display: none;
        flex: 0 0 28%;
        width: 28%;
        min-width: 260px;
        max-width: 360px;
        position: sticky;
        top: 20px;
        max-height: calc(100vh - 40px);
        overflow-y: auto;
        padding: 18px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .06);
        scrollbar-width: thin;
    }
    .consultation-workspace.documents-open .consultation-documents {
        display: block;
        animation: consultationDocumentsIn .24s ease;
    }
    @keyframes consultationDocumentsIn {
        from {
            opacity: 0;
            transform: translateX(-14px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    .documents-heading,
    .inventory-drawer-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
    }
    .documents-heading svg,
    .inventory-drawer-heading svg {
        width: 22px;
        height: 22px;
        color: #800000;
        flex: 0 0 auto;
    }
    .documents-heading h2,
    .inventory-drawer-heading h2 {
        margin: 0;
        color: #111827;
        font-size: 17px;
    }
    .documents-count {
        margin-left: auto;
        padding: 3px 8px;
        border-radius: 999px;
        background: #facc15;
        color: #111827;
        font-size: 11px;
        font-weight: 800;
    }
    .documents-panel-close {
        display: grid;
        place-items: center;
        width: 30px;
        height: 30px;
        padding: 0;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        background: #fff;
        color: #111827;
        font-size: 21px;
        line-height: 1;
        cursor: pointer;
    }
    .documents-panel-close:hover {
        border-color: #800000;
        background: #800000;
        color: #fff;
    }
    .document-list {
        display: grid;
        gap: 12px;
    }
    .document-card {
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
    }
    .document-preview {
        display: grid;
        place-items: center;
        width: 100%;
        height: 116px;
        background: #e5e7eb;
        color: #800000;
    }
    .document-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .document-preview svg {
        width: 38px;
        height: 38px;
    }
    .document-card-body {
        padding: 11px;
    }
    .document-card-title {
        display: block;
        margin-bottom: 9px;
        color: #111827;
        font-size: 12px;
        font-weight: 800;
    }
    .document-open {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #800000;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
    }
    .document-open:hover {
        color: #a16207;
    }
    .document-open svg {
        width: 15px;
        height: 15px;
    }
    .documents-empty {
        padding: 24px 12px;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
        text-align: center;
    }
    .consultation-main {
        flex: 1 1 auto;
        width: 72%;
        min-width: 640px;
    }
    .consult-card {
        margin-bottom: 20px;
        padding: 22px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .05);
    }
    .consult-card h3 {
        margin: 0 0 18px;
        color: #111827;
        font-size: 18px;
    }
    .patient-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        border-left: 4px solid #800000;
        background: #f8fafc;
    }
    .patient-header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .documents-panel-trigger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 38px;
        padding: 8px 12px;
        border: 1px solid #800000;
        border-radius: 7px;
        background: #fff;
        color: #800000;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        white-space: nowrap;
        transition: background-color .18s ease, color .18s ease;
    }
    .documents-panel-trigger:hover,
    .documents-panel-trigger[aria-expanded="true"] {
        background: #800000;
        color: #fff;
    }
    .documents-panel-trigger svg {
        width: 18px;
        height: 18px;
    }
    .patient-name {
        margin: 0 0 8px;
        color: #111827;
        font-size: 20px;
    }
    .patient-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
    }
    .patient-badge,
    .badge-source {
        display: inline-flex;
        align-items: center;
        padding: 4px 9px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }
    .patient-badge {
        background: #e2e8f0;
        color: #334155;
    }
    .source-online {
        border: 1px solid #bfdbfe;
        background: #dbeafe;
        color: #1e40af;
    }
    .source-walkin {
        border: 1px solid #fde68a;
        background: #fef3c7;
        color: #92400e;
    }
    .consultation-date {
        flex: 0 0 auto;
        color: #334155;
        font-size: 13px;
        font-weight: 700;
        text-align: right;
    }
    .consultation-date span {
        display: block;
        margin-bottom: 3px;
        color: #64748b;
        font-size: 11px;
        font-weight: 600;
    }
    .form-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-group:last-child {
        margin-bottom: 0;
    }
    .form-group label {
        display: block;
        margin-bottom: 6px;
        color: #111827;
        font-size: 13px;
        font-weight: 800;
    }
    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #94a3b8;
        border-radius: 6px;
        background: #fff;
        color: #111827;
        font-size: 14px;
    }
    .form-control:focus {
        border-color: #800000;
        outline: 3px solid rgba(128, 0, 0, .12);
    }
    .form-control::placeholder {
        color: #64748b;
    }
    .form-help {
        margin-top: 6px;
        color: #64748b;
        font-size: 11px;
    }
    .mar-required {
        border-color: #fca5a5;
        background: #fff7f7;
    }
    .choice-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }
    .choice-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .choice-card {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        border: 1px solid #cbd5e1;
        border-radius: 7px;
        background: #fff;
        color: #111827;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        transition: background-color .18s ease, border-color .18s ease, color .18s ease;
    }
    .choice-input:checked + .choice-card {
        border-color: #800000;
        background: #800000;
        color: #fff;
    }
    .medicine-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }
    .medicine-header h3 {
        margin: 0;
    }
    .medicine-selection-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 10px;
        align-items: center;
    }
    .inventory-tally-trigger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 38px;
        padding: 8px 12px;
        border: 1px solid #800000;
        border-radius: 7px;
        background: #fff;
        color: #800000;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        transition: background-color .18s ease, color .18s ease, transform .18s ease;
    }
    .inventory-tally-trigger:hover {
        background: #800000;
        color: #fff;
        transform: translateY(-1px);
    }
    .inventory-tally-trigger svg {
        width: 18px;
        height: 18px;
    }
    .selected-stock {
        display: none;
        align-items: center;
        gap: 7px;
        margin-top: 9px;
        padding: 8px 10px;
        border-radius: 6px;
        background: #ecfdf5;
        color: #166534;
        font-size: 12px;
        font-weight: 800;
    }
    .selected-stock.visible {
        display: flex;
    }
    .selected-stock.low {
        background: #fff7ed;
        color: #c2410c;
    }
    .form-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 12px;
    }
    .btn-save {
        flex: 1;
        min-height: 46px;
        padding: 12px 22px;
        border: 0;
        border-radius: 8px;
        background: #800000;
        color: #fff;
        font-weight: 800;
        cursor: pointer;
        transition: background-color .2s ease, color .2s ease;
    }
    .btn-save:hover {
        background: #facc15;
        color: #111827;
    }
    .btn-cancel {
        padding: 12px;
        color: #111827;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
    }
    #right-inventory-panel {
        position: fixed;
        top: 0;
        right: -350px;
        width: 320px;
        height: 100vh;
        background: #fff;
        box-shadow: -2px 0 5px rgba(0, 0, 0, .15);
        transition: right .3s ease;
        z-index: 1050;
        padding: 20px;
        overflow-y: auto;
        color: #111827;
    }
    #right-inventory-panel.open {
        right: 0;
    }
    #close-tally-btn {
        display: grid;
        place-items: center;
        width: 36px;
        height: 36px;
        margin-left: auto;
        border: 1px solid #cbd5e1;
        border-radius: 7px;
        background: #fff;
        color: #111827;
        font-size: 25px;
        line-height: 1;
        cursor: pointer;
    }
    #close-tally-btn:hover {
        border-color: #800000;
        background: #800000;
        color: #fff;
    }
    .inventory-drawer-subtitle {
        margin: -8px 0 18px;
        color: #64748b;
        font-size: 12px;
    }
    .inventory-tally-list {
        display: grid;
        gap: 9px;
    }
    .inventory-tally-item {
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
        transition: border-color .18s ease, background-color .18s ease;
    }
    .inventory-tally-item.selected {
        border-color: #800000;
        background: #fff7ed;
    }
    .inventory-tally-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
    }
    .inventory-tally-name {
        color: #111827;
        font-size: 13px;
        font-weight: 800;
    }
    .inventory-tally-meta {
        margin-top: 5px;
        color: #64748b;
        font-size: 11px;
    }
    .stock-badge {
        flex: 0 0 auto;
        padding: 4px 7px;
        border-radius: 999px;
        background: #dcfce7;
        color: #166534;
        font-size: 10px;
        font-weight: 900;
    }
    .stock-badge.low {
        background: #ffedd5;
        color: #c2410c;
    }
    html[data-theme="dark"] .consultation-documents,
    html[data-theme="dark"] .consult-card {
        border-color: #374151;
        background: #111827;
    }
    html[data-theme="dark"] .patient-header,
    html[data-theme="dark"] .document-card,
    html[data-theme="dark"] .inventory-tally-item {
        border-color: #374151;
        background: #1f2937;
    }
    html[data-theme="dark"] .documents-heading h2,
    html[data-theme="dark"] .inventory-drawer-heading h2,
    html[data-theme="dark"] .consult-card h3,
    html[data-theme="dark"] .patient-name,
    html[data-theme="dark"] .form-group label,
    html[data-theme="dark"] .document-card-title,
    html[data-theme="dark"] .inventory-tally-name,
    html[data-theme="dark"] .btn-cancel {
        color: #f8fafc;
    }
    html[data-theme="dark"] .form-control,
    html[data-theme="dark"] .choice-card,
    html[data-theme="dark"] .inventory-tally-trigger,
    html[data-theme="dark"] .documents-panel-trigger,
    html[data-theme="dark"] .documents-panel-close {
        border-color: #4b5563;
        background: #0f172a;
        color: #f8fafc;
    }
    html[data-theme="dark"] .mar-required {
        background: #2b1720;
    }
    @media (max-width: 1180px) {
        .form-grid-2 {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 820px) {
        .consultation-documents {
            flex-basis: 260px;
            width: 260px;
        }
        .document-list {
            grid-template-columns: 1fr;
        }
        .patient-header,
        .patient-header-actions {
            align-items: flex-start;
            flex-direction: column;
        }
        .consultation-date {
            text-align: left;
        }
    }
    @media (max-width: 520px) {
        .form-grid-2 {
            grid-template-columns: 1fr;
        }
        .consult-card,
        .consultation-documents {
            padding: 16px;
        }
        .btn-save {
            width: 100%;
        }
        .medicine-selection-row {
            grid-template-columns: minmax(360px, 1fr) auto;
        }
        .form-actions {
            align-items: stretch;
            flex-direction: column;
        }
        .btn-cancel {
            text-align: center;
        }
    }
</style>
@endpush

@section('content')
@php
    $role = \App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '');
    $walkinStoreRoute = $role === \App\Models\User::ROLE_ADMIN ? 'assistant.walkin.store' : 'walkin.store';
    $walkinIndexRoute = $role === \App\Models\User::ROLE_ADMIN ? 'assistant.walkin.index' : 'walkin.index';
    $studentDisplayRole = \App\Models\Appointment::normalizeUserType($student->user_role ?? $student->user_type ?? 'Student');
    $isAssistedIntake = ($user_source ?? '') === 'assisted';
    $studentDocuments = $studentDocuments ?? [];
@endphp

<div class="consultation-workspace">
    <aside class="consultation-documents" id="consultation-documents-panel" aria-label="Student uploaded documents" aria-hidden="true">
        <div class="documents-heading">
            <x-outline-icon name="document-text" />
            <h2>Uploaded Documents</h2>
            <span class="documents-count">{{ count($studentDocuments) }}</span>
            <button type="button" class="documents-panel-close" id="close-documents-btn" aria-label="Close uploaded documents">&times;</button>
        </div>

        @if(count($studentDocuments))
            <div class="document-list">
                @foreach($studentDocuments as $document)
                    <article class="document-card">
                        <a class="document-preview" href="{{ $document['url'] }}" target="_blank" rel="noopener">
                            @if($document['type'] === 'image')
                                <img src="{{ $document['url'] }}" alt="{{ $document['label'] }} preview">
                            @else
                                <x-outline-icon name="document-text" />
                            @endif
                        </a>
                        <div class="document-card-body">
                            <span class="document-card-title">{{ $document['label'] }}</span>
                            <a class="document-open" href="{{ $document['url'] }}" target="_blank" rel="noopener">
                                <x-outline-icon name="eye" />
                                Open document
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="documents-empty">No uploaded clinic documents are available for this student.</div>
        @endif
    </aside>

    <div class="consultation-main">
        <header class="patient-header consult-card">
            <div>
                <h2 class="patient-name">{{ $student->first_name }} {{ $student->last_name }}</h2>
                <div class="patient-badges">
                    <span class="patient-badge">{{ $studentDisplayRole }}</span>
                    <span class="patient-badge">{{ $student->student_number ?: $student->student_id ?: 'N/A' }}</span>
                    @if(($user_source ?? '') === 'online' && $latestAppointment)
                        <span class="badge-source source-online">Online Appointment</span>
                    @elseif($isAssistedIntake)
                        <span class="badge-source source-walkin">Assisted Intake</span>
                    @else
                        <span class="badge-source source-walkin">Walk-in Patient</span>
                    @endif
                </div>
                @if(($user_source ?? '') === 'online' && $latestAppointment)
                    <div class="form-help">
                        Scheduled {{ \Carbon\Carbon::parse($latestAppointment->date)->format('M d, Y') }}
                        at {{ \Carbon\Carbon::parse($latestAppointment->time)->format('g:i A') }}
                    </div>
                @endif
            </div>
            <div class="patient-header-actions">
                <button type="button" class="documents-panel-trigger" id="open-documents-btn" aria-controls="consultation-documents-panel" aria-expanded="false">
                    <x-outline-icon name="document-text" />
                    View Uploaded Documents
                </button>
                <div class="consultation-date">
                    <span>Today's Consultation</span>
                    {{ now()->format('F d, Y') }}
                </div>
            </div>
        </header>

        <form action="{{ route($walkinStoreRoute) }}" method="POST">
            @csrf
            <input type="hidden" name="student_number" value="{{ $student->student_number ?: $student->student_id }}">
            <input type="hidden" name="user_role" value="{{ $studentDisplayRole }}">
            <input type="hidden" name="user_type" value="{{ $user_source ?? 'walkin' }}">
            <input type="hidden" name="consultation_started_at" value="{{ now()->format('H:i:s') }}">

            <section class="consult-card">
                <h3>Physical Assessment</h3>
                <div class="form-group">
                    <label for="consultDob">Date of Birth</label>
                    <input type="date" id="consultDob" name="dob" class="form-control" value="{{ old('dob', $consultationDob ?? '') }}">
                    <div class="form-help">Prefilled from saved student information when available.</div>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="consultHeight">Height (cm)</label>
                        <input type="number" id="consultHeight" step="0.01" name="height" class="form-control" placeholder="165" value="{{ old('height', $consultationHeight ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label for="consultWeight">Weight (kg)</label>
                        <input type="number" id="consultWeight" step="0.01" name="weight" class="form-control" placeholder="60" value="{{ old('weight', $consultationWeight ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label for="consultTemp">Temperature (C)</label>
                        <input type="number" id="consultTemp" step="0.1" name="temp" class="form-control" placeholder="36.5" value="{{ old('temp') }}">
                    </div>
                    <div class="form-group">
                        <label for="consultBp">Blood Pressure</label>
                        <input type="text" id="consultBp" name="bp" class="form-control" placeholder="120/80" value="{{ old('bp') }}">
                    </div>
                    <div class="form-group">
                        <label for="consultPulse">Pulse Rate (bpm)</label>
                        <input type="number" id="consultPulse" name="pulse_rate" class="form-control" placeholder="72" value="{{ old('pulse_rate') }}">
                    </div>
                    <div class="form-group">
                        <label for="consultRespiratory">Respiratory Rate (cpm)</label>
                        <input type="number" id="consultRespiratory" name="respiratory_rate" class="form-control" placeholder="18" value="{{ old('respiratory_rate') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>Covid Positive?</label>
                    <div class="choice-grid">
                        <label>
                            <input type="radio" name="covid_status" class="choice-input" value="Yes" {{ old('covid_status') === 'Yes' ? 'checked' : '' }}>
                            <span class="choice-card">Yes</span>
                        </label>
                        <label>
                            <input type="radio" name="covid_status" class="choice-input" value="No" {{ old('covid_status', 'No') === 'No' ? 'checked' : '' }}>
                            <span class="choice-card">No</span>
                        </label>
                    </div>
                </div>
            </section>

            <section class="consult-card">
                <h3>Visit Details</h3>
                <div class="form-group">
                    <label for="consultReason">{{ ($user_source ?? '') === 'online' ? 'Appointment Remarks' : 'Reason for Visiting Clinic' }}</label>
                    <input type="text" id="consultReason" name="reason_for_visit" class="form-control" {{ ($user_source ?? '') === 'online' ? 'readonly' : '' }} value="{{ old('reason_for_visit', optional($latestAppointment)->remarks) }}">
                </div>
                <div class="form-group">
                    <label for="consultService">Purpose of Visit / Service</label>
                    <select id="consultService" class="form-control" @if(($user_source ?? '') === 'online') disabled @else name="service" @endif required>
                        <option value="" disabled {{ !old('service', optional($latestAppointment)->service) ? 'selected' : '' }}>-- Select Service --</option>
                        <option value="General Consultation" {{ old('service', optional($latestAppointment)->service) === 'General Consultation' ? 'selected' : '' }}>General Consultation</option>
                        <option value="BP Monitoring" {{ old('service', optional($latestAppointment)->service) === 'BP Monitoring' ? 'selected' : '' }}>BP Monitoring</option>
                    </select>
                    @if(($user_source ?? '') === 'online')
                        <input type="hidden" name="service" value="{{ old('service', optional($latestAppointment)->service) }}">
                    @endif
                </div>
                <div class="form-group">
                    <label for="consultCondition">Medical Condition (MAR Classification)</label>
                    <select name="condition_id" id="consultCondition" class="form-control mar-required" required>
                        <option value="" disabled {{ old('condition_id') ? '' : 'selected' }}>-- Select Diagnosis --</option>
                        @foreach($conditions as $condition)
                            <option value="{{ $condition->id }}" {{ (string) old('condition_id') === (string) $condition->id ? 'selected' : '' }}>
                                Category {{ optional($condition->category)->code }}: {{ $condition->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-help">Required for MAR Report.</div>
                </div>
                <div class="form-group">
                    <label for="consultCertificate">Medical Certificate / Clearance</label>
                    <select name="certificate_type" id="consultCertificate" class="form-control">
                        <option value="none" {{ old('certificate_type', 'none') === 'none' ? 'selected' : '' }}>None</option>
                        <option value="excused_letter" {{ old('certificate_type') === 'excused_letter' ? 'selected' : '' }}>Excused Letter</option>
                        <option value="coc_ijt" {{ old('certificate_type') === 'coc_ijt' ? 'selected' : '' }}>COC for IJT</option>
                        <option value="coc_ladderized" {{ old('certificate_type') === 'coc_ladderized' ? 'selected' : '' }}>COC for Ladderized</option>
                    </select>
                </div>
            </section>

            <section class="consult-card">
                <div class="medicine-header">
                    <h3>Medicine Dispensing</h3>
                </div>
                <div class="form-group">
                    <label for="consultMedicineSelect">Select Medicine (Inventory)</label>
                    <div class="medicine-selection-row">
                        <select name="item_id" id="consultMedicineSelect" class="form-control">
                            <option value="">-- No Medicine Issued --</option>
                            @foreach($items as $item)
                                @php
                                    $availableDispensingQuantity = $item->hasDispensingConversion()
                                        ? $item->availableDispensingQuantity()
                                        : (float) $item->quantity;
                                    $issueUnit = $item->hasDispensingConversion()
                                        ? ($item->dispensing_unit ?: $item->unit)
                                        : ($item->unit ?: 'pcs');
                                    $stockDisplay = rtrim(rtrim(number_format((float) $item->quantity, 2, '.', ''), '0'), '.');
                                    $availableDisplay = rtrim(rtrim(number_format($availableDispensingQuantity, 2, '.', ''), '0'), '.');
                                    $isLowStock = (float) $item->quantity <= (float) ($item->minimum_stock ?? 0);
                                @endphp
                                <option
                                    value="{{ $item->id }}"
                                    data-stock-unit="{{ $item->unit ?: 'pcs' }}"
                                    data-dispensing-unit="{{ $issueUnit }}"
                                    data-has-conversion="{{ $item->hasDispensingConversion() ? '1' : '0' }}"
                                    data-units-per-stock="{{ $item->units_per_stock_unit ?: 1 }}"
                                    data-available-dispensing="{{ $availableDispensingQuantity }}"
                                    data-low-stock="{{ $isLowStock ? '1' : '0' }}"
                                    {{ (string) old('item_id') === (string) $item->id ? 'selected' : '' }}
                                >
                                    {{ $item->name }} (Available: {{ $availableDisplay }} {{ $issueUnit }}@if($item->hasDispensingConversion()) | {{ $stockDisplay }} {{ $item->unit }}@endif)
                                </option>
                            @endforeach
                        </select>
                        <button type="button" id="open-tally-btn" class="btn btn-info btn-sm inventory-tally-trigger" aria-controls="right-inventory-panel" aria-expanded="false">&#128193; View Live Stock Tally</button>
                    </div>
                    <div class="selected-stock" id="selectedMedicineStock" aria-live="polite"></div>
                </div>
                <div class="form-group">
                    <label id="consultIssuedQuantityLabel" for="consultIssuedQuantityInput">Quantity to Issue</label>
                    <input type="number" name="issued_quantity" id="consultIssuedQuantityInput" class="form-control" min="0" step="0.01" placeholder="Enter amount" value="{{ old('issued_quantity') }}">
                    <div class="form-help" id="consultIssuedQuantityHelp">Select a medicine to see the dispensing unit and available stock.</div>
                </div>
            </section>

            <section class="consult-card">
                <h3>Clinical Findings</h3>
                <div class="form-group">
                    <label for="consultRemarks">Remarks / Assessment</label>
                    <textarea name="remarks" id="consultRemarks" class="form-control" rows="5" required placeholder="Describe symptoms or concerns...">{{ old('remarks') }}</textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-save">Save & Finalize Consultation</button>
                    <a href="{{ route($walkinIndexRoute) }}" class="btn-cancel">Cancel</a>
                </div>
            </section>
        </form>
    </div>
</div>

<div id="right-inventory-panel" aria-hidden="true" aria-label="Live medicine stock tally">
    <div class="inventory-drawer-heading">
        <x-outline-icon name="cube" />
        <h2>Live Medicine Stock</h2>
        <button type="button" id="close-tally-btn" aria-label="Close inventory tally">&times;</button>
    </div>
    <p class="inventory-drawer-subtitle">{{ $items->count() }} available medicine {{ $items->count() === 1 ? 'item' : 'items' }}</p>
    <div class="inventory-tally-list">
        @forelse($items as $item)
            @php
                $drawerAvailable = $item->hasDispensingConversion() ? $item->availableDispensingQuantity() : (float) $item->quantity;
                $drawerUnit = $item->hasDispensingConversion() ? ($item->dispensing_unit ?: $item->unit) : ($item->unit ?: 'pcs');
                $drawerAvailableDisplay = rtrim(rtrim(number_format($drawerAvailable, 2, '.', ''), '0'), '.');
                $drawerStockDisplay = rtrim(rtrim(number_format((float) $item->quantity, 2, '.', ''), '0'), '.');
                $drawerLowStock = (float) $item->quantity <= (float) ($item->minimum_stock ?? 0);
            @endphp
            <article class="inventory-tally-item" data-inventory-item="{{ $item->id }}">
                <div class="inventory-tally-row">
                    <span class="inventory-tally-name">{{ $item->name }}</span>
                    <span class="stock-badge {{ $drawerLowStock ? 'low' : '' }}">{{ $drawerLowStock ? 'Low' : 'In Stock' }}</span>
                </div>
                <div class="inventory-tally-meta">
                    {{ $drawerAvailableDisplay }} {{ $drawerUnit }} available
                    @if($item->hasDispensingConversion())
                        | {{ $drawerStockDisplay }} {{ $item->unit }} in storage
                    @endif
                </div>
            </article>
        @empty
            <div class="documents-empty">No medicines are currently available.</div>
        @endforelse
    </div>
</div>

<script>
    (function () {
        const medicineSelect = document.getElementById('consultMedicineSelect');
        const quantityLabel = document.getElementById('consultIssuedQuantityLabel');
        const quantityHelp = document.getElementById('consultIssuedQuantityHelp');
        const quantityInput = document.getElementById('consultIssuedQuantityInput');
        const selectedStock = document.getElementById('selectedMedicineStock');
        const drawer = document.getElementById('right-inventory-panel');
        const openButton = document.getElementById('open-tally-btn');
        const closeButton = document.getElementById('close-tally-btn');
        const workspace = document.querySelector('.consultation-workspace');
        const documentsPanel = document.getElementById('consultation-documents-panel');
        const openDocumentsButton = document.getElementById('open-documents-btn');
        const closeDocumentsButton = document.getElementById('close-documents-btn');

        const formatQty = function (value) {
            const numeric = Number(value || 0);
            if (Number.isNaN(numeric)) {
                return '0';
            }
            return Number.isInteger(numeric) ? String(numeric) : numeric.toFixed(2).replace(/\.?0+$/, '');
        };

        const setDrawerState = function (isOpen) {
            drawer.classList.toggle('open', isOpen);
            drawer.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            openButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            if (isOpen) {
                closeButton.focus();
            }
        };

        const setDocumentsState = function (isOpen) {
            workspace.classList.toggle('documents-open', isOpen);
            documentsPanel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            openDocumentsButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            if (isOpen) {
                closeDocumentsButton.focus();
            } else {
                openDocumentsButton.focus();
            }
        };

        const updateMedicineStock = function () {
            const selected = medicineSelect.options[medicineSelect.selectedIndex];
            document.querySelectorAll('[data-inventory-item]').forEach(function (item) {
                item.classList.toggle('selected', Boolean(selected && selected.value && item.dataset.inventoryItem === selected.value));
            });

            quantityInput.setCustomValidity('');
            if (!selected || !selected.value) {
                quantityLabel.textContent = 'Quantity to Issue';
                quantityHelp.textContent = 'Select a medicine to see the dispensing unit and available stock.';
                quantityInput.placeholder = 'Enter amount';
                quantityInput.removeAttribute('max');
                selectedStock.className = 'selected-stock';
                selectedStock.textContent = '';
                return;
            }

            const dispensingUnit = selected.dataset.dispensingUnit || selected.dataset.stockUnit || 'unit';
            const stockUnit = selected.dataset.stockUnit || 'pcs';
            const availableValue = Number(selected.dataset.availableDispensing || 0);
            const available = formatQty(availableValue);
            const hasConversion = selected.dataset.hasConversion === '1';
            const unitsPerStock = formatQty(selected.dataset.unitsPerStock || 1);
            const isLowStock = selected.dataset.lowStock === '1';

            quantityLabel.textContent = 'Quantity to Issue (' + dispensingUnit + ')';
            quantityInput.placeholder = 'Enter ' + dispensingUnit + ' quantity';
            quantityInput.max = String(availableValue);
            quantityHelp.textContent = hasConversion
                ? 'Available: ' + available + ' ' + dispensingUnit + ' (' + unitsPerStock + ' ' + dispensingUnit + ' per ' + stockUnit + ').'
                : 'Available: ' + available + ' ' + stockUnit + '.';
            selectedStock.className = 'selected-stock visible' + (isLowStock ? ' low' : '');
            selectedStock.textContent = (isLowStock ? 'Low stock: ' : 'Available: ') + available + ' ' + dispensingUnit;
        };

        quantityInput.addEventListener('input', function () {
            const selected = medicineSelect.options[medicineSelect.selectedIndex];
            const available = selected && selected.value ? Number(selected.dataset.availableDispensing || 0) : 0;
            const requested = Number(quantityInput.value || 0);
            quantityInput.setCustomValidity(requested > available ? 'Quantity cannot exceed the available medicine stock.' : '');
        });
        medicineSelect.addEventListener('change', updateMedicineStock);
        openButton.addEventListener('click', function () { setDrawerState(true); });
        closeButton.addEventListener('click', function () { setDrawerState(false); });
        openDocumentsButton.addEventListener('click', function () { setDocumentsState(true); });
        closeDocumentsButton.addEventListener('click', function () { setDocumentsState(false); });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && drawer.classList.contains('open')) {
                setDrawerState(false);
            } else if (event.key === 'Escape' && workspace.classList.contains('documents-open')) {
                setDocumentsState(false);
            }
        });

        updateMedicineStock();
    })();
</script>
@endsection
