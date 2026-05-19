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

    .inventory-scope-group {
        margin-bottom: 0;
        margin-top: 0;
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

    .inventory-scope-wrap {
        position: relative;
    }

    .inventory-scope-select {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .inventory-scope-display {
        width: 100%;
        min-height: 47px;
        padding: 11px 46px 11px 16px;
        border-radius: 999px;
        border: 1px solid var(--admin-primary-btn-border, #8b0000);
        background: var(--admin-primary-btn-bg, #8b0000);
        color: #ffffff;
        font-weight: 800;
        text-align: center;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20);
        transition: color .08s linear, transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease;
        z-index: 0;
    }

    .inventory-scope-display::before {
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

    .inventory-scope-display::after {
        content: "";
        position: absolute;
        right: 18px;
        top: 50%;
        width: 10px;
        height: 10px;
        border-right: 2px solid currentColor;
        border-bottom: 2px solid currentColor;
        transform: translateY(-50%) rotate(-45deg);
        transition: transform .2s ease, border-color .2s ease;
    }

    .inventory-scope-display:hover,
    .inventory-scope-display:focus,
    .inventory-scope-display.is-open {
        outline: none;
        background: #facc15 !important;
        border-color: #facc15;
        color: #111827;
        transform: translateY(-1px);
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16);
    }

    .inventory-scope-display.is-open::after {
        transform: translateY(-50%) rotate(135deg);
    }

    .inventory-scope-display:hover::before,
    .inventory-scope-display:focus::before,
    .inventory-scope-display.is-open::before {
        transform: translateX(135%);
    }

    .inventory-scope-menu {
        position: absolute;
        top: 0;
        left: calc(100% + 12px);
        right: auto;
        width: min(260px, 80vw);
        display: none;
        padding: 8px;
        border-radius: 16px;
        border: 1px solid rgba(112, 19, 27, 0.14);
        background: #ffffff;
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.16);
        z-index: 20;
    }

    .inventory-scope-wrap.is-open .inventory-scope-menu {
        display: grid;
        gap: 6px;
    }

    .inventory-scope-option {
        width: 100%;
        min-height: 42px;
        padding: 10px 12px;
        border: 1px solid rgba(112, 19, 27, 0.16);
        border-radius: 12px;
        background: #ffffff;
        color: #111827;
        cursor: pointer;
        font-weight: 800;
        text-align: left;
        box-shadow:
            0 0 0 2px rgba(112, 19, 27, 0.04),
            0 10px 22px rgba(112, 19, 27, 0.14);
        transition: background .18s ease, color .18s ease, transform .18s ease, border-color .18s ease, box-shadow .18s ease;
    }

    .inventory-scope-option:hover {
        border-color: rgba(143, 34, 48, 0.34);
        background: #8b0000;
        color: #facc15;
        transform: translateX(2px);
        box-shadow:
            0 0 0 2px rgba(250, 204, 21, 0.14),
            0 14px 26px rgba(112, 19, 27, 0.22);
    }

    .inventory-scope-option.is-selected {
        background: #facc15;
        border-color: #facc15;
        color: #111827;
        transform: translateX(2px);
        box-shadow:
            0 0 0 2px rgba(250, 204, 21, 0.22),
            0 14px 26px rgba(250, 204, 21, 0.26);
    }

    /* Buttons & Links */
    .btn-generate {
        width: 100%;
        min-height: 47px;
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

    .btn-generate:hover,
    .btn-generate:focus {
        transform: translateY(-1px);
        background: #facc15 !important;
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16);
        color: #111827 !important;
        outline: none;
    }

    .btn-generate:hover::after,
    .btn-generate:focus::after {
        transform: translateX(135%);
    }

    /* Dynamic Border & Button Colors */
    .border-mar { border-top: 5px solid #800000; }
    .bg-mar { background: linear-gradient(135deg, #70131B, #8f2230); }

    .border-inventory { border-top: 5px solid #800000; }
    .bg-inventory { background: #70131B; }

    .border-appointment { border-top: 5px solid #800000; }
    .bg-appointment { background: linear-gradient(135deg, #70131B, #8f2230); }

    @media (max-width: 900px) {
        .inventory-scope-menu {
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            width: auto;
        }
    }

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
                <div class="form-group inventory-scope-group">
                    <div class="inventory-scope-wrap" id="inventoryScopeWrap">
                        <select name="inventory_scope" id="inventoryScopeSelect" class="inventory-scope-select">
                            <option value="medicines" selected>Inventory of Medicines</option>
                            <option value="supplies">Inventory of Supplies</option>
                        </select>
                        <button type="button" class="inventory-scope-display" id="inventoryScopeDisplay" aria-haspopup="listbox" aria-expanded="false">
                            Click here to generate
                        </button>
                        <div class="inventory-scope-menu" id="inventoryScopeMenu" role="listbox" aria-label="Inventory report type">
                            <button type="button" class="inventory-scope-option" data-scope-value="medicines">Inventory of Medicines</button>
                            <button type="button" class="inventory-scope-option" data-scope-value="supplies">Inventory of Supplies</button>
                        </div>
                    </div>
                </div>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const wrap = document.getElementById('inventoryScopeWrap');
    const select = document.getElementById('inventoryScopeSelect');
    const display = document.getElementById('inventoryScopeDisplay');
    const options = Array.from(document.querySelectorAll('.inventory-scope-option'));

    if (!wrap || !select || !display || options.length === 0) {
        return;
    }

    let hasSelectedScope = false;

    const syncDisplay = function() {
        display.textContent = 'Click here to generate';
        options.forEach(function(option) {
            option.classList.toggle('is-selected', hasSelectedScope && option.dataset.scopeValue === select.value);
        });
    };

    const setOpen = function(isOpen) {
        wrap.classList.toggle('is-open', isOpen);
        display.classList.toggle('is-open', isOpen);
        display.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    };

    display.addEventListener('click', function(event) {
        event.preventDefault();
        setOpen(!wrap.classList.contains('is-open'));
    });

    options.forEach(function(option) {
        option.addEventListener('click', function() {
            select.value = option.dataset.scopeValue || 'medicines';
            hasSelectedScope = true;
            syncDisplay();
            setOpen(false);
            if (select.form) {
                select.form.submit();
            }
        });
    });

    document.addEventListener('click', function(event) {
        if (!wrap.contains(event.target)) {
            setOpen(false);
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            setOpen(false);
        }
    });

    syncDisplay();
});
</script>
@endsection
