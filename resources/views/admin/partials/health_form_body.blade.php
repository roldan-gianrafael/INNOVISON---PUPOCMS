<div class="print-container">
    <div class="print-page">
    <div class="document-code">
        <strong>PUP-HIFS-6-MEDS-028</strong><br>
        Rev. 0<br>
        June 26, 2024
    </div>
    <div class="header-section">
        <img src="{{ $healthFormLogo ?? asset('images/pup_logo.png') }}" class="logo">
        <div class="header-text">
            <p style="font-size: 9px;">Republic of the Philippines</p>
            <p class="univ-name">POLYTECHNIC UNIVERSITY OF THE PHILIPPINES</p>
            <p style="font-size: 10px;">Office of the Vice President for Administration</p>
            <p class="dept-name">MEDICAL SERVICES DEPARTMENT</p>
        </div>
        <div class="photo-box">
            <span>2x2 or passport-sized<br>current colored ID photo</span>
        </div>
    </div>

    <hr class="header-divider">

    <div class="form-title">HEALTH INFORMATION FORM FOR STUDENTS</div>

    <div class="section-header">PART I. STUDENT INFORMATION</div>
    <div class="row">
        <span class="label">Name:</span> <div class="field">{{ $profile->user->name }}</div>
        <span class="label">PUP Student No.:</span> <div class="field">{{ $profile->student_number ?: $profile->user->student_number }}</div>
    </div>
    <div class="row">
        <span class="label">Home Address:</span> <div class="field">{{ $profile->home_address ?? '' }}</div>
        <span class="label">School Year:</span> <div class="field">{{ $profile->school_year ?? '2025-2026' }}</div>
    </div>
    <div class="row">
        <span class="label">Age:</span> <div class="field">{{ $profile->age ?? '' }}</div>
        <span class="label">Sex:</span> <div class="field">{{ $profile->sex ?? '' }}</div>
        <span class="label">Civil Status:</span> <div class="field">{{ $profile->civil_status ?? '' }}</div>
        <span class="label">Course / College:</span> <div class="field">{{ $profile->course_college ?? '' }}</div>
    </div>
    <div class="row">
        <span class="label">Blood Type:</span> <div class="field">{{ $profile->blood_type ?? 'N/A' }}</div>
        <span class="label">Email Address:</span> <div class="field">{{ $profile->user->email }}</div>
    </div>
    <div class="row">
        <span class="label">Parent's Name / Guardian / Spouse:</span> <div class="field">{{ $profile->guardian_name ?? '' }}</div>
    </div>
    <div class="row contact-row">
        <span class="label">Landline:</span> <div class="field">{{ $profile->landline ?? 'N/A' }}</div>
        <span class="label">Cellphone:</span> <div class="field">{{ $profile->cellphone ?? '' }}</div>
    </div>

    <div class="section-header">PART II. MEDICAL HISTORY</div>
    <div class="row medical-attention-row">
        <span class="medical-subsection-title">1. Do you need medical attention or has known medical illness?</span>
        <div class="check-item"><div class="box-ui">{{ $profile->has_illness == 'No' ? '/' : '' }}</div> No</div>
        <div class="check-item"><div class="box-ui">{{ $profile->has_illness == 'Yes' ? '/' : '' }}</div> Yes</div>
    </div>
    <div class="medical-history-instruction">(Please check the following that apply as needed)</div>
    
    <div class="checkbox-grid">
        @php
            $illnesses = ['Asthma', 'Loss of Consciousness', 'Eye Disease / Defect', 'Accident Injuries', 'Diabetes', 'Heart Disease', 'Kidney Disease', 'Tuberculosis', 'Convulsion / Epilepsy', 'Migraine', 'Hyperventilation', 'High Blood Pressure', 'Hemophilia', 'Primary Complex'];
            $saved_history = is_array($profile->medical_history) ? $profile->medical_history : json_decode($profile->medical_history ?? '[]', true);
        @endphp
        @foreach($illnesses as $illness)
        @php
            $illnessChecked = in_array($illness, $saved_history, true)
                || (
                    in_array($illness, ['Tuberculosis', 'Primary Complex'], true)
                    && in_array('Tuberculosis / Primary Complex', $saved_history, true)
                );
        @endphp
        <div class="check-item">
            <div class="box-ui">{{ $illnessChecked ? '/' : '' }}</div> {{ $illness }}
        </div>
        @endforeach
        <div class="check-item" style="grid-column: span 2;">Others (Pls. Indicate): <div class="field">{{ $profile->other_illness ?? '' }}</div></div>
    </div>

    <div class="row">
        <span class="medical-subsection-title">2. Do you have disability?</span>
        <div class="check-item"><div class="box-ui">{{ $profile->has_disability == 'No' ? '/' : '' }}</div> None</div>
        <div class="check-item"><div class="box-ui">{{ $profile->has_disability == 'Yes' ? '/' : '' }}</div> Yes: What type of disability?</div>
        <div class="field">{{ $profile->disability_type ?? '' }}</div>
    </div>

    <div class="medical-subsection-title medical-subsection-heading">
        3. Additional Information for Students and Medical Conditions:
    </div>
    <div class="allergy-declaration">
        As a Parent / Guardian, I would like to declare that my child has history of allergies to the following:
    </div>
    <div class="row">
        <span class="label">Food (Please specify):</span> <div class="field">{{ $profile->food_allergies ?? '' }}</div>
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
        <div class="check-item medicine-other-field">
            Others: <div class="field">{{ $profile->other_med_allergies ?? '' }}</div>
        </div>
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

    <div class="row covid-history-row">
        <div style="flex: 1;">
            <span class="medical-subsection-title">4. COVID-19 Vaccination History:</span>
            <div class="vaccinated-choice">
                <span class="label">Vaccinated:</span>
                <div class="check-item"><div class="box-ui">{{ ($profile->covid_vaccinated ?? '') === 'Yes' ? '/' : '' }}</div> Yes</div>
                <div class="check-item"><div class="box-ui">{{ ($profile->covid_vaccinated ?? '') !== 'Yes' ? '/' : '' }}</div> No</div>
            </div>
        </div>
        <table class="vax-table" style="width: 65%;">
            @php $vax = is_array($profile->vaccine_history) ? $profile->vaccine_history : json_decode($profile->vaccine_history ?? '[]', true); @endphp
            <thead><tr><th>Dose</th><th>Date Received</th><th>Brand</th></tr></thead>
            <tbody>
                <tr><td>1st Dose</td><td>{{ $vax['first_dose']['date'] ?? '' }}</td><td>{{ $vax['first_dose']['brand'] ?? '' }}</td></tr>
                <tr><td>2nd Dose</td><td>{{ $vax['second_dose']['date'] ?? '' }}</td><td>{{ $vax['second_dose']['brand'] ?? '' }}</td></tr>
                <tr><td>Booster 1st Dose</td><td>{{ $vax['booster_1']['date'] ?? '' }}</td><td>{{ $vax['booster_1']['brand'] ?? '' }}</td></tr>
                <tr><td>Booster 2nd Dose</td><td>{{ $vax['booster_2']['date'] ?? '' }}</td><td>{{ $vax['booster_2']['brand'] ?? '' }}</td></tr>
            </tbody>
        </table>
    </div>

    <div class="cert-text cert-text-first">
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



    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-space"></div>
                <div class="sig-line">Signature of Parent/Guardian</div>
                <div class="signature-caption">For students below 18 years old</div>
            </td>
            <td>
                @if(empty($studentPrintCopy) && $profile->digital_signature)
                    <img src="{{ asset('storage/' . $profile->digital_signature) }}" class="sig-image" style="height: 40px; width: auto;">
                @else
                    <div class="signature-space"></div>
                @endif
                <div class="sig-line">{{ empty($studentPrintCopy) ? strtoupper($profile->user->name ?? '') : '' }}</div>
                <div class="signature-caption">Printed Name and Signature of Student</div>
            </td>
            <td>
                <div class="signature-space signature-date-space">
                    {{ empty($studentPrintCopy) && $profile->created_at ? $profile->created_at->format('m/d/Y') : '' }}
                </div>
                <div class="sig-line">Date</div>
            </td>
        </tr>
    </table>

    <div class="physician-section" style="display: block; width: 100%; clear: both; border: 2px solid #000; margin-top: 28px; padding: 15px; position: relative;">
        <p style="text-align: center; font-weight: bold; margin-bottom: 2px; font-size: 12px; text-transform: uppercase;">FOR PHYSICIAN ONLY</p>
        <p class="physician-check-instruction">Please Check</p>
        
        <table class="physician-clearance-table">
            <tr>
                <td class="physician-clearance-label">Medical Clearance:</td>
                <td>
                    <span class="box-ui physician-box">
                    {{ empty($studentPrintCopy) && in_array($profile->clearance_status, ['Issued', 'Fully Cleared'], true) ? '/' : '' }}
                    </span>
                    Issued
                </td>
                <td class="physician-pending-cell">
                    <span class="box-ui physician-box">
                    {{ empty($studentPrintCopy) && in_array($profile->clearance_status, ['Pending', 'For Verification'], true) ? '/' : '' }}
                    </span>
                    Pending, Reason:
                    <span class="physician-reason-line">
                        {{ empty($studentPrintCopy) && in_array($profile->clearance_status, ['Pending', 'For Verification'], true) ? $profile->pending_reason : '' }}
                    </span>
                </td>
            </tr>
        </table>

        <table class="physician-signature-table">
            <tr>
                <td>
                    <div class="physician-signature-line">
                        {{ empty($studentPrintCopy) && $profile->verified_at ? \Carbon\Carbon::parse($profile->verified_at)->format('m/d/Y') : '' }}
                    </div>
                    <div class="physician-signature-label">Date:</div>
                </td>
                <td>
                    <div class="physician-signature-line"></div>
                    <div class="physician-signature-label">Physician's Name and Signature</div>
                </td>
            </tr>
        </table>
    </div>

</div>
</div>
