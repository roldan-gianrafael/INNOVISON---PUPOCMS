@extends('layouts.admin')
@section('title', 'Walk-in Management')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .notification-toast {
        position: fixed; top: 25px; right: 25px;
        background: #15803d; color: white; padding: 15px 20px;
        border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        z-index: 10000; display: flex; align-items: center;
        justify-content: space-between; min-width: 380px;
        animation: slideInRight 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .btn-toast-action {
        background: rgba(255,255,255,0.25); border: 1px solid rgba(255,255,255,0.4);
        color: white; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;
    }

    .mode-header {
        padding: 20px; color: white; display: flex; align-items: center;
        justify-content: center; gap: 12px; border-radius: 12px 12px 0 0;
        margin: -25px -25px 25px -25px;
        transition: background 0.4s ease;
    }
    .bg-scan { background: #8B0000; }
    .bg-register { background: #334155; }

    .scanner-box {
        width: 100% !important; max-width: 480px; aspect-ratio: 16 / 9;
        margin: 0 auto; background: #1a1a1a; border: 2px dashed #cbd5e1;
        border-radius: 12px; overflow: hidden; position: relative;
    }
    .scanner-box video { object-fit: cover !important; }

    .scan-line-overlay {
        position: absolute; top: 0; left: 0; width: 100%; height: 4px;
        background: rgba(255, 255, 255, 0.6); z-index: 10;
        box-shadow: 0 0 10px white;
        animation: scan-animation 2s linear infinite;
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

    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    @keyframes scan-animation { 0% { top: 0; } 50% { top: 100%; } 100% { top: 0; } }
    @keyframes slideInRight { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>
@endpush

@section('content')
@php
    $role = strtolower((string) (optional(auth()->user())->user_role ?? ''));
    $basePrefix = $role === 'student_assistant' ? '/assistant' : '/admin';
@endphp

@if(session('consultation_done'))
<div id="successToast" class="notification-toast">
    <div style="display: flex; align-items: center; gap: 12px;">
        <span>DONE</span>
        <div>
            <strong style="display: block; font-size: 14px;">Consultation Done!</strong>
            <span style="font-size: 11px; opacity: 0.9;">Record saved successfully.</span>
        </div>
    </div>
    <button onclick="location.href='{{ url($basePrefix . '/walkin') }}'" class="btn-toast-action">Scan Again</button>
</div>
@endif

<div class="card p-4 shadow-sm" style="border-radius: 15px; border: none; max-width: 550px; margin: 20px auto;">
    
    <div id="dynamicHeader" class="mode-header bg-scan">
        <span id="headerIcon" style="font-size: 24px;">SCAN</span>
        <h3 id="headerTitle" style="margin: 0; font-weight: 700; text-transform: uppercase; font-size: 1rem; letter-spacing: 1px;">
            Scanner Ready
        </h3>
    </div>

    <div id="scanForm">
        <div id="scanner-container-scan" style="position: relative;">
            <div id="scan-loading">
                <div class="spinner"></div>
                <p style="margin-top:10px; color:#8B0000; font-weight:bold; font-size: 12px;">Verifying...</p>
            </div>
            <div id="readerScan" class="scanner-box">
                <div class="scan-line-overlay"></div>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <button type="button" id="btnShowManual" style="background:none; border:none; color:#8B0000; text-decoration:underline; cursor:pointer; font-weight:600; font-size: 0.85rem;">
                Type ID Number Manually
            </button>
        </div>

        <div id="manualInputArea" style="display:none;" class="mt-3">
            <form id="walkinFormManual" class="d-flex gap-2">
                <input type="text" id="student_id_manual" placeholder="Enter ID Number" class="form-control" style="margin-bottom:0;" required>
                <button type="submit" style="background:#8B0000; color:white; border:none; padding:0 20px; border-radius:8px; font-weight:700;">Find</button>
            </form>
        </div>

        <div class="mt-4 pt-3" style="border-top: 1px dashed #cbd5e1;">
            <a href="{{ url($basePrefix . '/appointments') }}" class="btn w-100 py-2" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #475569; font-weight: 600; font-size: 0.8rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none;">
                 BACK TO APPOINTMENTS LIST
            </a>
        </div>
    </div>

    <div id="registerForm" style="display:none;">
        <form id="formRegisterStudent">
            @csrf
            
            <div id="registerScannerContainer" style="display:none;">
                <div id="readerRegister" class="scanner-box mb-2">
                    <div class="scan-line-overlay" style="background: rgba(255, 255, 255, 0.6);"></div>
                </div>
                <p class="text-center text-muted mb-3" style="font-size: 11px;">Scan barcode now</p>
            </div>

            <div class="mb-3">
                <label style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase;">Barcode / ID Number</label>
                <div class="d-flex gap-2">
                    <input type="text" id="reg_student_id" class="form-control mb-0" style="background: #f1f5f9; font-weight: bold; border: 2px solid #cbd5e1;" readonly>
                    <input type="hidden" id="reg_barcode"> 
                    <button type="button" id="btnRescan" class="btn btn-secondary" style="border-radius: 8px; font-size: 12px; white-space: nowrap;">Rescan</button>
                </div>
            </div>
            
            <div class="mb-2">
                <label style="font-size: 11px; font-weight: 700; color: #475569;">SELECT USER ROLE</label>
                <select id="reg_user_type" class="form-control" required>
                    <option value="" disabled selected>-- Choose Role --</option>
                    <option value="Student">Student</option>
                    <option value="Faculty">Faculty</option>
                    <option value="Admin">Admin</option>
                    <option value="Dependent">Dependent</option>
                </select>
            </div>
            
            <div class="d-flex gap-2">
                <input type="text" id="reg_first_name" placeholder="First Name" class="form-control" required>
                <input type="text" id="reg_last_name" placeholder="Last Name" class="form-control" required>
            </div>
            
            <input type="email" id="reg_email" placeholder="Email Address" class="form-control" required>
            
            <div class="password-wrapper">
                <input type="password" id="reg_password" placeholder="Initial Password" class="form-control" required>
                <i class="fa-solid fa-eye password-toggle" onclick="togglePass('reg_password', this)"></i>
            </div>

            <div class="password-wrapper">
                <input type="password" id="reg_password_confirmation" placeholder="Confirm Password" class="form-control" required>
                <i class="fa-solid fa-eye password-toggle" onclick="togglePass('reg_password_confirmation', this)"></i>
            </div>
            
            <div id="notification" style="margin: 10px 0;"></div>
            
            <button type="button" id="confirmBtn" class="btn btn-success w-100 fw-bold py-3 mt-2" style="border-radius: 8px; background: #15803d; border: none; color: white;">
                CONFIRM REGISTRATION
            </button>
            
            <div class="text-center mt-3">
                <a href="javascript:location.reload()" style="font-size: 12px; color: #64748b; text-decoration: none;">Cancel and back to scan</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
    let mainScanner;
    let registerScanner;
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

    // Show/Hide Password Logic
    function togglePass(id, icon) {
        const input = document.getElementById(id);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.replace("fa-eye-slash", "fa-eye");
        }
    }

    $(document).ready(function() {
        startMainScanner();

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
            $.get("{{ url($basePrefix . '/walkin/get-student') }}", { student_id: id }, function(res) {
                $('#scan-loading').hide();
                if (res.status === 'found') {
                    window.location.href = res.redirect_url;
                } else {
                    if(mainScanner) {
                        mainScanner.stop().then(() => {
                            mainScanner = null;
                            if (confirm("User ID: " + id + " not found. Register?")) {
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
            $('#headerTitle').text('New User Registration');
            $('#headerIcon').text('REG');
            if(scannedId) {
                $('#reg_barcode').val(scannedId);
                $('#reg_student_id').val(scannedId);
                $('#registerScannerContainer').hide();
            } else {
                $('#registerScannerContainer').show();
                startRegisterScanner();
            }
        }

        function startRegisterScanner() {
            if (!registerScanner) {
                registerScanner = createScanner("readerRegister");
                registerScanner.start(
                    { facingMode: "environment" },
                    scannerConfig,
                    (decodedText) => {
                        $('#reg_barcode').val(decodedText);
                        $('#reg_student_id').val(decodedText);
                        $('#registerScannerContainer').slideUp();
                        if(registerScanner) {
                            registerScanner.stop().then(() => { registerScanner = null; });
                        }
                    }
                ).catch(err => console.warn(err));
            }
        }

        $('#btnRescan').on('click', function() {
            $('#registerScannerContainer').slideDown();
            startRegisterScanner();
        });

        $('#btnShowManual').on('click', function() {
            $('#manualInputArea').toggle();
        });

        $('#walkinFormManual').on('submit', function(e) {
            e.preventDefault();
            verifyUser($('#student_id_manual').val());
        });

        $('#confirmBtn').on('click', function() {
            const role = $('#reg_user_type').val();
            const pass = $('#reg_password').val();
            const confirmPass = $('#reg_password_confirmation').val();

            if(!role) { alert("Please select a User Role!"); return; }
            if(pass !== confirmPass) { 
                $('#notification').html('<p style="color:red; font-size:12px; font-weight:bold;">Passwords do not match!</p>');
                return; 
            }

            $(this).prop('disabled', true).text('PROCESSING...');
            
            const formData = {
                _token: "{{ csrf_token() }}",
                role: role,
                user_role: role,
                user_type: role,
                student_id: $('#reg_student_id').val(),
                first_name: $('#reg_first_name').val(),
                last_name: $('#reg_last_name').val(),
                email: $('#reg_email').val(),
                password: pass,
                password_confirmation: confirmPass,
                barcode: $('#reg_barcode').val() || $('#reg_student_id').val()
            };

            $.post("{{ url($basePrefix . '/walkin/register') }}", formData, function(res) {
                if(res.redirect_url) window.location.href = res.redirect_url;
                else window.location.reload();
            }).fail(function(xhr) {
                $('#confirmBtn').prop('disabled', false).text('CONFIRM REGISTRATION');
                let errorMsg = "Registration Failed.";
                if(xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors)[0][0];
                }
                $('#notification').html(`<p style="color:red; font-size:12px; font-weight:bold; background:#fee2e2; padding:10px; border-radius:8px;">${errorMsg}</p>`);
            });
        });
    });
</script>
@endpush
