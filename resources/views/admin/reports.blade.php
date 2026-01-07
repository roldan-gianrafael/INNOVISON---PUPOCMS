@extends('layouts.admin')

@section('title', 'Reports')

@push('styles')
<style>
    /* Stats Grid */
    .stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: #fff;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.04);
        border: 1px solid #f0f0f0;
        text-align: center;
    }
    .stat-num { font-size: 32px; font-weight: 800; color: #8B0000; margin-bottom: 4px; }
    .stat-label { color: #64748b; font-size: 13px; font-weight: 600; text-transform: uppercase; }

    /* Cards */
    .card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
        margin-bottom: 24px;
    }
    .card h3 { margin-top: 0; color: #1e293b; margin-bottom: 20px; }

    /* Controls */
    .controls {
        display: flex;
        gap: 12px;
        align-items: center;
        background: #f8fafc;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .date-input { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; color: #334155; }
    .btn-export { 
        background: #0f172a; color: white; padding: 9px 16px; 
        border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; 
        display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-export:hover { background: #334155; }

    /* Tables */
    table { width: 100%; border-collapse: collapse; }
    th { text-align: left; padding: 12px; background: #f1f5f9; color: #475569; font-size: 12px; text-transform: uppercase; font-weight: 700; }
    td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; }
    
    /* Responsive */
    @media (max-width: 800px) { .stats { grid-template-columns: repeat(2, 1fr); } }
</style>
@endpush

@section('content')

    <section class="stats">
        <div class="stat-card">
            <div class="stat-num">{{ $total }}</div>
            <div class="stat-label">Total Appointments</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color: #10b981;">{{ $approved }}</div>
            <div class="stat-label">Approved</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color: #ef4444;">{{ $cancelled }}</div>
            <div class="stat-label">Cancelled</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color: #f59e0b;">{{ $lowStock }}</div>
            <div class="stat-label">Low Stock Items</div>
        </div>
    </section>

    <section class="card">
        <h3>Appointment Report</h3>

        <form method="GET" action="{{ url('/admin/reports/export') }}" class="controls">
            <span style="font-weight: 600; color: #64748b; font-size: 14px;">Filter Date:</span>
            <input type="date" name="from_date" id="fromDate" class="date-input" onchange="filterTable()">
            <span style="color:#94a3b8">to</span>
            <input type="date" name="to_date" id="toDate" class="date-input" onchange="filterTable()">
            
            <button type="submit" class="btn-export" style="margin-left: auto;">
                📥 Export to CSV
            </button>
        </form>

        <table id="reportTable">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $appt)
                    <tr class="appt-row" data-date="{{ $appt->date }}">
                        <td style="font-weight: 600;">{{ $appt->name }}</td>
                        <td>{{ $appt->service }}</td>
                        <td>{{ \Carbon\Carbon::parse($appt->date)->format('M d, Y') }}</td>
                        <td>
                            <span style="
                                padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase;
                                background: {{ $appt->status == 'Approved' ? '#dcfce7' : ($appt->status == 'Pending' ? '#fff7ed' : '#fee2e2') }};
                                color: {{ $appt->status == 'Approved' ? '#15803d' : ($appt->status == 'Pending' ? '#c2410c' : '#b91c1c') }};
                            ">
                                {{ $appt->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align: center; padding: 20px; color: #888;">No records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin:0; color:#1e293b;">Inventory Status</h3>
            
            <a href="{{ url('/admin/reports/export-inventory') }}" class="btn-export">
                📦 Export Inventory CSV
            </a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Stock Level</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td style="font-weight: 600;">{{ $item->name }}</td>
                        <td>{{ $item->category }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>
                            @if($item->quantity == 0)
                                <span style="color: #b91c1c; font-weight: 700;">Out of Stock</span>
                            @elseif($item->quantity < 10)
                                <span style="color: #b45309; font-weight: 700;">Low Stock</span>
                            @else
                                <span style="color: #15803d; font-weight: 700;">Good</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align: center; padding: 20px;">No inventory items.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

@endsection

@push('scripts')
<script>
    // Simple Client-Side Filter for Immediate Visual Feedback
    function filterTable() {
        let from = document.getElementById('fromDate').value;
        let to = document.getElementById('toDate').value;
        let rows = document.querySelectorAll('.appt-row');

        rows.forEach(row => {
            let date = row.getAttribute('data-date');
            let show = true;

            if (from && date < from) show = false;
            if (to && date > to) show = false;

            row.style.display = show ? '' : 'none';
        });
    }
</script>
@endpush