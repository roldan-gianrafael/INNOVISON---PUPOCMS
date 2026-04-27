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
        border: 2px solid #facc15;
        cursor: pointer;
        position: relative;
    }

    .report-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
        filter: brightness(1.2);
    }

    .report-card.report-card-primary::before {
        content: "";
        position: absolute;
        inset: 8px;
        border-radius: 12px;
        border: 2px solid rgba(112, 19, 27, 0.92);
        pointer-events: none;
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
        margin-top: 15px;
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

    <div class="stats-grid">
        <div class="stat-card-mini">
            <span>Monthly Cases</span>
            <h3>{{ $totalConsultations ?? '0' }}</h3>
        </div>
        <div class="stat-card-mini" style="border-left-color: #70131B;">
            <span>Low Stock Items</span>
            <h3>{{ $lowStockCount ?? '0' }}</h3>
        </div>
        <div class="stat-card-mini" style="border-left-color: #8f2230;">
            <span>Scheduled Today</span>
            <h3>{{ $appointmentsToday ?? '0' }}</h3>
        </div>
    </div>

    <div class="reports-frame">
        <div class="reports-title-frame">
            <h2 class="reports-section-title"><x-outline-icon name="chart-bar" />Select Report to Generate</h2>
        </div>

        <div class="report-grid">
        
        <a href="{{ $marUrl }}" class="report-card report-card-primary">
            <div>
                <div class="report-label">Personnel Records</div>
                <div class="report-main-title">Medical Accomplishment (MAR)</div>
            </div>
            <div class="report-badge">Action Needed</div>
        </a>

        <a href="{{ $inventorySummaryUrl }}" class="report-card report-card-primary">
            <div>
                <div class="report-label">Stocks & Supplies</div>
                <div class="report-main-title">Inventory Summary</div>
            </div>
            <div class="report-badge">All Records</div>
        </a>

        <a href="{{ $appointmentStatisticsUrl }}" class="report-card report-card-primary">
            <div>
                <div class="report-label">Consultations</div>
                <div class="report-main-title">Appointment Statistics</div>
            </div>
            <div class="report-badge">Scheduled</div>
        </a>

        <a href="{{ $exportHubUrl }}" class="report-card">
            <div>
                <div class="report-label">Summary Report</div>
                <div class="report-main-title">Export Reports</div>
            </div>
            <div class="report-badge">All Reports</div>
        </a>

        <a href="{{ $feedbacksUrl }}" class="report-card">
            <div>
                <div class="report-label">Patient Experience</div>
                <div class="report-main-title">Feedbacks</div>
            </div>
            <div class="report-badge">Rate of Clinic</div>
        </a>

        <a href="{{ $healthFormsUrl }}" class="report-card">
            <div>
                <div class="report-label">Issued Documents</div>
                <div class="report-main-title">Health Forms</div>
            </div>
            <div class="report-badge">Issued Only</div>
        </a>

        @if($role !== \App\Models\User::ROLE_ADMIN)
            <a href="{{ route('admin.logs') }}" class="report-card">
                <div>
                    <div class="report-label">System Monitoring</div>
                    <div class="report-main-title">Audit Trail</div>
                </div>
                <div class="report-badge">Restricted</div>
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
