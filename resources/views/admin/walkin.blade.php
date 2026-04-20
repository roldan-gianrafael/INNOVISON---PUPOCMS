@extends('layouts.admin')
@section('title', 'Patient Intake')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .notification-toast {
        position: fixed; top: 25px; right: 25px;
        background: linear-gradient(135deg, #15803d, #166534); color: #ffffff; padding: 15px 20px;
        border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        z-index: 10000; display: flex; align-items: center;
        justify-content: space-between; min-width: 380px;
        gap: 16px;
        animation: slideInRight 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .toast-copy {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #ffffff;
    }
    .toast-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 48px;
        height: 28px;
        padding: 0 10px;
        border-radius: 999px;
        background: rgba(255,255,255,0.18);
        border: 1px solid rgba(255,255,255,0.28);
        color: #ffffff;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        flex-shrink: 0;
    }
    .toast-title {
        display: block;
        font-size: 14px;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.2;
    }
    .toast-subtitle {
        display: block;
        font-size: 11px;
        opacity: 0.95;
        color: rgba(255,255,255,0.92);
        margin-top: 2px;
    }
    .btn-toast-action {
        background: rgba(255,255,255,0.25); border: 1px solid rgba(255,255,255,0.4);
        color: #ffffff; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;
        flex-shrink: 0;
    }

    .mode-header {
        padding: 22px 24px; color: white; display: flex; align-items: center;
        justify-content: center; gap: 12px; border-radius: 12px 12px 0 0;
        margin: -25px -25px 25px -25px;
        transition: background 0.4s ease;
    }
    .bg-scan { background: linear-gradient(135deg, #7f1d1d, #991b1b 55%, #b91c1c); }
    .bg-register { background: linear-gradient(135deg, #1e293b, #334155 58%, #475569); }

    .mode-header-badge {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: rgba(255,255,255,0.16);
        border: 1px solid rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.08em;
    }

    .mode-header-copy {
        text-align: left;
    }

    .mode-header-copy h3 {
        color: #ffffff;
    }

    .mode-header-copy p {
        margin: 4px 0 0;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.96);
        opacity: 1;
        line-height: 1.45;
        letter-spacing: 0.01em;
    }

    .scanner-box {
        width: 100% !important; max-width: 480px; aspect-ratio: 16 / 9;
        margin: 0 auto; background: radial-gradient(circle at top, #1f2937 0%, #0f172a 58%, #020617 100%);
        border: 2px solid #cbd5e1;
        border-radius: 16px; overflow: hidden; position: relative;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.04), 0 20px 40px rgba(15, 23, 42, 0.18);
    }
    .scanner-box video { object-fit: cover !important; }
    .scanner-box::before {
        content: '';
        position: absolute;
        inset: 16px;
        border: 1px dashed rgba(255,255,255,0.18);
        border-radius: 12px;
        pointer-events: none;
        z-index: 2;
    }

    .scan-stage {
        transform-style: preserve-3d;
        transform-origin: center;
        transition: transform 0.55s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.25s ease;
    }

    .scan-stage.is-flipping {
        transform: rotateY(180deg) scale(0.98);
        opacity: 0.82;
    }

    .scan-line-overlay {
        position: absolute;
        left: 8%;
        width: 84%;
        height: 4px;
        border-radius: 999px;
        background: linear-gradient(90deg, rgba(16,185,129,0) 0%, rgba(52,211,153,0.95) 20%, rgba(167,243,208,1) 50%, rgba(52,211,153,0.95) 80%, rgba(16,185,129,0) 100%);
        z-index: 10;
        box-shadow: 0 0 14px rgba(110, 231, 183, 0.95), 0 0 28px rgba(16, 185, 129, 0.45);
        animation: scan-animation 2.1s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }
    .scan-line-overlay::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        top: -22px;
        height: 48px;
        background: linear-gradient(180deg, rgba(16,185,129,0) 0%, rgba(52,211,153,0.16) 45%, rgba(16,185,129,0) 100%);
        filter: blur(4px);
        pointer-events: none;
    }

    .form-control { display: block; width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; margin-bottom: 10px; }
    
    /* Password Toggle Styling Fix */
    .password-wrapper { position: relative; width: 100%; margin-bottom: 10px; }
    .password-wrapper .form-control { margin-bottom: 0; padding-right: 45px; }
    .password-toggle {
        position: absolute; right: 15px; top: 50%;
        transform: translateY(-50%);
        color: #64748b; cursor: pointer; z-index: 10;
        font-size: 1.1rem;
    }

    #scan-loading {
        display: none; position: absolute; inset: 0;
        background: rgba(255, 255, 255, 0.9); z-index: 20;
        flex-direction: column; justify-content: center; align-items: center;
        border-radius: 12px;
    }
    .spinner {
        width: 40px; height: 40px; border: 4px solid #f3f3f3;
        border-top: 4px solid #8B0000; border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    .scan-method-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
        padding: 14px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: linear-gradient(135deg, #fffaf9, #f8fafc);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    }

    .scan-method-title {
        margin: 0;
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
    }

    .scan-method-note {
        margin: 4px 0 0;
        font-size: 12px;
        color: #64748b;
        line-height: 1.5;
    }

    .btn-scan-switch {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        border-radius: 999px;
        padding: 10px 14px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        white-space: nowrap;
        box-shadow: 0 6px 14px rgba(15, 23, 42, 0.06);
    }

    .scan-method-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        color: #9a3412;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .scan-surface {
        padding: 16px;
        border-radius: 16px;
        background: linear-gradient(180deg, #ffffff, #f8fafc);
        border: 1px solid #e2e8f0;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.9), 0 12px 30px rgba(15, 23, 42, 0.05);
    }

    .scan-inline-note {
        margin: 0 0 14px;
        padding: 10px 12px;
        border-radius: 10px;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        color: #9a3412;
        font-size: 12px;
        line-height: 1.5;
    }

    .manual-find-btn {
        min-width: 128px;
        padding: 0 20px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #7f1d1d, #991b1b 55%, #b91c1c);
        color: #ffffff;
        font-weight: 800;
        letter-spacing: 0.03em;
        box-shadow: 0 12px 24px rgba(127, 29, 29, 0.24);
        transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
    }

    .manual-find-btn:hover,
    .manual-find-btn:focus {
        transform: translateY(-1px);
        box-shadow: 0 16px 28px rgba(127, 29, 29, 0.3);
        filter: brightness(1.03);
        outline: none;
    }

    .manual-find-btn:active {
        transform: translateY(0);
        box-shadow: 0 8px 18px rgba(127, 29, 29, 0.22);
    }

    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    @keyframes scan-animation {
        0% { top: 16%; opacity: 0.9; }
        50% { top: 78%; opacity: 1; }
        100% { top: 16%; opacity: 0.9; }
    }
    @keyframes slideInRight { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>
@endpush

@section('content')
@php
    $role = \App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '');
    $basePrefix = $role === \App\Models\User::ROLE_ADMIN ? '/assistant' : '/admin';
    $currentMode = in_array($mode ?? '', ['scan', 'assisted'], true) ? $mode : '';
    $idpBaseUrl = rtrim((string) config('services.idp.base_url', ''), '/');
    $idpClientId = trim((string) config('services.idp.client_id', ''));
    $portalRegisterUrl = ($idpBaseUrl !== '' && $idpClientId !== '')
        ? $idpBaseUrl . '/login?' . http_build_query(['client_id' => $idpClientId])
        : route('login');
@endphp

@if(session('consultation_done'))
<div id="successToast" class="notification-toast">
    <div class="toast-copy">
        <span class="toast-badge">Done</span>
        <div>
            <strong class="toast-title">Consultation Done!</strong>
            <span class="toast-subtitle">Record saved successfully.</span>
        </div>
    </div>
    <button onclick="location.href='{{ url($basePrefix . '/walkin') }}?mode=scan'" class="btn-toast-action">Open Scan / Bio</button>
</div>
@endif

<div style="max-width: 980px; margin: 20px auto;">
    @if($currentMode === '')
    <div class="card p-4 shadow-sm" style="border-radius: 18px; border: none; margin-bottom: 20px;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:18px; flex-wrap:wrap;">
            <div>
                <p style="margin:0 0 8px; font-size:12px; font-weight:800; letter-spacing:1px; color:#8B0000; text-transform:uppercase;">Patient Intake</p>
                <h2 style="margin:0; font-size:28px; font-weight:800; color:#0f172a;">Choose how you want to begin the consultation flow</h2>
                <p style="margin:10px 0 0; color:#475569; max-width:680px;">
                    Use the identity portal for official account registration, scan an existing school user through barcode or BioSync, or let clinic staff complete an assisted intake when the patient cannot register alone.
                </p>
            </div>
            <a href="{{ url($basePrefix . '/appointments') }}" class="btn" style="background:#f8fafc; border:1px solid #cbd5e1; color:#334155; font-weight:700; border-radius:12px; white-space:nowrap;">
                BACK TO APPOINTMENTS
            </a>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-top:24px;">
            <a href="{{ $portalRegisterUrl }}" target="_blank" rel="noopener noreferrer" style="text-decoration:none; color:inherit;">
                <div style="height:100%; padding:20px; border-radius:16px; border:1px solid #e2e8f0; background:linear-gradient(135deg, #fff7ed, #ffffff); box-shadow:0 10px 24px rgba(15, 23, 42, 0.05);">
                    <div style="width:48px; height:48px; border-radius:14px; background:#8B0000; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; margin-bottom:14px;">IDP</div>
                    <h3 style="margin:0 0 8px; font-size:18px; font-weight:800; color:#111827;">Register via IDP</h3>
                    <p style="margin:0; color:#475569; line-height:1.55;">Open the centralized identity portal in a new tab so the patient can create or complete their official account first.</p>
                </div>
            </a>

            <a href="{{ url()->current() }}?mode=scan" style="text-decoration:none; color:inherit;">
                <div style="height:100%; padding:20px; border-radius:16px; border:{{ $currentMode === 'scan' ? '2px solid #8B0000' : '1px solid #e2e8f0' }}; background:{{ $currentMode === 'scan' ? 'linear-gradient(135deg, #fff5f5, #ffffff)' : 'linear-gradient(135deg, #f8fafc, #ffffff)' }}; box-shadow:0 10px 24px rgba(15, 23, 42, 0.05);">
                    <div style="width:48px; height:48px; border-radius:14px; background:#0f172a; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; margin-bottom:14px;">SB</div>
                    <h3 style="margin:0 0 8px; font-size:18px; font-weight:800; color:#111827;">Scan / Bio</h3>
                    <p style="margin:0; color:#475569; line-height:1.55;">Use barcode scanning, BioSync, or manual ID entry to identify an existing school user and continue directly to consultation.</p>
                </div>
            </a>

            <a href="{{ url()->current() }}?mode=assisted" style="text-decoration:none; color:inherit;">
                <div style="height:100%; padding:20px; border-radius:16px; border:{{ $currentMode === 'assisted' ? '2px solid #334155' : '1px solid #e2e8f0' }}; background:{{ $currentMode === 'assisted' ? 'linear-gradient(135deg, #eef2ff, #ffffff)' : 'linear-gradient(135deg, #f8fafc, #ffffff)' }}; box-shadow:0 10px 24px rgba(15, 23, 42, 0.05);">
                    <div style="width:48px; height:48px; border-radius:14px; background:#334155; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; margin-bottom:14px;">AI</div>
                    <h3 style="margin:0 0 8px; font-size:18px; font-weight:800; color:#111827;">Assisted Intake</h3>
                    <p style="margin:0; color:#475569; line-height:1.55;">Let clinic staff capture the patient record on their behalf when illness or urgency makes self-registration impractical.</p>
                </div>
            </a>
        </div>
    </div>
    @endif

@if($currentMode !== '')
<div class="card p-4 shadow-sm" style="border-radius: 15px; border: none; max-width: 550px; margin: 20px auto;">
    
    <div id="dynamicHeader" class="mode-header {{ $currentMode === 'assisted' ? 'bg-register' : 'bg-scan' }}">
        <div id="headerIcon" class="mode-header-badge">{{ $currentMode === 'assisted' ? 'AI' : 'SB' }}</div>
        <div class="mode-header-copy">
            <h3 id="headerTitle" style="margin: 0; font-weight: 800; text-transform: uppercase; font-size: 1rem; letter-spacing: 1px;">
                {{ $currentMode === 'assisted' ? 'Assisted Intake Ready' : 'Scan / Bio Ready' }}
            </h3>
            <p id="headerSubtitle">
                {{ $currentMode === 'assisted' ? 'Capture the patient basics first, then continue to consultation.' : 'Choose barcode scanning or BioSync mode to identify the patient.' }}
            </p>
        </div>
    </div>

    <div id="scanForm" style="{{ $currentMode === 'assisted' ? 'display:none;' : '' }}">
        <div id="scanStage" class="scan-stage">
            <div class="scan-method-bar">
                <div>
                <p id="scanMethodTitle" class="scan-method-title">Scan Barcode</p>
                <p id="scanMethodNote" class="scan-method-note">Use the camera to capture the patient barcode, or switch to BioSync mode for identity matching.</p>
                <span id="scanMethodBadge" class="scan-method-badge">Barcode Active</span>
                </div>
                <button type="button" id="btnSwitchScanMode" class="btn-scan-switch">Switch to BioSync</button>
            </div>

            <div id="scanner-container-scan" class="scan-surface" style="position: relative;">
                <p id="scanInlineNote" class="scan-inline-note">Barcode mode is active. Point the camera at the patient barcode, or switch to BioSync for the upcoming biometric flow.</p>
                <div id="barcodeScanPanel">
                    <div id="scan-loading">
                        <div class="spinner"></div>
                        <p style="margin-top:10px; color:#8B0000; font-weight:bold; font-size: 12px;">Verifying...</p>
                    </div>
                    <div id="readerScan" class="scanner-box">
                        <div class="scan-line-overlay"></div>
                    </div>
                </div>

                <div id="bioSyncPendingPanel" style="display:none; background:linear-gradient(180deg, #f8fafc, #eef2ff); border:1px dashed #cbd5e1; border-radius:12px; padding:30px 22px; text-align:center;">
                    <div style="width:60px; height:60px; margin:0 auto 14px; border-radius:18px; background:#dbeafe; color:#1d4ed8; display:flex; align-items:center; justify-content:center; font-weight:900; box-shadow:0 10px 20px rgba(59,130,246,0.12);">BIO</div>
                    <h4 style="margin:0 0 8px; font-size:18px; color:#0f172a; font-weight:800;">BioSync Pending</h4>
                    <p style="margin:0; color:#64748b; line-height:1.6; font-size:13px;">This mode is reserved for the upcoming BioSync integration. For now, please switch back to barcode scanning or use assisted intake.</p>
                </div>
            </div>
        
            <div class="text-center mt-3">
                <button type="button" id="btnShowManual" style="background:none; border:none; color:#8B0000; text-decoration:underline; cursor:pointer; font-weight:600; font-size: 0.85rem;">
                    Type Student Number Manually
                </button>
            </div>

            <div id="manualInputArea" style="display:none;" class="mt-3">
                <form id="walkinFormManual" class="d-flex gap-2">
                    <input type="text" id="student_id_manual" placeholder="Enter student number" class="form-control" style="margin-bottom:0;" required>
                    <button type="submit" class="manual-find-btn">Find</button>
                </form>
            </div>

            <div class="mt-4 pt-3" style="border-top: 1px dashed #cbd5e1;">
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="{{ url($basePrefix . '/walkin') }}" class="btn w-100 py-2" style="flex:1 1 180px; background: #ffffff; border: 1px solid #cbd5e1; color: #475569; font-weight: 700; font-size: 0.8rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none;">
                     BACK TO INTAKE OPTIONS
                </a>
                <a href="{{ url($basePrefix . '/appointments') }}" class="btn w-100 py-2" style="flex:1 1 180px; background: #f8fafc; border: 1px solid #cbd5e1; color: #475569; font-weight: 600; font-size: 0.8rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none;">
                 BACK TO APPOINTMENTS LIST
                </a>
                </div>
            </div>
        </div>
    </div>

    <div id="registerForm" style="{{ $currentMode === 'assisted' ? '' : 'display:none;' }}">
        <form id="formRegisterStudent">
            @csrf

            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px 16px; margin-bottom:16px;">
                <strong style="display:block; color:#0f172a; font-size:13px; margin-bottom:4px;">Staff-assisted patient capture</strong>
                <p style="margin:0; color:#64748b; font-size:12px; line-height:1.5;">Capture the patient’s basic identity details here, then continue to the consult form for the clinical information and assessment.</p>
            </div>

            <div class="mb-3">
                <label style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase;">Student Number / Reference ID</label>
                <div class="d-flex gap-2">
                    <input type="text" id="reg_student_id" class="form-control mb-0" style="background: #ffffff; font-weight: bold; border: 2px solid #cbd5e1;" placeholder="Enter student number or reference ID" required>
                    <input type="hidden" id="reg_barcode">
                </div>
            </div>
            
            <div class="mb-2">
                <label style="font-size: 11px; font-weight: 700; color: #475569;">PATIENT ROLE</label>
                <select id="reg_user_type" class="form-control" required>
                    <option value="" disabled selected>-- Choose Patient Role --</option>
                    <option value="Guest">Guest</option>
                    <option value="Dependent">Dependent</option>
                    <option value="Student">Student</option>
                    <option value="Faculty">Faculty</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            
            <div class="d-flex gap-2">
                <input type="text" id="reg_first_name" placeholder="First Name" class="form-control" required>
                <input type="text" id="reg_last_name" placeholder="Last Name" class="form-control" required>
            </div>

            <div class="d-flex gap-2">
                <input type="date" id="reg_dob" class="form-control" style="margin-bottom:10px;" aria-label="Birthday">
                <select id="reg_gender" class="form-control" style="margin-bottom:10px;">
                    <option value="">Sex / Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <input type="text" id="reg_contact_no" placeholder="Contact Number" class="form-control">
            <input type="email" id="reg_email" placeholder="Email Address (optional)" class="form-control">

            <div style="background:#fff7ed; border:1px dashed #fdba74; border-radius:10px; padding:12px 14px; margin-bottom:10px;">
                <strong style="display:block; font-size:12px; color:#9a3412; margin-bottom:4px;">No password needed for assisted intake</strong>
                <p style="margin:0; font-size:12px; color:#7c2d12; line-height:1.5;">If no email is provided, the system will create a temporary assisted record and proceed straight to consultation.</p>
            </div>
            
            <div id="notification" style="margin: 10px 0;"></div>
            
            <button type="button" id="confirmBtn" class="btn btn-success w-100 fw-bold py-3 mt-2" style="border-radius: 8px; background: #15803d; border: none; color: white;">
                SAVE ASSISTED INTAKE
            </button>
            
            <div class="text-center mt-3">
                <a href="{{ url($basePrefix . '/walkin') }}" style="font-size: 12px; color: #64748b; text-decoration: none;">Back to intake options</a>
            </div>
        </form>
    </div>
</div>
@endif
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
    let mainScanner;
    const initialMode = @json($currentMode);
    let scanMethod = 'barcode';
    const supportedFormats = window.Html5QrcodeSupportedFormats ? [
        Html5QrcodeSupportedFormats.CODE_128,
        Html5QrcodeSupportedFormats.CODE_39,
        Html5QrcodeSupportedFormats.CODE_93,
        Html5QrcodeSupportedFormats.EAN_13,
        Html5QrcodeSupportedFormats.EAN_8,
        Html5QrcodeSupportedFormats.UPC_A,
        Html5QrcodeSupportedFormats.UPC_E,
        Html5QrcodeSupportedFormats.ITF,
        Html5QrcodeSupportedFormats.CODABAR,
        Html5QrcodeSupportedFormats.QR_CODE
    ] : [];
    const scannerConfig = {
        fps: 20,
        qrbox: { width: 400, height: 160 },
        aspectRatio: 1.777778
    };

    if (supportedFormats.length) {
        scannerConfig.formatsToSupport = supportedFormats;
    }

    function createScanner(targetId) {
        if (supportedFormats.length) {
            return new Html5Qrcode(targetId, { formatsToSupport: supportedFormats });
        }

        return new Html5Qrcode(targetId);
    }

    $(document).ready(function() {
        updateScanModeUI();

        if (initialMode === 'scan') {
            startMainScanner();
        }

        function startMainScanner() {
            if (!mainScanner) {
                mainScanner = createScanner("readerScan");
                mainScanner.start(
                    { facingMode: "environment" },
                    scannerConfig,
                    (decodedText) => { verifyUser(decodedText); }
                ).catch(err => console.warn(err));
            }
        }

        function verifyUser(id) {
            $('#scan-loading').css('display', 'flex');
            $('#notification').html('');
            $.get("{{ url($basePrefix . '/walkin/get-student') }}", { student_id: id }, function(res) {
                $('#scan-loading').hide();
                if (res.status === 'found') {
                    window.location.href = res.redirect_url;
                } else {
                    const statusText = res.lookup_status ? ` (PUPTAS status ${res.lookup_status})` : '';
                    const failureMessage = res.message
                        ? `${res.message}${statusText}`
                        : `No patient matched ${id} locally or in PUPTAS${statusText}.`;

                    $('#notification').html(`<p style="color:#991b1b; font-size:12px; font-weight:700; background:#fff1f2; padding:10px 12px; border-radius:10px; border:1px solid #fecdd3; margin-bottom:12px;">${failureMessage}</p>`);

                    if(mainScanner) {
                        mainScanner.stop().then(() => {
                            mainScanner = null;
                            if (confirm(`${failureMessage}\n\nOpen Assisted Intake instead?`)) {
                                showRegisterUI(id);
                            } else { window.location.reload(); }
                        });
                    }
                }
            }).fail(() => { $('#scan-loading').hide(); });
        }

        function showRegisterUI(scannedId = '') {
            $('#scanForm').hide();
            $('#registerForm').show();
            $('#dynamicHeader').removeClass('bg-scan').addClass('bg-register');
            $('#headerTitle').text('Assisted Intake Ready');
            $('#headerIcon').text('ASSIST');
            if(scannedId) {
                $('#reg_barcode').val(scannedId);
                $('#reg_student_id').val(scannedId);
            }
        }

        function updateScanModeUI() {
            const isBioSync = scanMethod === 'biosync';
            $('#scanMethodTitle').text(isBioSync ? 'BioSync' : 'Scan Barcode');
            $('#scanMethodNote').text(
                isBioSync
                    ? 'BioSync mode uses the same patient lookup path for now, while presenting the intake flow as biometric identification.'
                    : 'Use the camera to capture the patient barcode, or switch to BioSync mode for identity matching.'
            );
            $('#scanMethodBadge').text(isBioSync ? 'BioSync Active' : 'Barcode Active');
            $('#btnSwitchScanMode').text(isBioSync ? 'Switch to Scan Barcode' : 'Switch to BioSync');
            $('#headerTitle').text(isBioSync ? 'BioSync Ready' : 'Scan / Bio Ready');
            $('#headerSubtitle').text(
                isBioSync
                    ? 'BioSync is selected. The biometric integration panel is reserved for the next implementation step.'
                    : 'Choose barcode scanning or BioSync mode to identify the patient.'
            );
            $('#headerIcon').text(isBioSync ? 'BIO' : 'SB');
            $('#scanInlineNote').text(
                isBioSync
                    ? 'BioSync mode is selected. This section is currently in pending state while we complete the biometric workflow.'
                    : 'Barcode mode is active. Point the camera at the patient barcode, or switch to BioSync for the upcoming biometric flow.'
            );
            $('#barcodeScanPanel').toggle(!isBioSync);
            $('#bioSyncPendingPanel').toggle(isBioSync);
            $('#btnShowManual').toggle(!isBioSync);
            $('#manualInputArea').toggle(!isBioSync && $('#manualInputArea').is(':visible'));
        }

        $('#btnShowManual').on('click', function() {
            $('#manualInputArea').toggle();
        });

        $('#btnSwitchScanMode').on('click', function() {
            const $scanStage = $('#scanStage');
            if ($scanStage.hasClass('is-flipping')) {
                return;
            }

            $scanStage.addClass('is-flipping');

            window.setTimeout(function () {
            scanMethod = scanMethod === 'biosync' ? 'barcode' : 'biosync';
            updateScanModeUI();
            }, 180);

            window.setTimeout(function () {
                $scanStage.removeClass('is-flipping');
            }, 560);
        });

        $('#walkinFormManual').on('submit', function(e) {
            e.preventDefault();
            verifyUser($('#student_id_manual').val());
        });

        $('#confirmBtn').on('click', function() {
            const role = $('#reg_user_type').val();

            if(!role) { alert("Please select a User Role!"); return; }

            $(this).prop('disabled', true).text('PROCESSING...');
            
            const formData = {
                _token: "{{ csrf_token() }}",
                role: role,
                user_role: role,
                user_type: role,
                student_number: $('#reg_student_id').val(),
                first_name: $('#reg_first_name').val(),
                last_name: $('#reg_last_name').val(),
                email: $('#reg_email').val(),
                dob: $('#reg_dob').val(),
                gender: $('#reg_gender').val(),
                contact_no: $('#reg_contact_no').val(),
                barcode: $('#reg_barcode').val() || $('#reg_student_id').val()
            };

            $.post("{{ url($basePrefix . '/walkin/register') }}", formData, function(res) {
                if(res.redirect_url) window.location.href = res.redirect_url;
                else window.location.reload();
            }).fail(function(xhr) {
                $('#confirmBtn').prop('disabled', false).text('CONFIRM REGISTRATION');
                let errorMsg = "Assisted intake failed.";
                if(xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors)[0][0];
                }
                $('#notification').html(`<p style="color:red; font-size:12px; font-weight:bold; background:#fee2e2; padding:10px; border-radius:8px;">${errorMsg}</p>`);
            });
        });
    });
</script>
@endpush
