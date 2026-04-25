<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Health Information Form</title>
    <style>
        @page {
            size: 8.5in 13in;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            color: #000;
            background: #fff;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }

        .print-container {
            width: 100%;
            padding: 0.2in 0.5in;
            box-sizing: border-box;
            line-height: 1.2;
        }

        .header-section {
            display: flex;
            align-items: center;
            position: relative;
            margin-bottom: 8px;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin-right: 15px;
            margin-left: 50px;
        }

        .header-text p {
            margin: 0;
            line-height: 1.3;
        }

        .univ-name { font-size: 15px; font-weight: bold; }
        .dept-name { font-size: 17px; font-weight: bold; }

        .photo-box {
            position: absolute;
            right: 0;
            top: 0;
            width: 150px;
            height: 130px;
            border: 1px solid #000;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .form-title {
            text-align: center;
            font-weight: bold;
            font-style: italic;
            font-size: 16px;
            margin: 18px 0;
        }

        .section-header {
            font-weight: bold;
            font-style: italic;
            margin-top: 12px;
            text-transform: uppercase;
            font-size: 13px;
            padding-left: 5px;
        }

        .row {
            display: flex;
            margin-bottom: 6px;
            gap: 10px;
            align-items: baseline;
        }

        .field {
            border-bottom: 1px solid #000;
            flex: 1;
            padding-left: 5px;
            min-height: 18px;
            font-size: 14px;
            font-weight: bold;
            color: #000;
        }

        .label, .labels {
            white-space: nowrap;
            font-size: 13px;
        }

        .label {
            font-weight: bold;
        }

        .checkbox-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 5px;
            margin: 8px 0 8px 20px;
        }

        .check-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
        }

        .box-ui {
            width: 13px;
            height: 13px;
            border: 1px solid #000;
            display: inline-block;
            text-align: center;
            line-height: 12px;
            font-weight: bold;
        }

        .vax-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .vax-table th,
        .vax-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-size: 12px;
        }

        .cert-text {
            font-style: italic;
            font-size: 11px;
            text-align: justify;
            margin-top: 15px;
            line-height: 1.3;
        }

        .signature-row {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            align-items: flex-end;
            gap: 16px;
        }

        .sig-block {
            flex: 1;
            width: auto;
            text-align: center;
        }

        .sig-image {
            width: 120px;
            height: auto;
            margin-bottom: -10px;
        }

        .sig-line {
            border-bottom: 1px solid #000;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            min-height: 15px;
            margin-bottom: 2px;
        }
    </style>
</head>
<body>
@php
    $clearanceSignaturePath = optional(\App\Models\Setting::first())->clearance_signature_path ?: 'health_profiles/signatures/nurse-sign.png';
    $logoPath = public_path('images/pup_logo.png');
    $studentPhotoPath = $profile->student_photo ? public_path('storage/' . $profile->student_photo) : null;
    $studentSignaturePath = $profile->digital_signature ? public_path('storage/' . $profile->digital_signature) : null;
    $nurseSignaturePath = $clearanceSignaturePath ? public_path('storage/' . $clearanceSignaturePath) : null;
@endphp

<div class="no-print" style="text-align: right; padding: 10px; max-width: 8.5in; margin: 0 auto; display: flex; justify-content: flex-end; gap: 10px;">
    <button onclick="window.print()" style="background: #800000; border: none; padding: 10px 22px; font-weight: bold; color: white; border-radius: 5px; cursor: pointer;">
        PRINT FORM
    </button>
    <button onclick="window.close()" style="background: #64748b; border: none; padding: 10px 22px; font-weight: bold; color: white; border-radius: 5px; cursor: pointer;">
        CLOSE
    </button>
</div>

<div class="print-container">
    <div class="header-section">
        @if(file_exists($logoPath))
            <img src="{{ $logoPath }}" class="logo" alt="PUP Logo">
        @endif
        <div class="header-text">
            <p style="font-size: 9px;">Republic of the Philippines</p>
            <p class="univ-name">POLYTECHNIC UNIVERSITY OF THE PHILIPPINES</p>
            <p style="font-size: 10px;">Office of the Vice President for Administration</p>
            <p class="dept-name">MEDICAL SERVICES DEPARTMENT</p>
        </div>
        <div class="photo-box">
            @if($studentPhotoPath && file_exists($studentPhotoPath))
                <img src="{{ $studentPhotoPath }}" alt="Student Photo">
            @else
                <span style="font-size: 8px;">2x2 ID PHOTO</span>
            @endif
        </div>
    </div>

    <hr style="border: 0.5px solid #000;">

    <div class="form-title">HEALTH INFORMATION FORM</div>

    <div class="section-header">PART I. STUDENT INFORMATION</div>
    <div class="row">
        <span class="label">Name:</span> <div class="field">{{ $profile->user->name }}</div>
        <span class="label">Student No.:</span> <div class="field">{{ $profile->user->student_number }}</div>
    </div>
    <div class="row">
        <span class="label">Home Address:</span> <div class="field">{{ $profile->user->home_address ?? '' }}</div>
        <span class="label">School Year:</span> <div class="field">{{ $profile->school_year ?? '2025-2026' }}</div>
    </div>
    <div class="row">
        <span class="label">Height:</span> <div class="field">{{ $profile->height ?? 'N/A' }}</div>
        <span class="label">Weight:</span> <div class="field">{{ $profile->weight ?? 'N/A' }}</div>
    </div>
    <div class="row">
        <span class="label">Age:</span> <div class="field">{{ $profile->age ?? $calculatedAge ?? '' }}</div>
        <span class="label">Sex:</span> <div class="field">{{ $profile->sex ?? '' }}</div>
        <span class="label">Civil Status:</span> <div class="field">{{ $profile->civil_status ?? '' }}</div>
        <span class="label">Course:</span> <div class="field">{{ $profile->course_college ?? '' }}</div>
    </div>
    <div class="row">
        <span class="label">Blood Type:</span> <div class="field">{{ $profile->blood_type ?? 'N/A' }}</div>
        <span class="label">Email:</span> <div class="field">{{ $profile->user->email }}</div>
    </div>
    <div class="row">
        <span class="label">Parent/Guardian:</span> <div class="field">{{ $profile->guardian_name ?? '' }}</div>
        <span class="label">Landline:</span> <div class="field">{{ $profile->landline ?? 'N/A' }}</div>
        <span class="label">Cellphone:</span> <div class="field">{{ $profile->cellphone ?? '' }}</div>
    </div>

    <div class="section-header">PART II. MEDICAL HISTORY</div>
    <div class="row" style="margin-left: 5px;">
        <span>1. Known medical illness?</span>
        <div class="field" style="flex:0.3;">{{ $profile->has_illness ?? '' }}</div>
        <span class="labels">Name of illness/es:</span>
        <div class="field">{{ $profile->illness_details ?? '' }}</div>
    </div>
    <div class="row" style="margin-left: 5px;">
        <span>2. Has undergone operation?</span>
        <div class="field" style="flex:0.3;">{{ $profile->had_operation ?? '' }}</div>
        <span class="labels">Name of operation:</span>
        <div class="field">{{ $profile->operation_details ?? '' }}</div>
    </div>
    <div class="row" style="margin-left: 5px;">
        <span>3. Has hospitalization?</span>
        <div class="field" style="flex:0.3;">{{ $profile->was_hospitalized ?? '' }}</div>
        <span class="labels">Reason/s:</span>
        <div class="field">{{ $profile->hospitalization_details ?? '' }}</div>
    </div>
    <div class="row" style="margin-left: 5px;">
        <span>4. Has food/drug allergy?</span>
        <div class="field" style="flex:0.3;">{{ $profile->has_allergy ?? '' }}</div>
        <span class="labels">Specify:</span>
        <div class="field">{{ $profile->allergy_details ?? '' }}</div>
    </div>
    <div class="row" style="margin-left: 5px;">
        <span>5. Has congenital disorder?</span>
        <div class="field" style="flex:0.3;">{{ $profile->has_congenital_disorder ?? '' }}</div>
        <span class="labels">Specify:</span>
        <div class="field">{{ $profile->congenital_disorder_details ?? '' }}</div>
    </div>
    <div class="row" style="margin-left: 5px;">
        <span>6. Has disability?</span>
        <div class="field" style="flex:0.3;">{{ $profile->has_disability ?? '' }}</div>
        <span class="labels">Specify:</span>
        <div class="field">{{ $profile->disability_details ?? '' }}</div>
    </div>

    <div class="section-header">PART III. FAMILY HISTORY</div>
    <div class="checkbox-grid">
        @php
            $familyHistory = collect($profile->family_history ?? []);
            $familyConditions = ['Hypertension', 'Diabetes', 'Asthma', 'Cancer', 'Heart Disease', 'Tuberculosis', 'Kidney Disease', 'Mental Disorder'];
        @endphp
        @foreach($familyConditions as $condition)
            <div class="check-item">
                <span class="box-ui">{{ $familyHistory->contains($condition) ? '/' : '' }}</span>
                <span>{{ $condition }}</span>
            </div>
        @endforeach
    </div>

    <div class="section-header">PART IV. IMMUNIZATION HISTORY</div>
    <table class="vax-table">
        <thead>
            <tr>
                <th>Vaccine</th>
                <th>1st Dose</th>
                <th>2nd Dose</th>
                <th>Booster</th>
            </tr>
        </thead>
        <tbody>
            @php $vaccineHistory = $profile->vaccine_history ?? []; @endphp
            @foreach(['COVID-19', 'Hepatitis B', 'MMR', 'Tetanus'] as $vaccine)
                <tr>
                    <td>{{ $vaccine }}</td>
                    <td>{{ data_get($vaccineHistory, $vaccine . '.first_dose', '') }}</td>
                    <td>{{ data_get($vaccineHistory, $vaccine . '.second_dose', '') }}</td>
                    <td>{{ data_get($vaccineHistory, $vaccine . '.booster', '') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="cert-text">
        I hereby certify that the medical health information given to PUP Medical Services are true, correct and fully disclosed to the best of my knowledge and all the medical condition that may affect in the assessment for purpose of consultation/ issuance of medical clearance/ certificate as a student of PUP.
    </div>
    <div class="cert-text">
        I also understand that the PUP MSD and university will not be liable for any untoward incident that may arise due to any failure to disclose accurate information or intentionally providing false and deceptive information.
    </div>
    <div class="cert-text">
        In compliance with the Data Privacy Act of 2012 and its IRR, I voluntarily consent to the collection, processing and storage of my personal and health information for the purpose/s of health assessment/ treatment/ or research following research ethics guidelines for the improvement of healthcare services.
    </div>

    <div class="signature-row">
        <div class="sig-block">
            <div style="height: 60px;"></div>
            <div class="sig-line">Parent/Guardian Signature</div>
        </div>

        <div class="sig-block">
            @if($studentSignaturePath && file_exists($studentSignaturePath))
                <img src="{{ $studentSignaturePath }}" class="sig-image" style="height: 60px; width: auto;" alt="Student Signature">
            @else
                <div style="height: 60px;"></div>
            @endif
            <div class="sig-line">{{ strtoupper($profile->user->name ?? '') }}</div>
            <div style="font-size: 8px;">Student Digital Signature</div>
        </div>

        <div class="sig-block">
            <div style="padding-bottom: 5px; font-weight: bold; height: 60px; display: flex; align-items: flex-end; justify-content: center;">
                {{ $profile->created_at ? $profile->created_at->format('m/d/Y') : date('m/d/Y') }}
            </div>
            <div class="sig-line">Date Signed</div>
        </div>
    </div>

    <div style="display: block; width: 100%; clear: both; border: 2px solid #000; margin-top: 28px; padding: 15px; position: relative;">
        <p style="text-align: center; font-weight: bold; margin-bottom: 10px; font-size: 12px; text-transform: uppercase;">FOR PHYSICIAN ONLY</p>

        <div class="row" style="display: flex; align-items: center; gap: 15px;">
            <span style="font-weight: bold;">Medical Clearance:</span>

            <div class="check-item" style="display: flex; align-items: center; gap: 5px;">
                <div class="box-ui" style="width: 15px; height: 15px; border: 1px solid #000; display: flex; align-items: center; justify-content: center;">
                    {{ $profile->clearance_status == 'Issued' ? '/' : '' }}
                </div>
                Issued
            </div>

            <div class="check-item" style="display: flex; align-items: center; gap: 5px;">
                <div class="box-ui" style="width: 15px; height: 15px; border: 1px solid #000; display: flex; align-items: center; justify-content: center;">
                    {{ $profile->clearance_status == 'Pending' ? '/' : '' }}
                </div>
                Pending, Reason:
                <div class="field" style="border-bottom: 1px solid #000; min-width: 150px; padding-left: 5px; font-size: 12px; font-style: italic;">
                    {{ $profile->clearance_status == 'Pending' ? $profile->pending_reason : '' }}
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 25px; display: flex; align-items: flex-end; justify-content: space-between; gap: 28px;">
            <div style="flex: 0 0 220px; max-width: 220px;">
                <div class="field" style="border-bottom: 1px solid #000; text-align: center; font-weight: bold; min-height: 20px;">
                    {{ $profile->verified_at ? \Carbon\Carbon::parse($profile->verified_at)->format('m/d/Y') : date('m/d/Y') }}
                </div>
                <div style="font-size: 10px; text-align: center; font-weight: bold; margin-top: 2px;">Date</div>
            </div>

            <div style="flex: 1; text-align: center; position: relative; min-height: 80px;">
                @if($profile->clearance_status == 'Issued' && $nurseSignaturePath && file_exists($nurseSignaturePath))
                    <div style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 10;">
                        <img src="{{ $nurseSignaturePath }}" alt="Nurse Signature" style="height: 44px; width: auto;">
                    </div>
                @endif

                <div class="field" style="border-bottom: 1px solid #000; font-weight: bold; position: relative; z-index: 5; text-transform: uppercase; padding-top: 40px;">
                    MS. NURSE NAME, RN
                </div>
                <div style="font-size: 10px; font-weight: bold;">Physician's Name and Signature</div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
