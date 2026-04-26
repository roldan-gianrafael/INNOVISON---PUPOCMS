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

    .appointments-summary-title {
        font-weight: 800;
    }
    
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    
    h2,
    .card,
    .card *:not(.status):not(.type-badge):not(.btn-action):not(.dialog-btn):not(.btn-add-walkin) {
        color: #111827;
    }

    th {
        text-align: left;
        font-size: 12px;
        font-weight: 800;
        color: #111827;
        text-transform: uppercase;
        padding: 12px 16px;
        border-bottom: 2px solid #f1f5f9;
    }
    
    td {
        padding: 16px;
        border-bottom: 1px solid #f8fafc;
        font-size: 14px;
        color: #111827;
        vertical-align: middle;
    }

    /* Status Badges */
    .status { padding: 5px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .status.pending { background: #fff7ed; color: #c2410c; }
    .status.approved { background: #dcfce7; color: #15803d; }
    .status.cancelled { background: #fee2e2; color: #b91c1c; }
    .status.completed { background: #e0f2fe; color: #0369a1; }
    .status.missed { background: #ffedd5; color: #9a3412; }
    
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
    .action-list {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .btn-view { background: #fff3f5; color: #70131B; border: 1px solid #f0d7dc; }
    .btn-view:hover { background: #fae9ed; color: #5a0f16; }
    
    .btn-approve { background: #fbecef; color: #70131B; border: 1px solid #f3d7dd; }
    .btn-approve:hover { background: #f7e2e7; }

    .btn-reschedule { background: #f9eef0; color: #7a1b28; border: 1px solid #f0d6dc; }
    .btn-reschedule:hover { background: #f4dde3; }

    .btn-missed { background: #fff7ed; color: #9a3412; border: 1px solid #fed7aa; }
    .btn-missed:hover { background: #ffedd5; }

    .btn-reject,
    .btn-cancel { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    .btn-reject:hover,
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
    .modal-label { font-size: 12px; font-weight: 700; color: #111827; text-transform: uppercase; }
    .modal-val { font-size: 15px; color: #111827; font-weight: 500; }
    .modal-title {
        margin-top: 0;
        border-bottom: 1px solid #eee;
        padding-bottom: 12px;
        margin-bottom: 14px;
    }
    .modal-subtitle {
        font-size: 14px;
        color: #111827;
        margin-bottom: 16px;
    }
    .dialog-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 22px;
    }
    .dialog-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        border: none;
        border-radius: 8px;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
    }
    .dialog-btn-neutral {
        background: #eee;
        color: #333;
    }
    .dialog-btn-neutral:hover {
        background: #e5e7eb;
    }
    .dialog-btn-primary {
        background: #70131B;
        color: #fff;
    }
    .dialog-btn-primary:hover {
        background: #5a0f16;
    }
    .dialog-btn-approve {
        background: #166534;
        color: #fff;
    }
    .dialog-btn-approve:hover {
        background: #14532d;
    }
    .dialog-btn-reject {
        background: #b91c1c;
        color: #fff;
    }
    .dialog-btn-reject:hover {
        background: #991b1b;
    }
    .dialog-btn-warning {
        background: #b45309;
        color: #fff;
    }
    .dialog-btn-warning:hover {
        background: #92400e;
    }

    /* Form Inputs for Reschedule */
    .form-input {
        width: 100%;
        padding: 10px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        margin-top: 4px;
    }

    .action-header { margin-bottom: 20px; }

    .appointments-page-title {
        margin: 0;
        color: #111827;
    }

    .appointments-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
    }

    .appointments-toolbar-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-left: auto;
        flex-wrap: wrap;
    }

    .appointments-search-wrap {
        width: 300px;
        max-width: 100%;
        flex: 0 0 300px;
    }

    .appointments-search-wrap .voice-field-wrap {
        width: 100%;
    }

    .appointments-search-input {
        width: 100%;
        padding: 10px 16px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        color: #111827;
        background: #ffffff;
    }

    .btn-add-walkin {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #ffffff !important; 
        padding: 11px 18px;
        border-radius: 999px;
        text-decoration: none;
        font-weight: 800;
        font-size: 14px;
        white-space: nowrap;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20);
        transition: color .08s linear, transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        border: 1px solid #8f2230;
        z-index: 0;
    }

    .btn-add-walkin::after {
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

    .btn-icon {
        width: 28px;
        height: 28px;
        border-radius: 999px;
        background: #ffefb5;
        color: #70131B;
        font-size: 15px;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 1;
        margin-right: 0;
    }

    .btn-text {
        position: relative;
        z-index: 1;
    }

    .btn-add-walkin:hover {
        transform: translateY(-2px);
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16);
        text-decoration: none;
    }
    .btn-add-walkin:hover::after {
        transform: translateX(135%);
    }

    .appointment-highlight-row {
        position: relative;
        background: linear-gradient(180deg, rgba(255, 248, 208, 0.98), rgba(255, 243, 191, 0.98));
        box-shadow: inset 4px 0 0 #f59e0b;
        animation: appointmentHighlightPulse 2.2s ease-in-out 2;
    }

    .appointment-highlight-row td {
        background: transparent;
    }

    html[data-theme="dark"] .appointment-highlight-row {
        background: linear-gradient(180deg, rgba(120, 53, 15, 0.34), rgba(146, 64, 14, 0.28));
        box-shadow: inset 4px 0 0 #fbbf24;
    }

    @keyframes appointmentHighlightPulse {
        0%, 100% {
            box-shadow: inset 4px 0 0 #f59e0b, 0 0 0 rgba(245, 158, 11, 0);
        }
        50% {
            box-shadow: inset 4px 0 0 #f59e0b, 0 0 0 6px rgba(245, 158, 11, 0.14);
        }
    }

    html[data-theme="dark"] .appointments-page-title {
        color: #ffffff;
    }

    html[data-theme="dark"] .appointments-summary-title,
    html[data-theme="dark"] .student-name,
    html[data-theme="dark"] #apptTable td,
    html[data-theme="dark"] #apptTable td div,
    html[data-theme="dark"] #apptTable td span:not(.status):not(.type-badge),
    html[data-theme="dark"] #apptTable td[style],
    html[data-theme="dark"] #apptTable td div[style] {
        color: #ffffff !important;
    }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
@endpush

@section('content')
    @php
        $role = \App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '');
        $basePrefix = $role === \App\Models\User::ROLE_ADMIN ? '/assistant' : '/admin';
        $highlightAppointmentId = trim((string) request()->query('highlight_appointment', ''));
    @endphp

    <div class="appointments-toolbar">
        <h2 class="appointments-page-title">Appointments</h2>
        <div class="appointments-toolbar-actions">
            <div class="appointments-search-wrap">
                <input type="text" id="searchInput" class="appointments-search-input" placeholder="Search by name...">
            </div>
            <a href="{{ url($basePrefix . '/walkin?mode=scan') }}" class="btn-add-walkin">
                <span class="btn-icon">&#128247;</span>
                <span class="btn-text">Scan ID / Fingerprint</span>
            </a>
        </div>
    </div>
<div class="card">
        <div class="appointments-summary-title">Appointments Summary</div>
        <table id="apptTable">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Student Number</th>
                    <th>Type</th> <th>Service</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $appt)
                    <tr
                        data-appointment-row
                        data-appointment-id="{{ $appt->id }}"
                        class="{{ $highlightAppointmentId !== '' && $highlightAppointmentId === (string) $appt->id ? 'appointment-highlight-row' : '' }}"
                    >
                        <td>
                            <div style="font-weight: 700;" class="student-name">{{ $appt->name }}</div>
                            <div style="font-size: 12px; color: #111827;">{{ $appt->student_number ?: optional(optional($appt->user)->healthProfile)->student_number ?: optional($appt->user)->student_number ?: 'N/A' }}</div>
                        </td>
                        <td>{{ $appt->student_number ?: optional(optional($appt->user)->healthProfile)->student_number ?: optional($appt->user)->student_number ?: 'N/A' }}</td>
                       <td>
    @php
        // Preferred source field is `type`; fallback for legacy records.
        $currentType = strtolower(trim((string) ($appt->type ?? '')));
        if ($currentType === '') {
            $legacyType = strtolower(trim((string) ($appt->user_type ?? '')));
            if (in_array($legacyType, ['walkin', 'walk-in', 'online'], true)) {
                $currentType = str_replace('-', '', $legacyType);
            }
        }
    @endphp

    @if($currentType === 'walkin')
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
                            <div class="action-list">
                            <button
                                type="button"
                                class="btn-action btn-view"
                                title="View Details"
                                data-name="{{ $appt->name }}"
                                data-service="{{ $appt->service }}"
                                data-date="{{ $appt->date }}"
                                data-time="{{ $appt->time }}"
                                data-remarks="{{ $appt->remarks ?? 'No notes provided.' }}"
                                data-email="{{ $appt->email }}"
                                onclick="openInfoModal(this)">
                                View
                            </button>

                            @if($appt->status == 'Pending')
                                <a href="{{ url($basePrefix . '/appointments/' . $appt->id . '/Approved') }}" class="btn-action btn-approve" title="Approve" onclick="return confirm('Approve this appointment?')">Approve</a>
                                <button class="btn-action btn-reschedule" title="Reschedule" onclick="openRescheduleModal('{{ $appt->id }}', '{{ $appt->date }}', '{{ $appt->time }}')">Reschedule</button>
                                <a href="{{ url($basePrefix . '/appointments/' . $appt->id . '/Cancelled') }}" class="btn-action btn-reject btn-cancel" title="Reject" onclick="return confirm('Cancel this request?')">Reject</a>
                            
                            @elseif($appt->status == 'Approved')
                                @php
                                    $scheduledAt = \Carbon\Carbon::parse($appt->date . ' ' . $appt->time);
                                    $now = \Carbon\Carbon::now();
                                    $isFuture = $scheduledAt->isFuture();
                                    $showMissedAction = $now->greaterThan($scheduledAt);
                                @endphp

                                @if($isFuture)
                                    <button class="btn-action" style="background: #e2e8f0; color: #94a3b8; cursor: not-allowed; padding: 6px 12px; border-radius: 4px;" title="Scheduled for {{ \Carbon\Carbon::parse($appt->date)->format('M d, Y') }}">
                                        Consult (Scheduled)</button>
                                @else
                                    <a href="{{ url($basePrefix . '/walkin/form/' . $appt->student_id) }}?source=online" class="btn-action btn-consult" style="background: #0d6efd; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; margin-right: 5px; display: inline-flex; align-items: center; gap: 5px;">
                                        Consult</a>
                                    @if($showMissedAction)
                                        <a
                                            href="{{ url($basePrefix . '/appointments/' . $appt->id . '/' . rawurlencode('Missed Scheduled')) }}"
                                            class="btn-action btn-missed"
                                            title="Mark as Missed Scheduled"
                                            data-status-target="Missed Scheduled">
                                            Missed Scheduled
                                        </a>
                                    @endif
                                @endif
                            @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">No appointments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="infoModal" class="modal-overlay">
        <div class="modal-box">
            <h3 class="modal-title" style="color:#8B0000;">Appointment Details</h3>
            <div class="modal-row"><div class="modal-label">Student Name</div><div class="modal-val" id="mName"></div></div>
            <div class="modal-row"><div class="modal-label">Email</div><div class="modal-val" id="mEmail"></div></div>
            <div class="modal-row"><div class="modal-label">Service Request</div><div class="modal-val" id="mService"></div></div>
            <div class="modal-row"><div class="modal-label">Scheduled For</div><div class="modal-val" id="mDateTime"></div></div>
            <div class="modal-row"><div class="modal-label">Notes</div><div class="modal-val" id="mNotes" style="background:#f8fafc; padding:10px; border-radius:6px; font-size:13px;"></div></div>
            <div class="dialog-actions">
                <button type="button" class="dialog-btn dialog-btn-primary" onclick="closeInfoModal()">Close</button>
            </div>
        </div>
    </div>

    <div id="statusActionModal" class="modal-overlay">
        <div class="modal-box">
            <h3 id="statusActionTitle" class="modal-title" style="color:#70131B;">Appointment Action</h3>
            <p id="statusActionSubtitle" class="modal-subtitle">Confirm this appointment update.</p>
            <div class="modal-row"><div class="modal-label">Student Name</div><div class="modal-val" id="sName"></div></div>
            <div class="modal-row"><div class="modal-label">Service Request</div><div class="modal-val" id="sService"></div></div>
            <div class="modal-row"><div class="modal-label">Schedule</div><div class="modal-val" id="sDateTime"></div></div>
            <div class="dialog-actions">
                <button type="button" class="dialog-btn dialog-btn-neutral" onclick="closeStatusActionModal()">Cancel</button>
                <a id="statusActionConfirm" href="#" class="dialog-btn dialog-btn-primary">Confirm</a>
            </div>
        </div>
    </div>

    <div id="rescheduleModal" class="modal-overlay">
        <div class="modal-box">
            <h3 class="modal-title" style="color:#b45309;">Reschedule Appointment</h3>
            <form id="rescheduleForm" method="POST" action="">
                @csrf
                <p class="modal-subtitle">Select a new date and time for this appointment.</p>
                <div class="modal-row"><div class="modal-label">Student Name</div><div class="modal-val" id="rName"></div></div>
                <div class="modal-row"><div class="modal-label">Service Request</div><div class="modal-val" id="rService"></div></div>
                <div class="modal-row"><div class="modal-label">Current Schedule</div><div class="modal-val" id="rCurrentSchedule"></div></div>
                <div class="modal-row"><label class="modal-label">New Date</label><input type="date" name="date" id="rDate" class="form-input" required></div>
                <div class="modal-row"><label class="modal-label">New Time</label><input type="time" name="time" id="rTime" class="form-input" required></div>
                <div class="dialog-actions">
                    <button type="button" class="dialog-btn dialog-btn-neutral" onclick="closeRescheduleModal()">Cancel</button>
                    <button type="submit" class="dialog-btn dialog-btn-primary">Confirm New Schedule</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const appointmentsBaseUrl = @json(url($basePrefix . '/appointments'));
    const highlightedAppointmentId = @json($highlightAppointmentId);

    function safeText(value) {
        return (value ?? '').toString().trim() || '-';
    }

    function formatSchedule(date, time) {
        const rawDate = (date || '').toString().trim();
        const rawTime = (time || '').toString().trim();

        if (!rawDate && !rawTime) {
            return '-';
        }

        const normalizedTime = rawTime && rawTime.length === 5 ? rawTime + ':00' : rawTime;
        const parsed = rawDate ? new Date(rawDate + 'T' + (normalizedTime || '00:00:00')) : null;

        if (parsed && !Number.isNaN(parsed.getTime())) {
            const datePart = parsed.toLocaleDateString(undefined, { month: 'short', day: '2-digit', year: 'numeric' });
            const timePart = parsed.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
            return datePart + ' at ' + timePart;
        }

        return rawTime ? rawDate + ' at ' + rawTime : rawDate;
    }

    function getRowDataFromElement(element) {
        const row = element ? element.closest('tr') : null;
        if (!row) {
            return { name: '', service: '', date: '', time: '' };
        }

        const name = row.querySelector('.student-name')?.textContent?.trim() || '';
        const service = row.cells?.[2]?.textContent?.trim() || '';
        const dateNode = row.cells?.[3]?.querySelector('div');
        const timeNode = row.cells?.[3]?.querySelectorAll('div')?.[1];
        const date = dateNode?.textContent?.trim() || '';
        const time = timeNode?.textContent?.trim() || '';

        return { name, service, date, time };
    }

    function openInfoModal(triggerOrName, service, date, time, remarks, email) {
        let payload = {
            name: triggerOrName,
            service,
            date,
            time,
            remarks,
            email
        };

        if (triggerOrName && typeof triggerOrName === 'object' && triggerOrName.dataset) {
            payload = {
                name: triggerOrName.dataset.name,
                service: triggerOrName.dataset.service,
                date: triggerOrName.dataset.date,
                time: triggerOrName.dataset.time,
                remarks: triggerOrName.dataset.remarks,
                email: triggerOrName.dataset.email
            };
        }

        document.getElementById('mName').innerText = safeText(payload.name);
        document.getElementById('mService').innerText = safeText(payload.service);
        document.getElementById('mDateTime').innerText = formatSchedule(payload.date, payload.time);
        document.getElementById('mNotes').innerText = safeText(payload.remarks);
        document.getElementById('mEmail').innerText = safeText(payload.email);
        document.getElementById('infoModal').style.display = 'flex';
    }

    function closeInfoModal() {
        document.getElementById('infoModal').style.display = 'none';
    }

    function openStatusActionModal(trigger) {
        const fallback = getRowDataFromElement(trigger);
        const href = trigger?.getAttribute?.('href') || '';
        const matches = href.match(/\/appointments\/(\d+)\/([^/?#]+)/i);
        const decodedStatus = matches ? decodeURIComponent(matches[2]) : '';
        const statusTarget = trigger?.dataset?.statusTarget || decodedStatus || (href.includes('/Approved') ? 'Approved' : 'Cancelled');
        const id = trigger?.dataset?.id || (matches ? matches[1] : '');
        const actionUrl = id ? (appointmentsBaseUrl + '/' + id + '/' + encodeURIComponent(statusTarget)) : href;

        const name = trigger?.dataset?.name || fallback.name;
        const service = trigger?.dataset?.service || fallback.service;
        const date = trigger?.dataset?.date || fallback.date;
        const time = trigger?.dataset?.time || fallback.time;

        const isApprove = statusTarget === 'Approved';
        const isReject = statusTarget === 'Cancelled';
        const isMissed = statusTarget === 'Missed Scheduled';
        document.getElementById('statusActionTitle').innerText = isApprove
            ? 'Approve Appointment'
            : (isMissed ? 'Mark Appointment as Missed' : 'Reject Appointment');
        document.getElementById('statusActionSubtitle').innerText = isApprove
            ? 'This will mark the appointment as approved and notify the workflow.'
            : (isMissed
                ? 'Use this when the scheduled appointment time has passed and the patient did not show up.'
                : 'This will reject the appointment request and mark it as cancelled.');
        document.getElementById('sName').innerText = safeText(name);
        document.getElementById('sService').innerText = safeText(service);
        document.getElementById('sDateTime').innerText = formatSchedule(date, time);

        const confirmBtn = document.getElementById('statusActionConfirm');
        confirmBtn.href = actionUrl;
        confirmBtn.innerText = isApprove
            ? 'Confirm Approval'
            : (isMissed ? 'Confirm Missed Status' : 'Confirm Rejection');
        confirmBtn.className = 'dialog-btn ' + (isApprove ? 'dialog-btn-approve' : (isMissed ? 'dialog-btn-warning' : 'dialog-btn-reject'));

        document.getElementById('statusActionModal').style.display = 'flex';
    }

    function closeStatusActionModal() {
        document.getElementById('statusActionModal').style.display = 'none';
    }

    function openRescheduleModal(triggerOrId, currentDate, currentTime) {
        const form = document.getElementById('rescheduleForm');
        let id = '';
        let date = currentDate || '';
        let time = currentTime || '';
        let name = '';
        let service = '';

        if (triggerOrId && typeof triggerOrId === 'object' && triggerOrId.dataset) {
            id = triggerOrId.dataset.id || '';
            name = triggerOrId.dataset.name || '';
            service = triggerOrId.dataset.service || '';
            date = triggerOrId.dataset.date || '';
            time = triggerOrId.dataset.time || '';

            if (!id) {
                const fallback = getRowDataFromElement(triggerOrId);
                name = fallback.name;
                service = fallback.service;
                date = date || fallback.date;
                time = time || fallback.time;

                const href = triggerOrId.closest('td')?.querySelector('a.btn-approve')?.getAttribute('href') || '';
                const matches = href.match(/\/appointments\/(\d+)\/Approved/i);
                id = matches ? matches[1] : '';
            }
        } else {
            id = (triggerOrId ?? '').toString();
            const lookupTrigger = document.querySelector('a.btn-approve[href$="/' + id + '/Approved"]');
            const fallback = getRowDataFromElement(lookupTrigger);
            name = fallback.name;
            service = fallback.service;
            date = date || fallback.date;
            time = time || fallback.time;
        }

        if (!id) {
            return;
        }

        form.action = appointmentsBaseUrl + '/' + id + '/reschedule';
        document.getElementById('rName').innerText = safeText(name);
        document.getElementById('rService').innerText = safeText(service);
        document.getElementById('rCurrentSchedule').innerText = formatSchedule(date, time);
        document.getElementById('rDate').value = date;
        document.getElementById('rTime').value = (time || '').toString().slice(0, 5);
        document.getElementById('rDate').setAttribute('min', new Date().toISOString().slice(0, 10));
        document.getElementById('rescheduleModal').style.display = 'flex';
    }

    function closeRescheduleModal() {
        document.getElementById('rescheduleModal').style.display = 'none';
    }

    document.addEventListener('click', function(event) {
        const infoModal = document.getElementById('infoModal');
        const statusModal = document.getElementById('statusActionModal');
        const rescheduleModal = document.getElementById('rescheduleModal');

        if (event.target === infoModal) {
            closeInfoModal();
        }
        if (event.target === statusModal) {
            closeStatusActionModal();
        }
        if (event.target === rescheduleModal) {
            closeRescheduleModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeInfoModal();
            closeStatusActionModal();
            closeRescheduleModal();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        if (highlightedAppointmentId) {
            const highlightedRow = document.querySelector('[data-appointment-row][data-appointment-id="' + highlightedAppointmentId + '"]');
            if (highlightedRow) {
                highlightedRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const filter = this.value.toUpperCase();
                const tr = document.getElementById('apptTable').getElementsByTagName('tr');
                for (let i = 1; i < tr.length; i++) {
                    const td = tr[i].getElementsByTagName('td')[0];
                    if (td) {
                        const nameNode = td.getElementsByClassName('student-name')[0];
                        const txtValue = nameNode ? (nameNode.textContent || nameNode.innerText) : '';
                        tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? '' : 'none';
                    }
                }
            });
        }

        document.querySelectorAll('a.btn-approve, a.btn-cancel, a.btn-missed, button.btn-reject').forEach((el) => {
            el.removeAttribute('onclick');
            el.addEventListener('click', function(event) {
                event.preventDefault();
                openStatusActionModal(this);
            });
        });

        document.querySelectorAll('button.btn-reschedule').forEach((el) => {
            const inlineHandler = el.getAttribute('onclick') || '';
            if (!el.dataset.id) {
                const matches = inlineHandler.match(/openRescheduleModal\('([^']+)'\s*,\s*'([^']+)'\s*,\s*'([^']+)'\)/);
                if (matches) {
                    el.dataset.id = matches[1];
                    el.dataset.date = matches[2];
                    el.dataset.time = matches[3];
                }
            }

            if (!el.dataset.name || !el.dataset.service) {
                const fallback = getRowDataFromElement(el);
                el.dataset.name = el.dataset.name || fallback.name;
                el.dataset.service = el.dataset.service || fallback.service;
            }

            el.removeAttribute('onclick');
            el.addEventListener('click', function() {
                openRescheduleModal(this);
            });
        });
    });
</script>
@endpush
