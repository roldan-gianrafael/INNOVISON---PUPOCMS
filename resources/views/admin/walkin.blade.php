@extends('layouts.admin')
@section('title', 'Walk-in Management')

@push('styles')
<style>
    /* Ginaya ang styling sa Student Barcode Register */
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
        color: white; padding: 6px 14px; border-radius: 6px;
        font-size: 12px; font-weight: 700; cursor: pointer;
    }
    .toast-close-x { background: transparent; border: none; color: white; font-size: 22px; cursor: pointer; }
    
    .form-control { display: block; width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; transition: 0.3s; }
    .form-control:focus { border-color: #8B0000; outline: none; box-shadow: 0 0 0 3px rgba(139,0,0,0.1); }

    @keyframes slideInRight { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes scan-animation { 0% { top: 0; } 50% { top: 100%; } 100% { top: 0; } }

    #scan-loading {
        display: none; position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
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

    /* Ginawang rectangle ang focus area ng scanner */
    #readerScan video, #readerRegister video {
        object-fit: cover !important;
    }
</style>
@endpush

@section('content')

@if(session('consultation_done'))
<div id="successToast" class="notification-toast">
    <div style="display: flex; align-items: center; gap: 12px;">
        <span style="font-size: 20px;">✅</span>
        <div>
            <strong style="display: block; font-size: 14px;">Consultation Done!</strong>
            <span style="font-size: 12px; opacity: 0.9;">Record saved and inventory updated.</span>
        </div>
    </div>
    <div style="display: flex; gap: 8px; align-items: center; margin-left: 20px;">
        <button onclick="closeToastAndScan()" class="btn-toast-action">New Appointment</button>
        <button onclick="closeToast()" class="toast-close-x">&times;</button>
    </div>
</div>
@endif

<div class="card p-4">
    <h3 class="text-center" style="color:#8B0000; margin-bottom: 20px;">Walk-in Management</h3>

    <div class="text-center mt-3" style="margin-bottom: 30px;">
        <button type="button" id="btnScan" class="btn-save" style="padding:10px 25px; margin-right:10px; background:#8B0000; color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer;">
            📷 Scan Student/User
        </button>
        <button type="button" id="btnRegister" class="btn-edit" style="padding:10px 25px; background:#e2e8f0; color:#334155; border:none; border-radius:8px; font-weight:600; cursor:pointer;">
            📝 Register New User
        </button>
    </div>

    <div id="scanForm" style="display:none; margin-top:20px;">
        <div id="scanner-container-scan" style="position: relative; max-width:500px; margin:20px auto;">
            <div id="scan-loading">
                <div class="spinner"></div>
                <p style="margin-top:10px; color:#8B0000; font-weight:bold;">Checking Database...</p>
            </div>

            <div id="readerScan" style="width:100%; border: 2px dashed #cbd5e1; border-radius:12px; overflow:hidden; background: #f8fafc;"></div>
            <div id="scan-line" style="position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: rgba(139, 0, 0, 0.5); z-index: 5; animation: scan-animation 2s linear infinite;"></div>
        </div>
        
        <div class="text-center mt-3">
            <button type="button" id="btnShowManual" style="background:none; border:none; color:#8B0000; text-decoration:underline; cursor:pointer; font-weight:600;">
                Can't scan? Type ID Number instead
            </button>
        </div>

        <div id="manualInputArea" style="display:none;" class="text-center mt-3">
            <form id="walkinFormManual">
                <input type="text" id="student_id_manual" placeholder="Enter Student/User ID" class="form-control" style="width:250px; display:inline-block; padding:8px;" required>
                <button type="submit" class="btn-save" style="background:#8B0000; color:white; border:none; padding:8px 20px; border-radius:6px; margin-left:10px;">Find User</button>
            </form>
        </div>
    </div>

    <div id="registerForm" style="display:none; margin-top:20px;">
        <div id="scanner-container-register" style="position: relative; max-width:500px; margin:20px auto; display:none;">
            <div id="readerRegister" style="width:100%; border: 2px dashed #cbd5e1; border-radius:12px; overflow:hidden;"></div>
        </div>
        <form id="formRegisterStudent" method="POST" class="text-center mt-3">
            @csrf
            <button type="button" id="startRegisterScanner" style="padding:10px 20px; margin-bottom:15px; cursor:pointer; background:#333; color:white; border:none; border-radius:8px; font-weight:600;">📷 Scan Barcode for Registration</button><br>
            
            <select name="user_type" id="reg_user_type" class="form-control" style="width:300px; margin:10px auto;" required>
                <option value="" disabled selected>Select User Type</option>
                <option value="Student">Student</option>
                <option value="Faculty">Faculty</option>
                <option value="Admin">Admin</option>
                <option value="Dependent">Dependent</option>
            </select>
            
            <input type="text" name="student_id" id="reg_student_id" placeholder="ID Number" class="form-control" style="width:300px; margin:10px auto;" required>
            <input type="text" name="first_name" id="reg_first_name" placeholder="First Name" class="form-control" style="width:300px; margin:10px auto;" required>
            <input type="text" name="last_name" id="reg_last_name" placeholder="Last Name" class="form-control" style="width:300px; margin:10px auto;" required>
            <input type="email" name="email" id="reg_email" placeholder="Email" class="form-control" style="width:300px; margin:10px auto;" required>
            <input type="password" name="password" id="reg_password" placeholder="Password (Initial)" class="form-control" style="width:300px; margin:10px auto;" required>
            <input type="text" name="barcode" id="reg_barcode" placeholder="Scanned Barcode Value" class="form-control" style="width:300px; margin:10px auto; background:#f1f5f9;" readonly>
            
            <div id="notification" style="margin-top:10px;"></div>
            <button type="button" id="confirmBtn" style="padding:12px 25px; margin-top:10px; background:#28a745; color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer; width:300px;">Confirm & Open Consultation</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
    const isConsultationDone = "{{ session('consultation_done') ? 'true' : 'false' }}";

    function closeToast() {
        const toast = document.getElementById('successToast');
        if(toast) toast.style.display = 'none';
    }

    function closeToastAndScan() {
        closeToast();
        document.getElementById('btnScan').click();
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (isConsultationDone === 'true') setTimeout(closeToast, 7000);

        const btnScan = document.getElementById('btnScan');
        const btnRegister = document.getElementById('btnRegister');
        const scanForm = document.getElementById('scanForm');
        const registerForm = document.getElementById('registerForm');
        const manualInputArea = document.getElementById('manualInputArea');

        btnScan.addEventListener('click', () => { 
            scanForm.style.display = 'block'; 
            registerForm.style.display = 'none'; 
            startScanner();
        });

        btnRegister.addEventListener('click', () => { 
            registerForm.style.display = 'block'; 
            scanForm.style.display = 'none'; 
            if(html5QrcodeScannerScan) {
                html5QrcodeScannerScan.stop().then(() => { html5QrcodeScannerScan = null; });
            }
        });

        document.getElementById('btnShowManual').addEventListener('click', () => {
            manualInputArea.style.display = manualInputArea.style.display === 'none' ? 'block' : 'none';
        });

        // --- SCANNER FOR WALK-IN (STUDENT SIZE SETTINGS) ---
        let html5QrcodeScannerScan;
        function startScanner() {
            if (!html5QrcodeScannerScan) {
                html5QrcodeScannerScan = new Html5Qrcode("readerScan");
                html5QrcodeScannerScan.start(
                    { facingMode: "environment" },
                    { 
                        fps: 10, 
                        qrbox: { width: 400, height: 150 }, // GINAYANG RECTANGLE SIZE
                        aspectRatio: 1.777778 
                    },
                    (decodedText) => {
                        fetchStudent(decodedText);
                    }
                ).catch(err => console.log(err));
            }
        }

        function fetchStudent(scannedValue) {
            document.getElementById('scan-loading').style.display = 'flex';
            $.get("{{ route('walkin.getStudent') }}", { student_id: scannedValue }, function(res) {
                document.getElementById('scan-loading').style.display = 'none';
                if (res.status === 'found') {
                    window.location.href = res.redirect_url;
                } else {
                    alert("User not found. Switching to registration...");
                    scanForm.style.display = 'none';
                    registerForm.style.display = 'block';
                    document.getElementById('reg_barcode').value = scannedValue;
                    document.getElementById('reg_student_id').value = scannedValue;
                    if(html5QrcodeScannerScan) {
                        html5QrcodeScannerScan.stop().then(() => { html5QrcodeScannerScan = null; });
                    }
                }
            });
        }

        document.getElementById('walkinFormManual').addEventListener('submit', function(e) {
            e.preventDefault();
            fetchStudent(document.getElementById('student_id_manual').value);
        });

        // --- SCANNER FOR REGISTRATION (STUDENT SIZE SETTINGS) ---
        let html5QrcodeScannerRegister;
        document.getElementById('startRegisterScanner').addEventListener('click', () => {
            document.getElementById('scanner-container-register').style.display = 'block';
            if (!html5QrcodeScannerRegister) {
                html5QrcodeScannerRegister = new Html5Qrcode("readerRegister");
                html5QrcodeScannerRegister.start(
                    { facingMode: "environment" },
                    { 
                        fps: 10, 
                        qrbox: { width: 400, height: 150 }, // GINAYANG RECTANGLE SIZE
                        aspectRatio: 1.777778
                    },
                    (decodedText) => {
                        document.getElementById('reg_barcode').value = decodedText;
                        document.getElementById('reg_student_id').value = decodedText;
                        html5QrcodeScannerRegister.stop().then(() => {
                            html5QrcodeScannerRegister = null;
                            document.getElementById('scanner-container-register').style.display = 'none';
                            document.getElementById('notification').innerHTML = "<span style='color:green; font-weight:bold;'>✅ Barcode Scanned!</span>";
                        });
                    }
                );
            }
        });

        document.getElementById('confirmBtn').addEventListener('click', () => {
            const formData = {
                _token: "{{ csrf_token() }}",
                user_type: document.getElementById('reg_user_type').value,
                student_id: document.getElementById('reg_student_id').value,
                first_name: document.getElementById('reg_first_name').value,
                last_name: document.getElementById('reg_last_name').value,
                email: document.getElementById('reg_email').value,
                password: document.getElementById('reg_password').value,
                barcode: document.getElementById('reg_barcode').value
            };

            $.post("{{ route('walkin.registerStudent') }}", formData, function(res){
                if(res.redirect_url) window.location.href = res.redirect_url;
                else location.reload();
            }).fail(function(xhr){
                document.getElementById('notification').innerHTML = `<span style="color:red;">${xhr.responseJSON.message || 'Error!'}</span>`;
            });
        });
    });
</script>
@endpush