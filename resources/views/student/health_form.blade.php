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
            padding: 24px 28px;
            background: linear-gradient(135deg, var(--clinic-maroon) 0%, var(--clinic-maroon-dark) 100%);
            color: #fff;
            border-bottom: 2px solid var(--clinic-yellow);
        }

        .health-header h1 {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 800;
        }

        .health-header p {
            margin: 8px 0 0;
            font-size: 0.95rem;
            opacity: 0.95;
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

        .required {
            color: #b91c1c;
        }
    </style>
</head>
<body>
    @php
        $prefill = $healthFormPrefill ?? [];
    @endphp

    <div class="health-shell">
        <div class="health-header">
            <h1>Student Health Profile</h1>
            <p>Complete personal information and upload required clinic documents.</p>
        </div>

        <div class="section-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('store.health.form') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="student_id" value="{{ old('student_id', $prefill['student_id'] ?? $user->student_id) }}">

                <h2 class="section-title">Personal Information</h2>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Student Number <span class="required">*</span></label>
                        <input type="text" name="student_number" class="form-control" required value="{{ old('student_number', $prefill['student_number'] ?? $user->student_number) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">School Year <span class="required">*</span></label>
                        <input type="text" name="school_year" class="form-control" required value="{{ old('school_year', $prefill['school_year'] ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Course / College <span class="required">*</span></label>
                        <input type="text" name="course_college" class="form-control" required value="{{ old('course_college', $prefill['course_college'] ?? '') }}">
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Home Address <span class="required">*</span></label>
                        <input type="text" name="home_address" class="form-control" required value="{{ old('home_address', $prefill['home_address'] ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Zip Code <span class="required">*</span></label>
                        <input type="text" name="zipcode" class="form-control" required value="{{ old('zipcode', $prefill['zipcode'] ?? '') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Birthday <span class="required">*</span></label>
                        <input type="date" name="birthday" id="birthday" class="form-control" required value="{{ old('birthday', $prefill['birthday'] ?? '') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Age <span class="required">*</span></label>
                        <input type="number" name="age" id="age" class="form-control" min="15" max="100" required value="{{ old('age', $prefill['age'] ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sex <span class="required">*</span></label>
                        <select name="sex" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Male" {{ old('sex', $prefill['sex'] ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('sex', $prefill['sex'] ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Civil Status <span class="required">*</span></label>
                        <select name="civil_status" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Single" {{ old('civil_status', $prefill['civil_status'] ?? '') === 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ old('civil_status', $prefill['civil_status'] ?? '') === 'Married' ? 'selected' : '' }}>Married</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Height (cm) <span class="required">*</span></label>
                        <input type="number" step="0.01" min="0" name="height" class="form-control" required value="{{ old('height', $prefill['height'] ?? '') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Weight (kg) <span class="required">*</span></label>
                        <input type="number" step="0.01" min="0" name="weight" class="form-control" required value="{{ old('weight', $prefill['weight'] ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Blood Type <span class="required">*</span></label>
                        <input type="text" name="blood_type" class="form-control" required value="{{ old('blood_type', $prefill['blood_type'] ?? '') }}">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Contact Number <span class="required">*</span></label>
                        <input type="text" name="contact_no" class="form-control" required value="{{ old('contact_no', $prefill['contact_number'] ?? $user->contact_no) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Guardian Name <span class="required">*</span></label>
                        <input type="text" name="guardian_name" class="form-control" required value="{{ old('guardian_name', $prefill['guardian_name'] ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Guardian Contact <span class="required">*</span></label>
                        <input type="text" name="cellphone" class="form-control" required value="{{ old('cellphone', $prefill['cellphone'] ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Landline (Optional)</label>
                        <input type="text" name="landline" class="form-control" value="{{ old('landline', $prefill['landline'] ?? '') }}">
                    </div>
                </div>

                <h2 class="section-title mt-4">PWD Declaration</h2>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Are you a PWD? <span class="required">*</span></label>
                        <select id="has_disability" name="has_disability" class="form-select" required>
                            <option value="">Select</option>
                            <option value="No" {{ old('has_disability', $prefill['has_disability'] ?? '') === 'No' ? 'selected' : '' }}>No</option>
                            <option value="Yes" {{ old('has_disability', $prefill['has_disability'] ?? '') === 'Yes' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Disability Type <span class="required">*</span></label>
                        <input id="disability_type" type="text" name="disability_type" class="form-control" value="{{ old('disability_type', $prefill['disability_type'] ?? '') }}">
                    </div>
                </div>

                <h2 class="section-title mt-4">Document Uploads</h2>
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
                    <div class="col-md-6">
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
                    <a href="{{ url('/student/account') }}" class="btn btn-health btn-health-back">Back</a>
                    <button type="submit" class="btn btn-health btn-health-submit">Save Health Profile</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const birthdayInput = document.getElementById('birthday');
            const ageInput = document.getElementById('age');
            const disabilitySelect = document.getElementById('has_disability');
            const disabilityTypeInput = document.getElementById('disability_type');
            const pwdProofInput = document.getElementById('pwd_id_proof');

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
                if (!disabilitySelect || !disabilityTypeInput || !pwdProofInput) return;
                const isPwd = disabilitySelect.value === 'Yes';
                disabilityTypeInput.required = isPwd;
                pwdProofInput.required = isPwd;

                if (!isPwd) {
                    disabilityTypeInput.value = '';
                    pwdProofInput.value = '';
                }
            }

            birthdayInput?.addEventListener('change', updateAgeFromBirthday);
            disabilitySelect?.addEventListener('change', togglePwdRequirements);

            updateAgeFromBirthday();
            togglePwdRequirements();
        })();
    </script>

    @include('partials.student_voice_input_support')
</body>
</html>
