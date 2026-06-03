@extends('layouts.admin')

@section('title', 'Appointment Statistics')

@push('styles')
<style>
    .appt-stats-shell {
        max-width: 1380px;
        margin: 0 auto;
        padding: 22px;
    }
    .appt-stats-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 24px;
    }
    .appt-stats-title {
        margin: 0;
        font-size: 30px;
        font-weight: 900;
        color: #111827;
    }
    .appt-stats-copy {
        margin: 8px 0 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
        max-width: 760px;
    }
    .appt-stats-back {
        color: #64748b;
        text-decoration: none;
        font-weight: 800;
        white-space: nowrap;
    }
    .appt-stats-filter {
        display: flex;
        gap: 10px;
        align-items: end;
        margin-bottom: 22px;
        flex-wrap: wrap;
    }
    .appt-stats-filter label {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .appt-stats-filter input {
        min-width: 220px;
        height: 46px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 0 14px;
        color: #111827;
        background: #ffffff;
    }
    .appt-stats-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        min-height: 46px;
        padding: 0 18px;
        border-radius: 999px;
        border: 1px solid #8f2230;
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #ffffff;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20);
        transition: color .08s linear, transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        z-index: 0;
    }
    .appt-stats-btn::after {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(120deg,
                rgba(255, 248, 196, 0) 0%,
                rgba(255, 239, 181, 0.14) 22%,
                rgba(255, 239, 181, 0.52) 48%,
                rgba(255, 239, 181, 0.14) 72%,
                rgba(255, 248, 196, 0) 100%);
        transform: translateX(-135%);
        transition: transform 1.5s ease;
        z-index: -1;
    }
    .appt-stats-btn:hover {
        transform: translateY(-1px);
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16);
    }
    .appt-stats-btn:hover::after {
        transform: translateX(135%);
    }
    .appt-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }
    .appt-stat-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 20px 22px;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.10);
        border-top: 5px solid #7f1d2d;
    }
    .appt-stat-card span {
        display: block;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #64748b;
    }
    .appt-stat-card strong {
        display: block;
        margin-top: 8px;
        font-size: 28px;
        line-height: 1.1;
        font-weight: 900;
        color: #111827;
    }
    .appt-stats-layout {
        display: grid;
        grid-template-columns: 1.2fr .9fr;
        gap: 22px;
        align-items: start;
    }
    .appt-panel {
        background: #ffffff;
        border-radius: 20px;
        padding: 22px;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.10);
    }
    .appt-panel h3 {
        margin: 0 0 14px;
        font-size: 18px;
        font-weight: 900;
        color: #70131B;
    }
    .appt-table {
        width: 100%;
        border-collapse: collapse;
    }
    .appt-table th,
    .appt-table td {
        padding: 12px 10px;
        border-bottom: 1px solid #e5e7eb;
        text-align: left;
        font-size: 14px;
        color: #111827;
    }
    .appt-table th {
        font-size: 12px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .appt-empty {
        color: #64748b;
        font-size: 14px;
        padding: 10px 0 2px;
    }
    .appt-trend-list {
        display: grid;
        gap: 10px;
    }
    .appt-trend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
    }
    .appt-trend-item span {
        color: #475569;
        font-size: 14px;
        font-weight: 700;
    }
    .appt-trend-item strong {
        color: #70131B;
        font-size: 15px;
        font-weight: 900;
    }
    .appt-disease-list {
        display: grid;
        gap: 10px;
    }
    .appt-disease-item {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 12px;
        align-items: center;
        padding: 12px 14px;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
    }
    .appt-disease-rank {
        width: 32px;
        height: 32px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #70131B;
        color: #ffffff;
        font-size: 12px;
        font-weight: 900;
    }
    .appt-disease-name {
        margin: 0;
        font-size: 14px;
        font-weight: 800;
        color: #111827;
    }
    .appt-disease-category {
        margin: 3px 0 0;
        font-size: 12px;
        color: #64748b;
    }
    .appt-disease-count {
        color: #70131B;
        font-size: 15px;
        font-weight: 900;
        white-space: nowrap;
    }
    @media (max-width: 1024px) {
        .appt-stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .appt-stats-layout { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .appt-stats-grid { grid-template-columns: 1fr; }
        .appt-stats-head { flex-direction: column; }
    }
</style>
@endpush

@section('content')
@php
    $role = \App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '');
    $reportsHomeUrl = $role === \App\Models\User::ROLE_ADMIN ? url('/assistant/reports') : url('/admin/reports');
@endphp
<div class="appt-stats-shell">
    <div class="appt-stats-head">
        <div>
            <h1 class="appt-stats-title">Appointment Statistics</h1>
            <p class="appt-stats-copy">Review monthly appointment volume, status distribution, service demand, and daily activity from one report view.</p>
        </div>
        <a href="{{ $reportsHomeUrl }}" class="appt-stats-back">&larr; Back to Reports</a>
    </div>

    <form method="GET" class="appt-stats-filter">
        <div>
            <label for="apptStatsMonth">Month</label>
            <input id="apptStatsMonth" type="month" name="month" value="{{ $monthFilter }}">
        </div>
        <button type="submit" class="appt-stats-btn">Open Statistics</button>
    </form>

    <div class="appt-stats-grid">
        <div class="appt-stat-card"><span>Total Appointments</span><strong>{{ $totalAppointments }}</strong></div>
        <div class="appt-stat-card"><span>Approved / Scheduled</span><strong>{{ $approvedCount }}</strong></div>
        <div class="appt-stat-card"><span>Completed</span><strong>{{ $completedCount }}</strong></div>
        <div class="appt-stat-card"><span>Cancelled</span><strong>{{ $cancelledCount }}</strong></div>
        <div class="appt-stat-card"><span>Online</span><strong>{{ $onlineCount }}</strong></div>
        <div class="appt-stat-card"><span>Walk-in</span><strong>{{ $walkInCount }}</strong></div>
        <div class="appt-stat-card"><span>Month</span><strong>{{ \Carbon\Carbon::parse($monthFilter . '-01')->format('F Y') }}</strong></div>
        <div class="appt-stat-card"><span>Most Common Illness</span><strong>{{ $topDisease->condition ?? 'No data yet' }}</strong></div>
    </div>

    <section class="appt-panel" style="margin-top: 22px;">
        <h3>Daily Appointment Trend</h3>
        @if($dailyTrend->count() > 0)
            <canvas id="appointmentTrendChart" height="80"></canvas>
        @else
            <div class="appt-empty">No appointment data available for the selected month.</div>
        @endif
    </section>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvasEl = document.getElementById('appointmentTrendChart');
        if (!canvasEl) return;

        const chartData = @json($dailyTrend);
        if (!chartData || chartData.length === 0) return;

        const ctx = canvasEl.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.map(d => d.day),
                datasets: [{
                    label: 'Appointments',
                    data: chartData.map(d => d.count),
                    borderColor: '#70131B',
                    backgroundColor: 'rgba(112, 19, 27, 0.1)',
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointBackgroundColor: '#70131B',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>
@endpush

@endsection
