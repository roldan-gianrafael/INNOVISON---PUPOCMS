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
    .card,
    .card *:not(.status):not(.btn-action):not(.btn-sign) {
        color: #111827;
    }
    
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    
    th {
        text-align: left;
        font-size: 12px;
        font-weight: 800;
        color: #111827;
        text-transform: uppercase;
        padding: 12px 16px;
        border-bottom: 2px solid #f1f5f9;
    }
    
    td {
        padding: 16px;
        border-bottom: 1px solid #f8fafc;
        font-size: 14px;
        color: #111827;
        vertical-align: middle;
    }

    /* Status Badges */
    .status { padding: 5px 12px; border-radius: 99px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .status.pending { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
    .status.issued { background: #dcfce7; color: #15803d; }
    .status.review { background: #fee2e2; color: #b91c1c; }
    .status.submitted { background: #e0f2fe; color: #0369a1; }

    html[data-theme="dark"] .status.pending,
    html[data-theme="dark"] .status.issued,
    html[data-theme="dark"] .status.review,
    html[data-theme="dark"] .status.submitted {
        color: #ffffff !important;
    }

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

    .health-records-title {
        margin: 0;
        color: #111827;
    }

    .health-records-toolbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        width: 100%;
        margin-bottom: 20px;
    }

    .health-records-toolbar-actions {
        display: flex;
        align-items: flex-start;
        justify-content: flex-end;
        gap: 12px;
        flex-wrap: wrap;
        margin-left: auto;
    }

    .health-filter-shell {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 10px;
    }

    .health-filter-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        min-height: 44px;
        padding: 0 18px;
        border-radius: 999px;
        border: 1px solid #8f2230;
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #ffffff;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20);
        transition: color .08s linear, transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        z-index: 0;
        white-space: nowrap;
        color: #ffffff !important;
    }

    .health-filter-toggle,
    .health-filter-toggle:hover,
    .health-filter-toggle:focus {
        color: #ffffff !important;
    }

    .health-filter-toggle::after {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(120deg,
                rgba(255, 248, 196, 0) 0%,
                rgba(255, 239, 181, 0.14) 22%,
                rgba(255, 239, 181, 0.52) 48%,
                rgba(255, 239, 181, 0.14) 72%,
                rgba(255, 248, 196, 0) 100%);
        transform: translateX(-135%);
        transition: transform 1.5s ease;
        z-index: -1;
    }

    .health-filter-toggle:hover,
    .health-filter-toggle.is-open {
        transform: translateY(-1px);
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16);
    }

    .health-filter-toggle:hover::after,
    .health-filter-toggle.is-open::after {
        transform: translateX(135%);
    }

    .health-filter-form {
        display: none;
        align-items: flex-end;
        justify-content: flex-end;
        gap: 12px;
        flex-wrap: wrap;
        padding: 16px;
        border-radius: 18px;
        background: #ffffff;
        border: 1px solid rgba(127, 29, 45, 0.12);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
    }

    .health-filter-form.is-open {
        display: flex;
    }

    .health-filter-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .health-filter-field label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
    }

    .health-records-search,
    .health-filter-select {
        padding: 10px 16px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        min-width: 180px;
        color: #111827;
        background: #ffffff;
    }

    .health-records-search {
        width: 280px;
    }

    .health-filter-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        min-height: 44px;
        padding: 0 18px;
        border-radius: 999px;
        border: 1px solid #8f2230;
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #ffffff;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20);
        transition: color .08s linear, transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        z-index: 0;
    }

    .health-filter-btn::after {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(120deg,
                rgba(255, 248, 196, 0) 0%,
                rgba(255, 239, 181, 0.14) 22%,
                rgba(255, 239, 181, 0.52) 48%,
                rgba(255, 239, 181, 0.14) 72%,
                rgba(255, 248, 196, 0) 100%);
        transform: translateX(-135%);
        transition: transform 1.5s ease;
        z-index: -1;
    }

    .health-filter-btn:hover {
        transform: translateY(-1px);
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16);
    }

    .health-filter-btn:hover::after {
        transform: translateX(135%);
    }

    .health-filter-btn-reset {
        background: linear-gradient(135deg, #64748b, #475569);
        border-color: #475569;
        box-shadow:
            0 0 0 3px rgba(100, 116, 139, 0.12),
            0 10px 22px rgba(71, 85, 105, 0.20);
    }

    .health-summary-label {
        font-size: 17px;
        letter-spacing: 0.5px;
        display: inline-flex;
        flex-direction: column;
        line-height: 1.15;
    }

    .health-summary-value {
        color: #70131B;
        font-size: 17px;
    }

    .health-summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .health-table-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 12px;
    }

    .health-table-title {
        margin: 0;
        font-size: 18px;
        font-weight: 900;
        color: #70131B;
        letter-spacing: 0.01em;
    }

    .health-highlight-row {
        position: relative;
        background: linear-gradient(180deg, rgba(243, 232, 255, 0.98), rgba(237, 233, 254, 0.98));
        box-shadow: inset 4px 0 0 #7c3aed;
        animation: healthHighlightPulse 2.2s ease-in-out 2;
    }

    .health-highlight-row td {
        background: transparent;
    }

    @keyframes healthHighlightPulse {
        0%, 100% {
            box-shadow: inset 4px 0 0 #7c3aed, 0 0 0 rgba(124, 58, 237, 0);
        }
        50% {
            box-shadow: inset 4px 0 0 #7c3aed, 0 0 0 6px rgba(124, 58, 237, 0.12);
        }
    }

    html[data-theme="dark"] .health-records-title,
    html[data-theme="dark"] .health-table-title,
    html[data-theme="dark"] .health-filter-toggle,
    html[data-theme="dark"] .text-muted.health-summary-label,
    html[data-theme="dark"] .summary-item .health-summary-label span,
    html[data-theme="dark"] .health-filter-field label,
    html[data-theme="dark"] .health-records-search,
    html[data-theme="dark"] .health-records-search::placeholder,
    html[data-theme="dark"] .health-filter-select,
    html[data-theme="dark"] .health-summary-label,
    html[data-theme="dark"] .health-summary-value,
    html[data-theme="dark"] .summary-item h3,
    html[data-theme="dark"] .summary-item .text-danger,
    html[data-theme="dark"] .card .text-muted,
    html[data-theme="dark"] .card th,
    html[data-theme="dark"] .card td,
    html[data-theme="dark"] .card td *,
    html[data-theme="dark"] .student-name {
        color: #ffffff !important;
    }

    html[data-theme="dark"] .health-records-search,
    html[data-theme="dark"] .health-filter-select {
        background: rgba(15, 23, 42, 0.92);
        border-color: rgba(148, 163, 184, 0.24);
    }

    html[data-theme="dark"] .health-filter-form {
        background: rgba(15, 23, 42, 0.96);
        border-color: rgba(148, 163, 184, 0.14);
        box-shadow: 0 18px 32px rgba(0, 0, 0, 0.24);
    }

    html[data-theme="dark"] .summary-item .card {
        background: rgba(15, 23, 42, 0.96);
        border-color: rgba(148, 163, 184, 0.14);
    }

    html[data-theme="dark"] .health-highlight-row {
        background: linear-gradient(180deg, rgba(76, 29, 149, 0.34), rgba(91, 33, 182, 0.28));
        box-shadow: inset 4px 0 0 #a855f7;
    }

    @media (max-width: 980px) {
        .health-records-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .health-records-toolbar-actions,
        .health-filter-shell,
        .health-filter-form {
            justify-content: flex-start;
            margin-left: 0;
            align-items: stretch;
        }
    }
</style>
@endpush

@section('content')
    @php
        $role = \App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '');
        $basePrefix = $role === \App\Models\User::ROLE_ADMIN ? '/assistant' : '/admin';
        $canSignHealth = $role === \App\Models\User::ROLE_SUPERADMIN;
        $highlightHealthId = trim((string) request()->query('highlight_health', ''));
    @endphp

    {{-- Header with Search / Filters --}}
    <div class="health-records-toolbar">
        <h2 class="health-records-title">Student Health Records</h2>
        <div class="health-records-toolbar-actions">
            <input
                type="text"
                id="recordSearch"
                name="q"
                value="{{ $search ?? '' }}"
                class="health-records-search"
                form="healthFilterForm"
                placeholder="Search by student name or ID..."
            >
        </div>
    </div>

    {{-- Summary Cards - Hardcoded Side by Side --}}
    <div class="summary-container">
        <div class="summary-item">
            <div class="card p-3" style="padding: 15px 24px !important; border-left: 5px solid #70131B;">
                <div class="health-summary-row">
                    <small class="text-muted fw-bold text-uppercase health-summary-label"><span>Total</span><span>Submissions</span></small>
                    <h3 class="fw-bold mb-0 health-summary-value">{{ $records->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="summary-item">
            <div class="card p-3" style="padding: 15px 24px !important; border-left: 5px solid #dc3545;">
                <div class="health-summary-row">
                    <small class="text-muted fw-bold text-uppercase health-summary-label"><span>With Medical</span><span>Conditions</span></small>
                    <h3 class="fw-bold mb-0 text-danger">{{ $records->where('has_illness', 'Yes')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
<div class="card">
    <div class="health-table-head">
        <div class="health-table-title">Health Profile Summary</div>
        <div class="health-filter-shell">
            <button type="button" class="health-filter-toggle" id="healthFilterToggle" aria-expanded="false" aria-controls="healthFilterForm">
                Filter Health Forms
            </button>
            <form method="GET" class="health-filter-form" id="healthFilterForm">
                <div class="health-filter-field">
                    <label for="courseFilter">Course</label>
                    <select id="courseFilter" name="course" class="health-filter-select">
                        <option value="">All Courses</option>
                        @foreach(($courseOptions ?? collect()) as $courseOption)
                            <option value="{{ $courseOption }}" {{ ($courseFilter ?? '') === $courseOption ? 'selected' : '' }}>{{ $courseOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="health-filter-field">
                    <label for="monthFilter">Time</label>
                    <input
                        type="month"
                        id="monthFilter"
                        name="month"
                        value="{{ $monthFilter ?? '' }}"
                        class="health-filter-select"
                    >
                </div>
                <div class="health-filter-field">
                    <label for="yearFilter">Year Level</label>
                    <select id="yearFilter" name="year" class="health-filter-select">
                        <option value="">All Year Levels</option>
                        @foreach(($yearOptions ?? collect()) as $yearOption)
                            <option value="{{ $yearOption }}" {{ (string) ($yearFilter ?? '') === (string) $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="health-filter-btn">Apply</button>
                <a href="{{ route('admin.health_records') }}" class="health-filter-btn health-filter-btn-reset">Reset</a>
            </form>
        </div>
    </div>
    <table id="healthTable">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Full Name</th>
                <th>Course / Yr / Sec</th>
                <th>Medical Condition</th> {{-- Dating Medical Status --}}
                <th>Clearance Status</th> {{-- BAGONG COLUMN --}}
                <th>Submitted At</th>
                <th style="text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
                <tr
                    data-health-row
                    data-health-id="{{ $record->id }}"
                    class="{{ $highlightHealthId !== '' && $highlightHealthId === (string) $record->id ? 'health-highlight-row' : '' }}"
                >
                    <td class="fw-bold">{{ $record->user->student_number ?: $record->user->student_id }}</td>
                    <td>
                        <div class="student-name" style="font-weight: 700;">{{ $record->user->name }}</div>
                    </td>
                    <td>{{ $record->user->course }} {{ $record->user->year }}-{{ $record->user->section }}</td>
                    
                    {{-- Column 1: Medical Condition Status --}}
                    <td>
                        @if($record->has_illness == 'Yes')
                            <span class="status review">With Condition</span>
                        @else
                            <span class="status submitted">No Condition</span>
                        @endif
                    </td>

                    {{-- Column 2: Clearance Issuance Status --}}
                    <td>
                        @if($record->clearance_status == 'Issued')
                            <span class="status issued"><i class="fas fa-check-circle me-1"></i> Issued</span>
                        @elseif($record->clearance_status == 'Rejected')
                            <span class="status review">Rejected</span>
                        @elseif($record->clearance_status == 'Pending')
                            <span class="status pending">Pending</span>
                        @else
                            <span class="status submitted">Not Processed</span>
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
                            
                            @if($canSignHealth)
                                {{-- Change Sign Button appearance if already Issued --}}
                                @if($record->clearance_status == 'Issued')
                                    <button class="btn-action" style="background: #e2e8f0; color: #94a3b8; cursor: not-allowed;" disabled>
                                        Signed
                                    </button>
                                @else
                                    <a href="{{ route('admin.sign_page', $record->id) }}" class="btn-action btn-sign">
                                        Sign
                                    </a>
                                @endif
                            @else
                                <button class="btn-action" style="background: #e2e8f0; color: #64748b; cursor: default;" disabled>
                                    View Only
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">No health records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
    const healthFilterToggle = document.getElementById('healthFilterToggle');
    const healthFilterForm = document.getElementById('healthFilterForm');
    const hasActiveHealthFilters = @json(
        trim((string) ($search ?? '')) !== ''
        || trim((string) ($courseFilter ?? '')) !== ''
        || trim((string) ($monthFilter ?? '')) !== ''
        || trim((string) ($yearFilter ?? '')) !== ''
    );
    const highlightedHealthId = @json($highlightHealthId);

    function setHealthFilterOpenState(isOpen) {
        if (!healthFilterToggle || !healthFilterForm) {
            return;
        }

        healthFilterToggle.classList.toggle('is-open', isOpen);
        healthFilterToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        healthFilterForm.classList.toggle('is-open', isOpen);
    }

    if (healthFilterToggle && healthFilterForm) {
        setHealthFilterOpenState(hasActiveHealthFilters);

        healthFilterToggle.addEventListener('click', function () {
            setHealthFilterOpenState(!healthFilterForm.classList.contains('is-open'));
        });
    }

    if (highlightedHealthId) {
        window.addEventListener('DOMContentLoaded', function () {
            const highlightedRow = document.querySelector('[data-health-row][data-health-id="' + highlightedHealthId + '"]');
            if (highlightedRow) {
                highlightedRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }

</script>
@endpush
