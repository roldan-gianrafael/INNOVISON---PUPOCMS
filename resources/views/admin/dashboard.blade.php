@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* --- DASHBOARD CONTAINER --- */
    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* --- 1. STATS ROW (The "MedTrackr" Style) --- */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr); /* 5 Columns side-by-side */
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: #70131B; /* Dark Navy/Slate background like reference */
        color: #fff;
        border-radius: 16px;
        padding: 24px 20px;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 140px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
    }

    /* Distinct Colors for active states if needed, 
       but keeping them uniform looks more professional like the reference */
    
    .stat-label {
        font-size: 13px;
        font-weight: 500;
        color: #94a3b8; /* Muted gray text */
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
    }

    .stat-value {
        font-size: 38px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 12px;
        line-height: 1;
    }

    /* The "Pill" at the bottom (e.g. "Last 7 days") */
    .stat-badge {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        width: fit-content;
    }

    /* Badge Colors */
    .badge-neutral { background: rgba(255,255,255,0.1); color: #cbd5e1; }
    .badge-warning { background: rgba(245, 158, 11, 0.2); color: #fbbf24; } /* Orange tint */
    .badge-success { background: rgba(16, 185, 129, 0.2); color: #34d399; } /* Green tint */
    .badge-info    { background: rgba(59, 130, 246, 0.2); color: #60a5fa; } /* Blue tint */
    .badge-danger  { background: rgba(239, 68, 68, 0.2);  color: #f87171; } /* Red tint */


    /* --- 2. STATISTICS GRAPH PANEL --- */
    .chart-panel {
        margin-bottom: 24px;
    }

    .chart-header {
        margin-bottom: 16px;
    }

    .chart-title-wrap {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .chart-subtitle {
        margin: 0;
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
    }

    .chart-insight {
        font-size: 12px;
        font-weight: 700;
        color: #8B0000;
        background: #fff1f2;
        border: 1px solid #ffe4e6;
        border-radius: 999px;
        padding: 6px 12px;
        white-space: nowrap;
    }

    .charts-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .chart-card {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #fcfcfd;
        padding: 14px;
    }

    .chart-card-wide {
        grid-column: span 2;
    }

    .chart-card-title {
        margin: 0 0 10px;
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }

    .chart-wrap {
        position: relative;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
        padding: 12px;
    }

    .chart-large {
        height: 320px;
    }

    .chart-small {
        height: 260px;
    }

    .chart-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }

    .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #334155;
    }

    .legend-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .dot-pending { background: #8f2230; }
    .dot-upcoming { background: #a83242; }
    .dot-completed { background: #c15b69; }
    .dot-cancelled { background: #5a0f16; }

    /* --- 3. RECENT ACTIVITY PANEL (Styled like the charts in reference) --- */
    .panel {
        background: #fff; /* Keep white for readability of table */
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .panel-title {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .btn-view-all {
        font-size: 13px;
        font-weight: 600;
        color: #8B0000;
        text-decoration: none;
        padding: 6px 12px;
        background: #fff1f2;
        border-radius: 6px;
        transition: 0.2s;
    }
    .btn-view-all:hover { background: #ffe4e6; }

    /* Table Styles */
    table { width: 100%; border-collapse: separate; border-spacing: 0; }
    th { 
        text-align: left; 
        padding: 12px 16px; 
        color: #64748b; 
        font-size: 12px; 
        font-weight: 600; 
        text-transform: uppercase; 
        border-bottom: 1px solid #f1f5f9;
    }
    td { 
        padding: 16px; 
        border-bottom: 1px solid #f8fafc; 
        font-size: 14px; 
        color: #334155; 
    }
    tr:last-child td { border-bottom: none; }

    /* Status Pills in Table */
    .status-pill { padding: 4px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .st-approved { background: #dcfce7; color: #15803d; }
    .st-pending { background: #fffbeb; color: #b45309; }
    .st-completed { background: #dbeafe; color: #1e40af; }
    .st-cancelled { background: #fee2e2; color: #b91c1c; }

    /* Responsive */
    @media (max-width: 1200px) {
        .stats-grid { grid-template-columns: repeat(3, 1fr); }
        .charts-grid { grid-template-columns: 1fr; }
        .chart-card-wide { grid-column: span 1; }
    }
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .chart-large { height: 280px; }
        .chart-small { height: 240px; }
        .chart-insight { display: none; }
    }
    @media (max-width: 500px) { .stats-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
@php
    $role = strtolower((string) (optional(auth()->user())->user_role ?? ''));
    $appointmentsUrl = $role === 'student_assistant' ? url('/assistant/appointments') : url('/admin/appointments');
@endphp
<div class="dashboard-container">
    @php
        $totalAppointments = ($pending ?? 0) + ($upcoming ?? 0) + ($completed ?? 0) + ($cancelled ?? 0);
    @endphp

    <div class="stats-grid">
        
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Patients</div>
                <div class="stat-value">{{ $total }}</div>
            </div>
            <div class="stat-badge badge-neutral">All Records</div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Pending Requests</div>
                <div class="stat-value">{{ $pending }}</div>
            </div>
            <div class="stat-badge badge-warning">Action Needed</div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Upcoming</div>
                <div class="stat-value">{{ $upcoming }}</div>
            </div>
            <div class="stat-badge badge-success">Scheduled</div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Completed</div>
                <div class="stat-value">{{ $completed }}</div>
            </div>
            <div class="stat-badge badge-info">Consultations</div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Cancelled</div>
                <div class="stat-value">{{ $cancelled }}</div>
            </div>
            <div class="stat-badge badge-danger">Inactive</div>
        </div>

    </div>

    <div class="panel chart-panel">
        <div class="panel-header chart-header">
            <div class="chart-title-wrap">
                <h3 class="panel-title">Statistics Overview</h3>
                <p class="chart-subtitle">Current distribution of appointment statuses.</p>
            </div>
            <span class="chart-insight">{{ $totalAppointments }} Total Appointments</span>
        </div>

        <div class="charts-grid">
            <div class="chart-card chart-card-wide">
                <h4 class="chart-card-title">Monthly Appointments ({{ $currentYear }})</h4>
                <div class="chart-wrap chart-large">
                    <canvas id="monthlyBarChart" aria-label="Monthly appointment bar chart" role="img"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h4 class="chart-card-title">Monthly Completion Trend</h4>
                <div class="chart-wrap chart-small">
                    <canvas id="monthlyLineChart" aria-label="Monthly appointment line chart" role="img"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h4 class="chart-card-title">Status Radial Chart</h4>
                <div class="chart-wrap chart-small">
                    <canvas id="statusRadialChart" aria-label="Appointment status radial chart" role="img"></canvas>
                </div>
                <div class="chart-legend">
                    <span class="legend-item"><span class="legend-dot dot-pending"></span>Pending</span>
                    <span class="legend-item"><span class="legend-dot dot-upcoming"></span>Upcoming</span>
                    <span class="legend-item"><span class="legend-dot dot-completed"></span>Completed</span>
                    <span class="legend-item"><span class="legend-dot dot-cancelled"></span>Cancelled</span>
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h3 class="panel-title">Recent Activity</h3>
            <a href="{{ $appointmentsUrl }}" class="btn-view-all">View All</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentAppointments as $appt)
                    <tr>
                        <td style="font-weight: 600;">
                            {{ $appt->name }}<br>
                            <span style="font-size:11px; color:#94a3b8; font-weight:400;">{{ $appt->student_id }}</span>
                        </td>
                        <td>{{ $appt->service }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($appt->date)->format('M d') }}
                            <span style="color:#cbd5e1; margin:0 4px;">&bull;</span>
                            <span style="color:#64748b; font-size:12px;">{{ \Carbon\Carbon::parse($appt->time)->format('g:i A') }}</span>
                        </td>
                        <td>
                            @if($appt->status == 'Approved')
                                <span class="status-pill st-approved">Approved</span>
                            @elseif($appt->status == 'Pending')
                                <span class="status-pill st-pending">Pending</span>
                            @elseif($appt->status == 'Completed')
                                <span class="status-pill st-completed">Completed</span>
                            @else
                                <span class="status-pill st-cancelled">Cancelled</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align: center; padding: 30px; color: #94a3b8;">No recent activity.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Chart === 'undefined') return;

        const monthLabels = @json($monthLabels ?? []);
        const monthlyTotals = @json($monthlyTotals ?? []);
        const monthlyCompleted = @json($monthlyCompleted ?? []);

        const statusValues = [
            Number(@json((int) ($pending ?? 0))),
            Number(@json((int) ($upcoming ?? 0))),
            Number(@json((int) ($completed ?? 0))),
            Number(@json((int) ($cancelled ?? 0)))
        ];

        const monthlyBarCanvas = document.getElementById('monthlyBarChart');
        const monthlyLineCanvas = document.getElementById('monthlyLineChart');
        const statusRadialCanvas = document.getElementById('statusRadialChart');

        if (monthlyBarCanvas) {
            new Chart(monthlyBarCanvas, {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: 'Total Appointments',
                        data: monthlyTotals,
                        backgroundColor: 'rgba(112, 19, 27, 0.72)',
                        borderColor: '#70131B',
                        borderWidth: 1.4,
                        borderRadius: 10,
                        borderSkipped: false,
                        maxBarThickness: 36
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111827',
                            titleColor: '#ffffff',
                            bodyColor: '#e5e7eb',
                            displayColors: false,
                            padding: 10
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: '#475569',
                                font: { size: 11, weight: '600' }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                stepSize: 1,
                                color: '#64748b'
                            },
                            grid: {
                                color: 'rgba(148, 163, 184, 0.25)'
                            }
                        }
                    }
                }
            });
        }

        if (monthlyLineCanvas) {
            new Chart(monthlyLineCanvas, {
                type: 'line',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: 'Completed',
                        data: monthlyCompleted,
                        borderColor: '#8f2230',
                        backgroundColor: 'rgba(143, 34, 48, 0.14)',
                        borderWidth: 2.5,
                        pointRadius: 3.5,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#8f2230',
                        pointBorderWidth: 2,
                        fill: true,
                        tension: 0.35
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111827',
                            titleColor: '#ffffff',
                            bodyColor: '#e5e7eb',
                            displayColors: false,
                            padding: 10
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: '#475569',
                                font: { size: 11, weight: '600' }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                stepSize: 1,
                                color: '#64748b'
                            },
                            grid: {
                                color: 'rgba(148, 163, 184, 0.25)'
                            }
                        }
                    }
                }
            });
        }

        if (statusRadialCanvas) {
            new Chart(statusRadialCanvas, {
                type: 'polarArea',
                data: {
                    labels: ['Pending', 'Upcoming', 'Completed', 'Cancelled'],
                    datasets: [{
                        label: 'Appointment Status',
                        data: statusValues,
                        backgroundColor: [
                            'rgba(143, 34, 48, 0.78)',
                            'rgba(168, 50, 66, 0.78)',
                            'rgba(193, 91, 105, 0.78)',
                            'rgba(90, 15, 22, 0.78)'
                        ],
                        borderColor: ['#8f2230', '#a83242', '#c15b69', '#5a0f16'],
                        borderWidth: 1.2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111827',
                            titleColor: '#ffffff',
                            bodyColor: '#e5e7eb',
                            padding: 10
                        }
                    },
                    scales: {
                        r: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                color: '#64748b',
                                backdropColor: 'rgba(255, 255, 255, 0.7)'
                            },
                            grid: {
                                color: 'rgba(148, 163, 184, 0.25)'
                            },
                            angleLines: {
                                color: 'rgba(148, 163, 184, 0.2)'
                            },
                            pointLabels: {
                                color: '#475569',
                                font: { size: 11, weight: '600' }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
