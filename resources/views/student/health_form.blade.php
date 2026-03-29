<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fill Up - Student Health Information Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; padding: 40px 0; font-family: 'Segoe UI', sans-serif; }
        .form-card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); max-width: 1000px; margin: auto; }
        .section-title { background-color: #800000; color: white; padding: 12px; margin-top: 30px; border-radius: 6px; font-weight: bold; text-transform: uppercase; font-size: 0.95rem; }
        .form-label { font-weight: 600; font-size: 0.9rem; color: #333; }
        .sub-label { font-size: 0.85rem; font-style: italic; color: #666; margin-bottom: 15px; display: block; }
        .vax-table th { background-color: #f8f9fa; font-size: 0.85rem; text-align: center; }
        .upload-box { border: 2px dashed #ccc; padding: 20px; text-align: center; border-radius: 8px; background: #fafafa; }
    </style>
</head>
<body>

<div class="container">
    <div class="form-card">
        <h2 class="text-center fw-bold" style="color: #800000;">MEDICAL SERVICES DEPARTMENT</h2>
        <h5 class="text-center text-muted mb-5">Student Health Information Entry</h5>

        <p class="mb-0 text-muted" style="font-size: 0.9rem;">
                    <strong>Instruction:</strong> Please provide complete and truthful information. Type <b>"N/A"</b> or <b>"NONE"</b> for fields that do not apply to you. Do not leave any field blank to ensure successful submission.
                </p>
        <form action="{{ route('store.health.form') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="section-title">PART I. STUDENT INFORMATION</div>
    
    <div class="row mt-4">
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-7 mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control bg-light" value="{{ trim(implode(' ', array_filter([Auth::user()->name, optional($linkedAdminProfile)->suffix_name]))) }}" readonly>
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label">PUP Student No.</label>
                    <input type="text" class="form-control bg-light" value="{{ Auth::user()->student_id }}" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Suffix Name</label>
                    <input type="text" class="form-control bg-light" value="{{ optional($linkedAdminProfile)->suffix_name }}" readonly>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Home Address</label>
                    <input type="text" name="home_address" class="form-control" placeholder="House No., Street, Brgy, City" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">School Year</label>
                    <input type="text" name="school_year" class="form-control" value="2025-2026">
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label text-center d-block">Upload ID Photo</label>
            <div class="upload-box">
                <input type="file" name="student_photo" class="form-control form-control-sm" accept="image/*" required>
                <small class="text-muted">2x2 or Passport Size</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-3">
            <label class="form-label">Age</label>
            <input type="number" name="age" value="{{ $calculatedAge }}" class="form-control" readonly placeholder="Auto-calculated">
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label">Sex</label>
            <select name="sex" class="form-select" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label">Civil Status</label>
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
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" readonly>
        </div>
        <div class="col-md-7 mb-3">
            <label class="form-label">Parent's Name / Guardian / Spouse</label>
            <input type="text" name="guardian_name" class="form-control" required>
        </div>
        <div class="col-md-2 mb-3">
            <label class="form-label">Landline</label>
            <input type="text" name="landline" class="form-control">
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label">Cellphone No.</label>
            <input type="text" name="cellphone" class="form-control" required>
        </div>
    </div>

    <div class="section-title">PART II. MEDICAL HISTORY</div>
    
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
            <input type="file" name="chest_xray_result" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
            <small class="text-muted">Upload JPG, PNG, or PDF if available.</small>
        </div>
        </div>
          <div class="row mt-4">
        <div class="col-md-12 mt-3">
                    <label class="form-label">3. Medical Certificate</label>
                    <input type="file" name="medical_certificate" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    <small class="text-muted">Upload JPG, PNG, or PDF if you have a medical certificate.</small>
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

    <div class="section-title">PART III. PERSONAL SOCIAL HISTORY & VACCINATION</div>
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

    <div class="row mt-4">
        <div class="col-md-6 offset-md-3">
            <label class="form-label text-center d-block">Upload Digital Signature</label>
            <div class="upload-box">
                <input type="file" name="digital_signature" class="form-control" accept="image/*" required>
                <small class="text-muted">PNG or JPG with clear background</small>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <button type="submit" class="btn btn-success btn-lg px-5 shadow">SUBMIT HEALTH PROFILE</button>
    </div>
</form>

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

