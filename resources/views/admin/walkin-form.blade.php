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
</style>
@endpush

@section('content')

<div class="patient-header card">
    <div>
        <h3 style="margin-bottom: 5px;">{{ $student->first_name }} {{ $student->last_name }}</h3>
        <span class="badge-role">{{ $student->role }}</span>
        <span style="font-size: 13px; color: #64748b; margin-left: 10px;">ID: {{ $student->student_id }}</span>
    </div>
    <div style="text-align: right;">
        <span style="display: block; font-size: 12px; color: #94a3b8;">Consultation Date</span>
        <span style="font-weight: 600; color: #334155;">{{ now()->format('F d, Y') }}</span>
    </div>
</div>

<form action="{{ route('walkin.store') }}" method="POST">
    @csrf
    <input type="hidden" name="student_id" value="{{ $student->student_id }}">
    <input type="hidden" name="user_type" value="{{ $student->role }}">

    <div class="grid-2">
        <div>
            <div class="card">
                <h3>Visit Details</h3>
                <div class="form-group">
                    <label>Purpose of Visit / Service</label>
                    <select name="service" class="form-control" required>
                        <option value="" disabled selected>-- Select Service --</option>
                        <option value="General Consultation">General Consultation</option>
                        <option value="Medical Certificate">Medical Certificate</option>
                        <option value="BP Monitoring">BP Monitoring</option>
                    </select>
                </div>

                <div class="grid-2" style="margin-top: 20px;">
                    <div class="form-group">
                        <label>Temperature (°C)</label>
                        <input type="number" step="0.1" name="temp" class="form-control" placeholder="36.5">
                    </div>
                    <div class="form-group">
                        <label>Blood Pressure</label>
                        <input type="text" name="bp" class="form-control" placeholder="120/80">
                    </div>
                </div>
            </div>
        </div>

        <div>
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
                    <input type="number" name="issued_quantity" class="form-control" min="1" placeholder="Enter amount">
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
            <a href="{{ route('walkin.index') }}" style="text-decoration: none; padding: 12px; color: #64748b; font-weight: 600; font-size: 14px;">Cancel</a>
        </div>
    </div>
</form>

@endsection