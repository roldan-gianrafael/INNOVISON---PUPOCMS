<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ $monthFilter }}</title>

    <style>
    /* 1. Print & Base Styles */
    @page {
        margin: 115px 28px 85px;
    }

    @media print {
        .no-print { display: none !important; }
        body { margin: 0; padding: 0; }
        .page-header,
        .page-footer {
            position: fixed;
            left: 0;
            right: 0;
        }
        .page-header {
            top: -95px;
        }
        .page-footer {
            bottom: -65px;
        }
        .report-shell {
            margin: 0;
        }
    }
    
    body { 
        font-family: 'Arial', sans-serif; 
        color: #000; 
        line-height: 1.2; 
        margin: 40px; 
    }

    .pdf-mode .no-print {
        display: none !important;
    }

    .report-shell {
        position: relative;
    }

    .page-header {
        display: none;
        border-bottom: 2px solid #800000;
        padding: 0 28px 10px;
        background: #fff;
    }

    .page-header-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .page-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-header-left img {
        width: 44px;
        height: auto;
    }

    .page-header-copy {
        line-height: 1.15;
    }

    .page-header-copy strong {
        display: block;
        font-size: 14px;
        color: #800000;
        letter-spacing: 0.35px;
    }

    .page-header-copy span {
        display: block;
        font-size: 10px;
        color: #374151;
        text-transform: uppercase;
        font-weight: 700;
    }

    .page-header-meta {
        font-size: 10px;
        text-align: right;
        line-height: 1.3;
    }

    /* 2. Header & Logo Layout */
    .header-top { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 20px; 
    }

    .pup-logo-section {
        display: flex;
        align-items: center;
        gap: 12px; 
    }

    .pup-logo-section img {
        width: 70px; 
        height: auto;
    }

    .logo-text-box {
        text-align: left;
        line-height: 1.1;
    }

    .logo-text-box .title {
        font-weight: bold;
        font-size: 18px;
        color: #800000;
        letter-spacing: 0.5px;
    }

    .logo-text-box .sub {
        font-size: 11px;
        color: #333;
        text-transform: uppercase;
        font-weight: 600;
    }

    .bp-logo {
        border: 1.5px solid #000;
        padding: 10px 15px;
        font-weight: bold;
        font-size: 12px;
        text-align: center;
        width: 110px;
        line-height: 1.1;
    }

    /* 3. Report Info & Titles */
    .report-main-title { 
        text-align: center; 
        margin: 25px 0; 
        font-weight: bold; 
        font-size: 18px; 
        text-transform: uppercase;
        text-decoration: underline;
    }
            
    .info-section { 
        display: flex; 
        justify-content: space-between; 
        margin-bottom: 25px; 
        font-size: 13px; 
    }

    .info-left, .info-right { 
        width: 48%; 
    }

    .info-row { 
        margin-bottom: 8px; 
        display: flex; 
        align-items: flex-end;
    }

    .label { 
        font-weight: bold; 
        width: 130px; 
    }

    .value { 
        border-bottom: 1px solid #000; 
        flex-grow: 1; 
        padding-left: 8px; 
        padding-bottom: 2px;
    }

    /* 4. Table Customization */
    table { 
        width: 100%; 
        table-layout: fixed;
        border-collapse: collapse; 
        margin-top: 15px; 
        background: #fff; 
    }

    th, td { 
        border: 1px solid #000; 
        padding: 10px 6px; 
        font-size: 11px; 
        text-align: left; 
        overflow-wrap: anywhere;
        word-break: break-word;
        vertical-align: top;
    }

    th { 
        background-color: #f2f2f2; 
        font-weight: bold; 
        text-transform: uppercase;
        text-align: center;
    }

    .text-left { text-align: left; }
    .mar-report-table col:first-child {
        width: 38%;
    }

    .mar-report-table col.metric-col {
        width: 12.4%;
    }

    .bg-category { 
        background-color: #f9f9f9; 
        font-weight: bold; 
        text-align: left; 
        padding-left: 10px;
    }

    /* 5. Signatures & Footer */
    .footer-signatures { 
        margin-top: 50px; 
        display: flex; 
        justify-content: space-between; 
    }

    .sig-box { 
        width: 250px; 
        text-align: center; 
    }

    .sig-line { 
        border-top: 1px solid #000; 
        margin-top: 45px; 
        font-weight: bold; 
        padding-top: 5px; 
        text-transform: uppercase; 
        font-size: 12px; 
    }

    .official-footer { 
        border-top: 2px solid #800000; 
        margin-top: 60px; 
        padding-top: 12px; 
        font-size: 10px; 
        color: #333; 
    }

    .footer-details p { 
        margin: 2px 0; 
    }

    .footer-motto { 
        text-align: center; 
        font-weight: bold; 
        font-size: 15px; 
        margin-top: 15px; 
        text-transform: uppercase;
        color: #000;
    }

    .page-footer {
        display: none;
        padding: 8px 28px 0;
        background: #fff;
        border-top: 2px solid #800000;
        font-size: 9px;
        color: #374151;
    }

    .page-footer-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .page-footer-copy {
        line-height: 1.3;
    }

    .page-footer-motto {
        font-weight: 800;
        text-transform: uppercase;
        text-align: right;
        white-space: nowrap;
    }

    @media print {
        .page-header,
        .page-footer {
            display: block;
        }
    }

    /* 6. UI Components (No-Print) */
    .no-print-bar { 
        background: #1e293b; 
        color: white; 
        padding: 15px 25px; 
        display: flex; 
        justify-content: space-between; 
        align-items: center;
        border-radius: 8px; 
        margin-bottom: 25px; 
    }

    .btn-print { 
        background: #ef4444; 
        color: white; 
        border: none; 
        padding: 10px 20px; 
        cursor: pointer; 
        border-radius: 6px; 
        font-weight: bold; 
        transition: background 0.3s;
    }

    .btn-print:hover {
        background: #dc2626;
    }
</style>
</head>
<body class="{{ !empty($isPdf) ? 'pdf-mode' : '' }}">

    <div class="page-header" aria-hidden="true">
        <div class="page-header-inner">
            <div class="page-header-left">
                <img src="{{ asset('images/pup_logo.png') }}" alt="PUP Logo">
                <div class="page-header-copy">
                    <strong>PUP TAGUIG</strong>
                    <span>{{ strtoupper($title) }}</span>
                </div>
            </div>
            <div class="page-header-meta">
                <div>Medical Services Unit</div>
                <div>{{ date('F d, Y') }}</div>
            </div>
        </div>
    </div>

    <div class="page-footer" aria-hidden="true">
        <div class="page-footer-inner">
            <div class="page-footer-copy">
                General Santos Avenue, Lower Bicutan, Taguig City, Philippines 1632
                <br>Direct Line (02) 8837 5658 to 60 | Website: www.pup.edu.ph | Email: taguig@pup.edu.ph
            </div>
            <div class="page-footer-motto">The Country's First PolytechnicU</div>
        </div>
    </div>

    <div class="report-shell">
    <div class="no-print no-print-bar">
        <span><strong>Preview Mode:</strong> {{ $title }}</span>
        <div>
            <button onclick="window.print()" class="btn-print">Print / Save as PDF</button>
            <button onclick="window.close()" style="padding: 8px 15px; cursor: pointer;">Close</button>
        </div>
    </div>

    <div class="header-top">
    <div class="pup-logo-section">
        <img src="{{ asset('images/pup_logo.png') }}" alt="PUP Logo">
        <div class="logo-text-box">
            <div class="title">PUP TAGUIG</div>
        </div>
    </div>
    <div class="bp-logo">
        BAGONG PILIPINAS
    </div>
</div>

    <div class="report-main-title">
        Accomplishment Report as of {{ date('F d, Y') }}
    </div>

    <div class="info-section">
        <div class="info-left">
            <div class="info-row"><span class="label">Name:</span> <span class="value">Nurse Joyce</span></div>
            <div class="info-row"><span class="label">Position:</span> <span class="value">Nurse</span></div>
        </div>
        <div class="info-right">
            <div class="info-row"><span class="label">Date of Submission:</span> <span class="value">{{ date('m/d/Y') }}</span></div>
            <div class="info-row"><span class="label">Unit/Department:</span> <span class="value">Medical Services Unit</span></div>
        </div>
    </div>

    @if($type == 'mar')
        <table class="mar-report-table">
            <colgroup>
                <col>
                <col class="metric-col">
                <col class="metric-col">
                <col class="metric-col">
                <col class="metric-col">
                <col class="metric-col">
            </colgroup>
            <thead>
                <tr>
                    <th rowspan="2">MEDICAL CONDITIONS / SERVICES</th>
                    <th colspan="4">PATIENT TYPE</th>
                    <th rowspan="2">TOTAL</th>
                </tr>
                <tr>
                    <th>STUDENT</th>
                    <th>FACULTY</th>
                    <th>ADMIN</th>
                    <th>DEPENDENTS</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $consultationTotals = ['student' => 0, 'faculty' => 0, 'admin' => 0, 'dependent' => 0];
                @endphp
                <tr>
                    <td colspan="6" class="bg-category">1. CONSULTATION / TREATMENT</td>
                </tr>
                @foreach($data as $catIndex => $cat)
                    <tr class="bg-category">
                        <td colspan="6">{{ chr(65 + $catIndex) }}. {{ $cat->name }}</td>
                    </tr>
                    @foreach($cat->medicalConditions as $condition)
                        @php
                            $stu = $condition->consultations->where('user_type', 'Student')->count();
                            $fac = $condition->consultations->where('user_type', 'Faculty')->count();
                            $sta = $condition->consultations->filter(function ($consultation) {
                                return in_array($consultation->user_type, ['Admin', 'Staff'], true);
                            })->count();
                            $dep = $condition->consultations->filter(function ($consultation) {
                                return in_array($consultation->user_type, ['Dependent', 'Dependents'], true);
                            })->count();
                            $rowTotal = $stu + $fac + $sta +$dep;
                            $consultationTotals['student'] += $stu;
                            $consultationTotals['faculty'] += $fac;
                            $consultationTotals['admin'] += $sta;
                            $consultationTotals['dependent'] += $dep;
                        @endphp
                        <tr>
                            <td class="text-left" style="padding-left: 15px;">{{ $condition->name }}</td>
                            <td>{{ $stu ?: '' }}</td>
                            <td>{{ $fac ?: '' }}</td>
                            <td>{{ $sta ?: '' }}</td>
                            <td>{{ $dep ?: '' }}</td>
                            <td><strong>{{ $rowTotal }}</strong></td>
                        </tr>
                    @endforeach
                @endforeach
                <tr class="bg-category">
                    <td>Total Consultation</td>
                    <td>{{ $consultationTotals['student'] }}</td>
                    <td>{{ $consultationTotals['faculty'] }}</td>
                    <td>{{ $consultationTotals['admin'] }}</td>
                    <td>{{ $consultationTotals['dependent'] }}</td>
                    <td><strong>{{ array_sum($consultationTotals) }}</strong></td>
                </tr>
                <tr class="bg-category"><td colspan="6">2. MEDICAL CERTIFICATE / CLEARANCE - CERTIFICATE OF COMPLIANCE</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">A. Excused Letter</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr><td class="text-left" style="padding-left: 30px;">1. Category-based excused letter</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">B. COC for IJT</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">C. COC for Ladderized</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr class="bg-category"><td colspan="6">3. INJECTIONS</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">Injection Services</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr class="bg-category"><td colspan="6">4. REFERRALS</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">A. Ref. to Hospital without nurse</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">B. Ref. to Hospital with nurse</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">C. Referral</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr class="bg-category"><td colspan="6">5. OTHERS</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">Other Services</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr class="bg-category"><td colspan="6">6. ON-LINE CONSULTATION</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">A. Consultation</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">B. Medical Clearance</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">C. Others</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr class="bg-category"><td colspan="6">7. TRIAGE SURVEY</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">A. Online</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr class="bg-category"><td colspan="6">8. BULLETIN UPDATES</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">Bulletin Updates</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
            </tbody>
        </table>

        <table class="mar-report-table" style="margin-top: 28px;">
            <colgroup>
                <col>
                <col class="metric-col">
                <col class="metric-col">
                <col class="metric-col">
                <col class="metric-col">
                <col class="metric-col">
            </colgroup>
            <thead>
                <tr>
                    <th>GAD (CONSULTATION)</th>
                    <th>STUDENTS</th>
                    <th>FACULTY</th>
                    <th>ADMIN</th>
                    <th>DEPENDENT</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-category"><td colspan="6">GAD SUMMARY</td></tr>
                <tr><td class="text-left">Female</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr><td class="text-left">Male</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr class="bg-category"><td colspan="6">PWD</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">Male</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">Female</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr class="bg-category"><td colspan="6">Senior</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">Male</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">Female</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr class="bg-category"><td>Total</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
            </tbody>
        </table>

        <table class="mar-report-table" style="margin-top: 28px;">
            <colgroup>
                <col>
                <col class="metric-col">
                <col class="metric-col">
                <col class="metric-col">
                <col class="metric-col">
                <col class="metric-col">
            </colgroup>
            <thead>
                <tr>
                    <th>GAD (OJT/MEDICAL COMPLIANCE/MEDICAL FOR PROMOTION/EXCUSED LETTER)</th>
                    <th>STUDENTS</th>
                    <th>FACULTY</th>
                    <th>ADMIN</th>
                    <th>DEPENDENT</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-category"><td colspan="6">GAD SUMMARY</td></tr>
                <tr><td class="text-left">Female</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr><td class="text-left">Male</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr class="bg-category"><td colspan="6">PWD</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">Male</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">Female</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr class="bg-category"><td colspan="6">Senior</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">Male</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">Female</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr class="bg-category"><td>Total</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
            </tbody>
        </table>

        <table class="mar-report-table" style="margin-top: 28px;">
            <colgroup>
                <col>
                <col class="metric-col">
                <col class="metric-col">
                <col class="metric-col">
                <col class="metric-col">
                <col class="metric-col">
            </colgroup>
            <thead>
                <tr>
                    <th>GAD (TRIAGE ONLINE)</th>
                    <th>STUDENTS</th>
                    <th>FACULTY</th>
                    <th>ADMIN</th>
                    <th>DEPENDENT</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-category"><td colspan="6">GAD SUMMARY</td></tr>
                <tr><td class="text-left">Female</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr><td class="text-left">Male</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr class="bg-category"><td colspan="6">PWD</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">Male</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">Female</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr class="bg-category"><td colspan="6">Senior</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">Male</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">Female</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr class="bg-category"><td>Total</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
            </tbody>
        </table>

        <table class="mar-report-table" style="margin-top: 28px;">
            <colgroup>
                <col>
                <col class="metric-col">
                <col class="metric-col">
                <col class="metric-col">
                <col class="metric-col">
                <col class="metric-col">
            </colgroup>
            <thead>
                <tr>
                    <th>GAD (CONSULTATION + OJT/MEDICAL COMPLIANCE/MEDICAL FOR PROMOTION + TRIAGE)</th>
                    <th>STUDENTS</th>
                    <th>FACULTY</th>
                    <th>ADMIN</th>
                    <th>DEPENDENT</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-category"><td colspan="6">GAD SUMMARY</td></tr>
                <tr><td class="text-left">Female</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr><td class="text-left">Male</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr class="bg-category"><td colspan="6">PWD</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">Male</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">Female</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr class="bg-category"><td colspan="6">Senior</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">Male</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr><td class="text-left" style="padding-left: 15px;">Female</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                <tr class="bg-category"><td>Total</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
            </tbody>
        </table>



    @elseif($type == 'inventory')
    <table>
        <thead>
            <tr>
                    <th>ID</th>
                <th>ITEM DESCRIPTION</th>
                <th>CATEGORY</th>
                <th>UNIT</th>
                <th>STARTING STOCK</th>
                <th>CONSUMED</th>
                <th>CURRENT BALANCE</th>
                <th>DATE ADDED</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td class="text-left">{{ $item->name }}</td>
                <td>{{ $item->report_category }}</td>
                <td>{{ $item->unit }}</td>
                <td>{{ $item->starting_stock }}</td>
                <td>{{ $item->consumed }}</td>
                <td style="font-weight: bold;">{{ $item->current_balance }}</td>
                <td>{{ optional($item->date_added)->format('M d, Y') ?? optional($item->created_at)->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="8">No items found in the inventory.</td></tr>
            @endforelse
        </tbody>
    </table>




    @elseif($type == 'appointment')
        <table>
            <thead>
                <tr>
                    <th>DATE</th>
                    <th>PATIENT NAME</th>
                    <th>USER TYPE</th>
                    <th>PURPOSE / REASON</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $app)
                <tr>
                    <td>{{ date('M d, Y', strtotime($app->date)) }}</td>
                    <td class="text-left">{{ $app->name }}</td>
                    <td>{{ $app->user_type ?? 'N/A' }}</td>
                    <td>{{ $app->service }}</td>
                    <td>{{ $app->status }}</td>
                </tr>
                @empty
                <tr><td colspan="5">No recorded appointments.</td></tr>
                @endforelse
            </tbody>
        </table>
    @elseif($type == 'health_forms')
        <table>
            <thead>
                <tr>
                    <th>COURSE</th>
                    <th>ISSUED FORMS</th>
                    <th>WITH CONDITION</th>
                    <th>NO CONDITION</th>
                    <th>LAST ISSUED</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $form)
                <tr>
                    <td class="text-left">{{ $form->course }}</td>
                    <td>{{ $form->issued_count }}</td>
                    <td>{{ $form->with_condition_count }}</td>
                    <td>{{ $form->no_condition_count }}</td>
                    <td>{{ optional($form->last_issued_at)->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="5">No issued health forms found.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <div class="footer-signatures">
        <div class="sig-box">
            <p>Prepared by:</p>
            <div class="sig-line">NURSE / MEDICAL STAFF</div>
        </div>
        <div class="sig-box">
            <p>Noted by:</p>
            <div class="sig-line">BRANCH DIRECTOR</div>
        </div>
    </div>

    <div class="official-footer">
        <div class="footer-details">
            <p>General Santos Avenue, Lower Bicutan Taguig City Philippines, 1632</p>
            <p>Direct Line (02) 8837 5658 to 60</p>
            <p>Website: www.pup.edu.ph | Email: taguig@pup.edu.ph</p>
        </div>
        <div class="footer-motto">
            THE COUNTRY'S FIRST POLYTECHNICU
        </div>
    </div>
    </div>

</body>
</html>
