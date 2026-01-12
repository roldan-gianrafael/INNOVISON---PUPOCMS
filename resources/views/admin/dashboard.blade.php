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


    /* --- 2. RECENT ACTIVITY PANEL (Styled like the charts in reference) --- */
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
    @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 500px) { .stats-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="dashboard-container">

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

    <div class="panel">
        <div class="panel-header">
            <h3 class="panel-title">Recent Activity</h3>
            <a href="{{ url('/admin/appointments') }}" class="btn-view-all">View All</a>
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
                            <span style="color:#cbd5e1; margin:0 4px;">•</span>
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