@extends('layouts.admin')

@section('content')
<style>
    /* Main Container */
    .export-hub-container {
        padding: 30px;
        background: #f8fafc;
        min-height: 100vh;
    }

    .hub-header {
        margin-bottom: 30px;
    }

    .hub-header h2 {
        color: #1e293b;
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
        border: none;
        padding: 10px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
        color: white;
        text-align: center;
        text-decoration: none;
        display: block;
        transition: opacity 0.2s;
    }

    .btn-generate:hover {
        opacity: 0.9;
    }

    /* Dynamic Border & Button Colors */
    .border-mar { border-top: 5px solid #800000; }
    .bg-mar { background: #800000; }

    .border-inventory { border-top: 5px solid #800000; }
    .bg-inventory { background: #800000; }

    .border-appointment { border-top: 5px solid #800000; }
    .bg-appointment { background: #800000; }
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
            <form action="{{ route('reports.print') }}" method="GET" target="_blank">
                <input type="hidden" name="type" value="mar">
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
                <p>Complete list of medical supplies, current stock, expiration date...</p>
            </div>
            <form action="{{ route('reports.print') }}" method="GET" target="_blank">
                <input type="hidden" name="type" value="inventory">
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
            <form action="{{ route('reports.print') }}" method="GET" target="_blank">
                <input type="hidden" name="type" value="appointment">
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