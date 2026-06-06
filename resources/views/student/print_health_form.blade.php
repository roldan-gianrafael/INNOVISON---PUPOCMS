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
        .print-container { width: 100%; padding: 0.12in 0.42in; line-height: 1.12; }
        .header-section { display: flex; align-items: flex-start; position: relative; min-height: 130px; margin-bottom: 0; }
        .logo { width: 80px; height: 80px; margin: 0 15px 0 50px; object-fit: contain; }
        .header-text p { margin: 0; line-height: 1.3; font-size: 10px !important; }
        .header-text p:nth-child(3) { font-size: 11px !important; }
        .header-text .univ-name { font-size: 16px !important; font-weight: bold; }
        .header-text .dept-name { font-size: 18px !important; font-weight: bold; }
        .photo-box { position: absolute; top: 0; right: 0; display: flex; align-items: center; justify-content: center; width: 150px; height: 130px; overflow: hidden; border: 1px solid #000; text-align: center; }
        .photo-box img { width: 100%; height: 100%; object-fit: cover; }
        .header-divider { width: auto; margin: -43px 150px 0 0; border: 0; border-top: 1px solid #000; }
        .form-title { margin: 9px 0 8px; text-align: center; font-size: 16px; font-weight: bold; font-style: italic; }
        .section-header { margin-top: 7px; padding-left: 5px; font-size: 13px; font-weight: bold; font-style: italic; text-transform: uppercase; }
        .row { display: flex; align-items: baseline; gap: 8px; margin-bottom: 3px; }
        .field { min-height: 15px; flex: 1; padding-left: 4px; border-bottom: 1px solid #000; color: #000; font-size: 12px; font-weight: bold; }
        .label, .labels { white-space: nowrap; font-size: 11px; }
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
        .print-page .physician-section,
        .print-page .generated-report-caption {
            font-size: 11px !important;
        }
        .medical-subsection-title { font-weight: bold; line-height: 1.25; }
        .medical-subsection-heading { margin: 6px 0 4px; text-transform: uppercase; }
        .allergy-declaration { margin: 0 0 4px; font-size: 11px; line-height: 1.35; }
        .medicine-other-field { grid-column: span 2; }
        .contact-row .field { min-width: 120px; }
        .medical-attention-row { align-items: center; }
        .medical-history-instruction { margin: 0 0 3px 5px; font-size: 9px; font-style: italic; }
        .checkbox-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 3px; margin: 4px 0 4px 14px; }
        .check-item { display: flex; align-items: center; gap: 4px; font-size: 10px; }
        .box-ui { display: inline-block; width: 11px; height: 11px; border: 1px solid #000; font-weight: bold; line-height: 10px; text-align: center; }
        .vax-table { width: 100%; margin-top: 4px; border-collapse: collapse; }
        .vax-table th, .vax-table td { padding: 2px 3px; border: 1px solid #000; font-size: 10px; text-align: center; }
        .cert-text { margin-top: 8px; font-size: 9px; font-style: italic; line-height: 1.12; text-align: justify; }
        .cert-text-first { margin-top: 18px; }
        .signature-row { display: flex; align-items: flex-end; justify-content: space-between; gap: 14px; margin-top: 4px; break-inside: avoid; page-break-inside: avoid; }
        .sig-block { width: auto; flex: 1; text-align: center; }
        .sig-image { width: 120px; height: auto; margin-bottom: -10px; }
        .signature-space { height: 25px; }
        .signature-date-space { display: flex; align-items: flex-end; justify-content: center; padding-bottom: 3px; font-weight: bold; }
        .sig-line { min-height: 13px; margin-bottom: 1px; border-bottom: 1px solid #000; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .physician-section { margin-top: 6px !important; padding: 6px !important; break-inside: avoid; page-break-inside: avoid; }
        .physician-section > p { margin: 0 0 3px !important; }
        .physician-signature-row { margin-top: 6px !important; gap: 14px !important; }
        .clinic-verifier-block { min-height: 38px !important; }
        .clinic-verifier-line { padding-top: 10px !important; }
        .generated-report-caption { margin: 4px 0 0; padding-top: 2px; border-top: 1px solid #9ca3af; font-size: 8px; line-height: 1.1; break-inside: avoid; page-break-inside: avoid; }
        .generated-report-caption .signature-note { margin-bottom: 2px; color: #4b5563; font-weight: 700; text-align: right; }
        .generated-report-caption .privacy-note { color: #9f4b5a; font-weight: 800; text-align: center; }
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
            <a class="print-action-button is-download" href="{{ route('student.health_form.download') }}">Download PDF</a>
            <a class="print-action-button" href="{{ url('/student/account?view=health-record') }}">Close</a>
        </div>
    @endunless
    @php
        $studentPrintCopy = true;
        $healthFormLogo = ($pdfMode ?? false)
            ? public_path('images/pup_logo_print.jpg')
            : asset('images/pup_logo_print.jpg');
    @endphp
    @include('admin.partials.health_form_body')
    @unless($pdfMode ?? false)
        <script>
            window.addEventListener('load', function () {
                window.setTimeout(function () {
                    window.print();
                }, 250);
            });
        </script>
    @endunless
</body>
</html>
