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
        overflow: hidden;
    }

    .hero-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
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
        max-width: 960px;
        margin: 0 auto;
    }

    .page-intro {
        margin-bottom: 18px;
    }

    .page-intro-title {
        margin: 0;
        font-size: 28px;
        color: #600000;
        font-weight: 800;
    }

    .page-intro-text {
        margin: 6px 0 0;
        font-size: 14px;
        color: #6b7b7d;
        line-height: 1.5;
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
    .health-submit-overlay {
        position: fixed;
        inset: 0;
        z-index: 1200;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 42, 0.62);
        backdrop-filter: blur(10px);
        animation: overlayFadeIn 0.28s ease;
    }
    .health-submit-overlay.is-hiding {
        animation: overlayFadeOut 0.3s ease forwards;
    }
    .health-submit-overlay-card {
        text-align: center;
        padding: 30px 24px;
        max-width: 420px;
    }
    .health-submit-title {
        font-size: 28px;
        font-weight: 800;
        color: #ffffff;
        margin: 0 0 22px;
        letter-spacing: -0.02em;
    }
    .health-submit-circle {
        width: 132px;
        height: 132px;
        margin: 0 auto 18px;
        border-radius: 999px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        border: 3px solid rgba(255, 255, 255, 0.92);
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.16);
        transform: scale(0.84);
        animation: submitCircleResolve 0.9s ease forwards;
        animation-delay: 0.92s;
    }
    .health-submit-ring {
        position: absolute;
        inset: -9px;
        width: calc(100% + 18px);
        height: calc(100% + 18px);
        transform: rotate(-90deg);
    }
    .health-submit-ring circle {
        fill: none;
        stroke: #16a34a;
        stroke-width: 7;
        stroke-linecap: round;
        stroke-dasharray: 358;
        stroke-dashoffset: 358;
        animation: submitRingDraw 0.9s ease forwards;
    }
    .health-submit-check {
        position: absolute;
        width: 54px;
        height: 54px;
        opacity: 0;
        transform: scale(0.6);
        animation: submitCheckReveal 0.34s ease forwards;
        animation-delay: 1.18s;
    }
    .health-submit-subtext {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.84);
        margin: 0;
    }
    @keyframes overlayFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes overlayFadeOut {
        from { opacity: 1; }
        to { opacity: 0; visibility: hidden; }
    }
    @keyframes submitRingDraw {
        0% {
            stroke-dashoffset: 358;
        }
        100% {
            stroke-dashoffset: 0;
        }
    }
    @keyframes submitCircleResolve {
        0% {
            transform: scale(0.84);
            background: #ffffff;
            border-color: rgba(255, 255, 255, 0.92);
        }
        55% {
            transform: scale(1.04);
            background: #22c55e;
            border-color: #22c55e;
        }
        100% {
            transform: scale(1);
            background: #16a34a;
            border-color: #16a34a;
        }
    }
    @keyframes submitCheckReveal {
        0% {
            opacity: 0;
            transform: scale(0.6);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }
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
    .health-status-sync {
        margin-top: 10px;
        padding: 10px 12px;
        border-radius: 10px;
        font-size: 12px;
        line-height: 1.45;
        text-align: left;
    }
    .health-status-sync.syncing {
        background: #eff6ff;
        color: #1d4ed8;
    }
    .health-status-sync.synced {
        background: #ecfdf5;
        color: #166534;
    }
    .health-status-sync.failed,
    .health-status-sync.missing {
        background: #fef2f2;
        color: #991b1b;
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
@php
    $linkedAccessLevel = strtolower(trim((string) optional($linkedAdminProfile)->access_level));
    $linkedRoleLabel = in_array($linkedAccessLevel, ['clinic_staff', 'designee', 'superadmin', 'super_admin'], true)
        ? (str_contains($linkedAccessLevel, 'faculty') ? 'Faculty' : 'Admin')
        : null;
    $accountProfileData = $accountProfileData ?? [];
    $accountView = in_array(($accountView ?? 'profile'), ['profile', 'health-record', 'notifications'], true) ? $accountView : 'profile';
    $showOfficeField = in_array($linkedAccessLevel, ['clinic_staff', 'designee', 'superadmin', 'super_admin', 'faculty'], true) || str_contains($linkedAccessLevel, 'faculty');
    $displayStudentNumber = trim((string) ($accountProfileData['student_number'] ?? $user->student_number ?? ''));
    $displayCourse = trim((string) ($accountProfileData['course_college'] ?? $user->course ?? ''));
@endphp
<div class="container" style="padding: 0 20px 40px;">

    @if(session('health_profile_submitted'))
        <div class="health-submit-overlay" id="healthSubmitOverlay">
            <div class="health-submit-overlay-card">
                <h2 class="health-submit-title">Health Form Submitted</h2>
                <div class="health-submit-circle" aria-hidden="true">
                    <svg class="health-submit-ring" viewBox="0 0 120 120" aria-hidden="true">
                        <circle cx="60" cy="60" r="54"></circle>
                    </svg>
                    <svg class="health-submit-check" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 6L9 17l-5-5"></path>
                    </svg>
                </div>
                <p class="health-submit-subtext">Your record has been received and added to your account.</p>
            </div>
        </div>
    @endif

    @if(session('success') && !session('health_profile_submitted'))
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
            @php
                $healthProfile = \App\Models\HealthProfile::where('user_id', $user->id)->first();
            @endphp
            @if(!empty($healthProfile?->student_photo))
                <img src="{{ asset('storage/' . $healthProfile->student_photo) }}" alt="Student 2x2 Picture">
            @else
                {{ strtoupper(substr($user->name, 0, 1)) }}
            @endif
        </div>
        <div class="hero-info">
            <h1 class="hero-name">{{ $user->name }} <span class="hero-badge">Active</span></h1>
            <div class="hero-course" @if($linkedRoleLabel) style="display: none;" @endif>
                {{ $user->student_number }} • {{ $user->course ?? 'BS Information Technology' }}
            </div>
            @if($linkedRoleLabel)
                <div class="hero-course">
                    {{ $user->student_number }} - {{ $linkedRoleLabel }}
                </div>
            @endif

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
        @if(session('health_profile_submitted'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('healthSubmitOverlay');
    if (!overlay) {
        return;
    }

    window.setTimeout(() => {
        overlay.classList.add('is-hiding');
        window.setTimeout(() => overlay.remove(), 320);
    }, 3500);
});
</script>
@endif

@if($accountView === 'profile')
{{-- Full Profile Widget --}}
            <div class="page-intro">
    <h1 class="page-intro-title">Profile Information</h1>
    <p class="page-intro-text">Review your personal account details and keep your clinic information up to date.</p>
</div>
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
                    <input type="text" class="form-control" value="{{ $accountProfileData['course_college'] ?? $user->course }}" readonly style="background-color: #f8fafc;">
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
                    <input type="text" class="form-control" value="{{ $accountProfileData['sex'] ?? $user->gender }}" readonly style="background-color: #f8fafc;">
                </div>
                <div>
                    <label class="input-label">Birthday (DOB)</label>
                    <input type="text" class="form-control" value="{{ $accountProfileData['birthday'] ?? $user->DOB }}" readonly style="background-color: #f8fafc;">
                </div>
            </div>
        @endif

        @if(empty($linkedAdminProfile))
            <div class="profile-grid-2">
                <div>
                    <label class="input-label">Height (cm)</label>
                    <input type="text" name="height" class="form-control editable-input" value="{{ old('height', $accountProfileData['height'] ?? $user->height) }}" disabled>
                </div>
                <div>
                    <label class="input-label">Weight (kg)</label>
                    <input type="text" name="weight" class="form-control editable-input" value="{{ old('weight', $accountProfileData['weight'] ?? $user->weight) }}" disabled>
                </div>
            </div>
        @endif

        @if(empty($linkedAdminProfile))
            <div style="margin-bottom: 15px;">
                <label class="input-label">Contact Number</label>
                <input type="text" name="contact_no" class="form-control editable-input" value="{{ old('contact_no', $user->contact_no) }}" disabled>
            </div>
            <div style="margin-bottom: 15px;">
                <label class="input-label">Address</label>
                <textarea class="form-control" rows="2" disabled>{{ old('home_address', $accountProfileData['home_address'] ?? '') }}</textarea>
            </div>
            <div class="profile-grid-2">
                <div>
                    <label class="input-label">Emergency Contact Person</label>
                    <input type="text" class="form-control" value="{{ old('guardian_name', $accountProfileData['guardian_name'] ?? '') }}" disabled>
                </div>
                <div>
                    <label class="input-label">Emergency Contact Number</label>
                    <input type="text" class="form-control" value="{{ old('emergency_contact_no', $accountProfileData['cellphone'] ?? '') }}" disabled>
                </div>
            </div>
        @endif

        @if(!empty($linkedAdminProfile))
            <div class="profile-grid-2">
                <div>
                    <label class="input-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $accountProfileData['first_name'] ?? $linkedAdminProfile->first_name) }}" disabled>
                </div>
                <div>
                    <label class="input-label">Middle Name</label>
                    <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $accountProfileData['middle_name'] ?? $linkedAdminProfile->middle_name) }}" disabled>
                </div>
            </div>

            <div class="profile-grid-2">
                <div>
                    <label class="input-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $accountProfileData['last_name'] ?? $linkedAdminProfile->last_name) }}" disabled>
                </div>
                <div>
                    <label class="input-label">Suffix Name</label>
                    <input type="text" name="suffix_name" class="form-control" value="{{ old('suffix_name', $accountProfileData['suffix_name'] ?? $linkedAdminProfile->suffix_name) }}" disabled>
                </div>
            </div>

            <div class="profile-grid-2">
                <div>
                    <label class="input-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $accountProfileData['email'] ?? $linkedAdminProfile->email) }}" disabled>
                </div>
                <div>
                    <label class="input-label">Contact Number</label>
                    <input type="text" name="contact_no" class="form-control editable-input" value="{{ old('contact_no', $accountProfileData['contact_number'] ?? $user->contact_no) }}" disabled>
                </div>
            </div>

            <div class="profile-grid-2">
                <div>
                    <label class="input-label">Birthday</label>
                    <input type="date" name="birthday" class="form-control" value="{{ old('birthday', $accountProfileData['birthday'] ?? $linkedAdminProfile->birthday) }}" disabled>
                </div>
                <div>
                    <label class="input-label">Gender</label>
                    <input type="text" name="gender" class="form-control" value="{{ old('gender', $accountProfileData['sex'] ?? $linkedAdminProfile->gender) }}" disabled>
                </div>
            </div>

            <div class="profile-grid-2">
                <div>
                    <label class="input-label">Age</label>
                    <input type="number" name="age" class="form-control" value="{{ old('age', $accountProfileData['age'] ?? $linkedAdminProfile->age) }}" disabled>
                </div>
                <div>
                    <label class="input-label">Civil Status</label>
                    <input type="text" name="civil_status" class="form-control editable-input" value="{{ old('civil_status', $accountProfileData['civil_status'] ?? $linkedAdminProfile->civil_status) }}" disabled>
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label class="input-label">Address</label>
                <textarea name="address" class="form-control editable-input" rows="2" disabled>{{ old('address', $accountProfileData['home_address'] ?? $linkedAdminProfile->address) }}</textarea>
            </div>

            <div class="profile-grid-2">
                <div>
                    <label class="input-label">Emergency Contact Person</label>
                    <input type="text" name="emergency_contact_person" class="form-control editable-input" value="{{ old('emergency_contact_person', $accountProfileData['guardian_name'] ?? $linkedAdminProfile->emergency_contact_person) }}" disabled>
                </div>
                <div>
                    <label class="input-label">Emergency Contact Number</label>
                    <input type="text" name="emergency_contact_no" class="form-control editable-input" value="{{ old('emergency_contact_no', $accountProfileData['cellphone'] ?? $linkedAdminProfile->emergency_contact_no) }}" disabled>
                </div>
            </div>

            <div class="profile-grid-2">
                @if($showOfficeField)
                <div>
                    <label class="input-label">Office</label>
                    <input type="text" name="office" class="form-control editable-input" value="{{ old('office', $accountProfileData['office'] ?? $linkedAdminProfile->office) }}" disabled>
                </div>
                @endif
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
@elseif($accountView === 'health-record')
    @php
        $healthFormSubmitted = $hasSubmittedHealthProfile ?? ($user->healthProfile !== null);
        $status = $user->healthProfile->clearance_status ?? 'Pending';
        $puptasSyncStatus = optional($user->healthProfile)->puptas_sync_status;
        $puptasSyncMessage = trim((string) optional($user->healthProfile)->puptas_sync_message);
        $puptasSyncedAt = optional(optional($user->healthProfile)->puptas_synced_at)->format('M d, Y g:i A');
    @endphp
    <div class="page-intro">
        <h1 class="page-intro-title">Health Record</h1>
        <p class="page-intro-text">Check the status of your submitted health form, review clinic approval, and open your printable record.</p>
    </div>
    <div class="health-status-card">
        <span class="health-status-title">Health Information Record</span>

        @if($healthFormSubmitted)
            @if($status === 'Issued')
                <div class="health-status-summary">
                    <span class="health-status-state issued">Approved</span>
                    <p class="health-status-message">Your health profile is now approved and ready for printing.</p>
                </div>

                @if($puptasSyncStatus === 'synced')
                    <div class="health-status-sync synced">
                        <strong>PUPTAS sync complete.</strong>
                        @if($puptasSyncedAt)
                            Synced on {{ $puptasSyncedAt }}.
                        @endif
                    </div>
                @elseif($puptasSyncStatus === 'syncing')
                    <div class="health-status-sync syncing">
                        <strong>PUPTAS sync in progress.</strong>
                        {{ $puptasSyncMessage !== '' ? $puptasSyncMessage : 'Your approved clearance is being prepared for PUPTAS sync.' }}
                    </div>
                @elseif($puptasSyncStatus === 'failed')
                    <div class="health-status-sync failed">
                        <strong>PUPTAS sync failed.</strong>
                        {{ $puptasSyncMessage !== '' ? $puptasSyncMessage : 'The approved clearance has not been accepted by PUPTAS yet.' }}
                    </div>
                @elseif($puptasSyncStatus === 'missing_student_number')
                    <div class="health-status-sync missing">
                        <strong>PUPTAS sync is waiting for a valid student number.</strong>
                        {{ $puptasSyncMessage !== '' ? $puptasSyncMessage : 'The clinic approval is complete, but the admission sync cannot finish until the school student number is resolved.' }}
                    </div>
                @endif

                <div class="health-status-actions">
                    <a href="{{ route('print.health.form') }}" class="btn-print-form approved">Print Approved Form</a>
                    <a href="{{ route('print.health.form') }}" class="health-status-link">View Record Details</a>
                </div>
                <span class="health-status-note">Valid for Academic Year 2025-2026</span>
            @else
                <div class="health-status-summary">
                    <span class="health-status-state pending">Pending Review</span>
                    <p class="health-status-message">Your profile has been submitted and is currently <strong>awaiting medical review</strong>.</p>
                </div>

                <div class="health-status-actions">
                    <a href="{{ route('print.health.form') }}" class="btn-print-form pending">View Submitted Form</a>
                    <button class="btn-print-form disabled" disabled>Printing Disabled (Pending)</button>
                </div>
                <span class="health-status-note">Physician signature is required to print.</span>
            @endif
        @else
            <div class="health-status-summary">
                <span class="health-status-state incomplete">Not Completed</span>
                <p class="health-status-message">You haven't completed your health profile yet.</p>
            </div>
            <a href="{{ route('health.form') }}" class="btn-print-form incomplete">Complete Form Now</a>
            <span class="health-status-note">Required for clinic consultations.</span>
        @endif
    </div>
@else
    <div class="page-intro">
        <h1 class="page-intro-title">Notifications</h1>
        <p class="page-intro-text">Stay updated with appointment changes, health record progress, and important clinic activity.</p>
    </div>
    <div class="widget-card">
        <div class="section-title" style="font-size: 16px; margin-bottom: 15px;"> Notifications</div>
        <div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
            @if(collect($notifications ?? [])->isNotEmpty())
                <form action="{{ route('student.notifications.read_all') }}" method="POST">
                    @csrf
                    <button type="submit" style="padding:10px 14px; background:#8B0000; color:#fff; border:none; border-radius:8px; font-weight:700; cursor:pointer;">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        @forelse(collect($notifications ?? []) as $notif)
            <a href="{{ route('student.notifications.open', ['notificationId' => $notif['id']]) }}"
               style="display:flex; gap:12px; align-items:flex-start; padding:14px 16px; margin-bottom:12px; border:1px solid {{ !empty($notif['is_unread']) ? '#f5d0d0' : '#e2e8f0' }}; background:{{ !empty($notif['is_unread']) ? '#fff7f7' : '#ffffff' }}; border-radius:12px; text-decoration:none;">
                @if(!empty($notif['is_unread']))
                    <span style="width:10px; height:10px; margin-top:6px; border-radius:999px; background:#8B0000; flex:0 0 auto;"></span>
                @else
                    <span style="width:10px; height:10px; margin-top:6px; border-radius:999px; background:#cbd5e1; flex:0 0 auto;"></span>
                @endif
                <span style="flex:1; min-width:0;">
                    <span style="display:block; font-size:14px; line-height:1.5; color:#1f2937; font-weight:{{ !empty($notif['is_unread']) ? '800' : '600' }};">
                        {{ $notif['message'] ?? 'Notification available.' }}
                    </span>
                    <span style="display:block; margin-top:5px; font-size:12px; color:#64748b;">
                        {{ $notif['time'] ?? 'Just now' }}
                    </span>
                </span>
            </a>
        @empty
            <div style="padding:18px; border:1px dashed #cbd5e1; border-radius:12px; color:#64748b; text-align:center;">
                No notifications available right now.
            </div>
        @endforelse
    </div>
@endif
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


