@extends('layouts.student')

@section('title', 'My Account')

@push('styles')
<style>
    /* ... (Your existing styles remain unchanged) ... */
    
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
        grid-template-columns: 2fr 1fr; /* Left: Appointments, Right: Settings */
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
        border-left: 5px solid #cbd5e1; /* Default Gray Border */
        transition: transform 0.2s;
    }
    .appt-card:hover { transform: translateY(-3px); }

    /* Status Colors */
    .appt-card.approved { border-left-color: #10b981; } /* Green */
    .appt-card.pending { border-left-color: #f59e0b; }  /* Orange */
    .appt-card.cancelled { border-left-color: #ef4444; } /* Red */
    .appt-card.completed { border-left-color: #3b82f6; } /* Blue */

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
    .input-wrapper { position: relative; margin-bottom: 16px; }
    
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

    .btn-save {
        width: 100%;
        background: #334155;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-save:hover { background: #1e293b; }

    /* --- NOTIFICATIONS --- */
    .alert-success { background: #dcfce7; color: #15803d; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 600; border: 1px solid #bbf7d0; }
    .notif-item { display: flex; gap: 10px; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
    .notif-item:last-child { border-bottom: none; }
    .notif-icon { font-size: 16px; }
    .notif-text { font-size: 13px; color: #334155; line-height: 1.4; }
    .notif-time { display: block; font-size: 11px; color: #94a3b8; margin-top: 4px; }

    @media (max-width: 900px) {
        .account-layout { grid-template-columns: 1fr; }
        .hero-stats { gap: 20px; flex-wrap: wrap; }
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
        <div class="hero-avatar">JC</div>
        <div class="hero-info">
            <h1 class="hero-name">Juan Dela Cruz <span class="hero-badge">Active</span></h1>
            <div class="hero-course">2025-00001-TG-0 • BSIT / 2nd Year</div>
            
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-val">{{ $pendingCount ?? 0 }}</span>
                    <span class="stat-label">Pending</span>
                </div>

                <div class="stat-item">
                    <span class="stat-val">{{ $upcomingCount ?? 0 }}</span>
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
        
        <div>
            <div class="section-title">
                My Appointment History
            </div>

            @forelse($appointments as $appt)
                <div class="appt-card {{ strtolower($appt->status) }}">
                    <div class="appt-header">
                        <div class="appt-service">{{ $appt->service }}</div>
                        <div class="appt-date">
                            {{ \Carbon\Carbon::parse($appt->date)->format('M d, Y') }}<br>
                            <small>{{ \Carbon\Carbon::parse($appt->time)->format('g:i A') }}</small>
                        </div>
                    </div>
                    
                    @if($appt->notes)
                        <div class="appt-notes">
                            "{{ $appt->notes }}"
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

        <div>
            
            <div class="widget-card">
                <div class="section-title" style="font-size: 16px; margin-bottom: 15px;">
                    Notifications
                </div>
                
                @forelse($notifications as $notif)
                    <div class="notif-item">
                        <div class="notif-icon">{{ $notif['icon'] }}</div>
                        <div style="flex:1;">
                            <div class="notif-text">{{ $notif['message'] }}</div>
                            <span class="notif-time">{{ $notif['time'] }}</span>
                        </div>
                    </div>
                @empty
                    <div style="font-size: 14px; color: #64748b; text-align: center; padding: 20px 0;">
                        No new notifications.
                    </div>
                @endforelse
            </div>

            <div class="widget-card">
                <div class="section-title" style="font-size: 16px; margin-bottom: 15px;">
                    Profile Details
                </div>
                
                <form action="{{ url('/student/update-contact') }}" method="POST">
                    @csrf
                    
                    <label class="input-label">Full Name</label>
                    <div class="input-wrapper">
                        <input type="text" class="form-control" value="Juan Dela Cruz" disabled>
                    </div>

                    <label class="input-label">Student ID</label>
                    <div class="input-wrapper">
                        <input type="text" class="form-control" value="2025-00001-TG-0" disabled>
                    </div>

                    <label class="input-label">Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" class="form-control" value="juan@iskolar.pup.edu.ph" disabled>
                    </div>

                    <label class="input-label">Contact Number (Editable)</label>
                    <div class="input-wrapper">
                        <input type="text" name="contact_number" class="form-control" value="09123456789" required>
                    </div>

                    <button type="submit" class="btn-save">Update Contact Info</button>
                </form>
            </div>

        </div>

    </div>
</div>
@endsection