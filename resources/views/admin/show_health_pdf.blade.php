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
    $logoPath = public_path('images/pup_logo.png');
    $studentPhotoPath = $profile->student_photo ? public_path('storage/' . $profile->student_photo) : null;
    $studentSignaturePath = $profile->digital_signature ? public_path('storage/' . $profile->digital_signature) : null;
@endphp

<div class="no-print" style="text-align: right; padding: 10px; max-width: 8.5in; margin: 0 auto; display: flex; justify-content: flex-end; gap: 10px;">
    <button onclick="window.print()" style="background: #800000; border: none; padding: 10px 22px; font-weight: bold; color: white; border-radius: 5px; cursor: pointer;">
        PRINT FORM
    </button>
    <button onclick="window.close()" style="background: #64748b; border: none; padding: 10px 22px; font-weight: bold; color: white; border-radius: 5px; cursor: pointer;">
        CLOSE
    </button>
</div>

@include('admin.partials.health_form_body')
</body>
</html>
