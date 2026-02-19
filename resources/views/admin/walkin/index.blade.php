@extends('layouts.admin')
@section('title', 'Walk-in Management')

@section('content')
<div class="card p-4">
    <h3 class="text-center">Walk-in Management</h3>

    <!-- Action Buttons -->
    <div class="text-center mt-3">
        <button type="button" id="btnScan" style="padding:8px 20px; margin-right:10px;">Scan Student</button>
        <button type="button" id="btnRegister" style="padding:8px 20px;">Register Student</button>
    </div>

    <!-- Scan Student Form -->
    <div id="scanForm" style="display:none; margin-top:20px;">
        <div id="scanner-container-scan" style="position: relative; max-width:400px; margin:20px auto;">
            <div id="readerScan" style="width:100%; height:300px; border: 2px solid #333; border-radius:10px; overflow:hidden;"></div>
            <div id="scan-line" style="
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 4px;
                background: rgba(255,0,0,0.7);
                animation: scan-animation 2s linear infinite;
            "></div>
        </div>

        <form id="walkinForm" class="text-center mt-3">
            @csrf
            <input type="text" name="student_id" id="student_id_scan" placeholder="Scan or enter Student ID" style="width:250px; padding:8px;" required>
            <button type="submit" style="padding:8px 20px; margin-top:10px;">Add Walk-in</button>
        </form>

        <div id="walkinInfo" class="mt-3 text-center"></div>
    </div>

    <!-- Register Student Form -->
    <div id="registerForm" style="display:none; margin-top:20px;">
        <div id="scanner-container-register" style="position: relative; max-width:400px; margin:20px auto; display:none;">
            <div id="readerRegister" style="width:100%; height:300px; border: 2px solid #333; border-radius:10px; overflow:hidden;"></div>
            <div style="
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 4px;
                background: rgba(255,0,0,0.7);
                animation: scan-animation 2s linear infinite;
            "></div>
        </div>

        <form id="formRegisterStudent" method="POST" class="text-center mt-3">
            @csrf
            <button type="button" id="startRegisterScanner" style="padding:5px 10px; margin-bottom:10px;">Start Scanner</button><br>

            <input type="text" name="student_id" id="reg_student_id" placeholder="Student ID" style="width:250px; padding:8px; margin-top:10px;" required>
            <input type="text" name="first_name" id="reg_first_name" placeholder="First Name" style="width:250px; padding:8px; margin-top:10px;" required>
            <input type="text" name="last_name" id="reg_last_name" placeholder="Last Name" style="width:250px; padding:8px; margin-top:10px;" required>
            <input type="email" name="email" id="reg_email" placeholder="Email" style="width:250px; padding:8px; margin-top:10px;" required>
            <input type="password" name="password" id="reg_password" placeholder="Password" style="width:250px; padding:8px; margin-top:10px;" required>
            <input type="text" name="barcode" id="reg_barcode" placeholder="Barcode" style="width:250px; padding:8px; margin-top:10px;">

            <div id="notification" style="margin-top:10px; color:red;"></div>
            <button type="button" id="confirmBtn" style="padding:8px 20px; margin-top:10px;">Confirm Registration</button>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
@keyframes scan-animation {
    0% { top: 0; }
    50% { top: 100%; }
    100% { top: 0; }
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const btnScan = document.getElementById('btnScan');
    const btnRegister = document.getElementById('btnRegister');
    const scanForm = document.getElementById('scanForm');
    const registerForm = document.getElementById('registerForm');

    // Toggle forms
    btnScan.addEventListener('click', () => {
        scanForm.style.display = 'block';
        registerForm.style.display = 'none';
    });
    btnRegister.addEventListener('click', () => {
        registerForm.style.display = 'block';
        scanForm.style.display = 'none';
    });

    // ------------------- SCAN WALK-IN -------------------
    let html5QrcodeScannerScan;
    const studentInputScan = document.getElementById('student_id_scan');
    const walkinInfo = document.getElementById('walkinInfo');

    studentInputScan.addEventListener('change', () => fetchStudent(studentInputScan.value));

    btnScan.addEventListener('click', () => {
        if (!html5QrcodeScannerScan) {
            html5QrcodeScannerScan = new Html5Qrcode("readerScan");
            html5QrcodeScannerScan.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 150 }, formatsToSupport: [ Html5QrcodeSupportedFormats.CODE_128 ] },
                (decodedText) => {
                    studentInputScan.value = decodedText;
                    fetchStudent(decodedText);
                    html5QrcodeScannerScan.clear().catch(err => console.error(err));
                },
                (error) => { console.log("Scan error:", error); }
            ).catch(err => console.error("Scanner start error: ", err));
        }
    });

    function fetchStudent(student_id){
        $.get("{{ route('walkin.getStudent') }}", { student_id }, function(res){
            if(res.student){
                walkinInfo.innerHTML = `
                    <p><strong>Student ID:</strong> ${res.student.student_id}</p>
                    <p><strong>Name:</strong> ${res.student.first_name} ${res.student.last_name}</p>
                    <p><strong>Email:</strong> ${res.student.email}</p>`;
            } else {
                walkinInfo.innerHTML = `<p style="color:red;">Student not registered yet.</p>`;
            }
        });
    }

    // ------------------- REGISTRATION SCANNER -------------------
    let html5QrcodeScannerRegister;
    const startRegisterBtn = document.getElementById('startRegisterScanner');
    const scannerContainerRegister = document.getElementById('scanner-container-register');

    startRegisterBtn.addEventListener('click', () => {
        scannerContainerRegister.style.display = 'block';
        if (!html5QrcodeScannerRegister) {
            html5QrcodeScannerRegister = new Html5Qrcode("readerRegister");
            html5QrcodeScannerRegister.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 150 }, formatsToSupport: [ Html5QrcodeSupportedFormats.CODE_128 ] },
                (decodedText) => {
                    document.getElementById('reg_student_id').value = decodedText;

                    // Fetch existing student info
                    $.get("{{ route('walkin.getStudent') }}", { student_id: decodedText }, function(res){
                        if(res.student){
                            document.getElementById('reg_first_name').value = res.student.first_name;
                            document.getElementById('reg_last_name').value = res.student.last_name;
                            document.getElementById('reg_email').value = res.student.email;
                            document.getElementById('notification').innerText = "Student already exists!";
                        } else {
                            document.getElementById('reg_first_name').value = '';
                            document.getElementById('reg_last_name').value = '';
                            document.getElementById('reg_email').value = '';
                            document.getElementById('notification').innerText = '';
                        }
                    });

                    html5QrcodeScannerRegister.clear().catch(err => console.error(err));
                },
                (error) => { console.log("Scan error:", error); }
            ).catch(err => console.error("Scanner start error: ", err));
        }
    });

    // ------------------- CONFIRM REGISTRATION -------------------
    const confirmBtn = document.getElementById('confirmBtn');
    confirmBtn.addEventListener('click', () => {
        const student_id = document.getElementById('reg_student_id').value;
        const first_name = document.getElementById('reg_first_name').value;
        const last_name = document.getElementById('reg_last_name').value;
        const email = document.getElementById('reg_email').value;
        const password = document.getElementById('reg_password').value;
        const barcode = document.getElementById('reg_barcode').value;

        if(!student_id || !first_name || !last_name || !email || !password){
            alert('Please fill all required fields!');
            return;
        }

        if(confirm(`Confirm registration?\nStudent ID: ${student_id}\nName: ${first_name} ${last_name}\nEmail: ${email}`)){
            $.post("{{ route('walkin.registerStudent') }}", {
                _token: "{{ csrf_token() }}",
                student_id, first_name, last_name, email, password, barcode
            }, function(res){
                alert(res.message);
                document.getElementById('formRegisterStudent').reset();
                document.getElementById('notification').innerText = '';
            }).fail(function(xhr){
                alert(xhr.responseJSON.message || 'Error registering student!');
            });
        }
    });

});
</script>
@endpush
