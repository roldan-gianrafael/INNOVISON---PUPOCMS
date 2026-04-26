@extends('layouts.admin')

@section('content')
@php
    $role = \App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '');
    $printReportUrl = $role === \App\Models\User::ROLE_ADMIN ? url('/assistant/reports/print-reports') : url('/admin/reports/print-reports');
@endphp
<style>
    /* Main Container */
    .export-hub-container {
        padding: 14px;
        background: transparent;
    }

    .hub-header {
        margin-bottom: 30px;
    }

    .hub-header h2 {
        color: #4b0f17;
        margin: 0;
    }

    .hub-header p {
        color: #64748b;
    }

    /* Grid Layout */
    .hub-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    /* Card Styling */
    .report-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .report-card h3 {
        margin-top: 0;
        color: #1e293b;
    }

    .report-card p {
        color: #64748b;
        font-size: 14px;
        margin-bottom: 20px;
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: bold;
        color: #475569;
        margin-bottom: 5px;
    }

    .input-month {
        width: 100%;
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        outline-color: #800000;
    }

    /* Buttons & Links */
    .btn-generate {
        width: 100%;
        border: 1px solid #8f2230;
        padding: 11px 16px;
        border-radius: 999px;
        cursor: pointer;
        font-weight: 800;
        color: #ffffff;
        text-align: center;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #70131B, #8f2230);
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20);
        transition: color .08s linear, transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        z-index: 0;
    }

    .btn-generate::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, #9f3341, #b74a58);
        transform: scaleX(0);
        transform-origin: left center;
        transition: transform .26s ease;
        z-index: -1;
    }

    .btn-generate:hover {
        transform: translateY(-1px);
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16);
    }

    .btn-generate:hover::after {
        transform: scaleX(1);
    }

    /* Dynamic Border & Button Colors */
    .border-mar { border-top: 5px solid #800000; }
    .bg-mar { background: linear-gradient(135deg, #70131B, #8f2230); }

    .border-inventory { border-top: 5px solid #800000; }
    .bg-inventory { background: linear-gradient(135deg, #70131B, #8f2230); }

    .border-appointment { border-top: 5px solid #800000; }
    .bg-appointment { background: linear-gradient(135deg, #70131B, #8f2230); }

</style>

<div class="export-hub-container">
    
    <div class="hub-header">
        <h2>Export & Reports Hub</h2>
        <p>Select a report type to generate a printable document.</p>
    </div>

    <div class="hub-grid">
        
        <div class="report-card border-mar">
            <div>
                <h3>MAR Report</h3>
                <p>Monthly Accomplishment Report showing patient counts.</p>
            </div>
            <form action="{{ $printReportUrl }}" method="GET" target="_blank">
                <input type="hidden" name="type" value="mar">
                <input type="hidden" name="output" value="pdf">
                <div class="form-group">
                    <label>SELECT MONTH:</label>
                    <input type="month" name="month" value="{{ date('Y-m') }}" class="input-month">
                </div>
                <button type="submit" class="btn-generate bg-mar">
                    Generate MAR Report
                </button>
            </form>
        </div>



        
        <div class="report-card border-inventory">
            <div>
                <h3>Inventory Stock</h3>
                <p>View unit-based inventory movement with starting stock, consumed quantity, and current balance for the selected month.</p>
            </div>
            <form action="{{ $printReportUrl }}" method="GET" target="_blank">
                <input type="hidden" name="type" value="inventory">
                <input type="hidden" name="output" value="pdf">
                <div class="form-group">
                    <label>SELECT MONTH:</label>
                    <input type="month" name="month" value="{{ date('Y-m') }}" class="input-month">
                </div>
                <button type="submit" class="btn-generate bg-inventory">
                    View Stock Report
                </button>
            </form>
        </div>




        <div class="report-card border-appointment">
            <div>
                <h3>Appointments</h3>
                <p>Summary of student appointments and medical consultations for the selected period.</p>
            </div>
            <form action="{{ $printReportUrl }}" method="GET" target="_blank">
                <input type="hidden" name="type" value="appointment">
                <input type="hidden" name="output" value="pdf">
                <div class="form-group">
                    <label>SELECT MONTH:</label>
                    <input type="month" name="month" value="{{ date('Y-m') }}" class="input-month">
                </div>
                <button type="submit" class="btn-generate bg-appointment">
                    Generate Appointment Report
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
