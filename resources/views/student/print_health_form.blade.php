@extends('layouts.student')

@section('title', 'Health Information Form - Print')

@push('styles')
<style>
   /* --- PRINT SETTINGS --- */
@media print {
    /* 1. Itago lahat ng admin elements at buttons */
    header, footer, nav, .sidebar, .navbar, .no-print, 
    .main-header, .main-sidebar, .btn, .content-header { 
        display: none !important; 
    }

    /* 2. Siguraduhin na walang background at margin ang body */
    body { 
        visibility: hidden; 
        background: white !important; 
        margin: 0 !important; 
        padding: 0 !important;
    }

    /* 3. Ipakita lang ang form container */
    .print-container, .print-container * { 
        visibility: visible; 
    }

    /* 4. I-dikit sa pinakataas ang container */
    .print-container { 
        position: absolute !important; 
        left: 0 !important; 
        top: 0 !important; 
        width: 100% !important; 
        margin: 0 !important; 
        /* Dito mo kontrolin ang layo sa gilid at taas ng papel */
        padding: 0.2in 0.5in !important; 
        box-shadow: none !important;
        line-height: 1.2;
        border: none !important;
    }

    /* 5. Force the page size and REMOVE default browser margins */
    @page { 
        size: 8.5in 13in; 
        margin: 0 !important; /* Ginawang 0 para mawala ang 0.81 gap */
    }
}
        
        .row { margin-bottom: 4px !important; }
        .section-header { margin-top: 12px !important; }
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

    .header-section { display: flex; align-items: center; position: relative; margin-bottom: 8px; }
    .logo { width: 80px; height: 80px; margin-right: 15px; margin-left: 50px;} /* Pinalaki rin ang logo konti */
    .header-text p { margin: 0; line-height: 1.3; }
    
    /* Font Size Updates (+2) */
    .univ-name { font-size: 15px; font-weight: bold; } /* From 13px */
    .dept-name { font-size: 17px; font-weight: bold; } /* From 15px */

    .photo-box {
        position: absolute; right: 0; top: 0;
        width: 150px; height: 130px;
        border: 1px solid #000;
        text-align: center; display: flex; align-items: center; justify-content: center;
        overflow: hidden;
    }
    .photo-box img { width: 100%; height: 100%; object-fit: cover; }

    .form-title { text-align: center; font-weight: bold; font-style: italic; font-size: 16px; margin: 18px 0;  } /* From 14px */
    .section-header { font-weight: bold; font-style: italic; margin-top: 12px; text-transform: uppercase; font-size: 13px; padding-left: 5px; } /* From 11px */

    .row { display: flex; margin-bottom: 6px; gap: 10px; align-items: baseline; }
    .field { border-bottom: 1px solid #000; flex: 1; padding-left: 5px; min-height: 18px; font-size: 14px; font-weight: bold; color: #000; } /* From 12px */
    .label { font-weight: bold; white-space: nowrap; font-size: 13px; } /* From 11px */
    .labels {  white-space: nowrap; font-size: 13px; } /* From 11px */

    .checkbox-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 5px; margin: 8px 0 8px 20px; }
    .check-item { display: flex; align-items: center; gap: 6px; font-size: 12px; } /* From 10px */
    .box-ui { width: 13px; height: 13px; border: 1px solid #000; display: inline-block; text-align: center; line-height: 12px; font-weight: bold; }

    .vax-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    .vax-table th, .vax-table td { border: 1px solid #000; padding: 4px; text-align: center; font-size: 12px; } /* From 10px */
    
    .cert-text { font-style: italic; font-size: 11px; text-align: justify; margin-top: 15px; line-height: 1.3; } /* From 9px */
    .signature-row { display: flex; justify-content: space-between; margin-top: 20px; align-items: flex-end; }
    .sig-block { width: 35%; text-align: center; }
    .sig-image { width: 120px; height: auto; margin-bottom: -10px; }
    /* --- I-update ang sig-line sa <style> section --- */
.sig-line { 
    border-bottom: 1px solid #000; /* Ginawang bottom border para sa ibabaw ang text */
    font-size: 11px; 
    font-weight: bold; 
    text-transform: uppercase;
    min-height: 15px;
    margin-bottom: 2px;
}
.sig-label {
    font-size: 9px;
    font-weight: bold;
    color: #000;
}
</style>
@endpush

@section('content')
<div class="no-print" style="text-align: right; padding: 10px; max-width: 8.5in; margin: auto; display: flex; justify-content: flex-end; align-items: center; gap: 10px;">
    
    @if($profile->clearance_status == 'Issued')

        <button onclick="window.print()" class="btn btn-primary" style="background: #800000; border: none; padding: 10px 25px; font-weight: bold; color: white; border-radius: 5px; cursor: pointer;">
            CLICK TO PRINT FORM 🖨️
        </button>
    @else

        <div style="background: #fef2f2; color: #b91c1c; padding: 8px 15px; border-radius: 5px; font-size: 13px; font-weight: bold; border: 1px solid #fecaca;">
            ⚠️ VIEWING ONLY: Pending Medical Review
        </div>
        <button disabled style="background: #94a3b8; border: none; padding: 10px 25px; font-weight: bold; color: white; border-radius: 5px; cursor: not-allowed;">
            PRINTING DISABLED
        </button>
    @endif

    <a href="{{ route('account') }}" 
   style="display: inline-block; text-decoration: none; background: #64748b; border: none; padding: 10px 25px; font-weight: bold; color: white; border-radius: 5px; cursor: pointer; line-height: 1;">
    ✖
</a>
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

    <div class="section-header">PART III. PERSONAL SOCIAL HISTORY</div>
    <div class="row" style="margin-top: 5px;">
    <span class="labels">Cigarette Smoking :</span>
    <div class="check-item" style="margin-left: 10px;">
        <div class="box-ui">{{ ($profile->is_smoker ?? '') == 'Yes' ? '/' : '' }}</div> Yes
    </div>
    <div class="check-item">
        <div class="box-ui">{{ ($profile->is_smoker ?? '') == 'No' ? '/' : '' }}</div> No
    </div>
</div>

<div class="row">
    <span class="labels">Alcohol Drinking:</span>
    <div class="check-item" style="margin-left: 24px;">
        <div class="box-ui">{{ ($profile->is_drinker ?? '') == 'Yes' ? '/' : '' }}</div> Yes
    </div>
    <div class="check-item">
        <div class="box-ui">{{ ($profile->is_drinker ?? '') == 'No' ? '/' : '' }}</div> No
    </div>
</div>

    <div class="row">
        <div style="flex: 1;">
            <span class="label">COVID-19 Vaccination History:</span><br>
            <small>.</small>
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
        I hereby certify that the medical health information given to PUP Medical Services are true, correct and fully disclosed to the best of
my knowledge and all the medical condition that may affect in the assessment for purpose of consultation/ issuance of medical
clearance/ certificate as a student of PUP.
    </div>
<div class="cert-text">
        I also understand that the PUP MSD and university will not be liable for any untoward incident that may arise due to any failure to
disclose accurate information or intentionally providing false and deceptive information.
    </div>
    <div class="cert-text">
        In compliance with the Data Privacy Act of 2012 and its IRR, I voluntarily consent to the collection, processing and storage of my
personal and health information for the purpose/s of health assessment/ treatment/ or research following research ethics guidelines
for the improvement of healthcare services.
    </div>



    <div class="signature-row">
    {{-- Parent Signature Block --}}
    <div class="sig-block">
        <div style="height: 60px;"></div> {{-- Space para sa manual signature --}}
        <div class="sig-line">Parent/Guardian Signature</div>
    </div>

    {{-- Student Signature Block --}}
    <div class="sig-block">
        @if($profile->digital_signature)
            <img src="{{ asset('storage/' . $profile->digital_signature) }}" class="sig-image" style="height: 60px; width: auto;">
        @else
            <div style="height: 60px;"></div>
        @endif
        
        {{-- FIX: Gamitin ang name ng student mula sa profile, hindi Auth::user() --}}
        <div class="sig-line">{{ strtoupper($profile->user->name) }}</div>
        <div style="font-size: 8px;">Student Digital Signature</div>
    </div>

    {{-- Date Signed Block --}}
    <div class="sig-block">
        <div style="padding-bottom: 5px; font-weight: bold; height: 60px; display: flex; align-items: flex-end; justify-content: center;">
            {{-- FIX: Gamitin ang created_at date ng profile record --}}
            {{ $profile->created_at ? $profile->created_at->format('m/d/Y') : date('m/d/Y') }}
        </div>
        <div class="sig-line">Date Signed</div>
    </div>
</div>

   <div style="border: 2px solid #000; margin-top: 15px; padding: 15px; position: relative;">
        <p style="text-align: center; font-weight: bold; margin-bottom: 10px; font-size: 12px; text-transform: uppercase;">FOR PHYSICIAN ONLY</p>
        
        <div class="row" style="display: flex; align-items: center; gap: 15px;">
            <span style="font-weight: bold;">Medical Clearance:</span>
            
            <div class="check-item" style="display: flex; align-items: center; gap: 5px;">
                <div class="box-ui" style="width: 15px; height: 15px; border: 1px solid #000; display: flex; align-items: center; justify-content: center;">
                    {{ $profile->clearance_status == 'Issued' ? '✔' : '' }}
                </div> 
                Issued
            </div>

            <div class="check-item" style="display: flex; align-items: center; gap: 5px;">
                <div class="box-ui" style="width: 15px; height: 15px; border: 1px solid #000; display: flex; align-items: center; justify-content: center;">
                    {{ $profile->clearance_status == 'Pending' ? '✔' : '' }}
                </div> 
                Pending, Reason: 
                <div class="field" style="border-bottom: 1px solid #000; min-width: 150px; padding-left: 5px; font-size: 12px; font-style: italic;">
                    {{ $profile->clearance_status == 'Pending' ? $profile->pending_reason : '' }}
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 30px; display: flex; align-items: flex-end; gap: 20px;">
    
    <div style="flex: 0.4; text-align: center;">
        <div class="field" style="border-bottom: 1px solid #000; font-weight: bold; min-height: 20px; padding-bottom: 5px;">
            @if($profile->clearance_status == 'Issued')
                {{ $profile->verified_at ? \Carbon\Carbon::parse($profile->verified_at)->format('m/d/Y') : date('m/d/Y') }}
            @else
                &nbsp; 
            @endif
        </div>
        <div style="font-size: 10px; font-weight: bold; margin-top: 5px;">Date</div>
    </div>

    <div style="flex: 0.6; text-align: center; position: relative;">
        
        @if($profile->clearance_status == 'Issued')
            <div style="position: absolute; bottom: 25px; left: 50%; transform: translateX(-50%); z-index: 10;">
                <img src="{{ asset('storage/health_profiles/signatures/nurse-sign.png') }}" 
                     alt="Nurse Signature" 
                     style="height: 85px; width: auto; pointer-events: none;">
            </div>
        @endif

        <div class="field" style="border-bottom: 1px solid #000; font-weight: bold; position: relative; z-index: 5; text-transform: uppercase; padding-bottom: 5px;">
            MS. NURSE NAME, RN
        </div>
        <div style="font-size: 10px; font-weight: bold; margin-top: 5px;">Physician's Name and Signature</div>
    </div>

</div>
        </div>
    </div>
</div>
@endsection