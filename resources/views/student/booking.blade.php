@extends('layouts.student')

@section('title', 'Book Appointment')

@push('styles')
<style>
    /* --- PAGE LAYOUT --- */
    .page-header { margin-bottom: 30px; }
    .page-title { color: #8B0000; font-weight: 800; font-size: 32px; margin: 0 0 10px 0; }
    .page-subtitle { color: #64748b; font-size: 15px; }

    /* --- ALERTS --- */
    .alert { padding: 15px; border-radius: 8px; margin-bottom: 24px; border: 1px solid transparent; font-size: 14px; }
    .alert-success { background: #dcfce7; color: #155724; border-color: #c3e6cb; }
    .alert-danger { background: #fee2e2; color: #721c24; border-color: #f5c6cb; }
    .alert ul { margin: 5px 0 0 20px; padding: 0; }

    /* --- MAIN CARD --- */
    .booking-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow: hidden;
        border-top: 5px solid #8B0000;
        display: flex;
        flex-wrap: wrap;
    }

    .booking-form-section { flex: 2; padding: 40px; border-right: 1px solid #f1f5f9; min-width: 0; }
    .booking-info-section { flex: 1; padding: 40px; background: #fcfcfc; min-width: 0; }
    .booking-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    /* --- FORM STYLING --- */
    .form-section-title { color: #20343a; font-size: 20px; font-weight: 700; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
    
    .input-group { position: relative; margin-bottom: 24px; }
    .input-label { display: block; font-size: 13px; font-weight: 700; color: #64748b; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
    .input-wrapper { position: relative; }
    
    .form-control {
        width: 100%;
        padding: 12px 16px; 
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 15px;
        color: #334155;
        transition: all 0.2s ease;
        background: #fff;
    }
    .form-control:focus { border-color: #8B0000; box-shadow: 0 0 0 4px rgba(139, 0, 0, 0.05); outline: none; }
    
    /* READONLY STYLE */
    .form-control[readonly] {
        background: #f8fafc;
        color: #64748b;
        cursor: not-allowed;
        border-color: #e2e8f0;
    }

    textarea.form-control { resize: vertical; min-height: 100px; }
    .time-display-input {
        background: #f8fafc;
        cursor: default;
    }
    .time-slots {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
        gap: 10px;
        margin-top: 4px;
    }
    .time-slot-btn {
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #334155;
        border-radius: 8px;
        padding: 10px 8px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .time-slot-btn:hover {
        border-color: #8B0000;
        color: #8B0000;
    }
    .time-slot-btn.selected {
        background: #8B0000;
        border-color: #8B0000;
        color: #ffffff;
    }
    .time-slot-btn:disabled {
        background: #f8fafc;
        color: #94a3b8;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }
    .time-slot-hint {
        display: block;
        margin-top: 8px;
        font-size: 12px;
        color: #64748b;
    }
    .date-picker-wrapper {
        position: relative;
    }
    .date-display-input {
        background: #fff;
        cursor: pointer;
    }
    .date-picker-toggle {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        color: #334155;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        padding: 6px 10px;
        cursor: pointer;
    }
    .date-picker-toggle:hover {
        border-color: #8B0000;
        color: #8B0000;
    }
    .date-picker-panel {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        width: 320px;
        max-width: min(100vw - 40px, 320px);
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
        padding: 12px;
        z-index: 60;
    }
    .date-picker-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    .date-picker-nav {
        width: 32px;
        height: 32px;
        border: 1px solid #cbd5e1;
        background: #fff;
        border-radius: 8px;
        color: #334155;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
    }
    .date-picker-nav:hover:not(:disabled) {
        border-color: #8B0000;
        color: #8B0000;
    }
    .date-picker-nav:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }
    .date-picker-month {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
    }
    .date-picker-weekdays,
    .date-picker-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 6px;
    }
    .date-picker-weekdays span {
        text-align: center;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        padding: 4px 0;
    }
    .calendar-day,
    .calendar-empty {
        height: 36px;
        border-radius: 8px;
    }
    .calendar-empty {
        display: block;
    }
    .calendar-day {
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #1e293b;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }
    .calendar-day:hover:not(:disabled) {
        border-color: #8B0000;
        color: #8B0000;
    }
    .calendar-day:disabled {
        background: #f8fafc;
        color: #94a3b8;
        border-color: #e2e8f0;
        cursor: not-allowed;
        text-decoration: line-through;
    }
    .calendar-day.selected {
        background: #8B0000;
        border-color: #8B0000;
        color: #fff;
    }

    .btn-submit {
        background: #8B0000;
        color: white;
        border: none;
        padding: 14px 28px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        width: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex; align-items: center; justify-content: center; gap: 10px;
    }
    .btn-submit:hover { background: #70131B; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(139, 0, 0, 0.2); }

    /* --- WIDGETS --- */
    .info-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
    .info-title { font-size: 16px; font-weight: 700; color: #20343a; margin: 0 0 15px 0; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; }
    .empty-state { text-align: center; padding: 20px 0; color: #94a3b8; }
    .empty-icon { font-size: 32px; margin-bottom: 10px; opacity: 0.5; display: block; }
    
    .appt-item { padding: 12px; border: 1px solid #eee; border-radius: 8px; background: #fff; margin-bottom: 10px; }
    .appt-service { font-weight: 700; color: #8B0000; font-size: 14px; }
    .appt-time { font-size: 13px; color: #555; margin-top: 4px; }
    .appt-status { display: inline-block; margin-top: 6px; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; }
    
    .note-widget { background: #fffbeb; border-left: 4px solid #f59e0b; padding: 16px; border-radius: 8px; color: #92400e; font-size: 14px; line-height: 1.6; }
    .note-header { display: flex; align-items: center; gap: 8px; font-weight: 700; margin-bottom: 8px; color: #b45309; }

    .confirmation-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        z-index: 1100;
    }
    .confirmation-modal {
        width: min(520px, 100%);
        background: #ffffff;
        border-radius: 16px;
        border-top: 5px solid #8B0000;
        box-shadow: 0 18px 35px rgba(15, 23, 42, 0.25);
        padding: 24px;
        position: relative;
    }
    .confirmation-close {
        position: absolute;
        top: 12px;
        right: 12px;
        border: none;
        background: transparent;
        color: #64748b;
        font-size: 24px;
        line-height: 1;
        cursor: pointer;
        padding: 4px 8px;
    }
    .confirmation-title {
        margin: 0 0 8px 0;
        color: #8B0000;
        font-size: 24px;
        font-weight: 800;
    }
    .confirmation-subtitle {
        margin: 0 0 18px 0;
        color: #475569;
        font-size: 14px;
    }
    .confirmation-grid {
        display: grid;
        gap: 10px;
        margin-bottom: 18px;
    }
    .confirmation-item {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 14px;
        background: #f8fafc;
    }
    .confirmation-label {
        display: block;
        font-size: 12px;
        color: #64748b;
        margin-bottom: 2px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        font-weight: 700;
    }
    .confirmation-value {
        color: #1e293b;
        font-size: 15px;
        font-weight: 700;
    }
    .confirmation-status {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 700;
        background: #fff3cd;
        color: #b45309;
    }
    .confirmation-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }
    .confirmation-btn {
        border-radius: 10px;
        padding: 10px 14px;
        font-weight: 700;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        font-size: 14px;
    }
    .confirmation-btn-primary {
        background: #8B0000;
        color: #fff;
    }
    .confirmation-btn-primary:hover {
        background: #70131B;
    }
    .confirmation-btn-secondary {
        background: #fff;
        color: #8B0000;
        border-color: #8B0000;
    }
    .confirmation-btn-secondary:hover {
        background: #fff5f5;
    }

    @media (max-width: 900px) {
        .booking-card { flex-direction: column; }
        .booking-form-section { border-right: none; border-bottom: 1px solid #f1f5f9; }
    }

    @media (max-width: 680px) {
        .page-title { font-size: 26px; }
        .booking-form-section,
        .booking-info-section {
            padding: 24px 16px;
        }
        .booking-grid-2 {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .confirmation-modal {
            padding: 18px;
        }
        .confirmation-actions {
            justify-content: stretch;
        }
        .confirmation-btn {
            width: 100%;
            text-align: center;
        }
    }
</style>
@endpush

@section('content')
<div class="container" style="padding: 40px 20px;">
    
    <div class="page-header">
        <h1 class="page-title">Book an Appointment</h1>
        <p class="page-subtitle">Fill out the form below to request a consultation with the school nurse.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <strong>Success!</strong> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please check the form:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="booking-card">
        
        <div class="booking-form-section">
            <div class="form-section-title">
                <span style="background:#fee2e2; color:#8B0000; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:14px;">1</span>
                Appointment Details
            </div>

            
            <form id="bookingForm" method="POST" action="/student/appointments/store" autocomplete="off">
                @csrf 
                
                <div class="booking-grid-2">
                    <div class="input-group">
                        <label class="input-label">Full Name</label>
                        <div class="input-wrapper">
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" readonly>
                        </div>
                    </div>

                    <div class="input-group">
                        <label class="input-label">Student ID</label>
                        <div class="input-wrapper">
                           <input type="text" name="student_id" class="form-control" value="{{ $user->student_id }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="input-group">
                    <label class="input-label">Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" readonly>
                    </div>
                </div>

                <div class="booking-grid-2">
                    <div class="input-group">
                        <label class="input-label">Preferred Date</label>
                        <div class="input-wrapper date-picker-wrapper">
                            <input id="preferredDate" type="hidden" name="date" value="{{ old('date') }}" required>
                            <input id="preferredDateDisplay" type="text" class="form-control date-display-input" placeholder="Select a date" readonly>
                            <button type="button" class="date-picker-toggle" id="preferredDateToggle">Pick</button>
                            <div class="date-picker-panel" id="datePickerPanel" hidden>
                                <div class="date-picker-header">
                                    <button type="button" class="date-picker-nav" id="calendarPrev" aria-label="Previous month">&lt;</button>
                                    <div class="date-picker-month" id="calendarMonthLabel">Month 2026</div>
                                    <button type="button" class="date-picker-nav" id="calendarNext" aria-label="Next month">&gt;</button>
                                </div>
                                <div class="date-picker-weekdays">
                                    <span>Sun</span>
                                    <span>Mon</span>
                                    <span>Tue</span>
                                    <span>Wed</span>
                                    <span>Thu</span>
                                    <span>Fri</span>
                                    <span>Sat</span>
                                </div>
                                <div class="date-picker-days" id="calendarDays"></div>
                            </div>
                        </div>
                        <small class="time-slot-hint" id="dateHint">Choose a date to load available schedules.</small>
                    </div>

                    <div class="input-group">
                        <label class="input-label">Preferred Time</label>
                        <div class="input-wrapper">
                            <input id="preferredTimeDisplay" type="text" class="form-control time-display-input" readonly placeholder="Select a date first">
                            <input id="preferredTimeInput" type="hidden" name="time" value="{{ old('time') }}" required>
                        </div>
                    </div>
                </div>

                <div class="input-group">
                    <div id="timeSlots" class="time-slots" aria-live="polite"></div>
                    <small class="time-slot-hint" id="timeSlotsHint">Select a date to view available time slots.</small>
                </div>
                 
                <div class="input-group">
                    <label class="input-label">Service Type</label>
                    <div class="input-wrapper">
                        <select name="service" class="form-control" required>
                            <option value="" disabled selected>Select a Service...</option>
                            <option value="General Consultation">General Consultation</option>
                            <option value="Blood Pressure Monitoring">Blood Pressure Monitoring</option>
                        </select>
                    </div>
                </div>

                <div class="input-group">
                    <label class="input-label">Reason / Symptoms</label>
                    <textarea name="remarks" class="form-control" placeholder="Briefly describe what you are feeling..." rows="3">{{ old('remarks') }}</textarea>
                </div>

                <button type="submit" class="btn-submit">
                    Confirm Appointment ➜
                </button>
            </form>
        </div>

        <div class="booking-info-section">
            
            <div class="info-card">
                <h4 class="info-title">Upcoming Schedule</h4>
                
                <div class="app-list">
                    @forelse($appointments as $appt)
                        <div class="appt-item">
                            <div class="appt-service">{{ $appt->service }}</div>
                            <div class="appt-time">
                                {{ \Carbon\Carbon::parse($appt->date)->format('M d, Y') }} <br> 
                                <span style="font-weight:normal; font-size:12px; color:#777;">
                                    {{ \Carbon\Carbon::parse($appt->time)->format('g:i A') }}
                                </span>
                            </div>
                            
                            <div style="margin-top: 5px;">
                                @if($appt->status == 'Approved')
                                    <span class="appt-status" style="background: #dcfce7; color: #15803d;">
                                        ● Approved
                                    </span>
                                @else
                                    <span class="appt-status" style="background: #fff3cd; color: #b45309;">
                                        ● Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <span class="empty-icon">📆</span>
                            <div>No appointments scheduled.</div>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="note-widget">
                <div class="note-header">
                    <span>⚠️</span> Important Reminder
                </div>
                <p style="margin: 0;">
                    Clinic hours are <strong>8:00 AM - 7:00 PM</strong>, Mondays to Fridays. 
                    <br><br>
                    Please ensure your selected time falls within this range.
                </p>
            </div>

        </div>
    </div>
</div>

@if(session('appointment_confirmation'))
    @php($confirmation = session('appointment_confirmation'))
    <div class="confirmation-overlay" id="appointmentConfirmationOverlay">
        <div class="confirmation-modal" role="dialog" aria-modal="true" aria-labelledby="appointmentConfirmationTitle">
            <button type="button" class="confirmation-close" id="appointmentConfirmationClose" aria-label="Close confirmation">x</button>
            <h2 class="confirmation-title" id="appointmentConfirmationTitle">Appointment Submitted</h2>
            <p class="confirmation-subtitle">Your request has been received. Go to your profile to check your appointment status and updates.</p>

            <div class="confirmation-grid">
                <div class="confirmation-item">
                    <span class="confirmation-label">Service</span>
                    <span class="confirmation-value">{{ $confirmation['service'] ?? '-' }}</span>
                </div>
                <div class="confirmation-item">
                    <span class="confirmation-label">Preferred Date</span>
                    <span class="confirmation-value">{{ $confirmation['date'] ?? '-' }}</span>
                </div>
                <div class="confirmation-item">
                    <span class="confirmation-label">Preferred Time</span>
                    <span class="confirmation-value">{{ $confirmation['time'] ?? '-' }}</span>
                </div>
                <div class="confirmation-item">
                    <span class="confirmation-label">Current Status</span>
                    <span class="confirmation-status">{{ $confirmation['status'] ?? 'Pending' }}</span>
                </div>
            </div>

            <div class="confirmation-actions">
                <button type="button" class="confirmation-btn confirmation-btn-secondary" id="appointmentConfirmationDone">Stay Here</button>
                <a href="/student/account" class="confirmation-btn confirmation-btn-primary">Go To My Profile</a>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bookingForm = document.getElementById('bookingForm');
        const dateInput = document.getElementById('preferredDate');
        const dateDisplayInput = document.getElementById('preferredDateDisplay');
        const dateToggle = document.getElementById('preferredDateToggle');
        const datePickerPanel = document.getElementById('datePickerPanel');
        const calendarMonthLabel = document.getElementById('calendarMonthLabel');
        const calendarDays = document.getElementById('calendarDays');
        const calendarPrev = document.getElementById('calendarPrev');
        const calendarNext = document.getElementById('calendarNext');
        const timeInput = document.getElementById('preferredTimeInput');
        const timeDisplay = document.getElementById('preferredTimeDisplay');
        const timeSlots = document.getElementById('timeSlots');
        const slotsHint = document.getElementById('timeSlotsHint');
        const dateHint = document.getElementById('dateHint');
        const availabilityUrl = @json(url('/student/appointments/availability'));

        if (!dateInput || !dateDisplayInput || !dateToggle || !datePickerPanel || !calendarMonthLabel || !calendarDays || !calendarPrev || !calendarNext || !timeInput || !timeDisplay || !timeSlots || !slotsHint) {
            return;
        }

        const today = new Date();
        today.setHours(0, 0, 0, 0);
        let viewMonth = new Date(today.getFullYear(), today.getMonth(), 1);

        function pad2(value) {
            return String(value).padStart(2, '0');
        }

        function parseDateValue(value) {
            if (!value) return null;
            const parts = String(value).split('-');
            if (parts.length !== 3) return null;

            const year = Number(parts[0]);
            const month = Number(parts[1]);
            const day = Number(parts[2]);
            if (!year || !month || !day) return null;

            const parsed = new Date(year, month - 1, day);
            if (
                parsed.getFullYear() !== year ||
                parsed.getMonth() !== month - 1 ||
                parsed.getDate() !== day
            ) {
                return null;
            }

            parsed.setHours(0, 0, 0, 0);
            return parsed;
        }

        function toDateValue(dateObj) {
            return dateObj.getFullYear() + '-' + pad2(dateObj.getMonth() + 1) + '-' + pad2(dateObj.getDate());
        }

        function formatDateDisplay(value) {
            const parsed = parseDateValue(value);
            if (!parsed) return '';
            return parsed.toLocaleDateString([], { month: 'long', day: 'numeric', year: 'numeric' });
        }

        function isWeekendDateObj(dateObj) {
            const day = dateObj.getDay();
            return day === 0 || day === 6;
        }

        function isPastDateObj(dateObj) {
            return dateObj.getTime() < today.getTime();
        }

        function isSelectableDateObj(dateObj) {
            return !isPastDateObj(dateObj) && !isWeekendDateObj(dateObj);
        }

        function normalizeTime(raw) {
            if (!raw) return '';
            const text = String(raw).trim();
            return text.length >= 5 ? text.slice(0, 5) : text;
        }

        function formatTimeLabel(value) {
            if (!value) return '';
            const parts = value.split(':');
            const hour = Number(parts[0] || 0);
            const minute = Number(parts[1] || 0);
            const dt = new Date();
            dt.setHours(hour, minute, 0, 0);
            return dt.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
        }

        function setSelectedTime(value) {
            const normalized = normalizeTime(value);
            timeInput.value = normalized;
            timeDisplay.value = normalized ? formatTimeLabel(normalized) : '';
            timeDisplay.placeholder = normalized
                ? ''
                : (dateInput.value ? 'Choose an available time' : 'Select a date first');

            timeSlots.querySelectorAll('.time-slot-btn').forEach(function (btn) {
                btn.classList.toggle('selected', btn.dataset.value === normalized);
            });
        }

        function closeDatePanel() {
            datePickerPanel.hidden = true;
        }

        function renderCalendar() {
            const year = viewMonth.getFullYear();
            const month = viewMonth.getMonth();
            const firstOfMonth = new Date(year, month, 1);
            const firstWeekDay = firstOfMonth.getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            calendarMonthLabel.textContent = viewMonth.toLocaleDateString([], { month: 'long', year: 'numeric' });
            calendarDays.innerHTML = '';

            for (let i = 0; i < firstWeekDay; i++) {
                const emptyCell = document.createElement('span');
                emptyCell.className = 'calendar-empty';
                calendarDays.appendChild(emptyCell);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dayDate = new Date(year, month, day);
                dayDate.setHours(0, 0, 0, 0);
                const dateValue = toDateValue(dayDate);
                const dayButton = document.createElement('button');

                dayButton.type = 'button';
                dayButton.className = 'calendar-day';
                dayButton.textContent = String(day);

                const selectable = isSelectableDateObj(dayDate);
                if (!selectable) {
                    dayButton.disabled = true;
                    dayButton.title = isWeekendDateObj(dayDate)
                        ? 'Weekends are unavailable.'
                        : 'Past dates are unavailable.';
                } else {
                    dayButton.addEventListener('click', function () {
                        dateInput.value = dateValue;
                        dateDisplayInput.value = formatDateDisplay(dateValue);
                        if (dateHint) {
                            dateHint.textContent = 'Date selected. Now choose an available time slot.';
                        }
                        loadAvailability(dateValue, '');
                        renderCalendar();
                        closeDatePanel();
                    });
                }

                if (dateInput.value === dateValue) {
                    dayButton.classList.add('selected');
                }

                calendarDays.appendChild(dayButton);
            }

            const renderedCells = firstWeekDay + daysInMonth;
            const trailingCells = renderedCells % 7 === 0 ? 0 : (7 - (renderedCells % 7));
            for (let i = 0; i < trailingCells; i++) {
                const emptyCell = document.createElement('span');
                emptyCell.className = 'calendar-empty';
                calendarDays.appendChild(emptyCell);
            }

            const currentMonthStart = new Date(today.getFullYear(), today.getMonth(), 1).getTime();
            const viewingMonthStart = new Date(viewMonth.getFullYear(), viewMonth.getMonth(), 1).getTime();
            calendarPrev.disabled = viewingMonthStart <= currentMonthStart;
        }

        function openDatePanel() {
            datePickerPanel.hidden = false;
            renderCalendar();
        }

        function renderMessage(message) {
            timeSlots.innerHTML = '';
            slotsHint.textContent = message;
            setSelectedTime('');
        }

        function renderSlots(slots, preselectedTime) {
            timeSlots.innerHTML = '';
            const selected = normalizeTime(preselectedTime);
            let availableCount = 0;

            (slots || []).forEach(function (slot) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'time-slot-btn';
                btn.dataset.value = slot.value;
                btn.textContent = slot.label;

                if (!slot.available) {
                    btn.disabled = true;
                } else {
                    availableCount++;
                    btn.addEventListener('click', function () {
                        setSelectedTime(slot.value);
                    });
                }

                if (slot.available && selected && slot.value === selected) {
                    btn.classList.add('selected');
                }

                timeSlots.appendChild(btn);
            });

            if (availableCount === 0) {
                slotsHint.textContent = 'No available time slots for this date.';
                setSelectedTime('');
                return;
            }

            if (selected && slots.some(function (slot) { return slot.available && slot.value === selected; })) {
                setSelectedTime(selected);
            } else {
                setSelectedTime('');
            }

            slotsHint.textContent = 'Select one available time slot.';
        }

        function isWeekendDate(value) {
            if (!value) return false;
            const parsed = new Date(value + 'T00:00:00');
            const day = parsed.getDay();
            return day === 0 || day === 6;
        }

        async function loadAvailability(dateValue, preselectedTime) {
            if (!dateValue) {
                renderMessage('Select a date to view available time slots.');
                return;
            }

            slotsHint.textContent = 'Loading available time slots...';
            timeSlots.innerHTML = '';

            try {
                const response = await fetch(availabilityUrl + '?date=' + encodeURIComponent(dateValue), {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Unable to load available schedules.');
                }

                if (!data.available && (!data.slots || data.slots.length === 0)) {
                    renderMessage(data.message || 'No available time slots for this date.');
                    return;
                }

                renderSlots(data.slots, preselectedTime);

                if (data.message) {
                    slotsHint.textContent = data.message;
                }
            } catch (error) {
                renderMessage(error.message || 'Unable to load available schedules right now.');
            }
        }

        dateToggle.addEventListener('click', function () {
            if (datePickerPanel.hidden) {
                openDatePanel();
            } else {
                closeDatePanel();
            }
        });

        dateDisplayInput.addEventListener('click', function () {
            openDatePanel();
        });
        dateDisplayInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openDatePanel();
            }
        });

        calendarPrev.addEventListener('click', function () {
            if (calendarPrev.disabled) {
                return;
            }
            viewMonth = new Date(viewMonth.getFullYear(), viewMonth.getMonth() - 1, 1);
            renderCalendar();
        });
        calendarNext.addEventListener('click', function () {
            viewMonth = new Date(viewMonth.getFullYear(), viewMonth.getMonth() + 1, 1);
            renderCalendar();
        });

        document.addEventListener('click', function (event) {
            const clickedInsidePanel = datePickerPanel.contains(event.target);
            const clickedDisplay = dateDisplayInput.contains(event.target);
            const clickedToggle = dateToggle.contains(event.target);
            if (!clickedInsidePanel && !clickedDisplay && !clickedToggle) {
                closeDatePanel();
            }
        });

        const initialDate = dateInput.value;
        const initialTime = normalizeTime(timeInput.value);
        if (initialTime) {
            timeInput.value = initialTime;
        }

        if (initialDate && parseDateValue(initialDate) && isSelectableDateObj(parseDateValue(initialDate))) {
            const parsedInitial = parseDateValue(initialDate);
            viewMonth = new Date(parsedInitial.getFullYear(), parsedInitial.getMonth(), 1);
            dateDisplayInput.value = formatDateDisplay(initialDate);
            loadAvailability(initialDate, initialTime);
        } else {
            dateInput.value = '';
            dateDisplayInput.value = '';
            renderMessage('Select a date to view available time slots.');
            if (dateHint) {
                dateHint.textContent = 'Weekends and past dates are unavailable.';
            }
        }

        if (bookingForm) {
            bookingForm.addEventListener('submit', function (event) {
                let isValid = true;

                if (!dateInput.value) {
                    isValid = false;
                    if (dateHint) {
                        dateHint.textContent = 'Please choose an available weekday date.';
                    }
                    openDatePanel();
                }

                if (!timeInput.value) {
                    isValid = false;
                    slotsHint.textContent = 'Please select one available time slot.';
                }

                if (!isValid) {
                    event.preventDefault();
                    event.stopPropagation();
                }
            });
        }

        renderCalendar();

        const confirmationOverlay = document.getElementById('appointmentConfirmationOverlay');
        const confirmationClose = document.getElementById('appointmentConfirmationClose');
        const confirmationDone = document.getElementById('appointmentConfirmationDone');

        if (confirmationOverlay) {
            const closeConfirmation = function () {
                confirmationOverlay.style.display = 'none';
            };

            if (confirmationClose) {
                confirmationClose.addEventListener('click', closeConfirmation);
            }
            if (confirmationDone) {
                confirmationDone.addEventListener('click', closeConfirmation);
            }
            confirmationOverlay.addEventListener('click', function (event) {
                if (event.target === confirmationOverlay) {
                    closeConfirmation();
                }
            });
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && confirmationOverlay.style.display !== 'none') {
                    closeConfirmation();
                }
            });
        }
    });
</script>
@endpush
