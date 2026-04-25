@extends('layouts.admin')

@section('title', 'Medical Assessment - Print')

@push('styles')
<style>
@media print {
    html, body {
        background: white !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
    }

    body * {
        visibility: hidden !important;
    }

    .no-print,
    .no-print *,
    .sidebar,
    .sidebar-toggle,
    .sidebar-scroll-indicator,
    .medicine-alert-fab,
    .medicine-alert-panel,
    .medicine-hover-hint,
    .accessibility-launch-admin,
    .asw-menu-btn,
    .asw-widget,
    .asw-menu,
    .asw-overlay,
    .profile-dropdown,
    .logout-link,
    header,
    nav,
    footer,
    aside,
    .admin-layout > .sidebar,
    .admin-layout > .main > *:not(.print-container) {
        display: none !important;
    }

    .admin-layout,
    .main {
        display: block !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .print-container,
    .print-container * {
        visibility: visible !important;
    }

    .print-container {
        position: static !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0.35in 0.5in !important;
        box-shadow: none !important;
        border: none !important;
        max-width: none !important;
        min-height: auto !important;
        background: white !important;
    }

    @page {
        size: 8.5in 13in;
        margin: 0 !important;
    }
}

body { background-color: #e2e8f0; }

.print-container {
    font-family: Arial, sans-serif;
    color: #000;
    background: #fff;
    max-width: 8.5in;
    min-height: 13in;
    margin: 20px auto;
    padding: 0.45in 0.55in;
    box-shadow: 0 0 15px rgba(0,0,0,0.3);
    box-sizing: border-box;
}

.form-title {
    text-align: center;
    font-weight: bold;
    font-style: italic;
    font-size: 16px;
    margin: 0 0 22px;
}

.row {
    display: flex;
    margin-bottom: 8px;
    gap: 10px;
    align-items: baseline;
}

.label {
    font-weight: bold;
    white-space: nowrap;
    font-size: 13px;
}

.field {
    border-bottom: 1px solid #000;
    flex: 1;
    padding-left: 5px;
    min-height: 18px;
    font-size: 14px;
    font-weight: bold;
    color: #000;
}

</style>
@endpush

@section('content')
<div class="no-print" style="text-align: right; padding: 10px; max-width: 8.5in; margin: auto;">
    <a href="{{ route('admin.health_records') }}" class="btn" style="background: #64748b; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; margin-right: 10px;">
        BACK TO RECORDS
    </a>
    <a href="{{ route('admin.show_health', $profile->id) }}" class="btn" style="background: #800000; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; margin-right: 10px;">
        HEALTH FORM
    </a>
    <button onclick="window.print()" class="btn" style="background: #f59e0b; border: none; padding: 10px 25px; font-weight: bold; color: white; border-radius: 5px; cursor: pointer;">
        PRINT ASSESSMENT
    </button>
</div>

<div class="print-container">
    <div class="form-title">MEDICAL ASSESSMENT SUMMARY</div>

    <div class="row">
        <span class="label">Date:</span>
        <div class="field">{{ optional($profile->created_at)->format('m/d/Y') }}</div>
        <span class="label">Date of Birth:</span>
        <div class="field">{{ !empty($profile->user->DOB) ? \Carbon\Carbon::parse($profile->user->DOB)->format('m/d/Y') : '' }}</div>
    </div>
    <div class="row">
        <span class="label">Height:</span>
        <div class="field">{{ $profile->height ?? '' }}</div>
        <span class="label">ft</span>
        <span class="label">Weight:</span>
        <div class="field">{{ $profile->weight ?? '' }}</div>
        <span class="label">lbs</span>
    </div>
    <div class="row">
        <span class="label">BP:</span>
        <div class="field">&nbsp;</div>
        <span class="label">RR:</span>
        <div class="field">&nbsp;</div>
        <span class="label">Temp:</span>
        <div class="field">&nbsp;</div>
    </div>
    <div class="row">
        <span class="label">Covid Positive?</span>
        <div class="field">&nbsp;</div>
        <span class="label">Date:</span>
        <div class="field">&nbsp;</div>
    </div>
    <div class="row">
        <span class="label">Medical certificate issued by: Dr</span>
        <div class="field">{{ $profile->medical_certificate_issued_by ?? '' }}</div>
        <span class="label">Date:</span>
        <div class="field">&nbsp;</div>
    </div>
    <div class="row">
        <span class="label">Chest X-ray Result:</span>
        <div class="field">{{ $profile->chest_xray_result ? 'Uploaded' : '' }}</div>
        <span class="label">Date:</span>
        <div class="field">&nbsp;</div>
    </div>
</div>
@endsection
