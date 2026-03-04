@extends('layouts.admin')

@section('title', 'Clinical Consultation')

@push('styles')
<style>
    /* Inheriting your Inventory/Settings CSS */
    .card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; margin-bottom: 24px; }
    .card h3 { margin-top: 0; color: #8B0000; margin-bottom: 20px; font-size: 18px; }
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; margin-bottom: 6px; font-size: 13px; font-weight: 700; color: #64748b; }
    .form-control { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; color: #334155; }
    .btn-save { background: #8B0000; color: white; padding: 12px 24px; border-radius: 8px; border: none; font-weight: 700; cursor: pointer; transition: 0.2s; width: 100%; }
    .btn-save:hover { background: #600000; }

    .patient-header { display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #8B0000; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .badge-role { background: #e2e8f0; color: #334155; padding: 4px 10px; border-radius: 6px; font-size: 11px; text-transform: uppercase; font-weight: 700; }
    
    /* Highlight for MAR requirement */
    .mar-required { border: 1px solid #fecaca; background-color: #fef2f2; }

    .badge-source {
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    margin-left: 8px;
}
.source-online { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
.source-walkin { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }

.appt-date-info {
    font-size: 13px;
    color: #1e293b;
    background: #f1f5f9;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: 600;
}

</style>
@endpush

@section('content')
@php
    $role = strtolower((string) (optional(auth()->user())->user_role ?? ''));
    $basePrefix = $role === 'student_assistant' ? '/assistant' : '/admin';
@endphp

<div class="patient-header card">
    <div>
        <div style="display: flex; align-items: center; margin-bottom: 5px;">
            <h3 style="margin: 0;">{{ $student->first_name }} {{ $student->last_name }}</h3>
        
        @if($user_source == 'online' && $latestAppointment)
            <span class="badge-source source-online">Online Appointment Found</span>
        @else
            <span class="badge-source source-walkin">Walk-in Patient</span>
        @endif
        </div>
        
        <span class="badge-role">{{ $student->user_role }}</span>
        <span style="font-size: 13px; color: #64748b; margin-left: 10px;">ID: {{ $student->student_id }}</span>
        
        @if(($user_source ?? '') == 'online' && isset($latestAppointment))
            <div style="margin-top: 10px;">
                <span style="font-size: 12px; color: #64748b;">Scheduled for: </span>
                <span class="appt-date-info">
                    📅 {{ \Carbon\Carbon::parse($latestAppointment->date)->format('M d, Y') }} 
                    at {{ \Carbon\Carbon::parse($latestAppointment->time)->format('g:i A') }}
                </span>
            </div>
        @endif
    </div>

    <div style="text-align: right;">
        <span style="display: block; font-size: 12px; color: #94a3b8;">Today's Consultation</span>
        <span style="font-weight: 600; color: #334155;">{{ now()->format('F d, Y') }}</span>
    </div>
</div>

<form action="{{ url($basePrefix . '/walkin/store') }}" method="POST">
    @csrf
    <input type="hidden" name="student_id" value="{{ $student->student_id }}">
    <input type="hidden" name="user_role" value="{{ $student->user_role }}">
    <input type="hidden" name="user_type" value="{{ $user_source ?? 'walkin' }}">

    <div class="grid-2">
    <div>
        <div class="card">
            <h3>Physical Assessment</h3>
            
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" class="form-control">
            </div>

            <div class="grid-2" style="margin-top: 20px;">
                <div class="form-group">
                    <label>Height (ft)</label>
                    <input type="text" name="height" class="form-control" placeholder="5'4">
                </div>
                <div class="form-group">
                    <label>Weight (lbs)</label>
                    <input type="number" name="weight" class="form-control" placeholder="120">
                </div>
            </div>

                <div class="grid-2" style="margin-top: 20px;">
                    <div class="form-group">
                        <label>Temperature (&deg;C)</label>
                        <input type="number" step="0.1" name="temp" class="form-control" placeholder="36.5">
                    </div>
                    <div class="form-group">
                        <label>Blood Pressure</label>
                        <input type="text" name="bp" class="form-control" placeholder="120/80">
                    </div>
                </div>
            </div>
        </div>

<div class="grid-2" style="margin-top: 10px;"> <div class="form-group">
        <label>Pulse Rate (bpm)</label>
        <input type="number" name="pr" class="form-control" placeholder="72">
    </div>
    <div class="form-group">
        <label>Respiratory Rate (cpm)</label>
        <input type="number" name="rr" class="form-control" placeholder="18">
    </div>
</div>

            <div class="form-group">
                <label>Covid Positive?</label>
                <div style="display: flex; gap: 10px;">
                    <button type="button" style="flex: 1; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: white; cursor: pointer;">Yes</button>
                    <button type="button" style="flex: 1; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: white; cursor: pointer;">No</button>
                </div>
            </div>
            
        </div>
    </div>

    <div>
        <div class="card">
    <h3>Visit Details</h3>
    <div class="form-group">
    <label>{{ $user_source == 'online' ? 'Appointment Remarks' : 'Student Reason' }}</label>
    <input type="text" 
        name="student_reason" class="form-control" {{ $user_source == 'online' ? 'readonly' : '' }} 
        value="{{ $latestAppointment->remarks ?? '' }}"> {{-- Kung null, blanko lang --}}
    </div>
            <div class="form-group">
                <label>Purpose of Visit / Service</label>
                
                <select class="form-control" {{ ($user_source ?? '') == 'online' ? 'disabled' : '' }} required>
                    <option value="" disabled {{ !$latestAppointment ? 'selected' : '' }}>-- Select Service --</option>
                    
                    <option value="General Consultation" 
                        {{ (isset($latestAppointment) && $latestAppointment->service == 'General Consultation') ? 'selected' : '' }}>
                        General Consultation
                    </option>
                    
                    <option value="BP Monitoring" 
                        {{ (isset($latestAppointment) && $latestAppointment->service == 'BP Monitoring') ? 'selected' : '' }}>
                        BP Monitoring
                    </option>
                </select>

                @if(($user_source ?? '') == 'online')
                    <input type="hidden" name="service" value="{{ $latestAppointment->service }}">
                    <small style="color: #0d6efd; font-size: 11px;"><i>Service is locked based on online appointment request.</i></small>
                @else
                    <script>
                        document.currentScript.parentElement.querySelector('select').setAttribute('name', 'service');
                    </script>
                @endif
            </div>

            <div class="form-group">
                <label style="color: #8B0000;">Medical Condition (MAR Classification)</label>
                <select name="condition_id" class="form-control mar-required" required>
                    <option value="" disabled selected>-- Select Diagnosis --</option>
                    @foreach($conditions as $cond)
                        <option value="{{ $cond->id }}">
                            Category {{ $cond->category->code }}: {{ $cond->name }}
                        </option>
                    @endforeach
                </select>
                <small style="color: #94a3b8; font-size: 11px;">Required for MAR Report.</small>
            </div>
        </div>

        <div class="card">
            <h3>Medicine Dispensing</h3>
            <div class="form-group">
                <label>Select Medicine (Inventory)</label>
                <select name="item_id" class="form-control">
                    <option value="">-- No Medicine Issued --</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ $item->quantity <= 0 ? 'disabled' : '' }}>
                            {{ $item->name }} (Available: {{ $item->quantity }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Quantity to Issue</label>
                <input type="number" name="issued_quantity" class="form-control" min="0" placeholder="Enter amount">
            </div>
        </div>
    </div>
</div>

    <div class="card">
        <h3>Clinical Findings</h3>
        <div class="form-group">
            <label>Remarks / Assessment</label>
            <textarea name="remarks" class="form-control" rows="5" required placeholder="Describe symptoms or medical assessment..."></textarea>
        </div>
        
        <div style="display: flex; gap: 15px; margin-top: 10px;">
            <button type="submit" class="btn-save">Save & Finalize Consultation</button>
            <a href="{{ url($basePrefix . '/walkin') }}" style="text-decoration: none; padding: 12px; color: #64748b; font-weight: 600; font-size: 14px;">Cancel</a>
        </div>
    </div>
</form>

@endsection
