@extends('layouts.student')

@section('title', 'My Account')

@push('styles')
<style>
    /* --- HERO PROFILE SECTION --- */
    .profile-hero {
        background: linear-gradient(135deg, #8B0000 0%, #5a0f15 100%);
        border-radius: 16px;
        padding: 40px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 10px 20px rgba(139, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 30px;
        flex-wrap: wrap;
    }

    .hero-avatar {
        width: 100px;
        height: 100px;
        background: rgba(255,255,255,0.2);
        border: 2px solid rgba(255,255,255,0.4);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        font-weight: 800;
        color: #fff;
    }

    .hero-info { flex: 1; }
    .hero-name { font-size: 32px; font-weight: 800; margin: 0; line-height: 1.2; color: white; }
    .hero-course { font-size: 16px; opacity: 0.9; margin-top: 5px; font-weight: 500; }
    .hero-badge {
        background: #ffc107;
        color: #70131B;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        margin-left: 10px;
        vertical-align: middle;
    }

    /* Stats Row inside Hero */
    .hero-stats {
        display: flex;
        gap: 40px;
        margin-top: 10px;
        padding-top: 20px;
        border-top: 1px solid rgba(255,255,255,0.2);
    }
    .stat-item { text-align: left; }
    .stat-val { font-size: 24px; font-weight: 700; display: block; }
    .stat-label { font-size: 12px; text-transform: uppercase; opacity: 0.8; letter-spacing: 0.5px; }

    /* --- LAYOUT GRID --- */
    .account-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    /* --- APPOINTMENT CARDS --- */
    .section-title {
        color: #20343a;
        font-size: 20px;
        font-weight: 800;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .appt-card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 16px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
        border-left: 5px solid #cbd5e1;
        transition: transform 0.2s;
    }
    .appt-card:hover { transform: translateY(-3px); }

    .appt-card.approved { border-left-color: #10b981; }
    .appt-card.pending { border-left-color: #f59e0b; }
    .appt-card.cancelled { border-left-color: #ef4444; }
    .appt-card.completed { border-left-color: #3b82f6; }

    .appt-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
    .appt-service { font-size: 18px; font-weight: 700; color: #334155; }
    .appt-date { font-size: 14px; color: #64748b; font-weight: 600; text-align: right; }
    
    .appt-notes {
        background: #f8fafc;
        padding: 12px;
        border-radius: 8px;
        font-size: 14px;
        color: #475569;
        margin-bottom: 15px;
        border: 1px dashed #cbd5e1;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .status-badge.approved { background: #dcfce7; color: #15803d; }
    .status-badge.pending { background: #fffbeb; color: #b45309; }
    .status-badge.cancelled { background: #fee2e2; color: #b91c1c; }
    .status-badge.completed { background: #dbeafe; color: #1e40af; }

    .profile-grid-3,
    .profile-grid-2 {
        display: grid;
        gap: 10px;
        margin-bottom: 15px;
    }
    .profile-grid-3 { grid-template-columns: 2fr 1fr 1fr; }
    .profile-grid-2 { grid-template-columns: 1fr 1fr; }

    /* --- SIDEBAR WIDGETS --- */
    .widget-card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
    }

    .input-label { font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: 6px; text-transform: uppercase; display: block; }
    
    .form-control { 
        width: 100%; 
        padding: 10px 14px; 
        border: 1px solid #cbd5e1; 
        border-radius: 8px; 
        font-size: 14px; 
        color: #334155; 
        transition: 0.2s; 
        background: #fff; 
    }
    .form-control:focus { border-color: #8B0000; box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.05); outline: none; }
    .form-control:disabled { background: #f8fafc; color: #94a3b8; cursor: not-allowed; }

    /* --- NOTIFICATIONS --- */
    .alert-success { background: #dcfce7; color: #15803d; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 600; border: 1px solid #bbf7d0; }
    .notif-item { display: flex; gap: 10px; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
    .notif-item:last-child { border-bottom: none; }
    .notif-icon { font-size: 16px; }
    .notif-text { font-size: 13px; color: #334155; line-height: 1.4; }
    .notif-time { display: block; font-size: 11px; color: #94a3b8; margin-top: 4px; }

    /* --- BARCODE WIDGET --- */
    .barcode-status-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    .barcode-status-card::before {
        content: "";
        position: absolute;
        top: 0; left: 0; width: 4px; height: 100%;
    }
    .barcode-status-card.linked::before { background: #10b981; }
    .barcode-status-card.not-linked::before { background: #f59e0b; }

    .barcode-icon-box { font-size: 24px; margin-bottom: 10px; }
    .barcode-label { font-size: 11px; text-transform: uppercase; font-weight: 800; color: #64748b; letter-spacing: 0.5px; }
    .barcode-value { font-size: 16px; font-weight: 700; color: #1e293b; display: block; margin: 4px 0; }
    .btn-barcode-action { display: inline-block; margin-top: 10px; font-size: 12px; font-weight: 700; text-decoration: none; color: #8B0000; }
    .btn-barcode-action:hover { text-decoration: underline; }

    @media (max-width: 900px) {
        .account-layout { grid-template-columns: 1fr; }
        .hero-stats { gap: 20px; flex-wrap: wrap; }
    }

    @media (max-width: 760px) {
        .profile-hero {
            padding: 24px 18px;
            gap: 18px;
        }
        .hero-name {
            font-size: 24px;
        }
        .profile-grid-3,
        .profile-grid-2 {
            grid-template-columns: 1fr;
        }
    }

  
    /* --- HEALTH PROFILE STATUS WIDGET --- */
    .health-status-card {
        background: var(--card-bg, #fff);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(139, 0, 0, 0.08);
        border: 1px solid #fce7e7;
        margin-bottom: 24px;
        text-align: center;
        color: var(--text-main, #1e293b);
    }
    .health-status-title {
        font-size: 14px;
        font-weight: 800;
        color: #8B0000;
        text-transform: uppercase;
        margin-bottom: 15px;
        display: block;
    }
    .health-status-summary {
        margin-bottom: 15px;
    }
    .health-status-state {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }
    .health-status-state.issued { background: #dcfce7; color: #166534; }
    .health-status-state.pending { background: #fffbeb; color: #92400e; }
    .health-status-state.incomplete { background: #fef2f2; color: #991b1b; }
    .health-status-message {
        font-size: 13px;
        color: var(--text-main, #1e293b);
        margin: 8px 0 0;
        line-height: 1.45;
    }
    .health-status-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .health-status-link {
        font-size: 12px;
        color: var(--text-light, #64748b);
        text-decoration: underline;
        text-align: center;
    }
    .health-status-note {
        font-size: 12px;
        color: var(--text-light, #64748b);
        margin-top: 10px;
        display: block;
    }
    .btn-print-form {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 12px;
        background: #8B0000;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 700;
        font-size: 14px;
        transition: 0.3s;
    }
    .btn-print-form:hover {
        background: #5a0f15;
        box-shadow: 0 4px 12px rgba(139, 0, 0, 0.2);
        color: white;
    }
    .btn-print-form.approved { background: #059669; }
    .btn-print-form.pending { background: #8f2724; }
    .btn-print-form.incomplete { background: #800000; }
    .btn-print-form.disabled {
        background: #dd4b4b;
        cursor: not-allowed;
        font-size: 11px;
        opacity: 0.85;
    }
    html[data-theme="dark"] .health-status-card {
        border-color: var(--border);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.35);
    }
    html[data-theme="dark"] .health-status-title {
        color: #fca5a5;
    }
    html[data-theme="dark"] .health-status-link,
    html[data-theme="dark"] .health-status-note {
        color: var(--text-light, #a9b4c4);
    }
</style>
@endpush

@section('content')
<div class="container" style="padding: 40px 20px;">

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div style="background:#fee2e2; color:#b91c1c; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center; font-size:14px; border:1px solid #fecaca;">
            Please enter a valid numeric contact number.
        </div>
    @endif

    <div class="profile-hero">
        <div class="hero-avatar">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div class="hero-info">
            <h1 class="hero-name">{{ $user->name }} <span class="hero-badge">Active</span></h1>
            <div class="hero-course">
                {{ $user->student_id }} • {{ $user->course ?? 'BS Information Technology' }}
            </div>
            
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-val">{{ $pendingCount ?? 0 }}</span>
                    <span class="stat-label">Pending</span>
                </div>
                <div class="stat-item">
                    <span class="stat-val">{{ $approvedCount ?? 0 }}</span>
                    <span class="stat-label">Approved</span>
                </div>
                <div class="stat-item">
                    <span class="stat-val">{{ $completedCount ?? 0 }}</span>
                    <span class="stat-label">Completed</span>
                </div>
                <div class="stat-item">
                    <span class="stat-val">{{ $cancelledCount ?? 0 }}</span>
                    <span class="stat-label">Cancelled</span>
                </div>
            </div>
        </div>
    </div>

    <div class="account-layout">
        
        {{-- Left: History --}}
        <div>
            <div class="section-title">My Appointment History</div>

            @forelse($appointments as $appt)
                <div class="appt-card {{ strtolower($appt->status) }}">
                    <div class="appt-header">
                        <div class="appt-service">{{ $appt->service }}</div>
                        <div class="appt-date">
                            {{ \Carbon\Carbon::parse($appt->date)->format('M d, Y') }}<br>
                            <small>{{ \Carbon\Carbon::parse($appt->time)->format('g:i A') }}</small>
                        </div>
                    </div>
                    
                    @if($appt->remarks)
                        <div class="appt-notes">
                            "{{ $appt->remarks }}"
                        </div>
                    @endif

                    <span class="status-badge {{ strtolower($appt->status) }}">
                        ● {{ $appt->status }}
                    </span>
                </div>
            @empty
                <div style="text-align: center; padding: 40px; background: #fff; border-radius: 12px; border: 1px dashed #cbd5e1;">
                    <div style="font-size: 40px; margin-bottom: 10px; opacity: 0.5;">📭</div>
                    <div style="color: #64748b; font-weight: 600;">No appointment history found.</div>
                </div>
            @endforelse
        </div>
    
        {{-- Right: Widgets --}}
        <div>
            <div class="barcode-status-card {{ $user->barcode ? 'linked' : 'not-linked' }}">
                <div class="barcode-label">Clinic ID Link Status</div>
                
                @if($user->barcode)
                    <div class="barcode-icon-box">✅</div>
                    <span class="barcode-value">{{ $user->barcode }}</span>
                    <p style="font-size: 12px; color: #64748b; margin: 0;">Your account is ready for clinic walk-ins.</p>
                @else
                    <div class="barcode-icon-box">⚠️</div>
                    <span class="barcode-value" style="color: #b45309;">Not Yet Linked</span>
                    <p style="font-size: 12px; color: #64748b; margin: 0;">Scan your physical ID for quick check-ins.</p>
                    <a href="{{ route('barcode.register') }}" class="btn-barcode-action">Register Barcode Now →</a>
                @endif
            </div>
            
            {{-- Notification Div (Nandito pa rin) --}}
            <div class="widget-card">
                <div class="section-title" style="font-size: 16px; margin-bottom: 15px;">Notifications</div>
                @forelse($notifications as $notif)
                    <div class="notif-item">
                        <div class="notif-icon">{{ $notif['icon'] }}</div>
                        <div style="flex:1;">
                            <div class="notif-text">{{ $notif['message'] }}</div>
                            <span class="notif-time">{{ $notif['time'] }}</span>
                        </div>
                    </div>
                @empty
                    <div style="font-size: 14px; color: #64748b; text-align: center; padding: 20px 0;">No new notifications.</div>
                @endforelse
            </div>

            {{-- NEW: Health Form Quick Access Div --}}
            <div class="health-status-card">
    <span class="health-status-title">Health Information Record</span>
    
    @if($user->is_health_profile_completed)
        @php
            $status = $user->healthProfile->clearance_status ?? 'Pending';
        @endphp

        @if($status == 'Issued')
            {{-- CASE 1: APPROVED --}}
            <div class="health-status-summary">
                <span class="health-status-state issued">Approved</span>
                <p class="health-status-message">
                    Your health profile is now approved and ready for printing.
                </p>
            </div>
            
            <div class="health-status-actions">
                <a href="{{ route('print.health.form') }}" class="btn-print-form approved">
                    Print Approved Form
                </a>
        
                <a href="" class="health-status-link">
                    View Record Details
                </a>
            </div>
            <span class="health-status-note">Valid for Academic Year 2025-2026</span>

        @else
           
            <div class="health-status-summary">
                <span class="health-status-state pending">Pending Review</span>
                <p class="health-status-message">
                    Your profile has been submitted and is currently <strong>awaiting medical review</strong>.
                </p>
            </div>
            
            <div class="health-status-actions">
                <a href="{{ route('print.health.form') }}" class="btn-print-form pending">
                    View Submitted Form
                </a>
                <button class="btn-print-form disabled" disabled>
                    Printing Disabled (Pending)
                </button>
            </div>
            <span class="health-status-note">Physician signature is required to print.</span>
        @endif

    @else
        {{-- CASE 3: NOT COMPLETED --}}
        <div class="health-status-summary">
            <span class="health-status-state incomplete">Not Completed</span>
            <p class="health-status-message">You haven't completed your health profile yet.</p>
        </div>
        <a href="{{ route('health.form') }}" class="btn-print-form incomplete">
            Complete Form Now
        </a>
        <span class="health-status-note">Required for clinic consultations.</span>
    @endif
</div>
            

            {{-- Full Profile Widget --}}
            <div class="widget-card">
    <div class="section-title" style="font-size: 16px; margin-bottom: 15px;"> Profile Information</div>
    
    <form action="{{ route('student.updateContact') }}" method="POST">
        @csrf
        @if(!empty($linkedAdminProfile))
            <input type="hidden" name="admin_profile_id" value="{{ $linkedAdminProfile->admin_id }}">
        @endif
        
        @if(empty($linkedAdminProfile))
            <div class="profile-grid-3">
                <div>
                    <label class="input-label">Course</label>
                    <input type="text" class="form-control" value="{{ $user->course }}" readonly style="background-color: #f8fafc;">
                </div>
                <div>
                    <label class="input-label">Year</label>
                    <input type="text" name="year" class="form-control editable-input" value="{{ old('year', $user->year) }}" disabled>
                </div>
                <div>
                    <label class="input-label">Section</label>
                    <input type="text" name="section" class="form-control editable-input" value="{{ old('section', $user->section) }}" disabled>
                </div>
            </div>
        @endif

        @if(empty($linkedAdminProfile))
            <div class="profile-grid-2">
                <div>
                    <label class="input-label">Gender</label>
                    <input type="text" class="form-control" value="{{ $user->gender }}" readonly style="background-color: #f8fafc;">
                </div>
                <div>
                    <label class="input-label">Birthday (DOB)</label>
                    <input type="text" class="form-control" value="{{ $user->DOB }}" readonly style="background-color: #f8fafc;">
                </div>
            </div>
        @endif

        @if(empty($linkedAdminProfile))
            <div class="profile-grid-2">
                <div>
                    <label class="input-label">Height (cm)</label>
                    <input type="number" step="0.1" name="height" class="form-control editable-input" value="{{ old('height', $user->height) }}" disabled>
                </div>
                <div>
                    <label class="input-label">Weight (kg)</label>
                    <input type="number" step="0.1" name="weight" class="form-control editable-input" value="{{ old('weight', $user->weight) }}" disabled>
                </div>
            </div>
        @endif

        @if(empty($linkedAdminProfile))
            <div style="margin-bottom: 15px;">
                <label class="input-label">Contact Number</label>
                <input type="text" name="contact_no" class="form-control editable-input" value="{{ old('contact_no', $user->contact_no) }}" disabled>
            </div>
        @endif

        @if(!empty($linkedAdminProfile))
            <div class="profile-grid-2">
                <div>
                    <label class="input-label">First Name</label>
                    <input type="text" name="first_name" class="form-control editable-input" value="{{ old('first_name', $linkedAdminProfile->first_name) }}" disabled>
                </div>
                <div>
                    <label class="input-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control editable-input" value="{{ old('last_name', $linkedAdminProfile->last_name) }}" disabled>
                </div>
            </div>

            <div class="profile-grid-2">
                <div>
                    <label class="input-label">Email</label>
                    <input type="email" name="email" class="form-control editable-input" value="{{ old('email', $linkedAdminProfile->email) }}" disabled>
                </div>
                <div>
                    <label class="input-label">Contact Number</label>
                    <input type="text" name="contact_no" class="form-control editable-input" value="{{ old('contact_no', $user->contact_no) }}" disabled>
                </div>
            </div>

            <div class="profile-grid-2">
                <div>
                    <label class="input-label">Birthday</label>
                    <input type="date" name="birthday" class="form-control editable-input" value="{{ old('birthday', $linkedAdminProfile->birthday) }}" disabled>
                </div>
                <div>
                    <label class="input-label">Gender</label>
                    <input type="text" name="gender" class="form-control editable-input" value="{{ old('gender', $linkedAdminProfile->gender) }}" disabled>
                </div>
            </div>

            <div class="profile-grid-2">
                <div>
                    <label class="input-label">Age</label>
                    <input type="number" name="age" class="form-control editable-input" value="{{ old('age', $linkedAdminProfile->age) }}" disabled>
                </div>
                <div>
                    <label class="input-label">Civil Status</label>
                    <input type="text" name="civil_status" class="form-control editable-input" value="{{ old('civil_status', $linkedAdminProfile->civil_status) }}" disabled>
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label class="input-label">Address</label>
                <textarea name="address" class="form-control editable-input" rows="2" disabled>{{ old('address', $linkedAdminProfile->address) }}</textarea>
            </div>

            <div class="profile-grid-2">
                <div>
                    <label class="input-label">Emergency Contact Person</label>
                    <input type="text" name="emergency_contact_person" class="form-control editable-input" value="{{ old('emergency_contact_person', $linkedAdminProfile->emergency_contact_person) }}" disabled>
                </div>
                <div>
                    <label class="input-label">Emergency Contact Number</label>
                    <input type="text" name="emergency_contact_no" class="form-control editable-input" value="{{ old('emergency_contact_no', $linkedAdminProfile->emergency_contact_no) }}" disabled>
                </div>
            </div>

            <div class="profile-grid-2">
                <div>
                    <label class="input-label">Office</label>
                    <input type="text" name="office" class="form-control editable-input" value="{{ old('office', $linkedAdminProfile->office) }}" disabled>
                </div>
                <div>
                    <label class="input-label">Access Level</label>
                    <input type="text" name="access_level" class="form-control" value="{{ old('access_level', $linkedAdminProfile->access_level) }}" disabled>
                </div>
            </div>
        @endif

        <div id="profileActionBar">
            <button type="button" id="editBtn" onclick="enableEditing()" style="width: 100%; padding: 12px; background: #8B0000; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">
                Edit Profile
            </button>

            <div id="saveAction" style="display: none; gap: 10px;">
                <button type="submit" style="flex: 1; padding: 12px; background: #8B0000; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">
                    Save Changes
                </button>
                <button type="button" onclick="window.location.reload()" style="flex: 1; padding: 12px; background: #cbd5e1; color: #1e293b; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">
                    Cancel
                </button>
            </div>
        </div>
    </form>
</div>
        </div>
    </div>
</div>

<script>
function enableEditing() {
    // 1. I-enable lahat ng editable fields sa profile form
    const form = document.querySelector('form[action="{{ route('student.updateContact') }}"]');
    const inputs = form ? form.querySelectorAll('.editable-input') : [];
    
    inputs.forEach(input => {
        input.disabled = false;
        input.readOnly = false;
        input.style.borderColor = '#8B0000'; 
        input.style.backgroundColor = '#fff';
    });

    // 2. I-toggle ang buttons
    document.getElementById('editBtn').style.display = 'none';
    document.getElementById('saveAction').style.display = 'flex';
}
</script>
@endsection
