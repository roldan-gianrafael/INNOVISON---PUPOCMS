@extends('layouts.admin')

@section('title', 'Sign Health Clearance')

@push('styles')
<style>
    .card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; margin-bottom: 24px; }
    .manage-section { background: #fdfdfd; border: 1px dashed #cbd5e1; padding: 30px; border-radius: 10px; max-width: 900px; margin: 0 auto; }
    
    h3 { color: #334155; font-size: 18px; margin-top: 0; font-weight: 700; }

    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0; }
    .info-box { background: #f8fafc; padding: 12px 15px; border-radius: 8px; border: 1px solid #edf2f7; }
    .info-label { font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 3px; }
    .info-value { font-size: 15px; font-weight: 600; color: #1e293b; }

    .medical-alert { background: #fff5f5; border-left: 4px solid #ef4444; padding: 15px; border-radius: 6px; margin-bottom: 25px; }
    .form-control { padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%; font-size: 14px; }
    .form-label { display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 8px; }

    .btn-save { background: #70131B; color: white; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; font-weight: bold; transition: 0.2s; }
    .btn-save:hover { background: #8B0000; transform: translateY(-1px); }
    .btn-cancel { background: #e2e8f0; color: #475569; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-block; }
    
    .status-badge { font-size: 12px; padding: 5px 12px; border-radius: 20px; font-weight: 700; }
</style>
@endpush

@section('content')

<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.health_records') }}" style="text-decoration: none; color: #64748b; font-size: 14px;">&larr; Back to Health Records</a>
</div>

<div class="card manage-section">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h3>Sign Student Health Clearance</h3>
            <p style="font-size: 13px; color: #64748b;">Review medical profile and finalize clearance.</p>
        </div>
        <span class="status-badge {{ $record->has_illness == 'Yes' ? 'bg-danger text-white' : 'bg-success text-white' }}">
            {{ $record->has_illness == 'Yes' ? 'With Condition' : 'No Condition' }}
        </span>
    </div>

    {{-- Student Information Summary --}}
    <div class="info-grid">
        <div class="info-box">
            <div class="info-label">Student Name</div>
            <div class="info-value">{{ $record->user->name }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Student ID</div>
            <div class="info-value">{{ $record->user->student_id }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Course & Section</div>
            <div class="info-value">{{ $record->user->course }} {{ $record->user->year }}-{{ $record->user->section }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Contact Number</div>
            <div class="info-value" style="font-size: 15px; color: #1e293b; font-weight: 700;">
        {{ $record->contact_no ?? ($record->cellphone ?? 'NOT PROVIDED') }}
    </div>
        </div>
    </div>

    @if($record->has_illness == 'Yes')
    <div class="medical-alert" style="background-color: #fef2f2; border-left: 4px solid #b91c1c; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
        <h6 style="color: #b91c1c; font-weight: 800; font-size: 13px; margin-bottom: 5px;">
            <i class="fas fa-exclamation-triangle me-1"></i> DECLARED MEDICAL CONDITION/S:
        </h6>
        <p style="margin: 0; font-size: 14px; color: #334155; font-weight: 600;">
            @php
  
                $history = is_array($record->medical_history) 
                           ? $record->medical_history 
                           : json_decode($record->medical_history ?? '[]', true);
            @endphp

            @if(!empty($history))
                {{ implode(', ', $history) }}
            @endif

            @if($record->other_illness)
                {{ !empty($history) ? ', ' : '' }} {{ $record->other_illness }}
            @endif
        </p>
    </div>
@endif

    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 25px 0;">

    <form action="{{ route('admin.update_clearance', $record->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 20px;">
            <label class="form-label">Remarks / Pending Reason</label>
            <textarea name="pending_reason" class="form-control" rows="3" placeholder="Enter reason or additional instructions here...">{{ $record->pending_reason }}</textarea>
        </div>

        {{-- AUTO-SIGNATURE PREVIEW SECTION --}}
        <div style="margin-bottom: 25px; background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #edf2f7; text-align: center;">
            <label class="form-label" style="color: #70131B; display: block; text-align: left;">Medical Officer Signature Preview</label>
            
            <div style="position: relative; display: inline-block; min-height: 110px; width: 280px; border-bottom: 2px solid #334155; margin-top: 15px;">
                
                @if($record->clearance_status == 'Issued')
                    <div style="position: absolute; bottom: 5px; left: 50%; transform: translateX(-50%); z-index: 10;">
                        <img src="{{ asset('storage/health_profiles/signatures/nurse-sign.png') }}" 
                             alt="Nurse Signature" 
                             style="height: 90px; width: auto; pointer-events: none;">
                    </div>
                @else
                    <div style="padding-top: 40px; color: #000000; font-style: italic; font-size: 12px;">
                        Signature will be applied upon "Issued" status
                    </div>
                @endif

                <div style="position: relative; z-index: 5; font-weight: bold; text-transform: uppercase; margin-top: 55px; color: #1e293b;">
                    MS. NURSE NAME, RN
                </div>
            </div>
            <div style="font-size: 11px; color: #64748b; margin-top: 5px;">Nurse/Doctor/Physician</div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div>
                <label class="form-label">Clearance Action</label>
                <select name="clearance_status" class="form-control" required>
                    <option value="Issued" {{ $record->clearance_status == 'Issued' ? 'selected' : '' }}>Issue Clearance</option>
                    <option value="Pending" {{ $record->clearance_status == 'Pending' ? 'selected' : '' }}>Hold / Pending</option>
                    <option value="Rejected" {{ $record->clearance_status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <label class="form-label">Date Verified</label>
                <input type="date" name="verified_at" class="form-control" value="{{ $record->verified_at ?? date('Y-m-d') }}" readonly>
                <small class="text-muted" style="font-size: 11px;">Current Date</small>
            </div>
        </div>

        <div style="text-align: right; display: flex; justify-content: flex-end; gap: 10px;">
            <a href="{{ route('admin.health_records') }}" class="btn-cancel">Cancel</a>
            <button type="submit" class="btn-save">Update & Save</button>
        </div>
    </form>
</div>

@endsection