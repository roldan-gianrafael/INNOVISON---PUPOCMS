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
    .health-summary-card {
        position: relative;
        overflow: hidden;
    }
    .health-summary-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 14px;
        right: 14px;
        height: 5px;
        background: #70131B;
        border-radius: 999px;
        pointer-events: none;
        z-index: 1;
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
        min-width: 92px;
        min-height: 38px;
        padding: 8px 16px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.01em;
        cursor: pointer;
        border: 1px solid transparent;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease, background 0.18s ease, color 0.18s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
    }

    .btn-action svg {
        width: 14px;
        height: 14px;
        margin-right: 6px;
        flex: 0 0 auto;
        stroke-width: 2;
    }
    .btn-action:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .btn-view {
        background: linear-gradient(135deg, #ffffff, #fff3f5);
        color: #70131B;
        border-color: #f0d7dc;
    }

    .btn-view:hover {
        background: linear-gradient(135deg, #fff7f8, #ffe7ed);
        border-color: #d9a9b4;
        box-shadow: 0 14px 24px rgba(112, 19, 27, 0.12);
    }

    .btn-sign {
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #ffffff;
        border-color: #8f2230;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.10),
            0 12px 24px rgba(112, 19, 27, 0.18);
    }

    .btn-sign:hover {
        color: #ffffff;
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.16),
            0 14px 26px rgba(112, 19, 27, 0.20);
    }

    .btn-signed,
    .btn-readonly {
        background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
        color: #475569;
        border-color: #cbd5e1;
        cursor: not-allowed;
        box-shadow: none;
    }

    .btn-signed::before {
        content: "✓";
        margin-right: 6px;
        font-weight: 900;
    }

    .health-issued-badge {
        min-width: 118px;
        min-height: 38px;
        padding: 8px 14px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #166534;
        border: 1px solid #86efac;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.01em;
        box-shadow:
            0 0 0 3px rgba(34, 197, 94, 0.10),
            0 10px 20px rgba(22, 101, 52, 0.10);
    }

    .health-issued-badge svg {
        width: 16px;
        height: 16px;
        flex: 0 0 auto;
        stroke-width: 2;
        margin-right: 0;
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
        display: inline-flex;
        align-items: center;
        padding: 10px 18px;
        border-radius: 0 0 14px 14px;
        border: 0;
        border-bottom: 2px solid rgba(234, 215, 160, 0.9);
        background: transparent;
        box-shadow: none;
    }

    .health-records-title svg {
        width: 18px;
        height: 18px;
        margin-right: 10px;
        flex: 0 0 auto;
    }

    .health-records-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        width: 100%;
        margin-bottom: 20px;
        padding: 16px 18px;
        border-radius: 0 0 20px 20px;
        border: 0;
        border-bottom: 2px solid rgba(112, 19, 27, 0.72);
        background: linear-gradient(135deg, rgba(255, 253, 246, 0.76) 0%, rgba(255, 249, 231, 0.58) 42%, rgba(255, 255, 255, 0.82) 100%);
        box-shadow: 0 14px 26px rgba(112, 19, 27, 0.05);
    }

    .health-records-toolbar-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        flex-wrap: wrap;
        margin-left: auto;
    }
    .health-records-search-shell {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        justify-content: flex-end;
    }
    .health-records-search-wrap {
        width: 0;
        max-width: 100%;
        flex: 0 0 0;
        opacity: 0;
        overflow: hidden;
        pointer-events: none;
        transform: translateX(12px) scaleX(0.96);
        transform-origin: right center;
        transition:
            width .32s cubic-bezier(.22, 1, .36, 1),
            flex-basis .32s cubic-bezier(.22, 1, .36, 1),
            opacity .24s ease,
            transform .28s cubic-bezier(.22, 1, .36, 1);
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        padding: 0 !important;
        border-radius: 0 !important;
    }
    .health-records-search-shell.is-open .health-records-search-wrap {
        width: 320px;
        flex: 0 0 320px;
        opacity: 1;
        pointer-events: auto;
        transform: translateX(0) scaleX(1);
    }

    .health-filter-shell {
        display: flex;
        align-items: center;
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
        display: flex;
        align-items: flex-end;
        justify-content: flex-start;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 16px;
    }

    .health-filter-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .health-filter-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-left: auto;
        flex-wrap: nowrap;
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
        min-height: 48px;
        height: 48px;
        padding: 12px 18px;
        border-radius: 0 0 14px 14px;
        border: 0 !important;
        border-bottom: 3px solid #8f2230 !important;
        min-width: 180px;
        color: #111827;
        background: transparent !important;
        box-shadow: none !important;
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        appearance: none;
        -webkit-appearance: none;
    }

    .health-records-search {
        width: 280px;
    }
    .health-records-search::placeholder {
        color: #7f1d2d;
        font-weight: 700;
    }

    .health-records-search:focus,
    .health-filter-select:focus {
        outline: none;
        border-bottom-color: #70131B;
        box-shadow: none !important;
        transform: translateY(-1px);
    }
    .health-records-search-toggle {
        width: 50px !important;
        height: 50px !important;
        min-width: 50px !important;
        min-height: 50px !important;
        flex: 0 0 50px !important;
        padding: 0 !important;
        gap: 0 !important;
        border-radius: 999px !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        background: linear-gradient(135deg, #70131B, #8f2230) !important;
        border: 1px solid #8f2230 !important;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20) !important;
        outline: none !important;
    }
    .health-records-search-toggle svg {
        width: 28px !important;
        height: 28px !important;
        stroke-width: 2 !important;
        display: block;
    }
    .health-records-search-toggle:hover,
    .health-records-search-toggle:focus {
        border-color: #facc15 !important;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16) !important;
        outline: none !important;
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

    .health-filter-modal {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(15, 23, 42, 0.42);
        backdrop-filter: blur(6px);
        z-index: 1200;
    }

    .health-filter-modal.is-open {
        display: flex;
    }

    .health-filter-modal-card {
        width: min(760px, 100%);
        border-radius: 22px;
        background: #ffffff;
        border: 1px solid rgba(127, 29, 45, 0.12);
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
        padding: 22px;
    }

    .health-filter-modal-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 12px;
    }

    .health-filter-modal-title {
        margin: 0;
        font-size: 20px;
        font-weight: 900;
        color: #70131B;
    }

    .health-filter-modal-copy {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }

    .health-filter-modal-close {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border: 1px solid rgba(127, 29, 45, 0.12);
        border-radius: 999px;
        background: #ffffff;
        color: #111827;
        cursor: pointer;
    }

    .health-filter-modal-close svg {
        width: 18px;
        height: 18px;
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

    .health-row-clickable {
        cursor: pointer;
    }

    .health-row-clickable:hover td {
        background: rgba(220, 252, 231, 0.58);
    }

    .health-row-clickable td {
        transition: background 0.16s ease;
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

    html[data-theme="dark"] .health-records-search::placeholder {
        color: #fecdd3 !important;
    }

    html[data-theme="dark"] .health-records-title {
        border-color: rgba(250, 204, 21, 0.30);
        background: transparent;
        box-shadow: none;
    }

    html[data-theme="dark"] .health-records-search,
    html[data-theme="dark"] .health-filter-select {
        background: rgba(18, 8, 12, 0.86);
        border-color: rgba(250, 204, 21, 0.28);
        box-shadow:
            0 0 0 2px rgba(250, 204, 21, 0.06),
            0 10px 20px rgba(0, 0, 0, 0.20);
    }

    html[data-theme="dark"] .health-records-search-toggle {
        background: linear-gradient(135deg, #70131B, #8f2230) !important;
        border-color: rgba(250, 204, 21, 0.28) !important;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.16),
            0 12px 22px rgba(0, 0, 0, 0.24) !important;
    }

    html[data-theme="dark"] .health-records-toolbar {
        border-color: rgba(250, 204, 21, 0.24);
        background: linear-gradient(135deg, rgba(112, 19, 27, 0.68) 0%, rgba(86, 16, 26, 0.64) 48%, rgba(44, 14, 18, 0.72) 100%);
        box-shadow:
            0 0 0 2px rgba(250, 204, 21, 0.07),
            0 16px 28px rgba(0, 0, 0, 0.22);
    }

    html[data-theme="dark"] .btn-view {
        background: linear-gradient(135deg, rgba(127, 29, 45, 0.22), rgba(148, 28, 57, 0.18));
        border-color: rgba(250, 204, 21, 0.18);
        color: #ffffff;
    }

    html[data-theme="dark"] .btn-view:hover {
        border-color: rgba(250, 204, 21, 0.4);
        box-shadow: 0 14px 24px rgba(0, 0, 0, 0.22);
    }

    html[data-theme="dark"] .btn-sign {
        color: #ffffff;
    }

    html[data-theme="dark"] .btn-signed,
    html[data-theme="dark"] .btn-readonly {
        background: linear-gradient(135deg, rgba(71, 85, 105, 0.78), rgba(51, 65, 85, 0.92));
        border-color: rgba(148, 163, 184, 0.28);
        color: #e2e8f0;
    }

    html[data-theme="dark"] .health-issued-badge {
        background: linear-gradient(135deg, rgba(20, 83, 45, 0.96), rgba(21, 128, 61, 0.84));
        border-color: rgba(74, 222, 128, 0.30);
        color: #ecfdf5;
        box-shadow:
            0 0 0 3px rgba(34, 197, 94, 0.10),
            0 12px 22px rgba(0, 0, 0, 0.24);
    }

    html[data-theme="dark"] .health-filter-modal-card {
        background: rgba(15, 23, 42, 0.98);
        border-color: rgba(148, 163, 184, 0.14);
        box-shadow: 0 18px 32px rgba(0, 0, 0, 0.24);
    }

    html[data-theme="dark"] .health-filter-modal-title,
    html[data-theme="dark"] .health-filter-modal-close {
        color: #ffffff;
    }

    html[data-theme="dark"] .health-filter-modal-copy {
        color: #cbd5e1;
    }

    html[data-theme="dark"] .health-filter-modal-close {
        background: rgba(15, 23, 42, 0.92);
        border-color: rgba(148, 163, 184, 0.24);
    }

    html[data-theme="dark"] .health-summary-card::before {
        background: #facc15;
    }

    html[data-theme="dark"] .summary-item .card {
        background: rgba(15, 23, 42, 0.96);
        border-color: rgba(148, 163, 184, 0.14);
    }

    html[data-theme="dark"] .health-highlight-row {
        background: linear-gradient(180deg, rgba(76, 29, 149, 0.34), rgba(91, 33, 182, 0.28));
        box-shadow: inset 4px 0 0 #a855f7;
    }

    html[data-theme="dark"] .health-row-clickable:hover td {
        background: rgba(20, 83, 45, 0.34);
    }

    @media (max-width: 980px) {
        .health-records-toolbar {
            flex-direction: column;
            align-items: stretch;
            border-radius: 24px;
        }

        .health-records-toolbar-actions,
        .health-filter-shell {
            justify-content: flex-start;
            margin-left: 0;
            align-items: stretch;
        }

        .health-records-toolbar-actions {
            width: 100%;
        }

        .health-records-search-shell {
            width: 100%;
        }

        .health-records-search-wrap,
        .health-records-search-shell.is-open .health-records-search-wrap {
            width: 100%;
            flex: 1 1 100%;
        }

        .health-records-search-shell:not(.is-open) .health-records-search-wrap {
            width: 0;
            flex-basis: 0;
        }

        .health-records-search {
            width: 100%;
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
        <h2 class="health-records-title"><x-outline-icon name="document-text" />Student Health Records</h2>
        <div class="health-records-toolbar-actions">
            <div class="health-records-search-shell" id="healthRecordsSearchShell">
                <div class="health-records-search-wrap">
                    <input
                        type="text"
                        id="recordSearch"
                        name="q"
                        value="{{ $search ?? '' }}"
                        class="health-records-search"
                        placeholder="Search by student name or ID..."
                    >
                </div>
                <button type="button" class="health-filter-toggle health-records-search-toggle" id="healthRecordsSearchToggle" aria-label="Open search" aria-expanded="false" aria-controls="recordSearch">
                    <x-outline-icon name="magnifying-glass" />
                </button>
            </div>
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
<div class="card health-summary-card">
    <div class="health-table-head">
        <div class="health-table-title">Health Profile Summary</div>
        <div class="health-filter-shell">
            <button type="button" class="health-filter-toggle" id="healthFilterToggle" aria-expanded="false" aria-controls="healthFilterModal">
                Filter Health Forms
            </button>
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
                    data-view-url="{{ $record->clearance_status == 'Issued' ? route('admin.show_health', $record->id) : '' }}"
                    title="{{ $record->clearance_status == 'Issued' ? 'Click to view' : '' }}"
                    class="{{ implode(' ', array_filter([
                        $highlightHealthId !== '' && $highlightHealthId === (string) $record->id ? 'health-highlight-row' : '',
                        $record->clearance_status == 'Issued' ? 'health-row-clickable' : '',
                    ])) }}"
                >
                    <td class="fw-bold">{{ $record->user->student_number ?: $record->user->student_id }}</td>
                    <td>
                        <div class="student-name" style="font-weight: 700;">{{ $record->user->name }}</div>
                    </td>
                    <td>{{ $record->course_college ?: $record->user->course }} {{ $record->user->year }}-{{ $record->user->section }}</td>
                    
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
                        @if($record->clearance_status == 'Issued')
                            <div class="d-flex justify-content-center">
                                <span class="health-issued-badge" aria-hidden="true">
                                    <x-outline-icon name="check" />
                                    Issued
                                </span>
                            </div>
                        @else
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.show_health', $record->id) }}" class="btn-action btn-view">
                                    <x-outline-icon name="document-text" />
                                    View
                                </a>
                                
                                @if($canSignHealth)
                                    <a href="{{ route('admin.sign_page', $record->id) }}" class="btn-action btn-sign">
                                        <x-outline-icon name="pencil-square" />
                                        Sign
                                    </a>
                                @else
                                    <button class="btn-action btn-readonly" disabled>
                                        View Only
                                    </button>
                                @endif
                            </div>
                        @endif
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

<div class="health-filter-modal" id="healthFilterModal" aria-hidden="true">
    <div class="health-filter-modal-card">
        <div class="health-filter-modal-head">
            <div>
                <h3 class="health-filter-modal-title">Filter Health Forms</h3>
                <p class="health-filter-modal-copy">Narrow the student health form list by course, month, or year level.</p>
            </div>
            <button type="button" class="health-filter-modal-close" id="healthFilterCloseBtn" aria-label="Close filter popup">
                <x-outline-icon name="x-mark" />
            </button>
        </div>

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
            <div class="health-filter-actions">
                <button type="submit" class="health-filter-btn">Apply</button>
                <a href="{{ route('admin.health_records') }}" class="health-filter-btn health-filter-btn-reset">Reset</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const healthFilterToggle = document.getElementById('healthFilterToggle');
    const healthFilterModal = document.getElementById('healthFilterModal');
    const healthFilterCloseBtn = document.getElementById('healthFilterCloseBtn');
    const healthFilterForm = document.getElementById('healthFilterForm');
    const highlightedHealthId = @json($highlightHealthId);
    const healthRecordsSearchInput = document.getElementById('recordSearch');
    const healthRecordsSearchShell = document.getElementById('healthRecordsSearchShell');
    const healthRecordsSearchToggle = document.getElementById('healthRecordsSearchToggle');
    const healthRows = Array.from(document.querySelectorAll('#healthTable tbody tr[data-health-row]'));

    function setHealthFilterOpenState(isOpen) {
        if (!healthFilterToggle || !healthFilterModal) {
            return;
        }

        healthFilterToggle.classList.toggle('is-open', isOpen);
        healthFilterToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        healthFilterModal.classList.toggle('is-open', isOpen);
        healthFilterModal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
    }

    if (healthFilterToggle && healthFilterModal) {
        healthFilterToggle.addEventListener('click', function () {
            setHealthFilterOpenState(true);
        });
    }

    if (healthFilterCloseBtn) {
        healthFilterCloseBtn.addEventListener('click', function () {
            setHealthFilterOpenState(false);
        });
    }

    if (healthFilterModal) {
        healthFilterModal.addEventListener('click', function (event) {
            if (event.target === healthFilterModal) {
                setHealthFilterOpenState(false);
            }
        });
    }

    if (healthFilterForm) {
        healthFilterForm.addEventListener('submit', function () {
            setHealthFilterOpenState(false);
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

    if (healthRecordsSearchInput) {
        healthRecordsSearchInput.addEventListener('input', function () {
            const searchTerm = this.value.trim().toLowerCase();

            healthRows.forEach(function (row) {
                const rowText = row.innerText.toLowerCase();
                row.style.display = rowText.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    if (healthRecordsSearchShell && healthRecordsSearchInput && healthRecordsSearchToggle) {
        const setHealthRecordsSearchOpenState = function (isOpen) {
            healthRecordsSearchShell.classList.toggle('is-open', isOpen);
            healthRecordsSearchToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        };

        setHealthRecordsSearchOpenState(healthRecordsSearchInput.value.trim() !== '');

        healthRecordsSearchToggle.addEventListener('click', function () {
            const shouldOpen = !healthRecordsSearchShell.classList.contains('is-open');
            setHealthRecordsSearchOpenState(shouldOpen);

            if (shouldOpen) {
                window.requestAnimationFrame(function () {
                    healthRecordsSearchInput.focus();
                });
            }
        });
    }

    window.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-health-row][data-view-url]').forEach(function (row) {
            const viewUrl = row.getAttribute('data-view-url') || '';
            if (viewUrl.trim() === '') {
                return;
            }

            row.addEventListener('click', function (event) {
                if (event.target.closest('a, button, input, select, textarea, label')) {
                    return;
                }

                window.location.href = viewUrl;
            });
        });
    });

</script>
@endpush
