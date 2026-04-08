<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fill Up - Student Health Information Form</title>
    <script
        src="{{ asset('js/sienna-accessibility-custom.umd.js') }}?v={{ filemtime(public_path('js/sienna-accessibility-custom.umd.js')) }}"
        data-asw-position="bottom-right"
        data-asw-offset="24,12"
        data-asw-size="small"
        defer
    ></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --clinic-maroon: #800000;
            --clinic-maroon-dark: #5f0012;
            --clinic-maroon-soft: #f5e7ea;
            --clinic-grey-bg: #eef1f3;
            --clinic-panel: #ffffff;
            --clinic-field: #fbfbfc;
            --clinic-border: #d9dee5;
            --clinic-text: #111827;
            --clinic-muted: #5b6470;
            --clinic-yellow: #fff6cc;
        }
        body {
            background:
                linear-gradient(rgba(45, 10, 12, 0.72), rgba(28, 8, 8, 0.8)),
                url('{{ asset('images/PUPBG.jpg') }}') center center / cover no-repeat fixed;
            padding: 34px 0 48px;
            font-family: 'Segoe UI', sans-serif;
            color: var(--clinic-text);
        }
        .form-card {
            background: rgba(255, 255, 255, 0.96);
            padding: 34px;
            border-radius: 28px;
            box-shadow: 0 28px 60px rgba(15, 23, 42, 0.12);
            border: 1px solid rgba(128, 0, 0, 0.08);
            max-width: 1120px;
            margin: auto;
        }
        .intake-shell {
            display: grid;
            gap: 26px;
        }
        .stepper-shell {
            background: linear-gradient(180deg, #fafafb 0%, #f2f4f7 100%);
            border: 1px solid var(--clinic-border);
            border-radius: 24px;
            padding: 22px;
            position: sticky;
            top: 18px;
            z-index: 20;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }
        .stepper-track {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }
        :where(.asw-menu-btn) {
            position: fixed;
            left: auto !important;
            right: 20px !important;
            top: auto !important;
            bottom: 14px !important;
            background: #800000 !important;
            background-image: none !important;
            border: 2px solid #5f0012 !important;
            outline: none !important;
            box-shadow: 0 10px 24px rgba(128, 0, 0, 0.28) !important;
        }
        :where(.asw-menu-btn svg),
        :where(.asw-menu-btn svg path:not([fill="none"])) {
            fill: #ffffff !important;
            stroke: none !important;
        }
        :where(.asw-menu-btn svg path[fill="none"]) {
            stroke: none !important;
        }
        .step-card {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 84px;
            border-radius: 20px;
            padding: 16px 18px;
            border: 1px solid #d7dce3;
            background: #f6f7f9;
            color: #6b7280;
        }
        .step-card.active {
            background: linear-gradient(135deg, var(--clinic-maroon) 0%, #991b1b 100%);
            border-color: transparent;
            color: #ffffff;
            box-shadow: 0 18px 34px rgba(128, 0, 0, 0.22);
        }
        .step-card.completed {
            background: linear-gradient(135deg, #15803d 0%, #16a34a 100%);
            border-color: transparent;
            color: #ffffff;
            box-shadow: 0 16px 28px rgba(22, 163, 74, 0.18);
        }
        .step-card.warning {
            background: linear-gradient(135deg, #f59e0b 0%, #facc15 100%);
            border-color: transparent;
            color: #3f2b00;
            box-shadow: 0 16px 28px rgba(245, 158, 11, 0.2);
        }
        .step-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.78);
            color: var(--clinic-maroon);
            flex-shrink: 0;
        }
        .step-card.active .step-icon {
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
        }
        .step-card:not(.active):not(.completed) .step-icon {
            color: #7b8794;
        }
        .step-card.completed .step-icon {
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
        }
        .step-card.warning .step-icon {
            background: rgba(255, 255, 255, 0.26);
            color: #6b3f00;
        }
        .step-card.completed .step-icon .step-icon-default {
            display: none;
        }
        .step-card.completed .step-icon .step-icon-check {
            display: block;
        }
        .step-card.warning .step-icon .step-icon-default {
            display: none;
        }
        .step-card.warning .step-icon .step-icon-warning {
            display: block;
        }
        .step-icon-check {
            display: none;
        }
        .step-icon-warning {
            display: none;
        }
        .step-copy small {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            opacity: 0.75;
            margin-bottom: 5px;
            font-weight: 700;
        }
        .step-copy strong {
            display: block;
            font-size: 14px;
            line-height: 1.3;
        }
        .intro-panel {
            display: block;
        }
        .intro-copy,
        .intro-upload {
            border-radius: 24px;
            border: 1px solid var(--clinic-border);
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fb 100%);
            padding: 24px;
        }
        .intro-copy h2 {
            margin: 0;
            color: var(--clinic-maroon);
            font-size: 28px;
            font-weight: 800;
        }
        .intro-copy p {
            margin: 12px 0 0;
            color: var(--clinic-muted);
            font-size: 14px;
            line-height: 1.65;
        }
        .intro-upload h3 {
            margin: 0 0 14px;
            color: var(--clinic-maroon);
            font-size: 18px;
            font-weight: 800;
        }
        .upload-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }
        .verification-upload-shell {
            margin-top: 22px;
            padding-top: 22px;
            border-top: 1px solid rgba(128, 0, 0, 0.12);
        }
        .privacy-copy {
            margin-top: 18px;
            text-align: center;
            color: var(--clinic-muted);
            font-size: 0.92rem;
            line-height: 1.7;
            max-width: 760px;
            margin-left: auto;
            margin-right: auto;
        }
        .section-title {
            background: transparent;
            color: var(--clinic-maroon);
            padding: 0;
            margin-top: 26px;
            border-radius: 0;
            font-weight: 800;
            font-size: 1.12rem;
            text-transform: none;
            border-bottom: 2px solid rgba(128, 0, 0, 0.12);
            padding-bottom: 10px;
            letter-spacing: 0.01em;
        }
        .form-step {
            display: none;
        }
        .form-step.is-active {
            display: block;
        }
        .step-card {
            transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease, color 0.18s ease;
        }
        .step-card.is-clickable {
            cursor: pointer;
        }
        .step-card.is-clickable:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 22px rgba(15, 23, 42, 0.08);
        }
        .section-hint {
            margin: 10px 0 0;
            color: var(--clinic-muted);
            font-size: 0.92rem;
        }
        .step4-note-box {
            margin-top: 16px;
            border-radius: 18px;
            border: 1px solid rgba(128, 0, 0, 0.12);
            background: linear-gradient(180deg, #fffdf8 0%, #f8f4f1 100%);
            padding: 18px 20px;
        }
        .step4-note-box h4 {
            margin: 0 0 10px;
            color: var(--clinic-maroon);
            font-size: 1rem;
            font-weight: 800;
        }
        .step4-note-box p,
        .step4-note-box li {
            color: var(--clinic-muted);
            font-size: 0.92rem;
            line-height: 1.6;
        }
        .step4-note-box ul {
            margin: 0;
            padding-left: 20px;
        }
        .form-label {
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--clinic-text);
            margin-bottom: 8px;
        }
        .required-mark {
            color: var(--clinic-maroon);
            font-weight: 800;
            margin-left: 2px;
        }
        .sub-label { font-size: 0.85rem; font-style: italic; color: var(--clinic-text); margin-bottom: 15px; display: block; }
        .form-control,
        .form-select {
            min-height: 48px;
            border-radius: 14px;
            border: 1px solid var(--clinic-border);
            background: var(--clinic-field);
            color: var(--clinic-text);
            box-shadow: none;
            padding: 11px 14px;
        }
        textarea.form-control {
            min-height: 120px;
        }
        .form-control:focus,
        .form-select:focus {
            border-color: rgba(128, 0, 0, 0.5);
            box-shadow: 0 0 0 0.18rem rgba(128, 0, 0, 0.11);
            background: #ffffff;
        }
        .form-control.bg-light {
            background: #f2f4f7 !important;
            border-color: #dbe0e7;
        }
        .vax-table th { background-color: #f8f9fa; font-size: 0.85rem; text-align: center; }
        .upload-box {
            border: 2px dashed #c7ced8;
            padding: 22px 18px;
            text-align: center;
            border-radius: 18px;
            background: linear-gradient(180deg, #fcfcfd 0%, #f4f6f8 100%);
            min-height: 170px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 12px;
        }
        .upload-box svg {
            width: 42px;
            height: 42px;
            margin: 0 auto;
            color: var(--clinic-maroon);
        }
        .upload-box strong {
            display: block;
            color: var(--clinic-maroon);
            font-size: 0.96rem;
        }
        .upload-box span {
            display: block;
            color: var(--clinic-muted);
            font-size: 0.82rem;
        }
        .health-upload-field {
            border: 2px solid #800000;
            border-radius: 8px;
            padding: 10px 12px;
            background: #fff;
            color: #000;
        }
        .health-upload-field:focus {
            border-color: #5c0000;
            box-shadow: 0 0 0 0.2rem rgba(128, 0, 0, 0.15);
        }
        .health-upload-helper {
            color: #000 !important;
            display: block;
            margin-top: 6px;
            font-size: 0.82rem;
        }
        .text-muted {
            color: #000 !important;
        }
        .cta-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            margin-top: 34px;
        }
        .cta-group {
            display: flex;
            gap: 12px;
            margin-left: auto;
        }
        .btn-health-secondary {
            min-width: 170px;
            border: 1px solid rgba(128, 0, 0, 0.18);
            border-radius: 16px;
            padding: 15px 24px;
            background: #ffffff;
            color: var(--clinic-maroon);
            font-size: 0.96rem;
            font-weight: 800;
        }
        .btn-health-secondary:hover {
            background: #faf4f5;
            color: var(--clinic-maroon-dark);
        }
        .btn-health-submit {
            min-width: 220px;
            border: none;
            border-radius: 16px;
            padding: 15px 24px;
            background: linear-gradient(135deg, var(--clinic-maroon) 0%, var(--clinic-maroon-dark) 100%);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: 0.01em;
            box-shadow: 0 16px 28px rgba(128, 0, 0, 0.2);
        }
        .btn-health-submit:hover {
            background: linear-gradient(135deg, #8f0c0c 0%, #6d0217 100%);
            color: #ffffff;
        }
        @media (max-width: 992px) {
            .stepper-track {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 768px) {
            body {
                padding: 18px 0 28px;
            }
            .form-card {
                padding: 16px;
                border-radius: 20px;
            }
            .stepper-shell {
                top: 10px;
                padding: 14px;
                border-radius: 18px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .stepper-track {
                display: flex;
                gap: 12px;
                min-width: max-content;
            }
            .step-card {
                width: 250px;
                min-height: 78px;
                padding: 14px 15px;
            }
            .step-copy small {
                font-size: 10px;
            }
            .step-copy strong {
                font-size: 13px;
            }
            .upload-grid {
                grid-template-columns: 1fr;
            }
            .cta-row {
                flex-direction: column;
                align-items: stretch;
            }
            .cta-group {
                width: 100%;
                flex-direction: column;
                margin-left: 0;
            }
            .btn-health-secondary,
            .btn-health-submit {
                width: 100%;
            }
            .privacy-copy {
                font-size: 0.86rem;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-card">
        <div class="intake-shell">
        <div class="stepper-shell">
            <div class="stepper-track">
                <div class="step-card active is-clickable" data-step-target="1">
                    <div class="step-icon">
                        <svg class="step-icon-default" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21a8 8 0 0 0-16 0"></path>
                            <circle cx="12" cy="8" r="4"></circle>
                        </svg>
                        <svg class="step-icon-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6L9 17l-5-5"></path>
                        </svg>
                        <svg class="step-icon-warning" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 9v4"></path>
                            <path d="M12 17h.01"></path>
                            <path d="M10.3 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.7 3.86a2 2 0 0 0-3.4 0z"></path>
                        </svg>
                    </div>
                    <div class="step-copy">
                        <small>Step 1</small>
                        <strong>Personal Information</strong>
                    </div>
                </div>
                <div class="step-card is-clickable" data-step-target="2">
                    <div class="step-icon">
                        <svg class="step-icon-default" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 7h16"></path>
                            <path d="M7 7v10a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V7"></path>
                            <path d="M10 11h4"></path>
                            <path d="M12 9v4"></path>
                        </svg>
                        <svg class="step-icon-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6L9 17l-5-5"></path>
                        </svg>
                        <svg class="step-icon-warning" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 9v4"></path>
                            <path d="M12 17h.01"></path>
                            <path d="M10.3 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.7 3.86a2 2 0 0 0-3.4 0z"></path>
                        </svg>
                    </div>
                    <div class="step-copy">
                        <small>Step 2</small>
                        <strong>Medical History</strong>
                    </div>
                </div>
                <div class="step-card is-clickable" data-step-target="3">
                    <div class="step-icon">
                        <svg class="step-icon-default" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z"></path>
                            <path d="M9 12l2 2 4-4"></path>
                        </svg>
                        <svg class="step-icon-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6L9 17l-5-5"></path>
                        </svg>
                        <svg class="step-icon-warning" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 9v4"></path>
                            <path d="M12 17h.01"></path>
                            <path d="M10.3 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.7 3.86a2 2 0 0 0-3.4 0z"></path>
                        </svg>
                    </div>
                    <div class="step-copy">
                        <small>Step 3</small>
                        <strong>Personal Social History & Vaccination</strong>
                    </div>
                </div>
                <div class="step-card is-clickable" data-step-target="4">
                    <div class="step-icon">
                        <svg class="step-icon-default" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16l4-3 4 3 4-3 4 3V8z"></path>
                            <path d="M14 2v6h6"></path>
                        </svg>
                        <svg class="step-icon-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6L9 17l-5-5"></path>
                        </svg>
                        <svg class="step-icon-warning" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 9v4"></path>
                            <path d="M12 17h.01"></path>
                            <path d="M10.3 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.7 3.86a2 2 0 0 0-3.4 0z"></path>
                        </svg>
                    </div>
                    <div class="step-copy">
                        <small>Step 4</small>
                        <strong>Verification & Uploads</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="intro-panel">
            <div class="intro-copy">
                <h2>Personal Information</h2>
                <p>
                    Please provide complete and truthful information. Type <strong>N/A</strong> or <strong>NONE</strong>
                    for fields that do not apply to you. Required fields are marked with a maroon asterisk.
                </p>
            </div>
        </div>
        <form action="{{ route('store.health.form') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <section class="form-step is-active" data-step="1">
    <div class="row mt-4">
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-7 mb-3">
                    <label class="form-label">Full Name<span class="required-mark">*</span></label>
                    <input type="text" class="form-control bg-light" value="{{ trim(implode(' ', array_filter([optional($linkedAdminProfile)->first_name ?: Auth::user()->first_name, optional($linkedAdminProfile)->middle_name, optional($linkedAdminProfile)->last_name ?: Auth::user()->last_name, optional($linkedAdminProfile)->suffix_name]))) }}" readonly>
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label">PUP Student No.<span class="required-mark">*</span></label>
                    <input type="text" class="form-control bg-light" value="{{ Auth::user()->student_id }}" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Middle Name</label>
                    <input type="text" class="form-control bg-light" value="{{ optional($linkedAdminProfile)->middle_name }}" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Suffix Name</label>
                    <input type="text" class="form-control bg-light" value="{{ optional($linkedAdminProfile)->suffix_name }}" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Mailing Address<span class="required-mark">*</span></label>
                    <input type="text" name="home_address" class="form-control" placeholder="House No., Street, Brgy, City" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">School Year</label>
                    <input type="text" name="school_year" class="form-control" value="2025-2026">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Height</label>
                    <input type="text" name="height" class="form-control" placeholder="cm">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Weight</label>
                    <input type="text" name="weight" class="form-control" placeholder="kg">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-3">
            <label class="form-label">Age<span class="required-mark">*</span></label>
            <input type="number" name="age" value="{{ $calculatedAge }}" class="form-control" readonly placeholder="Auto-calculated">
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label">Sex<span class="required-mark">*</span></label>
            <select name="sex" class="form-select" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label">Civil Status<span class="required-mark">*</span></label>
            <select name="civil_status" class="form-select" required>
                <option value="" selected disabled>Select Status</option>
                <option value="Single">Single</option>
                <option value="Married">Married</option>
                <option value="Widowed">Widowed</option>
                <option value="Separated">Separated</option>
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label">Course / College</label>
            <input type="text" name="course_college" class="form-control" value="{{ Auth::user()->course }}" readonly>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Blood Type</label>
            <input type="text" name="blood_type" class="form-control" placeholder="e.g. O+">
        </div>
        <div class="col-md-8 mb-3">
            <label class="form-label">Email Address<span class="required-mark">*</span></label>
            <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" readonly>
        </div>
        <div class="col-md-7 mb-3">
            <label class="form-label">Parent's Name / Guardian / Spouse<span class="required-mark">*</span></label>
            <input type="text" name="guardian_name" class="form-control" required>
        </div>
        <div class="col-md-2 mb-3">
            <label class="form-label">Landline</label>
            <input type="text" name="landline" class="form-control">
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label">Phone Number<span class="required-mark">*</span></label>
            <input type="text" name="cellphone" class="form-control" required>
        </div>
    </div>

    </section>

    <section class="form-step" data-step="2">
    <div class="row mt-3">
        <div class="col-12 mb-2">
            <label class="form-label">1. Do you need medical attention or has known medical illness?</label>
            <div class="form-check form-check-inline ms-3">
                <input class="form-check-input illness-radio" type="radio" name="has_illness" value="No" id="illnessNo">
                <label class="form-check-label" for="illnessNo">No</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input illness-radio" type="radio" name="has_illness" value="Yes" id="illnessYes">
                <label class="form-check-label" for="illnessYes">Yes</label>
            </div>
        </div>
    </div>

    <span class="sub-label">(Please check the following that apply as needed)</span>
    
    <div class="row px-3">
        @php
            $illnesses = ['Asthma', 'Loss of Consciousness', 'Eye Disease/ Defect', 'Accident Injuries', 'Diabetes', 'Heart Disease', 'Kidney Disease', 'Tuberculosis', 'Convulsion/ Epilepsy', 'Hyperventilation', 'High Blood Pressure', 'Migraine'];
        @endphp
        @foreach($illnesses as $illness)
        <div class="col-md-4 mb-2">
            <div class="form-check">
                <input class="form-check-input illness-checkbox" type="checkbox" name="medical_history[]" value="{{ $illness }}" id="{{ $illness }}">
                <label class="form-check-label" for="{{ $illness }}">{{ $illness }}</label>
            </div>
        </div>
        @endforeach
        <div class="col-md-12 mt-2">
            <label class="form-label">Others (Pls. Indicate):</label>
            <input type="text" name="other_illness" class="form-control">
        </div>
        <div class="row mt-4">
        <div class="col-md-12 mt-3">
            <label class="form-label">2. Chest X-Ray Result</label>
            <input type="file" name="chest_xray_result" class="form-control health-upload-field" accept=".jpg,.jpeg,.png,.pdf">
            <small class="health-upload-helper">Upload JPG, PNG, or PDF if available.</small>
        </div>
        </div>
          <div class="row mt-4">
        <div class="col-md-12 mt-3">
                    <label class="form-label">3. Medical Certificate</label>
                    <input type="file" name="medical_certificate" class="form-control health-upload-field" accept=".jpg,.jpeg,.png,.pdf">
                    <small class="health-upload-helper">Upload JPG, PNG, or PDF if you have a medical certificate.</small>
                    <label class="form-label" style="margin-top: 12px;">Medical certificate issued by: Dr:</label>
                    <input
                        type="text"
                        name="medical_certificate_issued_by"
                        class="form-control"
                        placeholder="Enter doctor's name"
                        value="{{ old('medical_certificate_issued_by') }}"
                    >
                </div></div>
    </div>

    <div class="row mt-4">
        <div class="col-12 mb-2">
            <label class="form-label">4. Do you have disability?</label>
            <div class="form-check form-check-inline ms-3">
                <input class="form-check-input disability-radio" type="radio" name="has_disability" value="None" id="disabilityNo">
                <label class="form-check-label" for="disabilityNo">None</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input disability-radio" type="radio" name="has_disability" value="Yes" id="disabilityYes">
                <label class="form-check-label" for="disabilityYes">if Yes, What type?</label>
            </div>
            <input type="text" name="disability_type" id="disability_type" class="form-control d-inline-block w-50 ms-2" placeholder="Specify disability">
        </div>
        <div class="col-12 mt-3" id="pwdProofWrapper" style="display: none;">
            <label class="form-label">PWD ID / Proof</label>
            <input type="file" name="pwd_id_proof" id="pwd_id_proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
            <small class="text-muted">Required when disability is marked Yes.</small>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <label class="form-label">5. Additional Information for Students and Medical Conditions:</label>
            <p class="text-muted small italic">As a Parent/ Guardian, I would like to declare that my child has history of allergies to the following:</p>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Food (Please specify):</label>
                    <input type="text" name="food_allergies" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">No Known Allergies:</label>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="no_allergies" id="noAllergiesCheck" value="1">
                        <label class="form-check-label" for="noAllergiesCheck">I confirm no known allergies</label>
                    </div>
                </div>
            </div>

            <label class="form-label mt-2">Medicines:</label>
            <div class="row px-3">
                @php $meds = ['Aspirin', 'Ibuprofen', 'Amoxicillin', 'Mefenamic Acid', 'Penicillin']; @endphp
                @foreach($meds as $med)
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input medicine-checkbox" type="checkbox" name="medicine_allergies[]" value="{{ $med }}">
                        <label class="form-check-label">{{ $med }}</label>
                    </div>
                </div>
                @endforeach
                <div class="col-md-12 mt-2">
                    <input type="text" name="other_med_allergies" class="form-control" placeholder="Others: Specify">
                </div>
                
            </div>
        </div>
    </div>

    </section>

    <section class="form-step" data-step="3">
    <div class="row mt-4">
        <div class="col-md-12 mb-3">
            <label class="form-label">COVID-19 Vaccination History:</label>
            <table class="table table-bordered vax-table mt-2">
                <thead>
                    <tr><th>Dose</th><th>Date Received</th><th>Brand</th></tr>
                </thead>
                <tbody>
                    <tr><td>1st Dose</td><td><input type="date" name="vax_date_1" class="form-control form-control-sm"></td><td><input type="text" name="vax_brand_1" class="form-control form-control-sm"></td></tr>
                    <tr><td>2nd Dose</td><td><input type="date" name="vax_date_2" class="form-control form-control-sm"></td><td><input type="text" name="vax_brand_2" class="form-control form-control-sm"></td></tr>
                    <tr><td>Booster 1st Dose</td><td><input type="date" name="booster_date_1" class="form-control form-control-sm"></td><td><input type="text" name="booster_brand_1" class="form-control form-control-sm"></td></tr>
                    <tr><td>Booster 2nd Dose</td><td><input type="date" name="booster_date_2" class="form-control form-control-sm"></td><td><input type="text" name="booster_brand_2" class="form-control form-control-sm"></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    </section>

    <section class="form-step" data-step="4">
    <div class="step4-note-box">
        <h4>Upload Instructions</h4>
        <ul>
            <li>Upload a clear 2x2 picture in JPEG or PNG format.</li>
            <li>Upload a clear digital signature in PNG or JPG format.</li>
            <li>For best results, use a transparent digital signature image. You may use <strong>remove.bg</strong> to remove the background before uploading.</li>
            <li>Make sure the uploaded files are readable and belong to the student account holder.</li>
        </ul>
    </div>
    <div class="verification-upload-shell">
        <div class="intro-upload">
            <h3>Required Uploads</h3>
            <div class="upload-grid">
                <div class="upload-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 7h4l2-2h4l2 2h4v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7z"></path>
                        <circle cx="12" cy="13" r="3"></circle>
                    </svg>
                    <div>
                        <strong>Upload 2x2 Picture</strong>
                        <span>JPEG / PNG</span>
                    </div>
                    <input type="file" name="student_photo" class="form-control form-control-sm" accept="image/*" required>
                </div>
                <div class="upload-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h9"></path>
                        <path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                    </svg>
                    <div>
                        <strong>Draw or Upload Digital Signature</strong>
                        <span>PNG / JPG</span>
                    </div>
                    <input type="file" name="digital_signature" class="form-control form-control-sm" accept="image/*" required>
                </div>
            </div>
        </div>
    </div>
    </section>

    <div class="cta-row">
        <div id="stepStatusText" class="section-hint" style="margin: 0;">Step 1 of 4</div>
        <div class="cta-group">
            <button type="button" class="btn-health-secondary" id="prevStepBtn" style="display:none;">Back</button>
            <button type="button" class="btn-health-submit" id="nextStepBtn">Save &amp; Continue</button>
            <button type="submit" class="btn-health-submit" id="submitStepBtn" style="display:none;">Submit Health Profile</button>
        </div>
    </div>
    <p class="privacy-copy">
        We value your privacy. All information provided in this form is processed in compliance with data protection standards and is used solely for university clearance.
    </p>
</form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const steps = Array.from(document.querySelectorAll('.form-step'));
    const stepCards = Array.from(document.querySelectorAll('.step-card[data-step-target]'));
    const prevStepBtn = document.getElementById('prevStepBtn');
    const nextStepBtn = document.getElementById('nextStepBtn');
    const submitStepBtn = document.getElementById('submitStepBtn');
    const stepStatusText = document.getElementById('stepStatusText');
    const introTitle = document.querySelector('.intro-copy h2');
    const introBody = document.querySelector('.intro-copy p');
    const stepDescriptions = {
        1: {
            title: 'Personal Information',
            text: 'Please provide complete and truthful information. Type N/A or NONE for fields that do not apply to you. Required fields are marked with a maroon asterisk.',
        },
        2: {
            title: 'Medical History',
            text: 'Review illnesses, supporting records, and condition-specific declarations before moving to the next section.',
        },
        3: {
            title: 'Personal Social History & Vaccination',
            text: 'Complete your vaccination details and related personal history information in this section.',
        },
        4: {
            title: 'Verification & Uploads',
            text: 'Review your entries, confirm the required uploads, and submit your health profile once everything looks correct.',
        }
    };
    const attemptedSteps = new Set();
    let currentStep = 1;

    const validateStep = (stepNumber) => {
        const section = document.querySelector(`.form-step[data-step="${stepNumber}"]`);
        if (!section) {
            return true;
        }

        const customRequired = {
            4: [
                document.querySelector('input[name="student_photo"]'),
                document.querySelector('input[name="digital_signature"]')
            ]
        };

        const controls = [
            ...Array.from(section.querySelectorAll('input, select, textarea')).filter((field) => field.required),
            ...(customRequired[stepNumber] || [])
        ].filter(Boolean);

        for (const field of controls) {
            if (field.disabled) {
                continue;
            }

            const type = (field.type || '').toLowerCase();

            if (type === 'radio') {
                const group = section.querySelectorAll(`input[type="radio"][name="${field.name}"]`);
                if (!Array.from(group).some((radio) => radio.checked)) {
                    return false;
                }
                continue;
            }

            if (type === 'checkbox') {
                if (!field.checked) {
                    return false;
                }
                continue;
            }

            if (type === 'file') {
                if (!field.files || field.files.length === 0) {
                    return false;
                }
                continue;
            }

            if (!String(field.value || '').trim()) {
                return false;
            }
        }

        return true;
    };

    const renderStep = (stepNumber) => {
        currentStep = stepNumber;

        steps.forEach((section) => {
            section.classList.toggle('is-active', Number(section.dataset.step) === stepNumber);
        });

        stepCards.forEach((card, index) => {
            const cardStep = Number(card.dataset.stepTarget);
            const isValid = validateStep(cardStep);
            const isWarning = attemptedSteps.has(cardStep) && !isValid;
            card.classList.toggle('active', cardStep === stepNumber && !isWarning);
            card.classList.toggle('completed', cardStep < stepNumber && isValid);
            card.classList.toggle('warning', isWarning);
            card.classList.toggle('is-clickable', true);
        });

        if (stepStatusText) {
            stepStatusText.textContent = `Step ${stepNumber} of ${steps.length}`;
        }

        if (introTitle && introBody && stepDescriptions[stepNumber]) {
            introTitle.textContent = stepDescriptions[stepNumber].title;
            introBody.textContent = stepDescriptions[stepNumber].text;
        }

        if (prevStepBtn) {
            prevStepBtn.style.display = stepNumber === 1 ? 'none' : '';
        }

        if (nextStepBtn && submitStepBtn) {
            const isFinalStep = stepNumber === steps.length;
            nextStepBtn.style.display = isFinalStep ? 'none' : '';
            submitStepBtn.style.display = isFinalStep ? '' : 'none';
        }

        document.querySelector('.stepper-shell')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    prevStepBtn?.addEventListener('click', function () {
        if (currentStep > 1) {
            renderStep(currentStep - 1);
        }
    });

    nextStepBtn?.addEventListener('click', function () {
        if (!validateStep(currentStep)) {
            attemptedSteps.add(currentStep);
            renderStep(currentStep);
            const activeSection = document.querySelector(`.form-step[data-step="${currentStep}"]`);
            const invalidField = activeSection?.querySelector('[required]');
            invalidField?.focus();
            return;
        }

        attemptedSteps.delete(currentStep);

        if (currentStep < steps.length) {
            renderStep(currentStep + 1);
        }
    });

    stepCards.forEach((card) => {
        card.addEventListener('click', function () {
            const targetStep = Number(card.dataset.stepTarget);
            if (targetStep >= 1 && targetStep <= steps.length) {
                if (targetStep > currentStep && !validateStep(currentStep)) {
                    attemptedSteps.add(currentStep);
                    renderStep(currentStep);
                    return;
                }
                renderStep(targetStep);
            }
        });
    });

    // 1. Medical Illness Logic
    const illnessRadios = document.querySelectorAll('.illness-radio');
    const illnessCheckboxes = document.querySelectorAll('.illness-checkbox');
    const otherIllness = document.querySelector('input[name="other_illness"]');

    function toggleIllness() {
        const isNo = document.getElementById('illnessNo').checked;
        illnessCheckboxes.forEach(cb => {
            cb.disabled = isNo;
            if (isNo) cb.checked = false;
        });
        otherIllness.disabled = isNo;
        if (isNo) otherIllness.value = '';
    }

    // 2. Disability Logic
    const disabilityRadios = document.querySelectorAll('.disability-radio');
    const disabilityType = document.getElementById('disability_type');
    const pwdProofWrapper = document.getElementById('pwdProofWrapper');
    const pwdIdProof = document.getElementById('pwd_id_proof');

    function toggleDisability() {
        const isNone = document.getElementById('disabilityNo').checked;
        const isYes = document.getElementById('disabilityYes').checked;
        disabilityType.disabled = isNone;
        if (isNone) disabilityType.value = '';
        pwdProofWrapper.style.display = isYes ? 'block' : 'none';
        pwdIdProof.required = isYes;
        pwdIdProof.disabled = !isYes;
        if (!isYes) {
            pwdIdProof.value = '';
        }
    }

    // 3. Allergies Logic
    const noAllergiesCheck = document.getElementById('noAllergiesCheck');
    const foodAllergies = document.querySelector('input[name="food_allergies"]');
    const medicineCheckboxes = document.querySelectorAll('.medicine-checkbox');
    const otherMedAllergies = document.querySelector('input[name="other_med_allergies"]');

    function toggleAllergies() {
        const isNoAllergies = noAllergiesCheck.checked;
        foodAllergies.disabled = isNoAllergies;
        otherMedAllergies.disabled = isNoAllergies;
        medicineCheckboxes.forEach(cb => {
            cb.disabled = isNoAllergies;
            if (isNoAllergies) cb.checked = false;
        });
        if (isNoAllergies) {
            foodAllergies.value = '';
            otherMedAllergies.value = '';
        }
    }

    // Listeners
    illnessRadios.forEach(r => r.addEventListener('change', toggleIllness));
    disabilityRadios.forEach(r => r.addEventListener('change', toggleDisability));
    noAllergiesCheck.addEventListener('change', toggleAllergies);

    // Initial Run
    toggleIllness();
    toggleDisability();
    toggleAllergies();
    renderStep(1);

    function forceAccessibilityButtonTheme() {
        document.querySelectorAll('.asw-menu-btn').forEach((button) => {
            button.style.setProperty('right', '20px', 'important');
            button.style.setProperty('bottom', '14px', 'important');
            button.style.setProperty('left', 'auto', 'important');
            button.style.setProperty('top', 'auto', 'important');
            button.style.setProperty('background', '#800000', 'important');
            button.style.setProperty('background-image', 'none', 'important');
            button.style.setProperty('border', '2px solid #5f0012', 'important');
        });
    }

    forceAccessibilityButtonTheme();
    new MutationObserver(forceAccessibilityButtonTheme).observe(document.body, { childList: true, subtree: true });
});
</script>

@include('partials.student_voice_input_support')
</body>
</html>

