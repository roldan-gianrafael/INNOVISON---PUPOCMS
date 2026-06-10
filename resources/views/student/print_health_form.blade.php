<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Health Information Form</title>
    <style>
        @page { size: 8.5in 13in; margin: 0; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; background: #fff; color: #000; font-family: Arial, Helvetica, sans-serif; }
        .print-container { width: 100%; padding: 0.28in 0.42in 0.2in; line-height: 1.18; }
        .print-page { position: relative; }
        .document-code { position: absolute; top: -9px; right: 0; z-index: 2; width: 125px; font-family: Arial, sans-serif; font-size: 7px; line-height: 1.15; text-align: left; }
        .official-header-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .official-header-table td { padding: 0; border: 0; vertical-align: middle; }
        .official-logo-cell { width: 20%; text-align: center; }
        .official-heading-cell { width: 60%; padding-top: 8px !important; }
        .official-photo-cell { width: 20%; padding: 8px 0 0 8px !important; text-align: right; vertical-align: top !important; }
        .logo { width: 88px; height: 88px; object-fit: contain; }
        .official-heading-cell { font-family: "Times New Roman", Times, serif; }
        .official-heading-cell p { margin: 0; font-size: 10px; line-height: 1.17; }
        .official-heading-cell .univ-name { font-size: 16px; font-weight: normal; }
        .official-heading-cell .dept-name { margin-top: 2px; font-size: 17px; font-weight: normal; }
        .photo-box { width: 112px; height: 145px; margin-left: auto; padding: 34px 8px 0; overflow: hidden; border: 1px solid #000; font-size: 10px; line-height: 1.25; text-align: center; white-space: normal; word-wrap: break-word; }
        .official-title-table { width: 80%; margin-top: -58px; border-collapse: collapse; table-layout: fixed; }
        .official-title-table td { padding: 9px 0 5px; border-top: 1px solid #000; font-family: "Times New Roman", Times, serif; font-size: 14px; font-weight: bold; font-style: italic; text-align: center; }
        .section-header { width: 80%; margin-top: 8px; padding: 3px 0 4px; border: 0; font-family: "Times New Roman", Times, serif; font-size: 11px; font-weight: bold; font-style: italic; text-transform: uppercase; }
        .official-title-table + .section-header { margin-top: 34px; }
        .student-information-table { width: 100%; border-collapse: separate; border-spacing: 0 5px; table-layout: fixed; }
        .student-information-table td { padding: 0; border: 0; vertical-align: bottom; font-size: 10px; }
        .student-name-cell { width: 62% !important; padding-right: 8px !important; }
        .student-number-label { width: 14% !important; padding-right: 4px !important; font-weight: bold; white-space: nowrap; }
        .student-number-value { width: 24% !important; padding-left: 4px !important; border-bottom: 1px solid #000 !important; font-weight: bold; }
        .compound-information-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .compound-information-table td { padding: 0 3px 1px 0; font-size: 9px; vertical-align: bottom; }
        .student-full-name { display: inline-block; width: 86%; vertical-align: bottom; }
        .line-label { padding-right: 4px !important; font-weight: 700; white-space: nowrap; }
        .line-value { padding-left: 4px !important; border-bottom: 1px solid #000 !important; font-weight: bold; overflow-wrap: break-word; word-wrap: break-word; }
        .school-year-label,
        .email-label,
        .cellphone-label { padding-left: 10px !important; }
        .guardian-row { padding-top: 1px !important; white-space: nowrap; }
        .guardian-value { display: inline-block; width: 66%; white-space: normal; vertical-align: bottom; }
        .compound-row { padding: 0 !important; }
        .age-label { width: 5%; }
        .age-value { width: 10%; }
        .sex-label { width: 5%; padding-left: 7px !important; }
        .sex-value { width: 14%; }
        .civil-label { width: 11%; padding-left: 7px !important; }
        .civil-value { width: 14%; }
        .course-label { width: 17%; padding-left: 7px !important; }
        .course-value { width: 24%; padding-bottom: 1px !important; font-size: 8.5px !important; line-height: 1.05; white-space: normal; }
        .row { display: block; width: 100%; margin: 5px 0; }
        .field { display: inline-block; min-height: 13px; min-width: 90px; padding: 0 4px; border-bottom: 1px solid #000; color: #000; font-size: 10px; font-weight: bold; vertical-align: bottom; }
        .label, .labels { white-space: nowrap; font-size: 10px; }
        .label { font-weight: 700; }
        .print-page .row,
        .print-page .field,
        .print-page .label,
        .print-page .labels,
        .print-page .check-item,
        .print-page .medical-history-instruction,
        .print-page .medical-subsection-title,
        .print-page .vax-table th,
        .print-page .vax-table td,
        .print-page .cert-text,
        .print-page .sig-line,
        .print-page .physician-section {
            font-size: 10px !important;
        }
        .medical-subsection-title { font-weight: 700; line-height: 1.18; }
        .medical-subsection-heading { margin: 5px 0 3px; font-weight: 700; text-transform: none; }
        .disability-row .check-item { font-weight: 700; }
        .allergy-declaration { margin: 2px 0 3px 18px; font-size: 10px; line-height: 1.18; }
        .medicine-other-field { display: inline-block; }
        .medical-history-instruction { margin: 2px 0 3px 24px; font-size: 9px; font-style: italic; }
        .medical-check-table { width: 96%; margin: 3px 0 5px 4%; border-collapse: collapse; table-layout: fixed; }
        .medical-check-table td { width: 25%; padding: 2px 3px; border: 0; font-size: 9px; vertical-align: middle; }
        .write-line { display: inline-block; width: 72%; min-height: 9px; border-bottom: 1px solid #000; font-weight: bold; }
        .check-item { display: inline-block; margin-right: 10px; font-size: 9px; white-space: nowrap; vertical-align: middle; }
        .box-ui { display: inline-block; width: 13px; height: 13px; border: 1px solid #000; font-family: Arial, sans-serif; font-weight: bold; line-height: 12px; text-align: center; vertical-align: middle; }
        .covid-layout-table { width: 100%; margin-top: 5px; border-collapse: collapse; table-layout: fixed; }
        .covid-layout-table td { padding: 0; border: 0; vertical-align: top; }
        .covid-label-cell { width: 34%; padding-right: 8px !important; }
        .vaccinated-choice { margin: 5px 0 0 14px; }
        .vax-table { width: 100%; margin-top: 2px; border-collapse: collapse; }
        .vax-table th, .vax-table td { padding: 2px 3px; border: 1px solid #000; font-size: 8px; text-align: center; }
        .cert-text { margin-top: 5px; font-size: 8px; font-style: italic; line-height: 1.12; text-align: justify; }
        .cert-text-first { margin-top: 7px; }
        .signature-table { width: 100%; margin-top: 5px; border-collapse: separate; border-spacing: 12px 0; table-layout: fixed; page-break-inside: avoid; }
        .signature-table td { width: 33.333%; padding: 0; border: 0; vertical-align: bottom; text-align: center; }
        .sig-image { width: 120px; height: auto; margin-bottom: -10px; }
        .signature-space { height: 22px; }
        .signature-date-space { display: flex; align-items: flex-end; justify-content: center; padding-bottom: 3px; font-weight: bold; }
        .sig-line { min-height: 11px; margin-bottom: 2px; border-bottom: 1px solid #000; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .signature-caption { min-height: 14px; font-size: 8px; font-weight: 700; line-height: 1.08; text-align: center; }
        .physician-section { margin-top: 6px !important; padding: 6px !important; border-width: 1px !important; break-inside: avoid; page-break-inside: avoid; }
        .physician-section,
        .physician-section * { font-family: Arial, Helvetica, sans-serif; }
        .physician-section > p { margin: 0 0 1px !important; font-weight: 700 !important; }
        .physician-check-instruction { font-size: 7.5px; font-style: italic; font-weight: 700; text-align: center; }
        .physician-clearance-table,
        .physician-signature-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .physician-clearance-table td,
        .physician-signature-table td { padding: 1px 3px; border: 0; font-size: 7px; font-weight: 700; vertical-align: middle; }
        .physician-clearance-label { width: 20%; font-weight: bold; }
        .physician-clearance-table td:nth-child(2) { width: 13%; white-space: nowrap; }
        .physician-pending-cell { width: 67%; white-space: nowrap; }
        .physician-box { width: 9px !important; height: 9px !important; line-height: 8px !important; margin-right: 3px; }
        .physician-reason-line { display: inline-block; width: 55%; min-height: 9px; border-bottom: 1px solid #000; font-style: italic; vertical-align: bottom; }
        .physician-signature-table { margin-top: 5px; }
        .physician-signature-table td:first-child { width: 34%; padding-right: 12px; }
        .physician-signature-table td:last-child { width: 66%; padding-left: 12px; }
        .physician-signature-line { min-height: 15px; border-bottom: 1px solid #000; font-size: 7px; font-weight: bold; text-align: center; }
        .physician-signature-label { margin-top: 1px; font-size: 7px; font-weight: 700; text-align: center; }
        .print-action-bar { display: flex; justify-content: flex-end; gap: 8px; width: min(8.5in, 100%); margin: 0 auto; padding: 10px 12px 0; }
        .print-action-button { display: inline-flex; align-items: center; justify-content: center; padding: 9px 18px; border: 1px solid #800000; border-radius: 6px; background: #fff; color: #800000; font-size: 13px; font-weight: bold; text-decoration: none; }
        .print-action-button.is-download { background: #800000; color: #fff; }
        .print-action-button:hover { background: #facc15; color: #111827; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
    @unless($pdfMode ?? false)
        <div class="print-action-bar no-print">
            @if($adminViewer ?? false)
                <button class="print-action-button is-download" type="button" onclick="window.print()">Print</button>
                <button class="print-action-button" type="button" onclick="window.close()">Close View</button>
            @else
                <a class="print-action-button is-download" href="{{ route('student.health_form.download') }}">Download PDF</a>
                <a class="print-action-button" href="{{ url('/student/account?view=health-record') }}">Close</a>
            @endif
        </div>
    @endunless
    @php
        $studentPrintCopy = !($adminViewer ?? false);
        $healthFormLogo = ($pdfMode ?? false)
            ? public_path('images/pup_logo_print.jpg')
            : asset('images/pup_logo_print.jpg');
    @endphp
    @include('admin.partials.health_form_body')
    @if(!($pdfMode ?? false) && !($adminViewer ?? false))
        <script>
            window.addEventListener('load', function () {
                window.setTimeout(function () {
                    window.print();
                }, 250);
            });
        </script>
    @endif
</body>
</html>
