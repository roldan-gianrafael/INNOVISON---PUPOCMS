<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ $monthFilter }}</title>

    <style>
    /* 1. Print & Base Styles */
    @media print { 
        .no-print { display: none !important; } 
        body { margin: 0; padding: 10px; }
    }
    
    body { 
        font-family: 'Arial', sans-serif; 
        color: #000; 
        line-height: 1.2; 
        margin: 40px; 
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
        border-collapse: collapse; 
        margin-top: 15px; 
        background: #fff; 
    }

    th, td { 
        border: 1px solid #000; 
        padding: 10px 6px; 
        font-size: 11px; 
        text-align: center; 
    }

    th { 
        background-color: #f2f2f2; 
        font-weight: bold; 
        text-transform: uppercase;
    }

    .text-left { text-align: left; }
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
<body>



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
        <table>
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
                @foreach($data as $cat)
                    <tr class="bg-category">
                        <td colspan="1">CATEGORY: {{ $cat->name }}</td>
                    </tr>
                    @foreach($cat->medicalConditions as $condition)
                        @php
                            $stu = $condition->consultations->where('user_type', 'Student')->count();
                            $fac = $condition->consultations->where('user_type', 'Faculty')->count();
                            $sta = $condition->consultations->where('user_type', 'Admin')->count();
                            $dep = $condition->consultations->where('user_type', 'Dependendents')->count();
                            $rowTotal = $stu + $fac + $sta +$dep;
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
            </tbody>
        </table>



    @elseif($type == 'inventory')
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th>ITEM DESCRIPTION</th>
                <th>CATEGORY</th>
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
                <td>{{ $item->category }}</td>
                <td>{{ $item->quantity }}</td> {{-- Since matic bumabawas, current qty na ang starting natin --}}
                <td>0</td> {{-- Placeholder: 0 muna dahil walang consumed column --}}
                <td style="font-weight: bold;">{{ $item->quantity }}</td>
                <td>{{ $item->created_at->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="7">No items found in the inventory.</td></tr>
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

</body>
</html>