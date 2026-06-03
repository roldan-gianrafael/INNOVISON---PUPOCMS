@extends('layouts.admin')

@section('title', 'Reports')

@push('styles')
<style>
    /* --- DASHBOARD CONTAINER --- */
    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    /* --- REPORTS HEADER --- */
    .reports-header {
        margin-bottom: 24px;
        padding-bottom: 24px;
        border-bottom: 1px solid #e2e8f0;
    }

    .reports-header-kicker {
        margin: 0 0 8px;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 1.2px;
        color: #70131B;
        text-transform: uppercase;
    }

    .reports-header-title {
        margin: 0 0 12px;
        font-size: 32px;
        font-weight: 950;
        letter-spacing: -0.02em;
        color: #111827;
        line-height: 1.1;
    }

    .reports-header-description {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: #64748b;
        max-width: 720px;
        line-height: 1.5;
    }

    html[data-theme="dark"] .reports-header-kicker {
        color: #facc15;
    }

    html[data-theme="dark"] .reports-header-title {
        color: #f8fafc;
    }

    html[data-theme="dark"] .reports-header-description {
        color: #cbd5e1;
    }

    /* --- TOP STATS ROW (Naiiwan sa pwesto gaya ng gusto mo) --- */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 40px;
    }

    .stat-card-mini {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        border-left: 5px solid #8B0000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
    }

    .stat-card-mini span { font-size: 13px; color: #64748b; font-weight: 600; text-transform: uppercase; }
    .stat-card-mini h3 { font-size: 24px; color: #4b0f17; margin: 5px 0 0 0; }

    .reports-section-title {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-size: 18px;
        color: #111827;
        margin: 0;
        font-weight: 800;
        padding: 10px 18px;
        border-radius: 0 0 14px 14px;
        border: 0;
        border-bottom: 2px solid rgba(112, 19, 27, 0.72);
        background: transparent;
        box-shadow: none;
    }

    .reports-section-title svg {
        width: 18px;
        height: 18px;
        flex: 0 0 auto;
    }

    .reports-title-frame {
        display: inline-flex;
        align-items: center;
        border-radius: 0;
        border: 0;
        padding: 0;
        background: transparent;
        box-shadow: none;
        margin-bottom: 20px;
    }

    .reports-frame {
        position: relative;
        overflow: hidden;
        border-radius: 22px;
        border: 1px solid rgba(112, 19, 27, 0.10);
        padding: 24px;
        background: linear-gradient(180deg, rgba(255,255,255,0.92), rgba(250,244,246,0.96));
        box-shadow: 0 18px 34px rgba(112, 19, 27, 0.08);
    }

    .reports-frame::before {
        content: "";
        position: absolute;
        top: 0;
        left: 14px;
        right: 14px;
        height: 5px;
        background: #70131B;
        border-radius: 999px;
        pointer-events: none;
        z-index: 1;
    }

    /* --- REPORT BUTTONS (Gayang-gaya sa Dashboard Stats Cards) --- */
    .report-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr); /* 3 Columns para sa MAR, Inventory, Appt */
        gap: 20px;
    }

    .report-card {
        background: #70131B; /* Dark Maroon/Dark Navy style mo */
        color: #fff;
        border-radius: 16px;
        padding: 24px 20px;
        text-decoration: none; /* Alisin ang underline ng link */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 160px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid rgba(112, 19, 27, 0.22);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .report-card > * {
        position: relative;
        z-index: 2;
    }

    .report-card::after {
        content: "";
        position: absolute;
        top: -45%;
        bottom: -45%;
        left: -85%;
        width: 55%;
        background: linear-gradient(
            120deg,
            transparent 0%,
            rgba(255, 255, 255, 0.16) 45%,
            rgba(255, 255, 255, 0.34) 50%,
            transparent 100%
        );
        transform: translateX(0) skewX(-18deg);
        transition: transform 0.65s ease;
        pointer-events: none;
        z-index: 1;
    }

    .report-card:hover {
        background: #facc15 !important;
        background-image: none !important;
        color: #111111 !important;
        transform: translateY(-8px);
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.12),
            0 20px 30px rgba(139, 0, 0, 0.22);
        filter: none;
        border-color: #facc15 !important;
    }

    .report-card:hover::after {
        transform: translateX(360%) skewX(-18deg);
    }

    .report-card:hover .report-label,
    .report-card:hover .report-main-title,
    .report-card:hover .report-badge,
    .report-card:hover .report-card-icon {
        color: #111111 !important;
    }

    .report-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 18px;
    }

    .report-card-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 14px;
        color: #facc15;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.18);
        transition: transform 0.3s ease, background 0.3s ease, color 0.3s ease;
    }

    .report-card-icon svg {
        width: 22px;
        height: 22px;
        stroke-width: 1.8;
    }

    .report-card:hover .report-card-icon {
        background: rgba(17, 17, 17, 0.10);
        border-color: rgba(17, 17, 17, 0.14);
        transform: translateX(3px) scale(1.04);
    }

    .report-label {
        font-size: 14px;
        font-weight: 500;
        color: #cbd5e1; /* Muted gray text */
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .report-main-title {
        font-size: 22px;
        font-weight: 700;
        color: #fff;
        line-height: 1.2;
    }

    /* The "Pill" at the bottom gaya ng "Action Needed", etc. */
    .report-badge {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 8px;
        width: fit-content;
        background: rgba(255,255,255,0.1);
        color: #fff;
    }

    .report-card:hover .report-badge {
        background: rgba(17, 17, 17, 0.10) !important;
    }

    .back-nav {
        margin-top: 40px;
        text-align: center;
    }

    .btn-back-dashboard {
        color: #64748b;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: color 0.2s;
    }

    .btn-back-dashboard:hover { color: #8B0000; }

    html[data-theme="dark"] .stat-card-mini span,
    html[data-theme="dark"] .stat-card-mini h3,
    html[data-theme="dark"] .reports-section-title {
        color: #ffffff;
        border-bottom-color: rgba(143, 34, 48, 0.70);
    }

    html[data-theme="dark"] .reports-title-frame {
        background: transparent;
        border: 0;
        box-shadow: none;
    }

    html[data-theme="dark"] .reports-frame {
        background: linear-gradient(180deg, rgba(70, 19, 27, 0.92), rgba(46, 13, 19, 0.96));
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 20px 38px rgba(0, 0, 0, 0.24);
    }

    html[data-theme="dark"] .reports-frame::before {
        background: #facc15;
    }
</style>
@endpush

@section('content')
@php
    $role = \App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '');
    $dashboardUrl = $role === \App\Models\User::ROLE_ADMIN ? url('/assistant/dashboard') : url('/admin/dashboard');
    $marUrl = $role === \App\Models\User::ROLE_ADMIN ? url('/assistant/reports/mar') : url('/admin/reports/mar');
    $inventorySummaryUrl = $role === \App\Models\User::ROLE_ADMIN ? url('/assistant/reports/inventory-summary') : url('/admin/reports/inventory-summary');
    $appointmentStatisticsUrl = $role === \App\Models\User::ROLE_ADMIN ? url('/assistant/reports/appointment-statistics') : url('/admin/reports/appointment-statistics');
    $healthFormsUrl = $role === \App\Models\User::ROLE_ADMIN ? url('/assistant/reports/health-forms') : url('/admin/reports/health-forms');
    $feedbacksUrl = $role === \App\Models\User::ROLE_ADMIN ? url('/assistant/reports/feedbacks') : url('/admin/reports/feedbacks');
    $exportHubUrl = $role === \App\Models\User::ROLE_ADMIN ? url('/assistant/reports/export-hub') : url('/admin/reports/export-hub');
@endphp
<div class="dashboard-container">
    <div class="reports-frame">
        {{-- Reports Header --}}
        <div class="reports-header">
            <h1 class="reports-header-title">Reports</h1>
            <p class="reports-header-description">Access comprehensive reports on clinic operations, medical records, inventory, and patient statistics.</p>
        </div>

        <div class="report-grid">
        
        <a href="{{ $marUrl }}" class="report-card report-card-primary">
            <div>
                <div class="report-label">Personnel Records</div>
                <div class="report-main-title">Medical Accomplishment (MAR)</div>
            </div>
            <div class="report-card-footer">
                <div class="report-badge">Action Needed</div>
                <span class="report-card-icon"><x-outline-icon name="clipboard-document-list" /></span>
            </div>
        </a>

        <a href="{{ $inventorySummaryUrl }}" class="report-card report-card-primary">
            <div>
                <div class="report-label">Stocks & Supplies</div>
                <div class="report-main-title">Inventory Summary</div>
            </div>
            <div class="report-card-footer">
                <div class="report-badge">All Records</div>
                <span class="report-card-icon"><x-outline-icon name="cube" /></span>
            </div>
        </a>

        <a href="{{ $appointmentStatisticsUrl }}" class="report-card report-card-primary">
            <div>
                <div class="report-label">Consultations</div>
                <div class="report-main-title">Appointment Statistics</div>
            </div>
            <div class="report-card-footer">
                <div class="report-badge">Scheduled</div>
                <span class="report-card-icon"><x-outline-icon name="calendar-days" /></span>
            </div>
        </a>

        <a href="{{ $exportHubUrl }}" class="report-card">
            <div>
                <div class="report-label">Summary Report</div>
                <div class="report-main-title">Export Reports</div>
            </div>
            <div class="report-card-footer">
                <div class="report-badge">All Reports</div>
                <span class="report-card-icon"><x-outline-icon name="arrow-long-right" /></span>
            </div>
        </a>

        <a href="{{ $feedbacksUrl }}" class="report-card">
            <div>
                <div class="report-label">Patient Experience</div>
                <div class="report-main-title">Feedbacks</div>
            </div>
            <div class="report-card-footer">
                <div class="report-badge">Rate of Clinic</div>
                <span class="report-card-icon"><x-outline-icon name="megaphone" /></span>
            </div>
        </a>

        <a href="{{ $healthFormsUrl }}" class="report-card">
            <div>
                <div class="report-label">Issued Documents</div>
                <div class="report-main-title">Health Forms</div>
            </div>
            <div class="report-card-footer">
                <div class="report-badge">Issued Only</div>
                <span class="report-card-icon"><x-outline-icon name="document-text" /></span>
            </div>
        </a>

        @if($role !== \App\Models\User::ROLE_ADMIN)
            <a href="{{ route('admin.logs') }}" class="report-card">
                <div>
                    <div class="report-label">System Monitoring</div>
                    <div class="report-main-title">Audit Trail</div>
                </div>
                <div class="report-card-footer">
                    <div class="report-badge">Restricted</div>
                    <span class="report-card-icon"><x-outline-icon name="eye" /></span>
                </div>
            </a>
        @endif

        </div>
    </div>

    <div class="back-nav">
        <a href="{{ $dashboardUrl }}" class="btn-back-dashboard">
            &larr; Back to System Dashboard
        </a>
    </div>

</div>
@endsection
