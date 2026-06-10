<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Health Information Form</title>
    <style>
        @page { size: 8.5in 13in; margin: 0; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; background: #fff; color: #000; font-family: Arial, sans-serif; }
        .print-container { width: 100%; padding: 0.08in 0.38in; line-height: 1.05; }
        .print-page { position: relative; }
        .document-code { position: absolute; top: 0; right: 0; z-index: 2; width: 130px; font-size: 7px; line-height: 1.1; text-align: right; }
        .header-section { display: flex; align-items: flex-start; position: relative; min-height: 105px; margin-bottom: 0; padding-top: 9px; }
        .logo { width: 58px; height: 58px; margin: 0 10px 0 40px; object-fit: contain; }
        .header-text p { margin: 0; line-height: 1.15; font-size: 8px !important; }
        .header-text p:nth-child(3) { font-size: 8px !important; }
        .header-text .univ-name { font-size: 12px !important; font-weight: bold; }
        .header-text .dept-name { font-size: 14px !important; font-weight: bold; }
        .photo-box { position: absolute; top: 15px; right: 0; display: flex; align-items: center; justify-content: center; width: 115px; height: 90px; overflow: hidden; border: 1px solid #000; padding: 6px; font-size: 7px; line-height: 1.15; text-align: center; }
        .photo-box img { width: 100%; height: 100%; object-fit: cover; }
        .header-divider { width: auto; margin: -25px 115px 0 0; border: 0; border-top: 1px solid #000; }
        .form-title { margin: 4px 0 3px; text-align: center; font-size: 12px; font-weight: bold; font-style: italic; }
        .section-header { margin-top: 3px; padding-left: 4px; font-size: 9px; font-weight: bold; font-style: italic; text-transform: uppercase; }
        .row { display: flex; align-items: baseline; gap: 5px; margin-bottom: 1px; }
        .field { min-height: 10px; flex: 1; padding-left: 3px; border-bottom: 1px solid #000; color: #000; font-size: 8px; font-weight: bold; }
        .label, .labels { white-space: nowrap; font-size: 8px; }
        .label { font-weight: bold; }
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
            font-size: 8px !important;
        }
        .medical-subsection-title { font-weight: bold; line-height: 1.1; }
        .medical-subsection-heading { margin: 3px 0 2px; text-transform: uppercase; }
        .allergy-declaration { margin: 0 0 1px; font-size: 8px; line-height: 1.1; }
        .medicine-other-field { grid-column: span 2; }
        .contact-row .field { min-width: 120px; }
        .medical-attention-row { align-items: center; }
        .medical-history-instruction { margin: 0 0 1px 5px; font-size: 7px; font-style: italic; }
        .checkbox-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1px 3px; margin: 2px 0 2px 12px; }
        .check-item { display: flex; align-items: center; gap: 3px; font-size: 7px; }
        .box-ui { display: inline-block; width: 9px; height: 9px; border: 1px solid #000; font-weight: bold; line-height: 8px; text-align: center; }
        .covid-history-row { align-items: flex-start; margin-top: 3px; }
        .vaccinated-choice { display: flex; align-items: center; gap: 5px; margin: 3px 0 0 14px; }
        .vax-table { width: 100%; margin-top: 2px; border-collapse: collapse; }
        .vax-table th, .vax-table td { padding: 1px 2px; border: 1px solid #000; font-size: 7px; text-align: center; }
        .cert-text { margin-top: 3px; font-size: 7px; font-style: italic; line-height: 1.02; text-align: justify; }
        .cert-text-first { margin-top: 5px; }
        .signature-table { width: 100%; margin-top: 2px; border-collapse: separate; border-spacing: 10px 0; table-layout: fixed; page-break-inside: avoid; }
        .signature-table td { width: 33.333%; padding: 0; border: 0; vertical-align: bottom; text-align: center; }
        .sig-image { width: 120px; height: auto; margin-bottom: -10px; }
        .signature-space { height: 17px; }
        .signature-date-space { display: flex; align-items: flex-end; justify-content: center; padding-bottom: 3px; font-weight: bold; }
        .sig-line { min-height: 10px; margin-bottom: 1px; border-bottom: 1px solid #000; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .signature-caption { min-height: 13px; font-size: 7px; line-height: 1.05; text-align: center; }
        .physician-section { margin-top: 3px !important; padding: 4px !important; border-width: 1px !important; break-inside: avoid; page-break-inside: avoid; }
        .physician-section > p { margin: 0 0 1px !important; }
        .physician-check-instruction { font-size: 7.5px; font-style: italic; text-align: center; }
        .physician-clearance-table,
        .physician-signature-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .physician-clearance-table td,
        .physician-signature-table td { padding: 1px 3px; border: 0; font-size: 7px; vertical-align: middle; }
        .physician-clearance-label { width: 20%; font-weight: bold; }
        .physician-clearance-table td:nth-child(2) { width: 13%; white-space: nowrap; }
        .physician-pending-cell { width: 67%; white-space: nowrap; }
        .physician-box { width: 9px !important; height: 9px !important; line-height: 8px !important; margin-right: 3px; }
        .physician-reason-line { display: inline-block; width: 55%; min-height: 9px; border-bottom: 1px solid #000; font-style: italic; vertical-align: bottom; }
        .physician-signature-table { margin-top: 5px; }
        .physician-signature-table td:first-child { width: 34%; padding-right: 12px; }
        .physician-signature-table td:last-child { width: 66%; padding-left: 12px; }
        .physician-signature-line { min-height: 15px; border-bottom: 1px solid #000; font-size: 7px; font-weight: bold; text-align: center; }
        .physician-signature-label { margin-top: 1px; font-size: 7px; font-weight: bold; text-align: center; }
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
