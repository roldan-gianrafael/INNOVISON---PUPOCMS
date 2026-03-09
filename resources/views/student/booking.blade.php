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
    .date-picker-only {
        caret-color: transparent;
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
                        <div class="input-wrapper">
                            <input
                                id="preferredDate"
                                type="date"
                                name="date"
                                class="form-control date-picker-only"
                                required
                                min="{{ now()->toDateString() }}"
                                value="{{ old('date') }}"
                                autocomplete="off">
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateInput = document.getElementById('preferredDate');
        const timeInput = document.getElementById('preferredTimeInput');
        const timeDisplay = document.getElementById('preferredTimeDisplay');
        const timeSlots = document.getElementById('timeSlots');
        const slotsHint = document.getElementById('timeSlotsHint');
        const dateHint = document.getElementById('dateHint');
        const availabilityUrl = @json(url('/student/appointments/availability'));

        if (!dateInput || !timeInput || !timeDisplay || !timeSlots || !slotsHint) {
            return;
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

        function openNativePicker() {
            if (typeof dateInput.showPicker === 'function') {
                try {
                    dateInput.showPicker();
                } catch (error) {
                    // Browser blocks showPicker unless in direct user gesture.
                }
            }
        }

        dateInput.addEventListener('focus', openNativePicker);
        dateInput.addEventListener('click', openNativePicker);
        dateInput.addEventListener('keydown', function (event) {
            const blockedKeys = ['Backspace', 'Delete'];
            const printableKey = event.key && event.key.length === 1;
            if (printableKey || blockedKeys.includes(event.key)) {
                event.preventDefault();
            }
        });
        dateInput.addEventListener('paste', function (event) {
            event.preventDefault();
        });

        dateInput.addEventListener('change', function () {
            dateInput.setCustomValidity('');
            if (isWeekendDate(dateInput.value)) {
                if (dateHint) {
                    dateHint.textContent = 'Weekends are unavailable. Please choose Monday to Friday.';
                }
                renderMessage('Appointments are available from Monday to Friday only.');
                dateInput.setCustomValidity('Please choose a weekday appointment date.');
                dateInput.reportValidity();
                return;
            }
            if (dateHint) {
                dateHint.textContent = 'Date selected. Now choose an available time slot.';
            }
            loadAvailability(dateInput.value, '');
        });

        const initialDate = dateInput.value;
        const initialTime = normalizeTime(timeInput.value);
        if (initialTime) {
            timeInput.value = initialTime;
        }

        if (initialDate) {
            loadAvailability(initialDate, initialTime);
        } else {
            renderMessage('Select a date to view available time slots.');
        }
    });
</script>
@endpush
