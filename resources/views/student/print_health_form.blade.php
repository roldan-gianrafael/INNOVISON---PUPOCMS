@extends('layouts.student')

@section('title', 'Health Information Form - Print')

@push('styles')
<style>
    /* --- PRINT SETTINGS --- */
    @media print {
        header, footer, nav, .sidebar, .navbar, .no-print, .main-header, .main-sidebar, .btn { 
            display: none !important; 
        }

        body { 
            visibility: hidden; 
            background: none !important; 
            margin: 0; padding: 0;
        }

        .print-container, .print-container * { 
            visibility: visible; 
        }

        .print-container { 
            position: absolute; 
            left: 0; top: 0; 
            width: 100%; 
            margin: 0 !important; 
            padding: 0 !important;
            box-shadow: none !important;
            line-height: 1.1;
        }

        @page { 
            size: 8.5in 13in; 
            margin: 0.4in 0.4in; 
        }
        
        .row { margin-bottom: 2px !important; }
        .section-header { margin-top: 8px !important; }
    }

    /* --- SCREEN VIEW --- */
    body { background-color: #e2e8f0; }

    .print-container {
        font-family: Arial, sans-serif;
        color: #000;
        background: #fff;
        max-width: 8.5in; 
        min-height: 13in; 
        margin: 20px auto;
        padding: 0.4in 0.5in;
        box-shadow: 0 0 15px rgba(0,0,0,0.3);
        position: relative;
        box-sizing: border-box;
    }

    .header-section { display: flex; align-items: center; position: relative; margin-bottom: 5px; }
    .logo { width: 70px; height: 70px; margin-right: 15px; }
    .header-text p { margin: 0; line-height: 1.2; }
    .univ-name { font-size: 13px; font-weight: bold; }
    .dept-name { font-size: 15px; font-weight: bold; }

    .photo-box {
        position: absolute; right: 0; top: 0;
        width: 120px; height: 120px;
        border: 1px solid #000;
        text-align: center; display: flex; align-items: center; justify-content: center;
        overflow: hidden;
    }
    .photo-box img { width: 100%; height: 100%; object-fit: cover; }

    .form-title { text-align: center; font-weight: bold; font-style: italic; font-size: 14px; margin: 15px 0; text-decoration: underline; }
    .section-header { font-weight: bold; font-style: italic; margin-top: 10px; text-transform: uppercase; font-size: 11px;  padding-left: 5px; }

    .row { display: flex; margin-bottom: 4px; gap: 8px; align-items: baseline; }
    .field { border-bottom: 1px solid #000; flex: 1; padding-left: 5px; min-height: 16px; font-size: 12px; font-weight: bold; color: #000; }
    .label { font-weight: bold; white-space: nowrap; font-size: 11px; }

    .checkbox-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 3px; margin: 5px 0 5px 20px; }
    .check-item { display: flex; align-items: center; gap: 4px; font-size: 10px; }
    .box-ui { width: 11px; height: 11px; border: 1px solid #000; display: inline-block; text-align: center; line-height: 10px; font-weight: bold; }

    .vax-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
    .vax-table th, .vax-table td { border: 1px solid #000; padding: 2px; text-align: center; font-size: 10px; }
    
    .cert-text { font-style: italic; font-size: 9px; text-align: justify; margin-top: 10px; line-height: 1.2; }
    .signature-row { display: flex; justify-content: space-between; margin-top: 30px; align-items: flex-end; }
    .sig-block { width: 30%; text-align: center; }
    .sig-image { width: 100px; height: auto; margin-bottom: -10px; }
    .sig-line { border-top: 1px solid #000; font-size: 9px; padding-top: 2px; font-weight: bold; }
</style>
@endpush

@section('content')
<div class="no-print" style="text-align: right; padding: 10px; max-width: 8.5in; margin: auto;">
    <button onclick="window.print()" class="btn btn-primary" style="background: #800000; border: none; padding: 10px 25px; font-weight: bold; color: white; border-radius: 5px;">
        CLICK TO PRINT FORM 🖨️
    </button>

    <button onclick="window.history.back()" style="background: #64748b; border: none; padding: 10px 25px; font-weight: bold; color: white; border-radius: 5px; cursor: pointer;">
         ✖
    </button>

</div>

<div class="print-container">
    <div class="header-section">
        <img src="{{ asset('images/pup_logo.png') }}" class="logo">
        <div class="header-text">
            <p style="font-size: 9px;">Republic of the Philippines</p>
            <p class="univ-name">POLYTECHNIC UNIVERSITY OF THE PHILIPPINES</p>
            <p style="font-size: 10px;">Office of the Vice President for Administration</p>
            <p class="dept-name">MEDICAL SERVICES DEPARTMENT</p>
        </div>
        <div class="photo-box">
            @if($profile->student_photo)
               <img src="{{ asset('storage/' . $profile->student_photo) }}" alt="Student Photo">
            @else
                <span style="font-size: 8px;">2x2 ID PHOTO</span>
            @endif
        </div>
    </div>

    <hr style="border: 0.5px solid #000;">

    <div class="form-title">HEALTH INFORMATION FORM FOR STUDENTS</div>

    <div class="section-header">PART I. STUDENT INFORMATION</div>
    <div class="row">
        <span class="label">Name:</span> <div class="field">{{ Auth::user()->name }}</div>
        <span class="label">Student No.:</span> <div class="field">{{ Auth::user()->student_id }}</div>
    </div>
    <div class="row">
        <span class="label">Home Address:</span> <div class="field">{{ $profile->home_address ?? '' }}</div>
        <span class="label">School Year:</span> <div class="field">{{ $profile->school_year ?? '2025-2026' }}</div>
    </div>
    <div class="row">
        <span class="label">Age:</span> <div class="field">{{ $profile->age ?? '' }}</div>
        <span class="label">Sex:</span> <div class="field">{{ $profile->sex ?? '' }}</div>
        <span class="label">Civil Status:</span> <div class="field">{{ $profile->civil_status ?? '' }}</div>
        <span class="label">Course:</span> <div class="field">{{ $profile->course_college ?? '' }}</div>
    </div>
    <div class="row">
        <span class="label">Blood Type:</span> <div class="field">{{ $profile->blood_type ?? 'N/A' }}</div>
        <span class="label">Email:</span> <div class="field">{{ Auth::user()->email }}</div>
    </div>
    <div class="row">
        <span class="label">Parent/Guardian:</span> <div class="field">{{ $profile->guardian_name ?? '' }}</div>
        <span class="label">Landline:</span> <div class="field">{{ $profile->landline ?? 'N/A' }}</div>
        <span class="label">Cellphone:</span> <div class="field">{{ $profile->cellphone ?? '' }}</div>
    </div>

    <div class="section-header">PART II. MEDICAL HISTORY</div>
    <div class="row" style="margin-left: 5px;">
        <span>1. Known medical illness?</span>
        <div class="check-item"><div class="box-ui">{{ $profile->has_illness == 'No' ? '/' : '' }}</div> No</div>
        <div class="check-item"><div class="box-ui">{{ $profile->has_illness == 'Yes' ? '/' : '' }}</div> Yes</div>
    </div>
    
    <div class="checkbox-grid">
        @php
            $illnesses = ['Asthma', 'Loss of Consciousness', 'Eye Disease/ Defect', 'Accident Injuries', 'Diabetes', 'Heart Disease', 'Kidney Disease', 'Tuberculosis', 'Convulsion/ Epilepsy', 'Hyperventilation', 'High Blood Pressure', 'Migraine'];
            $saved_history = is_array($profile->medical_history) ? $profile->medical_history : json_decode($profile->medical_history ?? '[]', true);
        @endphp
        @foreach($illnesses as $illness)
        <div class="check-item">
            <div class="box-ui">{{ in_array($illness, $saved_history) ? '/' : '' }}</div> {{ $illness }}
        </div>
        @endforeach
        <div class="check-item" style="grid-column: span 2;">Others: <div class="field">{{ $profile->other_illness ?? '' }}</div></div>
    </div>

    <div class="row">
        <span>2. Do you have disability?</span>
        <div class="check-item"><div class="box-ui">{{ $profile->has_disability == 'None' ? '/' : '' }}</div> None</div>
        <div class="check-item"><div class="box-ui">{{ $profile->has_disability == 'Yes' ? '/' : '' }}</div> Yes:</div>
        <div class="field">{{ $profile->disability_type ?? '' }}</div>
    </div>

    <div class="section-header">3. ALLERGIES & MEDICAL CONDITIONS</div>
    <div class="row">
        <span class="label">Food:</span> <div class="field">{{ $profile->food_allergies ?? 'None' }}</div>
        <span class="label">No Known Allergies:</span> <div class="box-ui">{{ $profile->no_allergies ? '/' : '' }}</div>
    </div>
    <div class="row px-4">
        <span class="label">Medicines:</span>
        @php
            $meds_list = ['Aspirin', 'Ibuprofen', 'Amoxicillin', 'Mefenamic Acid', 'Penicillin'];
            $saved_meds = is_array($profile->medicine_allergies) ? $profile->medicine_allergies : json_decode($profile->medicine_allergies ?? '[]', true);
        @endphp
        @foreach($meds_list as $med)
            <div class="check-item">
                <div class="box-ui">{{ in_array($med, $saved_meds) ? '/' : '' }}</div> {{ $med }}
            </div>
        @endforeach
    </div>

    <div class="section-header">PART III. SOCIAL & VAX HISTORY</div>
    <div class="row">
        <span class="label">Smoking:</span> 
        <div class="check-item"><div class="box-ui">{{ ($profile->is_smoker ?? '') == 'Yes' ? '/' : '' }}</div> Yes</div>
        <div class="check-item"><div class="box-ui">{{ ($profile->is_smoker ?? '') == 'No' ? '/' : '' }}</div> No</div>
        <span class="label" style="margin-left:20px;">Alcohol:</span> 
        <div class="check-item"><div class="box-ui">{{ ($profile->is_drinker ?? '') == 'Yes' ? '/' : '' }}</div> Yes</div>
        <div class="check-item"><div class="box-ui">{{ ($profile->is_drinker ?? '') == 'No' ? '/' : '' }}</div> No</div>
    </div>

    <div class="row">
        <div style="flex: 1;">
            <span class="label">COVID-19 Vax History:</span><br>
            <small>Doses received and brand information recorded for safety protocols.</small>
        </div>
        <table class="vax-table" style="width: 65%;">
            @php $vax = is_array($profile->vaccine_history) ? $profile->vaccine_history : json_decode($profile->vaccine_history ?? '[]', true); @endphp
            <thead><tr><th>Dose</th><th>Date Received</th><th>Brand</th></tr></thead>
            <tbody>
                <tr><td>1st Dose</td><td>{{ $vax['dose1']['date'] ?? '' }}</td><td>{{ $vax['dose1']['brand'] ?? '' }}</td></tr>
                <tr><td>2nd Dose</td><td>{{ $vax['dose2']['date'] ?? '' }}</td><td>{{ $vax['dose2']['brand'] ?? '' }}</td></tr>
                <tr><td>Booster 1</td><td>{{ $vax['booster1']['date'] ?? '' }}</td><td>{{ $vax['booster1']['brand'] ?? '' }}</td></tr>
                <tr><td>Booster 2</td><td>{{ $vax['booster2']['date'] ?? '' }}</td><td>{{ $vax['booster2']['brand'] ?? '' }}</td></tr>
            </tbody>
        </table>
    </div>

    <div class="cert-text">
        I hereby certify that the medical health information given to PUP Medical Services are true, correct and fully disclosed to the best of my knowledge and all the medical condition that may affect in the assessment for purpose of consultation/ issuance of medical clearance/ certificate as a student of PUP. I voluntarily consent to the collection, processing and storage of my personal and health information.
    </div>

    <div class="signature-row">
        <div class="sig-block">
            <div class="sig-line">Parent/Guardian Signature</div>
        </div>
        <div class="sig-block">
            @if($profile->digital_signature)
                <img src="{{ asset('storage/' . $profile->digital_signature) }}" class="sig-image">
            @endif
            <div class="sig-line">{{ strtoupper(Auth::user()->name) }}</div>
            <div style="font-size: 8px;">Student Digital Signature</div>
        </div>
        <div class="sig-block">
            <div style="padding-bottom: 5px; font-weight: bold;">{{ date('m/d/Y') }}</div>
            <div class="sig-line">Date Signed</div>
        </div>
    </div>

    <div style="border: 1px solid #000; margin-top: 15px; padding: 10px;">
        <p style="text-align: center; font-weight: bold; margin: 0; font-size: 11px;">FOR PHYSICIAN ONLY</p>
        <div class="row">
            <span>Status:</span>
            <div class="check-item"><div class="box-ui"></div> Issued</div>
            <div class="check-item"><div class="box-ui"></div> Pending, Reason: <div class="field" style="width: 150px;"></div></div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="field" style="flex: 0.4;">Date:</div>
            <div class="field">Physician's Name and Signature</div>
        </div>
    </div>
</div>
@endsection