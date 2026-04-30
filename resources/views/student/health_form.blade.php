<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fill Up - Health Profile</title>
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
            --clinic-maroon: #7f1d2d;
            --clinic-maroon-dark: #5f0012;
            --clinic-yellow: #facc15;
            --panel: #ffffff;
            --field: #f8fafc;
            --border: #d1d5db;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background:
                linear-gradient(rgba(39, 14, 17, 0.82), rgba(22, 8, 8, 0.84)),
                url('{{ asset('images/PUPBG.jpg') }}') center center / cover no-repeat fixed;
            padding: 28px 12px;
        }

        .health-shell {
            max-width: 980px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.97);
            border: 1px solid rgba(127, 29, 45, 0.16);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.18);
            overflow: hidden;
        }

        .health-header {
            height: 12px;
            background: linear-gradient(135deg, var(--clinic-maroon) 0%, var(--clinic-maroon-dark) 100%);
            border-bottom: 2px solid var(--clinic-yellow);
        }

        .form-intro {
            margin-bottom: 18px;
        }

        .form-intro h1 {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 800;
            color: #70131b;
        }

        .form-intro p {
            margin: 8px 0 0;
            font-size: 0.95rem;
            color: #4b5563;
        }

        .section-body {
            padding: 24px 28px 28px;
        }

        .section-title {
            margin: 0 0 16px;
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--clinic-maroon);
            border-bottom: 2px solid rgba(127, 29, 45, 0.12);
            padding-bottom: 8px;
        }

        .stepper-shell {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            min-height: 91px;
            position: fixed;
            left: 50%;
            top: 14px;
            transform: translateX(-50%);
            width: min(972px, calc(100vw - 10px));
            box-sizing: border-box;
            z-index: 70;
            background: rgba(255, 255, 255, 0.92);
            padding: 10px;
            border-radius: 16px;
            border: 1px solid rgba(127, 29, 45, 0.12);
            backdrop-filter: blur(8px);
        }

        .stepper-spacer {
            height: 109px;
        }

        .step-chip {
            border: 1px solid rgba(127, 29, 45, 0.2);
            border-radius: 14px;
            padding: 12px 14px;
            background: #fff7d6;
            opacity: 0.78;
            transition: all 0.2s ease;
        }

        .step-chip small {
            display: block;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-size: 0.72rem;
            margin-bottom: 2px;
            font-weight: 700;
        }

        .step-chip strong {
            color: #70131b;
            font-size: 0.95rem;
        }

        .step-chip.is-active {
            background: linear-gradient(135deg, var(--clinic-maroon) 0%, var(--clinic-maroon-dark) 100%);
            border-color: transparent;
            opacity: 1;
            box-shadow: 0 8px 20px rgba(127, 29, 45, 0.24);
        }

        .step-chip.is-active small,
        .step-chip.is-active strong {
            color: #fff;
        }

        .profile-readonly-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 16px;
        }

        .readonly-item {
            border: 1px solid rgba(127, 29, 45, 0.12);
            background: #fff;
            border-radius: 12px;
            padding: 10px 12px;
        }

        .readonly-item small {
            display: block;
            color: #6b7280;
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .readonly-item strong {
            color: #111827;
            font-size: 0.93rem;
        }

        .form-label {
            font-weight: 700;
            color: #111827;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--field);
            min-height: 46px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: rgba(127, 29, 45, 0.5);
            box-shadow: 0 0 0 0.18rem rgba(127, 29, 45, 0.12);
        }

        .upload-card {
            border: 1px dashed rgba(127, 29, 45, 0.35);
            background: linear-gradient(180deg, #fffef6 0%, #fff8dc 100%);
            border-radius: 14px;
            padding: 14px;
            height: 100%;
        }

        .upload-card strong {
            display: block;
            color: #70131b;
            margin-bottom: 6px;
        }

        .upload-card small {
            color: #5b6470;
            display: block;
            margin-top: 6px;
        }

        .btn-row {
            margin-top: 22px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-health {
            border: none;
            border-radius: 12px;
            padding: 12px 18px;
            font-weight: 700;
        }

        .btn-health-back {
            background: #e5e7eb;
            color: #111827;
        }

        .btn-health-submit {
            background: linear-gradient(135deg, var(--clinic-maroon) 0%, var(--clinic-maroon-dark) 100%);
            color: #fff;
            box-shadow: 0 10px 22px rgba(127, 29, 45, 0.28);
        }

        .btn-health-next {
            background: linear-gradient(135deg, var(--clinic-maroon) 0%, var(--clinic-maroon-dark) 100%);
            color: #fff;
            box-shadow: 0 10px 22px rgba(127, 29, 45, 0.28);
        }

        .required {
            color: #b91c1c;
        }

        .pwd-toggle {
            display: flex;
            gap: 10px;
            margin-top: 4px;
        }

        .pwd-radio {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .pwd-option {
            min-width: 92px;
            text-align: center;
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            color: #334155;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .pwd-radio:checked + .pwd-option {
            background: linear-gradient(135deg, var(--clinic-maroon) 0%, var(--clinic-maroon-dark) 100%);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 8px 16px rgba(127, 29, 45, 0.2);
        }

        #pwdUploadWrap {
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        #pwdUploadWrap.is-hidden {
            display: none;
        }

        .step-panel.is-hidden {
            display: none;
        }

        .step-one-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .form-field {
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(127, 29, 45, 0.12);
            background: #fff;
            border-radius: 12px;
            padding: 10px 12px;
        }

        .form-field.span-2 {
            grid-column: span 2;
        }

        .form-field .form-label {
            display: block;
            color: #6b7280;
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .form-field .form-control,
        .form-field .form-select {
            border: 0;
            background: transparent;
            box-shadow: none;
            border-radius: 0;
            min-height: 24px;
            padding: 0;
            color: #111827;
            font-weight: 700;
        }

        .form-field .form-control:focus,
        .form-field .form-select:focus {
            border: 0;
            box-shadow: none;
            background: transparent;
        }

        .step-fill-note {
            margin: 0 0 12px;
            color: #7f1d2d;
            font-size: 0.84rem;
            font-weight: 700;
        }

        .field-helper {
            margin-top: 6px;
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 600;
        }

        .privacy-note {
            margin: 14px 0 0;
            text-align: center;
            font-size: 0.78rem;
            color: #5b6470;
            line-height: 1.5;
        }

        @media (max-width: 768px) {
            .stepper-shell,
            .profile-readonly-grid,
            .step-one-grid {
                grid-template-columns: 1fr;
            }

            .form-field.span-2 {
                grid-column: span 1;
            }

            .stepper-shell {
                top: 8px;
                width: calc(100vw - 16px);
            }

            .stepper-spacer {
                height: 119px;
            }
        }
    </style>
</head>
<body>
    @php
        $prefill = $healthFormPrefill ?? [];
    @endphp

    <div class="health-shell">
        <div class="health-header"></div>

        <div class="section-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            @php
                $selectedPwd = old('has_disability', $prefill['has_disability'] ?? 'No');
                $stepTwoErrorFields = ['has_disability', 'disability_type', 'medical_certificate', 'chest_xray_result', 'pwd_id_proof', 'student_photo'];
                $startStep = collect($stepTwoErrorFields)->contains(fn ($field) => $errors->has($field)) ? 2 : 1;
            @endphp

            <div class="stepper-shell">
                <div class="step-chip {{ $startStep === 1 ? 'is-active' : '' }}" id="chipStep1">
                    <small>Step 1</small>
                    <strong>Information</strong>
                </div>
                <div class="step-chip {{ $startStep === 2 ? 'is-active' : '' }}" id="chipStep2">
                    <small>Step 2</small>
                    <strong>Upload Requirements</strong>
                </div>
            </div>
            <div class="stepper-spacer"></div>

            <form action="{{ route('store.health.form') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="course_college" value="{{ old('course_college', $prefill['course_college'] ?? $user->course) }}">
                <input type="hidden" name="student_number" value="{{ old('student_number', $prefill['student_number'] ?? $user->student_number) }}">

                <div class="step-panel {{ $startStep === 2 ? 'is-hidden' : '' }}" id="stepPanel1">
                    <h2 class="section-title">Step 1: Information</h2>
                    <div class="form-intro">
                        <h1>Student Health Profile</h1>
                        <p>Complete personal information and upload required clinic documents.</p>
                    </div>
                    <div class="profile-readonly-grid">
                        <div class="readonly-item">
                            <small>Student Name</small>
                            <strong>{{ old('full_name', $prefill['full_name'] ?? $user->name) }}</strong>
                        </div>
                        <div class="readonly-item">
                            <small>Course</small>
                            <strong>{{ old('course_college', $prefill['course_college'] ?? $user->course) }}</strong>
                        </div>
                        <div class="readonly-item">
                            <small>Email</small>
                            <strong>{{ old('email', $prefill['email'] ?? $user->email) }}</strong>
                        </div>
                        <div class="readonly-item">
                            <small>Student Number</small>
                            <strong>{{ old('student_number', $prefill['student_number'] ?? $user->student_number) }}</strong>
                        </div>
                    </div>

                    <p class="step-fill-note">Please fill in this fields.</p>
                    <div class="step-one-grid">
                        <div class="form-field">
                            <label class="form-label">School Year <span class="required">*</span></label>
                            <input type="text" name="school_year" class="form-control" required value="{{ old('school_year', $prefill['school_year'] ?? '') }}">
                            <p class="field-helper">Type NONE if nothing to add.</p>
                        </div>

                        <div class="form-field span-2">
                            <label class="form-label">Home Address <span class="required">*</span></label>
                            <input type="text" name="home_address" class="form-control" readonly required value="{{ old('home_address', $prefill['home_address'] ?? '') }}">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Zip Code <span class="required">*</span></label>
                            <input type="text" name="zipcode" class="form-control" readonly required value="{{ old('zipcode', $prefill['zipcode'] ?? '') }}">
                        </div>

                        <div class="form-field">
                            <label class="form-label">Birthday <span class="required">*</span></label>
                            <input type="date" name="birthday" id="birthday" class="form-control" readonly required value="{{ old('birthday', $prefill['birthday'] ?? '') }}">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Age <span class="required">*</span></label>
                            <input type="number" name="age" id="age" class="form-control" min="15" max="100" readonly required value="{{ old('age', $prefill['age'] ?? '') }}">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Gender <span class="required">*</span></label>
                            <input type="text" name="sex" class="form-control" readonly required value="{{ old('sex', $prefill['sex'] ?? '') }}">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Civil Status <span class="required">*</span></label>
                            <input type="text" name="civil_status" class="form-control" readonly required value="{{ old('civil_status', $prefill['civil_status'] ?? '') }}">
                        </div>

                        <div class="form-field">
                            <label class="form-label">Height (cm) <span class="required">*</span></label>
                            <input type="number" step="0.01" min="0" name="height" class="form-control" required value="{{ old('height', $prefill['height'] ?? '') }}">
                            <p class="field-helper">Enter a numeric value.</p>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Weight (kg) <span class="required">*</span></label>
                            <input type="number" step="0.01" min="0" name="weight" class="form-control" required value="{{ old('weight', $prefill['weight'] ?? '') }}">
                            <p class="field-helper">Enter a numeric value.</p>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Blood Type <span class="required">*</span></label>
                            <select name="blood_type" class="form-select" required>
                                @php
                                    $selectedBloodType = old('blood_type', $prefill['blood_type'] ?? '');
                                    $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'Unknown'];
                                @endphp
                                <option value="">Select Blood Type</option>
                                @foreach ($bloodTypes as $bloodType)
                                    <option value="{{ $bloodType }}" {{ $selectedBloodType === $bloodType ? 'selected' : '' }}>{{ $bloodType }}</option>
                                @endforeach
                            </select>
                            <p class="field-helper">Type NONE if nothing to add.</p>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Contact Number <span class="required">*</span></label>
                            <input type="text" name="contact_no" class="form-control" readonly required value="{{ old('contact_no', $prefill['contact_number'] ?? $user->contact_no) }}">
                        </div>

                        <div class="form-field">
                            <label class="form-label">Guardian Name <span class="required">*</span></label>
                            <input type="text" name="guardian_name" class="form-control" required value="{{ old('guardian_name', $prefill['guardian_name'] ?? '') }}">
                            <p class="field-helper">Type NONE if nothing to add.</p>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Guardian Contact <span class="required">*</span></label>
                            <input type="text" name="cellphone" class="form-control" required value="{{ old('cellphone', $prefill['cellphone'] ?? '') }}">
                            <p class="field-helper">Type NONE if nothing to add.</p>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Landline (Optional)</label>
                            <input type="text" name="landline" class="form-control" value="{{ old('landline', $prefill['landline'] ?? '') }}">
                            <p class="field-helper">Type NONE if nothing to add.</p>
                        </div>
                    </div>

                    <div class="btn-row">
                        <a href="{{ url('/student/account') }}" class="btn btn-health btn-health-back">Back</a>
                        <button type="button" class="btn btn-health btn-health-next" id="nextToStep2">Next</button>
                    </div>
                    <p class="privacy-note">
                        Data Privacy Notice: The information you provide is collected for school clinic documentation and health clearance processing only, in compliance with school data privacy requirements.
                    </p>
                </div>

                <div class="step-panel {{ $startStep === 1 ? 'is-hidden' : '' }}" id="stepPanel2">
                    <h2 class="section-title">Step 2: Uploading Documents</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-field">
                                <label class="form-label">Are you a PWD? <span class="required">*</span></label>
                                <div class="pwd-toggle" id="pwdToggle">
                                    <input class="pwd-radio" type="radio" name="has_disability" id="pwd_no" value="No" required {{ $selectedPwd !== 'Yes' ? 'checked' : '' }}>
                                    <label class="pwd-option" for="pwd_no">No</label>
                                    <input class="pwd-radio" type="radio" name="has_disability" id="pwd_yes" value="Yes" {{ $selectedPwd === 'Yes' ? 'checked' : '' }}>
                                    <label class="pwd-option" for="pwd_yes">Yes</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-field">
                                <label class="form-label">Disability Type <span class="required">*</span></label>
                                <input id="disability_type" type="text" name="disability_type" class="form-control" value="{{ old('disability_type', $prefill['disability_type'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <h2 class="section-title mt-4">Required Uploads</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="upload-card">
                                <strong>Medical Certificate (PDF) <span class="required">*</span></strong>
                                <input type="file" name="medical_certificate" class="form-control" accept=".pdf,application/pdf" required>
                                <small>Allowed: PDF only, max 4MB.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="upload-card">
                                <strong>Chest X-ray Result (PDF) <span class="required">*</span></strong>
                                <input type="file" name="chest_xray_result" class="form-control" accept=".pdf,application/pdf" required>
                                <small>Allowed: PDF only, max 4MB.</small>
                            </div>
                        </div>
                        <div class="col-md-6" id="pwdUploadWrap">
                            <div class="upload-card">
                                <strong>PWD ID (PDF, if PWD is Yes)</strong>
                                <input id="pwd_id_proof" type="file" name="pwd_id_proof" class="form-control" accept=".pdf,application/pdf">
                                <small>Required only when PWD = Yes.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="upload-card">
                                <strong>2x2 Photo (Image) <span class="required">*</span></strong>
                                <input type="file" name="student_photo" class="form-control" accept=".jpg,.jpeg,.png,image/jpeg,image/png" required>
                                <small>Allowed: JPG/PNG only, max 2MB.</small>
                            </div>
                        </div>
                    </div>

                    <div class="btn-row">
                        <button type="button" class="btn btn-health btn-health-back" id="backToStep1">Back</button>
                        <button type="submit" class="btn btn-health btn-health-submit">Save Health Profile</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const form = document.querySelector('form[action="{{ route('store.health.form') }}"]');
            const stepPanel1 = document.getElementById('stepPanel1');
            const stepPanel2 = document.getElementById('stepPanel2');
            const chipStep1 = document.getElementById('chipStep1');
            const chipStep2 = document.getElementById('chipStep2');
            const nextToStep2Btn = document.getElementById('nextToStep2');
            const backToStep1Btn = document.getElementById('backToStep1');
            const birthdayInput = document.getElementById('birthday');
            const ageInput = document.getElementById('age');
            const disabilityRadios = document.querySelectorAll('input[name="has_disability"]');
            const disabilityTypeInput = document.getElementById('disability_type');
            const pwdProofInput = document.getElementById('pwd_id_proof');
            const pwdUploadWrap = document.getElementById('pwdUploadWrap');
            let currentStep = {{ $startStep }};

            function setStep(step) {
                currentStep = step;
                const showStep1 = step === 1;
                stepPanel1?.classList.toggle('is-hidden', !showStep1);
                stepPanel2?.classList.toggle('is-hidden', showStep1);
                chipStep1?.classList.toggle('is-active', showStep1);
                chipStep2?.classList.toggle('is-active', !showStep1);
            }

            function validateStepOne() {
                if (!stepPanel1) return true;
                const requiredFields = Array.from(stepPanel1.querySelectorAll('input[required], select[required], textarea[required]'));
                let isValid = true;

                requiredFields.forEach((field) => {
                    if (typeof field.reportValidity === 'function') {
                        const valid = field.reportValidity();
                        if (!valid && isValid) {
                            field.focus();
                            isValid = false;
                        }
                    } else if (!field.checkValidity()) {
                        if (isValid) {
                            field.focus();
                            isValid = false;
                        }
                    }
                });

                return isValid;
            }

            function updateAgeFromBirthday() {
                if (!birthdayInput || !ageInput || !birthdayInput.value) return;
                const birthday = new Date(birthdayInput.value);
                if (Number.isNaN(birthday.getTime())) return;

                const today = new Date();
                let age = today.getFullYear() - birthday.getFullYear();
                const monthDiff = today.getMonth() - birthday.getMonth();

                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthday.getDate())) {
                    age--;
                }

                if (age >= 0) {
                    ageInput.value = age;
                }
            }

            function togglePwdRequirements() {
                if (!disabilityTypeInput || !pwdProofInput) return;
                const selected = document.querySelector('input[name="has_disability"]:checked');
                const isPwd = selected?.value === 'Yes';

                disabilityTypeInput.required = isPwd;
                disabilityTypeInput.disabled = !isPwd;
                pwdProofInput.required = isPwd;
                pwdProofInput.disabled = !isPwd;
                pwdUploadWrap?.classList.toggle('is-hidden', !isPwd);

                if (!isPwd) {
                    disabilityTypeInput.value = '';
                    pwdProofInput.value = '';
                }
            }

            birthdayInput?.addEventListener('change', updateAgeFromBirthday);
            disabilityRadios.forEach((radio) => {
                radio.addEventListener('change', togglePwdRequirements);
            });
            nextToStep2Btn?.addEventListener('click', () => {
                if (!validateStepOne()) {
                    return;
                }
                setStep(2);
                stepPanel2?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
            backToStep1Btn?.addEventListener('click', () => {
                setStep(1);
                stepPanel1?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
            form?.addEventListener('submit', (event) => {
                if (currentStep === 1) {
                    event.preventDefault();
                    if (!validateStepOne()) {
                        return;
                    }
                    setStep(2);
                    return;
                }
            });

            updateAgeFromBirthday();
            togglePwdRequirements();
            setStep(currentStep);
        })();
    </script>

    @include('partials.student_voice_input_support')
</body>
</html>
