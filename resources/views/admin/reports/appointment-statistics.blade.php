@extends('layouts.admin')

@section('title', 'Daily Treatment Record')

@push('styles')
<style>
    .treatment-record-shell {
        max-width: 1600px;
        margin: 0 auto;
        padding: 22px;
    }
    .treatment-record-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 22px;
    }
    .treatment-record-title {
        margin: 0;
        color: #111827;
        font-size: 30px;
        font-weight: 900;
    }
    .treatment-record-subtitle {
        max-width: 780px;
        margin: 7px 0 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
    }
    .treatment-record-back {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #70131b;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
    }
    .treatment-record-back svg {
        width: 18px;
        height: 18px;
        transform: rotate(180deg);
    }
    .treatment-filter {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        margin-bottom: 20px;
        padding: 16px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #fff;
    }
    .treatment-field {
        min-width: 230px;
    }
    .treatment-field label,
    .logbook-search label {
        display: block;
        margin-bottom: 6px;
        color: #475569;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
    }
    .treatment-control {
        width: 100%;
        min-height: 43px;
        padding: 9px 12px;
        border: 1px solid #94a3b8;
        border-radius: 6px;
        background: #fff;
        color: #111827;
        font-size: 14px;
    }
    .treatment-control:focus {
        border-color: #70131b;
        outline: 3px solid rgba(112, 19, 27, .12);
    }
    .treatment-filter-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 43px;
        padding: 9px 17px;
        border: 1px solid #70131b;
        border-radius: 6px;
        background: #70131b;
        color: #fff;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        transition: background-color .18s ease, color .18s ease;
    }
    .treatment-filter-button:hover {
        border-color: #facc15;
        background: #facc15;
        color: #111827;
    }
    .treatment-filter-button svg {
        width: 17px;
        height: 17px;
    }
    .treatment-metrics {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }
    .treatment-metric {
        display: grid;
        grid-template-columns: 46px minmax(0, 1fr);
        gap: 13px;
        align-items: center;
        min-height: 112px;
        padding: 18px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 6px 18px rgba(15, 23, 42, .06);
    }
    .treatment-metric-icon {
        display: grid;
        place-items: center;
        width: 46px;
        height: 46px;
        border-radius: 7px;
        background: #70131b;
        color: #fff;
    }
    .treatment-metric-icon.yellow {
        background: #facc15;
        color: #111827;
    }
    .treatment-metric-icon.green {
        background: #166534;
    }
    .treatment-metric-icon svg {
        width: 23px;
        height: 23px;
    }
    .treatment-metric-label {
        display: block;
        margin-bottom: 5px;
        color: #64748b;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
    }
    .treatment-metric-value {
        display: block;
        overflow-wrap: anywhere;
        color: #111827;
        font-size: 25px;
        font-weight: 900;
        line-height: 1.15;
    }
    .treatment-metric-value.text-value {
        font-size: 18px;
    }
    .form-b-panel {
        overflow: hidden;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 8px 22px rgba(15, 23, 42, .07);
    }
    .form-b-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        padding: 18px;
        border-bottom: 1px solid #cbd5e1;
        background: #f8fafc;
    }
    .form-b-kicker {
        margin: 0 0 3px;
        color: #70131b;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
    }
    .form-b-title {
        margin: 0;
        color: #111827;
        font-size: 20px;
        font-weight: 900;
    }
    .form-b-month {
        margin: 4px 0 0;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
    }
    .logbook-search {
        width: min(100%, 330px);
    }
    .logbook-search-wrap {
        position: relative;
    }
    .logbook-search-wrap svg {
        position: absolute;
        top: 50%;
        left: 11px;
        width: 17px;
        height: 17px;
        color: #64748b;
        transform: translateY(-50%);
        pointer-events: none;
    }
    .logbook-search .treatment-control {
        padding-left: 36px;
    }
    .form-b-table-wrap {
        overflow-x: auto;
    }
    .form-b-table {
        width: 100%;
        min-width: 1450px;
        border-collapse: collapse;
        table-layout: fixed;
    }
    .form-b-table th,
    .form-b-table td {
        border-right: 1px solid #cbd5e1;
        border-bottom: 1px solid #cbd5e1;
        vertical-align: top;
    }
    .form-b-table th:last-child,
    .form-b-table td:last-child {
        border-right: 0;
    }
    .form-b-table th {
        padding: 10px 8px;
        background: #70131b;
        color: #fff;
        font-size: 11px;
        font-weight: 900;
        line-height: 1.35;
        text-align: center;
        text-transform: uppercase;
    }
    .form-b-table td {
        padding: 10px 9px;
        color: #1f2937;
        font-size: 12px;
        line-height: 1.45;
    }
    .form-b-table tbody tr:nth-child(even) {
        background: #f8fafc;
    }
    .form-b-table tbody tr:hover {
        background: #fffbea;
    }
    .form-b-table .col-date { width: 92px; }
    .form-b-table .col-time { width: 76px; }
    .form-b-table .col-patient { width: 175px; }
    .form-b-table .col-course { width: 165px; }
    .form-b-table .col-complaint { width: 260px; }
    .form-b-table .col-treatment { width: 190px; }
    .form-b-table .col-qty { width: 65px; }
    .form-b-table .col-staff { width: 175px; }
    .patient-name {
        display: block;
        color: #111827;
        font-weight: 800;
    }
    .patient-number,
    .cell-secondary {
        display: block;
        margin-top: 3px;
        color: #64748b;
        font-size: 10px;
    }
    .diagnosis-label {
        display: inline-block;
        margin-top: 5px;
        padding: 2px 6px;
        border-radius: 4px;
        background: #fef3c7;
        color: #92400e;
        font-size: 10px;
        font-weight: 800;
    }
    .quantity-cell,
    .time-cell,
    .date-cell {
        text-align: center;
        white-space: nowrap;
    }
    .form-b-empty {
        padding: 38px 20px !important;
        color: #64748b !important;
        text-align: center;
    }
    .form-b-no-results {
        display: none;
        padding: 24px;
        color: #64748b;
        font-size: 13px;
        text-align: center;
    }
    .form-b-footer {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        padding: 12px 18px;
        color: #64748b;
        font-size: 11px;
    }
    html[data-theme="dark"] .treatment-filter,
    html[data-theme="dark"] .treatment-metric,
    html[data-theme="dark"] .form-b-panel {
        border-color: #374151;
        background: #111827;
    }
    html[data-theme="dark"] .form-b-heading,
    html[data-theme="dark"] .form-b-table tbody tr:nth-child(even) {
        background: #1f2937;
    }
    html[data-theme="dark"] .treatment-record-title,
    html[data-theme="dark"] .treatment-metric-value,
    html[data-theme="dark"] .form-b-title,
    html[data-theme="dark"] .patient-name {
        color: #f8fafc;
    }
    html[data-theme="dark"] .treatment-control {
        border-color: #4b5563;
        background: #0f172a;
        color: #f8fafc;
    }
    html[data-theme="dark"] .form-b-table td {
        border-color: #374151;
        color: #e5e7eb;
    }
    html[data-theme="dark"] .form-b-table tbody tr:hover {
        background: #332d19;
    }
    @media (max-width: 900px) {
        .treatment-metrics {
            grid-template-columns: 1fr;
        }
        .form-b-heading {
            align-items: stretch;
            flex-direction: column;
        }
        .logbook-search {
            width: 100%;
        }
    }
    @media (max-width: 620px) {
        .treatment-record-shell {
            padding: 14px;
        }
        .treatment-record-header,
        .treatment-filter {
            align-items: stretch;
            flex-direction: column;
        }
        .treatment-field,
        .treatment-filter-button {
            width: 100%;
        }
        .treatment-record-title {
            font-size: 25px;
        }
        .form-b-footer {
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
@php
    $role = \App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '');
    $reportsHomeUrl = $role === \App\Models\User::ROLE_ADMIN ? url('/assistant/reports') : url('/admin/reports');
    $selectedMonthLabel = \Carbon\Carbon::createFromFormat('Y-m-d', $monthFilter . '-01')->format('F Y');
    $quantityDisplay = rtrim(rtrim(number_format((float) $totalMedicinesDispensed, 2, '.', ','), '0'), '.');
@endphp

<div class="treatment-record-shell">
    <header class="treatment-record-header">
        <div>
            <h1 class="treatment-record-title">Daily Treatment Record</h1>
            <p class="treatment-record-subtitle">Official digital Form B logbook for clinic consultations, treatment provided, medicines dispensed, and attending personnel.</p>
        </div>
        <a href="{{ $reportsHomeUrl }}" class="treatment-record-back">
            <x-outline-icon name="arrow-long-right" />
            Back to Reports
        </a>
    </header>

    <form method="GET" class="treatment-filter">
        <div class="treatment-field">
            <label for="treatmentMonth">Reporting Month</label>
            <input id="treatmentMonth" class="treatment-control" type="month" name="month" value="{{ $monthFilter }}">
        </div>
        <button type="submit" class="treatment-filter-button">
            <x-outline-icon name="calendar-days" />
            Apply Month
        </button>
    </form>

    <section class="treatment-metrics" aria-label="Treatment record summary">
        <article class="treatment-metric">
            <span class="treatment-metric-icon">
                <x-outline-icon name="users" />
            </span>
            <div>
                <span class="treatment-metric-label">Total Patients Treated</span>
                <strong class="treatment-metric-value">{{ number_format($totalPatientsTreated) }}</strong>
            </div>
        </article>
        <article class="treatment-metric">
            <span class="treatment-metric-icon yellow">
                <x-outline-icon name="cube" />
            </span>
            <div>
                <span class="treatment-metric-label">Total Medicines Dispensed</span>
                <strong class="treatment-metric-value">{{ $quantityDisplay ?: '0' }}</strong>
            </div>
        </article>
        <article class="treatment-metric">
            <span class="treatment-metric-icon green">
                <x-outline-icon name="clipboard-document-list" />
            </span>
            <div>
                <span class="treatment-metric-label">Most Common Illness</span>
                <strong class="treatment-metric-value text-value">{{ $topDisease->condition ?? 'No data recorded' }}</strong>
            </div>
        </article>
    </section>

    <section class="form-b-panel">
        <div class="form-b-heading">
            <div>
                <p class="form-b-kicker">PUP Taguig Medical Clinic · Form B</p>
                <h2 class="form-b-title">Digital Treatment Logbook</h2>
                <p class="form-b-month">{{ $selectedMonthLabel }}</p>
            </div>
            <div class="logbook-search">
                <label for="treatmentRecordSearch">Search Patient</label>
                <div class="logbook-search-wrap">
                    <x-outline-icon name="magnifying-glass" />
                    <input id="treatmentRecordSearch" class="treatment-control" type="search" placeholder="Name or student number" autocomplete="off">
                </div>
            </div>
        </div>

        <div class="form-b-table-wrap">
            <table class="form-b-table">
                <thead>
                    <tr>
                        <th class="col-date">Date</th>
                        <th class="col-time">Time In</th>
                        <th class="col-time">Time Out</th>
                        <th class="col-patient">Patient Name</th>
                        <th class="col-course">Course-Yr &amp; Sec / Dept</th>
                        <th class="col-complaint">Complaints / Impression</th>
                        <th class="col-treatment">Treatment / Medicines</th>
                        <th class="col-qty">Qty</th>
                        <th class="col-staff">Physician / Attending Staff</th>
                    </tr>
                </thead>
                <tbody id="treatmentRecordBody">
                    @forelse($consultations as $consultation)
                        @php
                            $patient = $consultation->user;
                            $patientName = trim((string) ($patient?->name ?: $consultation->name)) ?: 'Unnamed Patient';
                            $studentNumber = trim((string) ($patient?->student_number ?: $patient?->student_id));
                            $course = trim((string) ($patient?->course ?: optional($patient?->healthProfile)->course_college));
                            $yearSection = trim(implode(' - ', array_filter([
                                trim((string) $patient?->year),
                                trim((string) $patient?->section),
                            ])));
                            $courseDepartment = trim(implode(' / ', array_filter([$course, $yearSection])));
                            $reason = trim((string) ($consultation->reason_for_visit ?: $consultation->comments));
                            $diagnosis = trim((string) optional($consultation->medicalCondition)->name);
                            $medicineName = trim((string) (optional($consultation->medicineItem)->name ?: $consultation->medicine));
                            $medicineQuantity = (float) $consultation->medicine_quantity;
                            $staffName = trim((string) ($consultation->attending_staff_name ?: optional($consultation->attendingStaff)->name));
                            $timeIn = $consultation->time_in ?: optional($consultation->created_at)->format('H:i:s');
                            $timeOut = $consultation->time_out ?: optional($consultation->updated_at)->format('H:i:s');
                        @endphp
                        <tr
                            class="treatment-record-row"
                            data-patient-name="{{ \Illuminate\Support\Str::lower($patientName) }}"
                            data-student-number="{{ \Illuminate\Support\Str::lower($studentNumber) }}"
                        >
                            <td class="date-cell">{{ optional($consultation->consultation_date)->format('m/d/Y') ?: '-' }}</td>
                            <td class="time-cell">{{ $timeIn ? \Carbon\Carbon::parse($timeIn)->format('g:i A') : '-' }}</td>
                            <td class="time-cell">{{ $timeOut ? \Carbon\Carbon::parse($timeOut)->format('g:i A') : '-' }}</td>
                            <td>
                                <span class="patient-name">{{ $patientName }}</span>
                                <span class="patient-number">{{ $studentNumber ?: 'No student number' }}</span>
                            </td>
                            <td>{{ $courseDepartment ?: ($consultation->user_role ?: '-') }}</td>
                            <td>
                                {{ $reason ?: 'No complaint recorded' }}
                                @if($diagnosis !== '')
                                    <span class="diagnosis-label">{{ $diagnosis }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $medicineName !== '' && strtolower($medicineName) !== 'none' ? $medicineName : 'No medicine issued' }}
                                @if($consultation->service)
                                    <span class="cell-secondary">{{ $consultation->service }}</span>
                                @endif
                            </td>
                            <td class="quantity-cell">
                                {{ $medicineQuantity > 0 ? rtrim(rtrim(number_format($medicineQuantity, 2, '.', ''), '0'), '.') : '-' }}
                            </td>
                            <td>{{ $staffName ?: 'Clinic Staff' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="form-b-empty">No treatment records were logged for {{ $selectedMonthLabel }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="form-b-no-results" id="treatmentNoResults">No patient matched your search.</div>
        <footer class="form-b-footer">
            <span id="treatmentVisibleCount">{{ $consultations->count() }} record{{ $consultations->count() === 1 ? '' : 's' }}</span>
            <span>Generated from finalized clinic consultations</span>
        </footer>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('treatmentRecordSearch');
        const rows = Array.from(document.querySelectorAll('.treatment-record-row'));
        const noResults = document.getElementById('treatmentNoResults');
        const visibleCount = document.getElementById('treatmentVisibleCount');

        if (!searchInput || !noResults || !visibleCount) {
            return;
        }

        const updateSearch = function () {
            const query = searchInput.value.trim().toLowerCase();
            let shown = 0;

            rows.forEach(function (row) {
                const patientName = row.dataset.patientName || '';
                const studentNumber = row.dataset.studentNumber || '';
                const matches = query === '' || patientName.includes(query) || studentNumber.includes(query);
                row.hidden = !matches;
                if (matches) {
                    shown += 1;
                }
            });

            noResults.style.display = rows.length > 0 && shown === 0 ? 'block' : 'none';
            visibleCount.textContent = shown + ' record' + (shown === 1 ? '' : 's');
        };

        searchInput.addEventListener('input', updateSearch);
    });
</script>
@endsection
