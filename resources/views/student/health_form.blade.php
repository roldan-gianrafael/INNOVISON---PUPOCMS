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
/* === PREMIUM GLASS UI UPGRADE === */

:root {
    --clinic-maroon: #800000;
    --clinic-maroon-dark: #5f0012;
    --clinic-glass: rgba(255,255,255,0.75);
}

/* BACKGROUND */
body {
    background:
        linear-gradient(rgba(20, 5, 6, 0.85), rgba(20, 5, 6, 0.9)),
        url('{{ asset('images/PUPBG.jpg') }}') center/cover no-repeat fixed;
}

/* GLASS CARD */
.form-card {
    backdrop-filter: blur(18px);
    background: var(--clinic-glass);
    border: 1px solid rgba(255,255,255,0.2);
}

/* STEPPER PREMIUM */
.step-card {
    transition: all 0.25s ease;
    border-radius: 18px;
}

.step-card:hover {
    transform: translateY(-3px) scale(1.01);
}

.step-card.active {
    transform: scale(1.03);
}

/* INPUT FOCUS */
.form-control:focus,
.form-select:focus {
    transform: scale(1.01);
    transition: all 0.15s ease;
}

/* BUTTON UPGRADE */
.btn-health-submit {
    transition: all 0.25s ease;
}

.btn-health-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 35px rgba(128,0,0,0.35);
}

/* STEP ANIMATION */
.form-step {
    opacity: 0;
    transform: translateY(15px);
    transition: all 0.3s ease;
}

.form-step.is-active {
    opacity: 1;
    transform: translateY(0);
}

/* INPUT ERROR */
.input-error {
    border: 1px solid #dc2626 !important;
    background: #fff0f0;
}

/* SUCCESS STATE */
.input-success {
    border: 1px solid #16a34a !important;
    background: #f0fff4;
}
</style>
</head>
<body>

<div class="container">
    <div class="form-card">
        <div class="intake-shell">
        @if (session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger mb-3">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <strong>Please review the required fields before submitting.</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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

    <div class="col-md-6 form-row-wrapper">
        <div class="form-row">
            <label class="form-label">Full Name<span class="required-mark">*</span></label>
            <input type="text" class="form-control bg-light"
                value="{{ trim(implode(' ', array_filter([optional($linkedAdminProfile)->first_name ?: Auth::user()->first_name, optional($linkedAdminProfile)->middle_name, optional($linkedAdminProfile)->last_name ?: Auth::user()->last_name, optional($linkedAdminProfile)->suffix_name]))) }}"
                readonly>
        </div>
    </div>

    <div class="col-md-6 form-row-wrapper">
        <div class="form-row">
            <label class="form-label">PUP Student No.<span class="required-mark">*</span></label>
            <input type="text" class="form-control bg-light"
                value="{{ Auth::user()->student_id }}" readonly>
        </div>
    </div>

    <div class="col-md-6 form-row-wrapper">
        <div class="form-row">
            <label class="form-label">Home Address<span class="required-mark">*</span></label>
            <input type="text" name="home_address" class="form-control"
                value="{{ old('home_address') }}">
        </div>
    </div>

    <div class="col-md-6 form-row-wrapper">
        <div class="form-row">
            <label class="form-label">School Year</label>
            <input type="text" name="school_year" class="form-control"
                value="{{ old('school_year', '2025-2026') }}">
        </div>
    </div>

    <div class="col-md-6 form-row-wrapper">
        <div class="form-row">
            <label class="form-label">Height</label>
            <input type="text" name="height" class="form-control"
                value="{{ old('height') }}">
        </div>
    </div>

    <div class="col-md-6 form-row-wrapper">
        <div class="form-row">
            <label class="form-label">Weight</label>
            <input type="text" name="weight" class="form-control"
                value="{{ old('weight') }}">
        </div>
    </div>

    <div class="col-md-6 form-row-wrapper">
        <div class="form-row">
            <label class="form-label">Age<span class="required-mark">*</span></label>
            <input type="number" name="age" class="form-control"
                value="{{ old('age', $calculatedAge) }}"
                {{ $calculatedAge ? 'readonly' : '' }}>
        </div>
    </div>

    <div class="col-md-6 form-row-wrapper">
        <div class="form-row">
            <label class="form-label">Sex<span class="required-mark">*</span></label>
            <select name="sex" class="form-select">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
    </div>

    <div class="col-md-6 form-row-wrapper">
        <div class="form-row">
            <label class="form-label">Civil Status<span class="required-mark">*</span></label>
            <select name="civil_status" class="form-select">
                <option disabled selected>Select</option>
                <option>Single</option>
                <option>Married</option>
            </select>
        </div>
    </div>

    <div class="col-md-6 form-row-wrapper">
        <div class="form-row">
            <label class="form-label">Course / College<span class="required-mark">*</span></label>
            <input type="text" name="course_college" class="form-control"
                value="{{ old('course_college', Auth::user()->course) }}">
        </div>
    </div>

    <div class="col-md-6 form-row-wrapper">
        <div class="form-row">
            <label class="form-label">Blood Type</label>
            <input type="text" name="blood_type" class="form-control"
                value="{{ old('blood_type') }}">
        </div>
    </div>

    <div class="col-md-6 form-row-wrapper">
        <div class="form-row">
            <label class="form-label">Email Address<span class="required-mark">*</span></label>
            <input type="email" name="email" class="form-control"
                value="{{ Auth::user()->email }}" readonly>
        </div>
    </div>

    <div class="col-md-6 form-row-wrapper">
        <div class="form-row">
            <label class="form-label">Guardian Name<span class="required-mark">*</span></label>
            <input type="text" name="guardian_name" class="form-control"
                value="{{ old('guardian_name') }}">
        </div>
    </div>

    <div class="col-md-6 form-row-wrapper">
        <div class="form-row">
            <label class="form-label">Phone Number<span class="required-mark">*</span></label>
            <input type="text" name="cellphone" class="form-control"
                value="{{ old('cellphone') }}">
        </div>
    </div>

</div>

</section>

    <section class="form-step" data-step="2">
    <div class="row mt-3">
        <div class="col-12 mb-2">
            <label class="form-label">1. Do you need medical attention or has known medical illness?</label>
            <div class="form-check form-check-inline ms-3">
                <input class="form-check-input illness-radio" type="radio" name="has_illness" value="No" id="illnessNo" {{ old('has_illness') === 'No' ? 'checked' : '' }}>
                <label class="form-check-label" for="illnessNo">No</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input illness-radio" type="radio" name="has_illness" value="Yes" id="illnessYes" {{ old('has_illness') === 'Yes' ? 'checked' : '' }}>
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
            <input type="text" name="other_illness" class="form-control" value="{{ old('other_illness') }}">
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
                <input class="form-check-input disability-radio" type="radio" name="has_disability" value="None" id="disabilityNo" {{ old('has_disability') === 'None' ? 'checked' : '' }}>
                <label class="form-check-label" for="disabilityNo">None</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input disability-radio" type="radio" name="has_disability" value="Yes" id="disabilityYes" {{ old('has_disability') === 'Yes' ? 'checked' : '' }}>
                <label class="form-check-label" for="disabilityYes">if Yes, What type?</label>
            </div>
            <input type="text" name="disability_type" id="disability_type" class="form-control d-inline-block w-50 ms-2" placeholder="Specify disability" value="{{ old('disability_type') }}">
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
                    <input type="text" name="food_allergies" class="form-control" value="{{ old('food_allergies') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">No Known Allergies:</label>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="no_allergies" id="noAllergiesCheck" value="1" {{ old('no_allergies') ? 'checked' : '' }}>
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
                    <input type="text" name="other_med_allergies" class="form-control" placeholder="Others: Specify" value="{{ old('other_med_allergies') }}">
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

    const validationErrors = @json($errors->keys());
    const steps = Array.from(document.querySelectorAll('.form-step'));
    const stepCards = Array.from(document.querySelectorAll('.step-card[data-step-target]'));

    const prevStepBtn = document.getElementById('prevStepBtn');
    const nextStepBtn = document.getElementById('nextStepBtn');
    const submitStepBtn = document.getElementById('submitStepBtn');
    const stepStatusText = document.getElementById('stepStatusText');

    const introTitle = document.querySelector('.intro-copy h2');
    const introBody = document.querySelector('.intro-copy p');

    const stepDescriptions = {
        1: { title: 'Personal Information', text: 'Please provide complete and truthful information.' },
        2: { title: 'Medical History', text: 'Review illnesses and supporting records.' },
        3: { title: 'Vaccination', text: 'Complete vaccination details.' },
        4: { title: 'Uploads', text: 'Upload required documents before submission.' }
    };

    let currentStep = 1;
    const attemptedSteps = new Set();

    // =========================
    // 🔥 VALIDATION (IMPROVED)
    // =========================
    const validateStep = (stepNumber) => {
        const section = document.querySelector(`.form-step[data-step="${stepNumber}"]`);
        if (!section) return true;

        const fields = Array.from(section.querySelectorAll('input, select, textarea'));

        for (const field of fields) {
            if (field.disabled) continue;

            const type = field.type;

            // RADIO GROUP FIX
            if (type === 'radio') {
                const group = section.querySelectorAll(`input[name="${field.name}"]`);
                if (!Array.from(group).some(r => r.checked)) {
                    return false;
                }
                continue;
            }

            // FILE
            if (type === 'file') {
                if (field.required && (!field.files || field.files.length === 0)) {
                    return false;
                }
                continue;
            }

            // NORMAL INPUT
            if (field.required && !field.value.trim()) {
                return false;
            }
        }

        return true;
    };

    // =========================
    // 🔥 FIND ERROR STEP
    // =========================
    const resolveStepFromErrors = () => {
        const map = {
            home_address: 1,
            age: 1,
            sex: 1,
            civil_status: 1,
            course_college: 1,
            guardian_name: 1,
            cellphone: 1,
            has_illness: 2,
            has_disability: 2,
            student_photo: 4,
            digital_signature: 4,
        };

        for (const key of validationErrors) {
            if (map[key]) return map[key];
        }

        return 1;
    };

    // =========================
    // 🔥 RENDER STEP
    // =========================
    const renderStep = (step) => {
        currentStep = step;

        steps.forEach(s => {
            s.classList.toggle('is-active', Number(s.dataset.step) === step);
        });

        stepCards.forEach(card => {
            const s = Number(card.dataset.stepTarget);
            const valid = validateStep(s);
            const warning = attemptedSteps.has(s) && !valid;

            card.classList.toggle('active', s === step && !warning);
            card.classList.toggle('completed', s < step && valid);
            card.classList.toggle('warning', warning);
        });

        stepStatusText.textContent = `Step ${step} of ${steps.length}`;

        if (stepDescriptions[step]) {
            introTitle.textContent = stepDescriptions[step].title;
            introBody.textContent = stepDescriptions[step].text;
        }

        prevStepBtn.style.display = step === 1 ? 'none' : '';
        nextStepBtn.style.display = step === steps.length ? 'none' : '';
        submitStepBtn.style.display = step === steps.length ? '' : '';

        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // =========================
    // 🔥 NAVIGATION
    // =========================
    nextStepBtn.addEventListener('click', () => {
        if (!validateStep(currentStep)) {
            attemptedSteps.add(currentStep);
            renderStep(currentStep);

            const firstInvalid = document.querySelector(`.form-step[data-step="${currentStep}"] input:invalid, select:invalid`);
            if (firstInvalid) firstInvalid.focus();

            return;
        }

        attemptedSteps.delete(currentStep);
        renderStep(currentStep + 1);
    });

    prevStepBtn.addEventListener('click', () => {
        renderStep(currentStep - 1);
    });

    stepCards.forEach(card => {
        card.addEventListener('click', () => {
            const step = Number(card.dataset.stepTarget);

            if (step > currentStep && !validateStep(currentStep)) {
                attemptedSteps.add(currentStep);
                renderStep(currentStep);
                return;
            }

            renderStep(step);
        });
    });

    // =========================
    // 🔥 MEDICAL LOGIC
    // =========================
    const illnessNo = document.getElementById('illnessNo');
    const illnessCheckboxes = document.querySelectorAll('.illness-checkbox');
    const otherIllness = document.querySelector('input[name="other_illness"]');

    function toggleIllness() {
        const disabled = illnessNo.checked;

        illnessCheckboxes.forEach(cb => {
            cb.disabled = disabled;
            if (disabled) cb.checked = false;
        });

        otherIllness.disabled = disabled;
        if (disabled) otherIllness.value = '';
    }

    document.querySelectorAll('.illness-radio').forEach(r => r.addEventListener('change', toggleIllness));

    // =========================
    // 🔥 DISABILITY LOGIC
    // =========================
    const disabilityNo = document.getElementById('disabilityNo');
    const disabilityYes = document.getElementById('disabilityYes');
    const disabilityType = document.getElementById('disability_type');
    const pwdWrapper = document.getElementById('pwdProofWrapper');
    const pwdProof = document.getElementById('pwd_id_proof');

    function toggleDisability() {
        if (disabilityNo.checked) {
            disabilityType.value = '';
            disabilityType.disabled = true;
            pwdWrapper.style.display = 'none';
            pwdProof.required = false;
        } else {
            disabilityType.disabled = false;
            pwdWrapper.style.display = 'block';
            pwdProof.required = true;
        }
    }

    document.querySelectorAll('.disability-radio').forEach(r => r.addEventListener('change', toggleDisability));

    // =========================
    // 🔥 ALLERGY LOGIC
    // =========================
    const noAllergies = document.getElementById('noAllergiesCheck');
    const food = document.querySelector('input[name="food_allergies"]');
    const meds = document.querySelectorAll('.medicine-checkbox');
    const otherMeds = document.querySelector('input[name="other_med_allergies"]');

    function toggleAllergies() {
        const disabled = noAllergies.checked;

        food.disabled = disabled;
        otherMeds.disabled = disabled;

        meds.forEach(cb => {
            cb.disabled = disabled;
            if (disabled) cb.checked = false;
        });

        if (disabled) {
            food.value = '';
            otherMeds.value = '';
        }
    }

    noAllergies.addEventListener('change', toggleAllergies);

    // =========================
    // 🔥 INIT
    // =========================
    toggleIllness();
    toggleDisability();
    toggleAllergies();

    const initialStep = validationErrors.length ? resolveStepFromErrors() : 1;
    if (validationErrors.length) attemptedSteps.add(initialStep);

    renderStep(initialStep);

    // =========================
    // 🔥 ACCESSIBILITY BUTTON FIX
    // =========================
    function fixAccessibilityBtn() {
        document.querySelectorAll('.asw-menu-btn').forEach(btn => {
            btn.style.right = '20px';
            btn.style.bottom = '14px';
            btn.style.background = '#800000';
        });
    }

    fixAccessibilityBtn();
    new MutationObserver(fixAccessibilityBtn).observe(document.body, { childList: true, subtree: true });

});
</script>

@include('partials.student_voice_input_support')
</body>
</html>
