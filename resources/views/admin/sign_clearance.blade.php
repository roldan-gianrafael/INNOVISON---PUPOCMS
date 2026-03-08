@extends('layouts.admin')

@section('title', 'Sign Health Clearance')

@push('styles')
<style>
    .clearance-card {
        background: #fff;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        max-width: 900px;
        margin: 0 auto;
    }

    .profile-header {
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 20px;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    .info-item {
        background: #f8fafc;
        padding: 15px;
        border-radius: 10px;
        border: 1px solid #edf2f7;
    }

    .info-label {
        font-size: 11px;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
    }

    .medical-note {
        background: #fff5f5;
        border-left: 5px solid #dc3545;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .form-group label {
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 12px;
    }

    .btn-submit {
        background: #0804ff;
        color: #ffffff;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 700;
        border: none;
        transition: 0.3s;
        box-shadow: 0 4px 12px rgba(8, 4, 255, 0.2);
    }

    .btn-submit:hover {
        background: #0603c7;
        transform: translateY(-2px);
    }

    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: 0.3s;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.health_records') }}" class="text-decoration-none me-3" style="color: #64748b;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h2 class="mb-0 text-white">Issue Health Clearance</h2>
    </div>

    <div class="clearance-card">
        <div class="profile-header">
            <div>
                <h4 class="fw-bold mb-1" style="color: #70131B;">Medical Review</h4>
                <p class="text-muted mb-0">Please review the student's health profile before signing.</p>
            </div>
            <div class="text-end">
                <span class="badge rounded-pill {{ $record->has_illness == 'Yes' ? 'bg-danger' : 'bg-success' }} px-3 py-2">
                    {{ $record->has_illness == 'Yes' ? 'With Medical Condition' : 'No Known Condition' }}
                </span>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Full Name</div>
                <div class="info-value">{{ $record->user->name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Student ID</div>
                <div class="info-value">{{ $record->user->student_id }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Course & Section</div>
                <div class="info-value">{{ $record->user->course }} {{ $record->year }}-{{ $record->section }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Contact Number</div>
                <div class="info-value">{{ $record->contact_number ?? 'N/A' }}</div>
            </div>
        </div>

        @if($record->has_illness == 'Yes')
        <div class="medical-note">
            <h6 class="fw-bold text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Declared Condition/s:</h6>
            <p class="mb-0">{{ $record->illness_details ?? 'No details provided.' }}</p>
        </div>
        @endif

        <form action="{{ route('admin.update_clearance', $record->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="fw-bold mb-2">Physician's Remarks / Notes</label>
                <textarea name="remarks" class="form-control" rows="4" placeholder="Enter health recommendations or remarks..."></textarea>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="fw-bold mb-2">Clearance Status</label>
                    <select name="clearance_status" class="form-select form-control" required>
                        <option value="Issued">Issue Clearance (Cleared)</option>
                        <option value="Pending">Hold (Pending Review)</option>
                        <option value="Rejected">Rejected (Medical Reason)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold mb-2">Effective Until</label>
                    <input type="date" name="expiry_date" class="form-control" value="{{ date('Y-12-31') }}">
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.health_records') }}" class="btn-cancel text-center">Cancel</a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-file-signature me-2"></i> Sign & Issue Clearance
                </button>
            </div>
        </form>
    </div>
</div>
@endsection