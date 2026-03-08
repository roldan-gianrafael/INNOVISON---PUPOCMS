@extends('layouts.admin')

@section('title', 'Student Health Records')

@push('styles')
<style>
    /* Table & Card Styling */
    .card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
        height: 100%; /* Para pantay ang taas nila */
    }
    
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    
    th {
        text-align: left;
        font-size: 12px;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        padding: 12px 16px;
        border-bottom: 2px solid #f1f5f9;
    }
    
    td {
        padding: 16px;
        border-bottom: 1px solid #f8fafc;
        font-size: 14px;
        color: #334155;
        vertical-align: middle;
    }

    /* Status Badges */
    .status { padding: 5px 12px; border-radius: 99px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .status.pending { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
    .status.issued { background: #dcfce7; color: #15803d; }
    .status.review { background: #fee2e2; color: #b91c1c; }
    .status.submitted { background: #e0f2fe; color: #0369a1; }

    /* Buttons */
    .btn-action {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-view { background: #fff3f5; color: #70131B; border: 1px solid #f0d7dc; }
    .btn-view:hover { background: #fae9ed; }
    
   .btn-sign { 
        background: #0804ff; 
        color: #ffffff; 
        box-shadow: 0 2px 6px rgba(255, 255, 255, 0.2); 
    }
    
    .btn-sign:hover { 
        background: #ffffff; 
        color: white; 
    }

    /* Custom Flex Grid para sa Summary Cards */
    .summary-container {
        display: flex;
        gap: 20px;
        width: 100%;
        margin-bottom: 25px;
    }
    .summary-item {
        flex: 1; /* Hahatiin ang space sa dalawa (50/50) */
    }
</style>
@endpush

@section('content')
    @php
        $role = strtolower((string) (optional(auth()->user())->user_role ?? ''));
        $basePrefix = $role === 'student_assistant' ? '/assistant' : '/admin';
    @endphp

    {{-- Header with Search --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin:0; color:#ffffff;">Student Health Records</h2>
        <input type="text" id="recordSearch" placeholder="Search by student name or ID..." 
               style="padding: 10px 16px; border-radius: 8px; border: 1px solid #cbd5e1; width: 350px;">
    </div>

    {{-- Summary Cards - Hardcoded Side by Side --}}
    <div class="summary-container">
        <div class="summary-item">
            <div class="card p-3" style="padding: 15px 24px !important; border-left: 5px solid #70131B;">
                <small class="text-muted fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Total Submissions</small>
                <h3 class="fw-bold mb-0" style="color: #70131B;">{{ $records->count() }}</h3>
            </div>
        </div>
        <div class="summary-item">
            <div class="card p-3" style="padding: 15px 24px !important; border-left: 5px solid #dc3545;">
                <small class="text-muted fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">With Medical Conditions</small>
                <h3 class="fw-bold mb-0 text-danger">{{ $records->where('has_illness', 'Yes')->count() }}</h3>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="card">
        <div class="fw-bold mb-2 text-muted" style="font-size: 13px;">Health Profile Summary</div>
        <table id="healthTable">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Course / Yr / Sec</th>
                    <th>Medical Status</th>
                    <th>Submitted At</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                    <tr>
                        <td class="fw-bold">{{ $record->user->student_id }}</td>
                        <td>
                            <div class="student-name" style="font-weight: 700;">{{ $record->user->name }}</div>
                        </td>
                        <td>{{ $record->user->course }} {{ $record->year }}-{{ $record->section }}</td>
                        <td>
                            @if($record->clearance_status == 'Pending')
                                <span class="status pending">Pending</span>
                            @elseif($record->clearance_status == 'Issued')
                                <span class="status issued">Issued</span>
                            @elseif($record->has_illness == 'Yes')
                                <span class="status review">For Review</span>
                            @else
                                <span class="status submitted">Submitted</span>
                            @endif
                        </td>
                        <td style="color: #94a3b8; font-size: 12px;">
                            {{ $record->created_at->format('M d, Y') }}
                        </td>
                        <td style="text-align: center;">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.show_health', $record->id) }}" class="btn-action btn-view">
                                    View
                                </a>
                                <a href="{{ route('admin.sign_page', $record->id) }}" class="btn-action btn-sign">
                                    Sign
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">No health records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('recordSearch').addEventListener('keyup', function() {
        let filter = this.value.toUpperCase();
        let tr = document.getElementById("healthTable").getElementsByTagName("tr");
        for (let i = 1; i < tr.length; i++) {
            let rowText = tr[i].textContent || tr[i].innerText;
            tr[i].style.display = rowText.toUpperCase().indexOf(filter) > -1 ? "" : "none";
        }
    });
</script>
@endpush