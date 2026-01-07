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
    .btn-view { background: #f1f5f9; color: #475569; }
    .btn-view:hover { background: #e2e8f0; color: #0f172a; }
    
    .btn-approve { background: #dcfce7; color: #15803d; }
    .btn-approve:hover { background: #bbf7d0; }

    .btn-reschedule { background: #fffbeb; color: #b45309; }
    .btn-reschedule:hover { background: #fde68a; }

    .btn-cancel { background: #fee2e2; color: #b91c1c; }
    .btn-cancel:hover { background: #fecaca; }

    .btn-complete { background: #3b82f6; color: white; }
    .btn-complete:hover { background: #2563eb; }

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
    
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
@endpush

@section('content')

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin:0; color:#1e293b;">Appointments</h2>
        
        <input type="text" id="searchInput" placeholder="Search by name..." 
               style="padding: 10px 16px; border-radius: 8px; border: 1px solid #cbd5e1; width: 300px;">
    </div>

    <div class="card">
        <table id="apptTable">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Service</th>
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
                                    onclick="openInfoModal(
                                        '{{ $appt->name }}', 
                                        '{{ $appt->service }}', 
                                        '{{ $appt->date }}', 
                                        '{{ $appt->time }}', 
                                        '{{ $appt->notes ?? 'None' }}', 
                                        '{{ $appt->email }}'
                                    )">
                                View
                            </button>

                            @if($appt->status == 'Pending')
                                <a href="{{ url('/admin/appointments/'.$appt->id.'/Approved') }}" class="btn-action btn-approve" title="Approve" onclick="return confirm('Approve this appointment?')">✓</a>
                                
                                <button class="btn-action btn-reschedule" title="Reschedule"
                                        onclick="openRescheduleModal('{{ $appt->id }}', '{{ $appt->date }}', '{{ $appt->time }}')">
                                    📅
                                </button>

                                <a href="{{ url('/admin/appointments/'.$appt->id.'/Cancelled') }}" class="btn-action btn-cancel" title="Reject" onclick="return confirm('Cancel this request?')">✕</a>
                            
                            @elseif($appt->status == 'Approved')
                                <a href="{{ url('/admin/appointments/'.$appt->id.'/Completed') }}" 
                                   class="btn-action btn-complete" 
                                   title="Mark as Done" 
                                   onclick="return confirm('Mark this appointment as completed?')">
                                    ✅ Complete
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">No appointments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="infoModal" class="modal-overlay">
        <div class="modal-box">
            <h3 style="margin-top:0; color:#8B0000; border-bottom:1px solid #eee; padding-bottom:12px; margin-bottom:16px;">Appointment Details</h3>
            
            <div class="modal-row">
                <div class="modal-label">Student Name</div>
                <div class="modal-val" id="mName"></div>
            </div>
            <div class="modal-row">
                <div class="modal-label">Email</div>
                <div class="modal-val" id="mEmail"></div>
            </div>
            <div class="modal-row">
                <div class="modal-label">Service Request</div>
                <div class="modal-val" id="mService"></div>
            </div>
            <div class="modal-row">
                <div class="modal-label">Scheduled For</div>
                <div class="modal-val" id="mDateTime"></div>
            </div>
            <div class="modal-row">
                <div class="modal-label">Notes</div>
                <div class="modal-val" id="mNotes" style="background:#f8fafc; padding:10px; border-radius:6px; font-size:13px;"></div>
            </div>

            <div style="text-align: right; margin-top: 20px;">
                <button onclick="closeInfoModal()" style="padding: 8px 16px; background: #334155; color: white; border: none; border-radius: 6px; cursor: pointer;">Close</button>
            </div>
        </div>
    </div>

    <div id="rescheduleModal" class="modal-overlay">
        <div class="modal-box">
            <h3 style="margin-top:0; color:#b45309; border-bottom:1px solid #eee; padding-bottom:12px; margin-bottom:16px;">Reschedule Appointment</h3>
            
            <form id="rescheduleForm" method="POST" action="">
                @csrf
                <p style="font-size: 14px; color: #64748b; margin-bottom: 16px;">Select a new date and time for this appointment. The student will be updated.</p>

                <div class="modal-row">
                    <label class="modal-label">New Date</label>
                    <input type="date" name="date" id="rDate" class="form-input" required>
                </div>

                <div class="modal-row">
                    <label class="modal-label">New Time</label>
                    <input type="time" name="time" id="rTime" class="form-input" required>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px;">
                    <button type="button" onclick="closeRescheduleModal()" style="padding: 10px 16px; background: #eee; color: #333; border: none; border-radius: 6px; cursor: pointer;">Cancel</button>
                    <button type="submit" style="padding: 10px 16px; background: #b45309; color: white; border: none; border-radius: 6px; cursor: pointer;">Confirm New Schedule</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // --- 1. VIEW INFO MODAL LOGIC ---
    function openInfoModal(name, service, date, time, notes, email) {
        document.getElementById('mName').innerText = name;
        document.getElementById('mService').innerText = service;
        document.getElementById('mDateTime').innerText = date + ' at ' + time;
        document.getElementById('mNotes').innerText = notes;
        document.getElementById('mEmail').innerText = email;
        
        document.getElementById('infoModal').style.display = 'flex';
    }

    function closeInfoModal() {
        document.getElementById('infoModal').style.display = 'none';
    }

    // --- 2. RESCHEDULE MODAL LOGIC ---
    function openRescheduleModal(id, currentDate, currentTime) {
        // Set the form action dynamically based on ID
        var form = document.getElementById('rescheduleForm');
        form.action = '/admin/appointments/' + id + '/reschedule';

        // Pre-fill current values
        document.getElementById('rDate').value = currentDate;
        document.getElementById('rTime').value = currentTime;

        document.getElementById('rescheduleModal').style.display = 'flex';
    }

    function closeRescheduleModal() {
        document.getElementById('rescheduleModal').style.display = 'none';
    }

    // Close modals if clicked outside
    window.onclick = function(event) {
        if (event.target == document.getElementById('infoModal')) {
            closeInfoModal();
        }
        if (event.target == document.getElementById('rescheduleModal')) {
            closeRescheduleModal();
        }
    }

    // --- 3. SEARCH LOGIC (Functional) ---
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toUpperCase();
        let table = document.getElementById("apptTable");
        let tr = table.getElementsByTagName("tr");

        // Loop through all table rows (start at 1 to skip header)
        for (let i = 1; i < tr.length; i++) {
            // Get the first column (Student Name)
            let td = tr[i].getElementsByTagName("td")[0];
            
            if (td) {
                // Get text inside the 'student-name' div
                let nameDiv = td.getElementsByClassName("student-name")[0];
                let txtValue = nameDiv.textContent || nameDiv.innerText;

                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    });
</script>
@endpush