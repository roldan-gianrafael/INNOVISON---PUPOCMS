@extends('layouts.admin')
@section('title', 'Walk-in Patients')

@section('content')
<div class="card p-4">
    <h3 class="text-center">Walk-in Appointments</h3>

    <!-- Start Scanner Button -->
    <div class="text-center mt-3">
        <button type="button" id="startScanner" style="padding:8px 20px;">Start Scanner</button>
    </div>

    <!-- Scanner Container with animation -->
    <div id="scanner-container" style="position: relative; max-width:400px; margin:20px auto;">
        <div id="reader" style="width:100%; height:300px; border: 2px solid #333; border-radius:10px;"></div>
        <div id="scan-line" style="
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: rgba(255,0,0,0.7);
            display: none;
            animation: scan 2s infinite;
        "></div>
    </div>

    <!-- Walk-in Form -->
    <form id="walkinForm" enctype="multipart/form-data" class="text-center mt-3">
        @csrf
        <input type="text" name="student_id" id="student_id"
               placeholder="Scan or enter Student ID" style="width:250px; padding:8px;">
        <br><br>
        <canvas id="barcodeCanvas" style="margin:10px auto; display:block;"></canvas>
        <input type="text" name="service" placeholder="Service" style="width:250px; padding:8px;">
        <br><br>
        <input type="file" name="attachment">
        <br><br>
        <button type="submit" style="padding:8px 20px;">Add Walk-in</button>
    </form>

    <div id="message" class="text-center mt-2"></div>
    <div id="appointmentTable" class="mt-4"></div>

    <!-- Student Info Modal -->
    <div id="studentModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
         background:rgba(0,0,0,0.6); justify-content:center; align-items:center;">
        <div style="background:#fff; padding:20px; border-radius:10px; width:400px; position:relative;">
            <span style="position:absolute; top:10px; right:15px; cursor:pointer;" onclick="closeModal()">X</span>
            <div id="modalContent"></div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@keyframes scan {
    0% { top: 0; }
    50% { top: 100%; }
    100% { top: 0; }
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
let scanner;

// Start scanner
document.getElementById('startScanner').addEventListener('click', function(){
    document.getElementById('scan-line').style.display = 'block';

    Html5Qrcode.getCameras().then(devices => {
        if(devices.length){
            let cameraId = devices[0].id;

            scanner = new Html5Qrcode("reader");

            scanner.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: { width: 250, height: 150 },
                    formatsToSupport: [ Html5QrcodeSupportedFormats.CODE_128 ]
                },
                (decodedText) => {
                    document.getElementById('student_id').value = decodedText;
                    generateBarcode(decodedText);

                    scanner.stop().then(()=>{
                        document.getElementById('scan-line').style.display='none';
                    }).catch(err=>console.error(err));
                },
                (error) => console.log("Scan failed:", error)
            ).catch(err=>console.error("Unable to start scanner:", err));
        }
    }).catch(err => alert("Camera not found: " + err));
});

// Generate barcode when typing
document.getElementById('student_id').addEventListener('input', function(){
    let value = this.value.trim();
    if(value) generateBarcode(value);
});

function generateBarcode(text){
    JsBarcode("#barcodeCanvas", text, {
        format: "CODE128",
        displayValue: true,
        fontSize: 14,
        width: 2,
        height: 50
    });
}

// AJAX form submit
$('#walkinForm').submit(function(e){
    e.preventDefault();
    let formData = new FormData(this);

    $.ajax({
        url:"{{ route('walkin.store') }}",
        method:"POST",
        data:formData,
        contentType:false,
        processData:false,
        success:function(res){
            $('#message').html("<span style='color:green'>Walk-in added successfully!</span>");
            displayAppointment(res);
            $('#walkinForm')[0].reset();
            $('#barcodeCanvas').getContext('2d').clearRect(0,0, $('#barcodeCanvas').width, $('#barcodeCanvas').height);
        },
        error:function(xhr){
            let err = xhr.responseJSON?.message || 'Something went wrong!';
            $('#message').html('<span style="color:red">'+err+'</span>');
        }
    });
});

function displayAppointment(data){
    let html = `
    <table border="1" width="100%" style="margin-top:10px;">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Service</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>${data.student.student_id}</td>
                <td>${data.student.name}</td>
                <td>${data.student.email}</td>
                <td>${data.appointment.service}</td>
                <td>${data.appointment.date}</td>
                <td>${data.appointment.time}</td>
                <td>${data.appointment.status}</td>
                <td><button onclick='viewInfo(${JSON.stringify(data.student)})'>View Info</button></td>
            </tr>
        </tbody>
    </table>`;
    $('#appointmentTable').html(html);
}

function viewInfo(student){
    let html = `<h4>Student Information</h4>
        <p><strong>Student ID:</strong> ${student.student_id}</p>
        <p><strong>Name:</strong> ${student.name}</p>
        <p><strong>Email:</strong> ${student.email}</p>`;
    $('#modalContent').html(html);
    document.getElementById('studentModal').style.display='flex';
}

function closeModal(){
    document.getElementById('studentModal').style.display='none';
}
</script>
@endpush
