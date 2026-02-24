@extends('layouts.admin')

@section('title', 'Export Hub')

@push('styles')
<style>
    .dashboard-container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    
    /* Re-using your exact card style */
    .report-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .report-card {
        background: #70131B;
        color: #fff;
        border-radius: 16px;
        padding: 24px 20px;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 160px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: none;
    }

    .report-card:hover {
        transform: translateY(-8px);
        filter: brightness(1.2);
    }

    .report-label { font-size: 14px; font-weight: 500; color: #cbd5e1; text-transform: uppercase; margin-bottom: 5px; }
    .report-main-title { font-size: 22px; font-weight: 700; color: #fff; line-height: 1.2; }
    .report-badge { 
        display: inline-block; font-size: 11px; font-weight: 600; padding: 6px 12px; 
        border-radius: 8px; width: fit-content; background: rgba(255,255,255,0.1); color: #fff; margin-top: 15px; 
    }

    .filter-card {
        background: #fff; padding: 20px; border-radius: 12px; margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-left: 5px solid #8B0000;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.reports') }}" style="text-decoration: none; color: #64748b; font-weight: 600; font-size: 14px;">
            ← Back to Reports
        </a>
    </div>

    <h2 style="font-size: 24px; color: #1e293b; margin-bottom: 10px; font-weight: 700;">Export Center</h2>
    <p style="color: #64748b; margin-bottom: 30px;">Select the data you want to download as PDF.</p>

    <div class="filter-card">
        <form action="{{ route('reports.exportHub') }}" method="GET" style="display: flex; align-items: center; gap: 15px;">
            <div>
                <label style="font-size: 12px; font-weight: 700; color: #64748b; display: block; margin-bottom: 5px;">SELECT MONTH</label>
                <input type="month" name="month" value="{{ request('month', date('Y-m')) }}" 
                       style="padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0; outline: none;">
            </div>
            <button type="submit" style="margin-top: 20px; padding: 10px 20px; background: #8B0000; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                Update Links
            </button>
        </form>
    </div>

    <div class="report-grid">
        <a href="#" class="report-card">
            <div>
                <div class="report-label">Monthly Accomplishment</div>
                <div class="report-main-title">MAR Report Data</div>
            </div>
            <div class="report-badge">Download PDF</div>
        </a>

        <a href="#" class="report-card">
            <div>
                <div class="report-label">Clinic Schedules</div>
                <div class="report-main-title">All Appointments</div>
            </div>
            <div class="report-badge">Download PDF</div>
        </a>

        <a href="#" class="report-card">
            <div>
                <div class="report-label">Supplies Log</div>
                <div class="report-main-title">Inventory Stock</div>
            </div>
            <div class="report-badge">Download PDF</div>
        </a>
    </div>
</div>
@endsection