@extends('layouts.admin')

@section('title', 'Manage Appointments')

@push('styles')
<style>
    /* Table Styling */
    .card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
    }
    
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    
    th {
        text-align: left;
        font-size: 12px;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        padding: 12px 16px;
        border-bottom: 2px solid #f1f5f9;
    }
    
    td {
        padding: 16px;
        border-bottom: 1px solid #f8fafc;
        font-size: 14px;
        color: #334155;
        vertical-align: middle;
    }

    /* Status Badges */
    .status { padding: 5px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .status.pending { background: #fff7ed; color: #c2410c; }
    .status.approved { background: #dcfce7; color: #15803d; }
    .status.cancelled { background: #fee2e2; color: #b91c1c; }
    .status.completed { background: #e0f2fe; color: #0369a1; }
    
    /* Type Badges */
    .type-badge { padding: 4px 8px; border-radius: 6px; font-size: 10px; font-weight: 800; text-transform: uppercase; border: 1px solid; }
    .type-online { background: #eff6ff; color: #1d4ed8; border-color: #dbeafe; }
    .type-walkin { background: #fdf2f8; color: #be185d; border-color: #fce7f3; }

    /* Buttons */
    .btn-action {
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 4px;
    }
    .btn-view { background: #fff3f5; color: #70131B; border: 1px solid #f0d7dc; }
    .btn-view:hover { background: #fae9ed; color: #5a0f16; }
    
    .btn-approve { background: #fbecef; color: #70131B; border: 1px solid #f3d7dd; }
    .btn-approve:hover { background: #f7e2e7; }

    .btn-reschedule { background: #f9eef0; color: #7a1b28; border: 1px solid #f0d6dc; }
    .btn-reschedule:hover { background: #f4dde3; }

    .btn-cancel { background: #fee2e2; color: #b91c1c; }
    .btn-cancel:hover { background: #fecaca; }

    .btn-complete { background: #70131B; color: white; }
    .btn-complete:hover { background: #5a0f16; }

    /* Modal Overlay */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.2s;
    }
    .modal-box {
        background: #fff;
        padding: 24px;
        border-radius: 12px;
        width: 400px;
        max-width: 90%;
        position: relative;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .modal-row { margin-bottom: 12px; }
    .modal-label { font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; }
    .modal-val { font-size: 15px; color: #334155; font-weight: 500; }

    /* Form Inputs for Reschedule */
    .form-input {
        width: 100%;
        padding: 10px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        margin-top: 4px;
    }

    .action-header { margin-bottom: 20px; }

    .btn-add-walkin {
        display: inline-flex;
        align-items: center;
        background-color: #8B0000; 
        color: #ffffff !important; 
        padding: 12px 24px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 700;
        font-size: 16px;
        box-shadow: 0 4px 15px rgba(139, 0, 0, 0.3);
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .btn-icon { font-size: 20px; margin-right: 10px; line-height: 1; }

    .btn-add-walkin:hover {
        background-color: #a50000;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(139, 0, 0, 0.4);
        text-decoration: none;
    }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
@endpush

@section('content')

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin:0; color:#1e293b;">Appointments</h2>
        <input type="text" id="searchInput" placeholder="Search by name..." 
               style="padding: 10px 16px; border-radius: 8px; border: 1px solid #cbd5e1; width: 300px;">
    </div>

    <div class="action-header">
        <a href="{{ url('/admin/walkin?mode=scan') }}" class="btn-add-walkin">
            <span class="btn-icon">📷</span>
            <span class="btn-text">Scan Barcode</span>
        </a>
    </div>

    <div class="card">
        Appointments Summary
        <table id="apptTable">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Type</th> <th>Service</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $appt)
                    <tr>
                        <td>
                            <div style="font-weight: 700;" class="student-name">{{ $appt->name }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">{{ $appt->student_id }}</div>
                        </td>
                       <td>
    @php
        // Palitan ang 'type' ng 'user_type' base sa sinasave ng controller mo
        $currentType = strtolower($appt->user_type ?? ''); 
    @endphp

    @if($currentType == 'walkin' || $currentType == 'walk-in')
        <span class="type-badge type-walkin">Walk-in</span>
    @else
        <span class="type-badge type-online">Online</span>
    @endif
</td>
                        <td>{{ $appt->service }}</td>
                        <td>
                            <div>{{ \Carbon\Carbon::parse($appt->date)->format('M d, Y') }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">{{ \Carbon\Carbon::parse($appt->time)->format('g:i A') }}</div>
                        </td>
                        <td>
                            <span class="status {{ strtolower($appt->status) }}">{{ $appt->status }}</span>
                        </td>
                        <td>
                            <button class="btn-action btn-view" title="View Details"
                                onclick="openInfoModal('{{ $appt->name }}', '{{ $appt->service }}', '{{ $appt->date }}', '{{ $appt->time }}', '{{ $appt->remarks }}', '{{ $appt->email }}')">
                                View
                            </button>

                            @if($appt->status == 'Pending')
                                <a href="{{ url('/admin/appointments/' . $appt->id . '/Approved') }}" class="btn-action btn-approve" title="Approve" onclick="return confirm('Approve this appointment?')">✓</a>
                                <button class="btn-action btn-reschedule" title="Reschedule" onclick="openRescheduleModal('{{ $appt->id }}', '{{ $appt->date }}', '{{ $appt->time }}')">📅</button>
                                <a href="{{ url('/admin/appointments/' . $appt->id . '/Cancelled') }}" class="btn-action btn-cancel" title="Reject" onclick="return confirm('Cancel this request?')">✕</a>
                            
                            @elseif($appt->status == 'Approved')
                                @php
                                    $apptDate = \Carbon\Carbon::parse($appt->date)->startOfDay();
                                    $today = \Carbon\Carbon::today();
                                    $isFuture = $apptDate->gt($today);
                                @endphp

                                @if($isFuture)
                                    <button class="btn-action" style="background: #e2e8f0; color: #94a3b8; cursor: not-allowed; padding: 6px 12px; border-radius: 4px;" title="Scheduled for {{ \Carbon\Carbon::parse($appt->date)->format('M d, Y') }}">
                                        ⏳ Consult
                                    </button>
                                @else
                                    <a href="{{ url('/admin/walkin/form/' . $appt->student_id) }}?source=online" class="btn-action btn-consult" style="background: #0d6efd; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; margin-right: 5px; display: inline-flex; align-items: center; gap: 5px;">
                                        🔍 Consult
                                    </a>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">No appointments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="infoModal" class="modal-overlay">
        <div class="modal-box">
            <h3 style="margin-top:0; color:#8B0000; border-bottom:1px solid #eee; padding-bottom:12px; margin-bottom:16px;">Appointment Details</h3>
            <div class="modal-row"><div class="modal-label">Student Name</div><div class="modal-val" id="mName"></div></div>
            <div class="modal-row"><div class="modal-label">Email</div><div class="modal-val" id="mEmail"></div></div>
            <div class="modal-row"><div class="modal-label">Service Request</div><div class="modal-val" id="mService"></div></div>
            <div class="modal-row"><div class="modal-label">Scheduled For</div><div class="modal-val" id="mDateTime"></div></div>
            <div class="modal-row"><div class="modal-label">Notes</div><div class="modal-val" id="mNotes" style="background:#f8fafc; padding:10px; border-radius:6px; font-size:13px;"></div></div>
            <div style="text-align: right; margin-top: 20px;"><button onclick="closeInfoModal()" style="padding: 8px 16px; background: #334155; color: white; border: none; border-radius: 6px; cursor: pointer;">Close</button></div>
        </div>
    </div>

    <div id="rescheduleModal" class="modal-overlay">
        <div class="modal-box">
            <h3 style="margin-top:0; color:#b45309; border-bottom:1px solid #eee; padding-bottom:12px; margin-bottom:16px;">Reschedule Appointment</h3>
            <form id="rescheduleForm" method="POST" action="">
                @csrf
                <p style="font-size: 14px; color: #64748b; margin-bottom: 16px;">Select a new date and time for this appointment.</p>
                <div class="modal-row"><label class="modal-label">New Date</label><input type="date" name="date" id="rDate" class="form-input" required></div>
                <div class="modal-row"><label class="modal-label">New Time</label><input type="time" name="time" id="rTime" class="form-input" required></div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px;">
                    <button type="button" onclick="closeRescheduleModal()" style="padding: 10px 16px; background: #eee; color: #333; border: none; border-radius: 6px; cursor: pointer;">Cancel</button>
                    <button type="submit" style="padding: 10px 16px; background: #70131B; color: white; border: none; border-radius: 6px; cursor: pointer;">Confirm New Schedule</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function openInfoModal(name, service, date, time, remarks, email) {
        document.getElementById('mName').innerText = name;
        document.getElementById('mService').innerText = service;
        document.getElementById('mDateTime').innerText = date + ' at ' + time;
        document.getElementById('mNotes').innerText = remarks;
        document.getElementById('mEmail').innerText = email;
        document.getElementById('infoModal').style.display = 'flex';
    }

    function closeInfoModal() { document.getElementById('infoModal').style.display = 'none'; }

    function openRescheduleModal(id, currentDate, currentTime) {
        var form = document.getElementById('rescheduleForm');
        form.action = '{{ url("/admin/appointments") }}/' + id + '/reschedule';
        document.getElementById('rDate').value = currentDate;
        document.getElementById('rTime').value = currentTime;
        document.getElementById('rescheduleModal').style.display = 'flex';
    }

    function closeRescheduleModal() { document.getElementById('rescheduleModal').style.display = 'none'; }

    window.onclick = function(event) {
        if (event.target == document.getElementById('infoModal')) closeInfoModal();
        if (event.target == document.getElementById('rescheduleModal')) closeRescheduleModal();
    }

    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toUpperCase();
        let tr = document.getElementById("apptTable").getElementsByTagName("tr");
        for (let i = 1; i < tr.length; i++) {
            let td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                let txtValue = td.getElementsByClassName("student-name")[0].textContent || td.getElementsByClassName("student-name")[0].innerText;
                tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
            }
        }
    });
</script>
@endpush
