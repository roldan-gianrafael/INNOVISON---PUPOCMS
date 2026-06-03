<div class="print-container">
    <div class="print-page">
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
        <span class="label">Age:</span> <div class="field">{{ $profile->age ?? '' }}</div>
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
    <div class="sig-block">
        <div style="height: 60px;"></div>
        <div class="sig-line">Parent/Guardian Signature</div>
    </div>

    <div class="sig-block">
        @if($profile->digital_signature)
            <img src="{{ asset('storage/' . $profile->digital_signature) }}" class="sig-image" style="height: 60px; width: auto;">
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
                    {{ $profile->clearance_status == 'Issued' ? 'âœ”' : '' }}
                </div> 
                Issued
            </div>

            <div class="check-item" style="display: flex; align-items: center; gap: 5px;">
                <div class="box-ui" style="width: 15px; height: 15px; border: 1px solid #000; display: flex; align-items: center; justify-content: center;">
                    {{ in_array($profile->clearance_status, ['Pending', 'For Verification'], true) ? 'âœ”' : '' }}
                </div> 
                For Verification, Reason: 
                <div class="field" style="border-bottom: 1px solid #000; min-width: 150px; padding-left: 5px; font-size: 12px; font-style: italic;">
                    {{ in_array($profile->clearance_status, ['Pending', 'For Verification'], true) ? $profile->pending_reason : '' }}
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
                <div class="field" style="border-bottom: 1px solid #000; font-weight: bold; position: relative; z-index: 5; text-transform: uppercase; padding-top: 40px;">
                    CLINIC VERIFIER
                </div>
                <div style="font-size: 10px; font-weight: bold;">Clinic Verification / Approval</div>
            </div>
        </div>
    </div>

    <div class="generated-report-caption">
        <div class="signature-note">This is system-generated, signature is not required.</div>
        <div class="privacy-note">
            This document contains personal-identifiable information that is subject to Data Privacy.<br>
            Please keep this document protected and in a safe place.
        </div>
    </div>
</div>
