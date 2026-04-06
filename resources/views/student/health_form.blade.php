<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fill Up - Student Health Information Form</title>
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
                radial-gradient(circle at top left, rgba(128, 0, 0, 0.08), transparent 28%),
                linear-gradient(180deg, #f3f5f7 0%, #e9edf0 100%);
            padding: 34px 0 48px;
            font-family: 'Segoe UI', sans-serif;
            color: var(--clinic-text);
        }
        .form-card {
            background: var(--clinic-panel);
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
        }
        .stepper-track {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
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
            background: #f7ebee;
            border-color: rgba(128, 0, 0, 0.16);
            color: var(--clinic-maroon);
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
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(280px, 0.8fr);
            gap: 18px;
            align-items: stretch;
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
        .section-hint {
            margin: 10px 0 0;
            color: var(--clinic-muted);
            font-size: 0.92rem;
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
            justify-content: flex-end;
            margin-top: 34px;
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
            .intro-panel {
                grid-template-columns: 1fr;
            }
            .stepper-track {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 768px) {
            body {
                padding: 18px 0 28px;
            }
            .form-card {
                padding: 20px;
                border-radius: 20px;
            }
            .upload-grid,
            .stepper-track {
                grid-template-columns: 1fr;
            }
            .cta-row {
                justify-content: stretch;
            }
            .btn-health-submit {
                width: 100%;
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
                <div class="step-card active">
                    <div class="step-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21a8 8 0 0 0-16 0"></path>
                            <circle cx="12" cy="8" r="4"></circle>
                        </svg>
                    </div>
                    <div class="step-copy">
                        <small>Step 1</small>
                        <strong>Personal Information</strong>
                    </div>
                </div>
                <div class="step-card">
                    <div class="step-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 7h16"></path>
                            <path d="M7 7v10a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V7"></path>
                            <path d="M10 11h4"></path>
                            <path d="M12 9v4"></path>
                        </svg>
                    </div>
                    <div class="step-copy">
                        <small>Step 2</small>
                        <strong>Medical History</strong>
                    </div>
                </div>
                <div class="step-card">
                    <div class="step-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z"></path>
                            <path d="M9 12l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="step-copy">
                        <small>Step 3</small>
                        <strong>Personal Social History & Vaccination</strong>
                    </div>
                </div>
                <div class="step-card">
                    <div class="step-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16l4-3 4 3 4-3 4 3V8z"></path>
                            <path d="M14 2v6h6"></path>
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
        <form action="{{ route('store.health.form') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="section-title">Step 1. Personal Information</div>
    <p class="section-hint">Review your student identity details and complete the core contact and profile information below.</p>
    
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

    <div class="section-title">Step 2. Medical History</div>
    
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

    <div class="section-title">Step 3. Personal Social History & Vaccination</div>
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

    <div class="section-title">Step 4. Verification & Uploads</div>
    <p class="section-hint">Review your entries, confirm your supporting documents, and continue to submission.</p>

    <div class="cta-row">
        <button type="submit" class="btn-health-submit">Save &amp; Continue</button>
    </div>
</form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>

@include('partials.student_voice_input_support')
</body>
</html>

