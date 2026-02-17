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
        <div id="reader" style="width:100%; height:300px; border: 2px solid #333; border-radius:10px; overflow:hidden;"></div>
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

    <!-- Walk-in Form -->
    <form id="walkinForm" enctype="multipart/form-data" class="text-center mt-3">
        @csrf
        <input type="text" name="student_id" id="student_id" placeholder="Scan or enter Student ID" style="width:250px; padding:8px;">
        <input type="file" name="attachment" style="margin-top:10px; display:block; margin:auto;">
        <button type="submit" style="padding:8px 20px; margin-top:10px;">Add Walk-in</button>
    </form>

    <div id="message" class="text-center mt-2"></div>

    <!-- Latest Walk-in -->
    <h4 class="mt-4 text-center">Latest Walk-in</h4>
    <div id="appointmentTable"></div>

    <!-- Student Info Modal -->
    <div id="studentModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
         background:rgba(0,0,0,0.6); justify-content:center; align-items:center;">
        <div style="background:#fff; padding:20px; border-radius:10px; width:400px; position:relative;">
            <span style="position:absolute; top:10px; right:15px; cursor:pointer;" onclick="closeModal()">X</span>
            <div id="modalContent"></div>
            <div class="text-center mt-3">
                <button onclick="selectStudent()" style="padding:5px 10px;">Select</button>
                <button onclick="closeModal()" style="padding:5px 10px;">Close</button>
            </div>
        </div>
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
let selectedStudent = null;
document.addEventListener('DOMContentLoaded', () => {
    const studentInput = document.getElementById('student_id');
    const startBtn = document.getElementById('startScanner');
    const form = document.getElementById('walkinForm');
    let html5QrcodeScanner;

    // Start scanner
    startBtn.addEventListener('click', () => {
        if (!html5QrcodeScanner) {
            html5QrcodeScanner = new Html5Qrcode("reader");

            html5QrcodeScanner.start(
                { facingMode: "environment" },
                { 
                    fps: 10, 
                    qrbox: { width: 250, height: 150 }, 
                    formatsToSupport: [ Html5QrcodeSupportedFormats.CODE_128 ]
                },
                (decodedText) => {
                    studentInput.value = decodedText;
                    fetchStudent(decodedText);
                    html5QrcodeScanner.clear().catch(err => console.error(err));
                },
                (error) => {
                    console.log("Scan failed:", error);
                }
            ).catch(err => console.error("Unable to start scanner: ", err));
        }
    });

    // Manual input student_id detection
    studentInput.addEventListener('change', function(){
        let id = this.value.trim();
        if(id) fetchStudent(id);
    });

    function fetchStudent(student_id){
        $.get("{{ route('walkin.getStudent') }}", { student_id }, function(res){
            selectedStudent = res.student;
            showStudentInfo(res.student);
        });
    }

    // AJAX form submit
    $('#walkinForm').on('submit', function(e){
        e.preventDefault();
        if(!selectedStudent){
            $('#message').html('<span style="color:red;">Select a student first!</span>');
            return;
        }
        let formData = new FormData(this);
        formData.append('student_id', selectedStudent.student_id);

        $.ajax({
            url: "{{ route('walkin.store') }}",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                $('#message').html('<span style="color:green;">Walk-in added successfully!</span>');
                displayAppointment(res);
                form.reset();
                selectedStudent = null;
            },
            error: function(xhr){
                let err = xhr.responseJSON?.message || 'Something went wrong!';
                $('#message').html('<span style="color:red;">'+err+'</span>');
            }
        });
        closeModal();
    });

    // Display appointment
    function displayAppointment(data){
        let html = `
        <table border="1" cellpadding="8" cellspacing="0" width="100%" style="margin-top:10px;">
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
                    <td>${data.appointment.service ?? '-'}</td>
                    <td>${data.appointment.date}</td>
                    <td>${data.appointment.time}</td>
                    <td>${data.appointment.status}</td>
                    <td><button onclick="showStudentInfo(${JSON.stringify(data.student)})">View Info</button></td>
                </tr>
            </tbody>
        </table>`;
        $('#appointmentTable').html(html);
    }
});

// Modal functions
function showStudentInfo(student){
    let html = `<h4>Student Information</h4>
        <p><strong>Student ID:</strong> ${student.student_id}</p>
        <p><strong>Name:</strong> ${student.name}</p>
        <p><strong>Email:</strong> ${student.email}</p>`;
    $('#modalContent').html(html);
    document.getElementById('studentModal').style.display = 'flex';
}

function closeModal(){
    document.getElementById('studentModal').style.display = 'none';
}

// When clicking Select button in modal
function selectStudent(){
    if(selectedStudent){
        $('#student_id').val(selectedStudent.student_id);
        closeModal();
    }
}
</script>
@endpush
