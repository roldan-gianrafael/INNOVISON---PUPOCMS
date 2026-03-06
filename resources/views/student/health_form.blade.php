@extends('layouts.student')

@section('title', 'Health Information Form')

@push('styles')
<style>
    /* --- PRINT STYLES: LONG BOND PAPER (8.5 x 13) --- */
    @media print {
        @page {
            size: 8.5in 13in;
            margin: 0.5in;
        }
        body * { visibility: hidden; }
        .print-container, .print-container * { visibility: visible; }
        .print-container { 
            position: absolute; 
            left: 0; 
            top: 0; 
            width: 100%; 
            margin: 0; 
            padding: 0; 
            border: none; 
        }
        .no-print { display: none !important; }
        main { padding: 0 !important; margin: 0 !important; }
    }

    /* --- FORM STYLES --- */
    .print-container {
        background: #fff;
        max-width: 816px; /* 8.5 inches approx */
        margin: 20px auto;
        padding: 40px 50px;
        border: 1px solid #e2e8f0;
        color: #000;
        font-family: Arial, Helvetica, sans-serif; /* Arial Font */
    }

    /* OFFICIAL HEADER: CENTERED GROUP, LEFT-ALIGNED TEXT */
    .header-wrapper {
        display: flex;
        align-items: center; 
        justify-content: center; 
        gap: 15px; 
        margin-bottom: 10px;
        position: relative;
        width: 100%;
    }

    .header-logo-left { 
        width: 85px; 
        height: 85px; 
        object-fit: contain; 
    }
    
    .header-text { 
        text-align: left; 
        line-height: 1.1;
    }

    .header-text .gov-name { font-size: 11px; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
    .header-text .univ-name { font-size: 17px; font-weight: bold; margin: 2px 0; }
    .header-text .vp-office { font-size: 11px; margin: 0; font-weight: normal; }
    .header-text .dept-name { font-size: 19px; font-weight: bold; margin-top: 2px; }

    .header-right-box {
        position: absolute;
        right: 0;
        top: 0;
        width: 90px;
        height: 90px;
        border: 1px solid #000;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        z-index: 10;
        text-align: center;
    }

    .header-divider {
        border-top: 2px solid #000;
        margin-top: -20px; 
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
        width: 100%;
    }

    .form-title {
        text-align: center;
        font-weight: bold;
        font-size: 18px;
        margin-bottom: 25px;
        text-transform: uppercase;
    }

    /* SECTION & GRID STYLES */
    .section-title { 
        background: #f1f5f9; 
        padding: 5px 10px; 
        font-weight: bold; 
        text-transform: uppercase; 
        border: 1px solid #000;
        margin-top: 20px;
        font-size: 14px;
    }

    .info-row { display: flex; flex-wrap: wrap; gap: 15px; margin-top: 12px; }
    .input-group { display: flex; align-items: baseline; flex: 1; min-width: 200px; }
    .input-group label { font-weight: bold; margin-right: 8px; white-space: nowrap; font-size: 13px; }
    
    .line-fill { 
        border-bottom: 1px solid #000; 
        flex: 1; 
        padding-left: 5px; 
        font-size: 14px; 
        min-height: 1.2em;
    }

    .line-fill input {
        border: none;
        width: 100%;
        background: transparent;
        outline: none;
        font-family: inherit;
        font-size: inherit;
    }

    .medical-history-grid { 
        display: grid; 
        grid-template-columns: repeat(3, 1fr); 
        gap: 10px; 
        padding: 10px; 
    }
    
    table.vax-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; }
    table.vax-table th, table.vax-table td { border: 1px solid #000; padding: 8px; text-align: left; }

    .signature-section { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 40px; text-align: center; }
    .sig-box { border-top: 1px solid #000; margin-top: 50px; padding-top: 5px; font-size: 12px; font-weight: bold; }

    .cut-line { 
        border-top: 2px dashed #000; 
        margin-top: 40px; 
        padding-top: 10px; 
        position: relative; 
        text-align: center;
        font-style: italic;
        font-size: 11px;
    }
    .cut-line::before { content: "✂"; position: absolute; left: 0; top: -14px; font-size: 18px; }

    .physician-section { border: 2px solid #000; padding: 20px; margin-top: 20px; }
</style>
@endpush

@section('content')
<div class="container no-print" style="margin-top: 20px; text-align: right; max-width: 816px;">
    <button onclick="window.print()" class="btn btn-primary" style="background: #1a202c; border:none; padding: 10px 20px; border-radius: 8px; font-family: Arial;">Print Form 🖨️</button>
</div>

<form action="{{ route('student.health.save') }}" method="POST">
    @csrf
    <div class="print-container">
        <div class="header-wrapper">
            <img src="{{ asset('images/pup_logo.png') }}" class="header-logo-left" alt="PUP Logo">
            <div class="header-text">
                <p class="gov-name">Republic of the Philippines</p>
                <p class="univ-name">POLYTECHNIC UNIVERSITY OF THE PHILIPPINES</p>
                <p class="vp-office">Office of the Vice President for Administration</p>
                <p class="dept-name">MEDICAL SERVICES DEPARTMENT</p>
            </div>
            <div class="header-right-box">2x2<br>ID Photo</div>
        </div>
        
        <div class="header-divider"></div>

        <div class="form-title">Health Information Form for Students</div>

        <div class="section-title">Part 1: Student Information</div>
        
        <div class="info-row">
            <div class="input-group" style="flex: 2;"><label>Name:</label><div class="line-fill">{{ Auth::user()->name }}</div></div>
            <div class="input-group"><label>PUP Student No:</label><div class="line-fill">{{ Auth::user()->student_id ?? '__________' }}</div></div>
        </div>

        <div class="info-row">
            <div class="input-group" style="flex: 2;"><label>Home Address:</label><div class="line-fill"><input type="text" name="address" required></div></div>
            <div class="input-group"><label>School Year:</label><div class="line-fill">2025 - 2026</div></div>
        </div>

        <div class="info-row">
            <div class="input-group" style="flex: 0.5;"><label>Age:</label><div class="line-fill"><input type="number" name="age" required></div></div>
            <div class="input-group" style="flex: 0.8;"><label>Sex:</label><div class="line-fill"><input type="text" name="sex" required></div></div>
            <div class="input-group"><label>Civil Status:</label><div class="line-fill"><input type="text" name="civil_status" required></div></div>
            <div class="input-group" style="flex: 1.5;"><label>Course/College:</label><div class="line-fill">{{ Auth::user()->course ?? '__________' }}</div></div>
        </div>

        <div class="info-row">
            <div class="input-group"><label>Blood Type:</label><div class="line-fill"><input type="text" name="blood_type" required></div></div>
            <div class="input-group" style="flex: 2;"><label>Email Address:</label><div class="line-fill">{{ Auth::user()->email }}</div></div>
        </div>

        <div class="info-row">
            <div class="input-group"><label>Parent's Name/Guardian/Spouse:</label><div class="line-fill"><input type="text" name="emergency_contact_name" required></div></div>
        </div>

        <div class="info-row">
            <div class="input-group"><label>Landline:</label><div class="line-fill"><input type="text" name="landline"></div></div>
            <div class="input-group"><label>Cellphone:</label><div class="line-fill"><input type="text" name="emergency_contact_number" required></div></div>
        </div>

        <div class="section-title">Part 2: Medical History</div>
        <p style="font-size: 11px; margin: 5px 10px;">(Please check if you have/had any of the following)</p>
        <div class="medical-history-grid">
            @php $conditions = ['Asthma', 'Diabetes', 'Hypertension', 'Heart Disease', 'Seizures', 'Allergies', 'Anxiety/Depression', 'Vision Problems', 'Thyroid Problem', 'Primary Complex', 'Kidney Disease', 'Others: ______']; @endphp
            @foreach($conditions as $condition)
            <label style="font-size: 13px; font-weight: normal;">
                <input type="checkbox" name="medical_history[]" value="{{ $condition }}"> {{ $condition }}
            </label>
            @endforeach
        </div>

        <div class="section-title">Part 3: Personal & Social History</div>
        <div class="info-row" style="padding-left: 10px;">
            <div class="input-group">
                <label>Do you smoke?</label>
                <input type="radio" name="is_smoker" value="Yes" required> <span style="margin-right:10px">Yes</span>
                <input type="radio" name="is_smoker" value="No" required> No
            </div>
            <div class="input-group">
                <label>Do you drink alcohol?</label>
                <input type="radio" name="is_drinker" value="Yes" required> <span style="margin-right:10px">Yes</span>
                <input type="radio" name="is_drinker" value="No" required> No
            </div>
        </div>

        <div class="section-title">Part 4: Immunization / Vaccine History</div>
        <table class="vax-table">
            <thead>
                <tr><th>Vaccine Type</th><th>Date Given</th><th>Brand/Remarks</th></tr>
            </thead>
            <tbody>
                <tr><td>COVID-19 (Dose 1 & 2)</td><td><input type="text" name="vax_date_covid" style="width:100%; border:none;"></td><td><input type="text" name="vax_brand_covid" style="width:100%; border:none;"></td></tr>
                <tr><td>Flu Vaccine</td><td><input type="text" name="vax_date_flu" style="width:100%; border:none;"></td><td><input type="text" name="vax_brand_flu" style="width:100%; border:none;"></td></tr>
                <tr><td>Others:</td><td><input type="text" name="vax_date_other" style="width:100%; border:none;"></td><td><input type="text" name="vax_brand_other" style="width:100%; border:none;"></td></tr>
            </tbody>
        </table>

        <p style="margin-top: 30px; font-style: italic; font-size: 13px; line-height: 1.4;">
            I hereby certify that the above information is true and correct to the best of my knowledge. 
            I authorize the PUP Health Services to use this information for my medical care.
        </p>

        <div class="signature-section">
            <div class="sig-box">Signature of Parent/Guardian</div>
            <div class="sig-box">Signature of Student</div>
            <div class="sig-box">Date Signed</div>
        </div>

        <div class="cut-line">Detach here for Physician's Copy</div>

        <div class="physician-section">
            <h4 style="margin: 0 0 10px 0; text-align: center; font-size: 16px;">FOR PHYSICIAN USE ONLY</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 14px;">
                <div>BP: ________________ HR: ________________</div>
                <div>Findings: ___________________________</div>
            </div>
            <div style="margin-top: 15px; font-size: 14px;">Remarks/Recommendations: ____________________________________________________</div>
        </div>

        <div class="no-print" style="margin-top: 40px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
            <button type="submit" class="btn btn-success" style="padding: 15px 50px; font-size: 18px; font-weight: bold; background: #28a745; color: white; border: none; border-radius: 10px; cursor: pointer;">Finalize and Save Profile</button>
        </div>
    </div>
</form>
@endsection