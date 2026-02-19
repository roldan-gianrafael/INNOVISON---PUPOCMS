@extends('layouts.student')

@section('title', 'Register Barcode')

@push('styles')
<style>
    .barcode-card {
        max-width: 500px;
        margin: 50px auto;
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        text-align: center;
    }

    .barcode-card h2 {
        margin-bottom: 20px;
        color: #8B0000;
    }

    .barcode-input {
        width: 100%;
        padding: 12px 15px;
        font-size: 18px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        margin-bottom: 15px;
    }

    .btn-scan, .btn-submit {
        width: 100%;
        padding: 12px;
        margin-bottom: 10px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-scan { background: #f59e0b; color: #fff; }
    .btn-scan:hover { background: #d97706; }

    .btn-submit { background: #a31b1b; color: #fff; }
    .btn-submit:hover { background: #2701ff; }

    #reader {
        width: 100%;
        margin: 20px auto;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 10px ;
        display: none;
    }

    .submitted-message {
        color: green;
        font-weight: 600;
        margin-bottom: 15px;
    }
</style>
@endpush

@section('content')
<div class="barcode-card">

    <h2>Register Your Barcode</h2>

    <p><strong>Student:</strong> {{ $user->name }}</p>
    <p><strong>Student ID:</strong> {{ $user->student_id ?? '2025-0000-TG-0' }}</p>

    @if(session('success'))
        <div class="submitted-message">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('barcode.store') }}">
        @csrf
        <input type="text" name="barcode" id="barcode" class="barcode-input"
               readonly placeholder="Scanned barcode display here..."
               value="{{ $user->barcode ?? '' }}"
               @if($user->barcode) disabled @endif>

        @if(!$user->barcode)
            <button type="button" class="btn-scan" id="start-scan">Start Scanning</button>
            <button type="submit" class="btn-submit">Submit</button>
        @endif
    </form>
@if($user->barcode)
    <form method="POST" action="{{ route('barcode.reset') }}">
        @csrf
        <button type="submit" 
                style="background:red;color:white;padding:10px 15px;border:none;border-radius:8px;margin-top:15px;">
            Reset Barcode (Test)
        </button>
    </form>
@endif
    <div id="reader"></div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    const startButton = document.getElementById('start-scan');
    const barcodeInput = document.getElementById('barcode');
    const readerDiv = document.getElementById('reader');
    let scanner;

    if(startButton) {
        startButton.addEventListener('click', function() {
            readerDiv.style.display = 'block'; // show scanner
            scanner = new Html5Qrcode("reader");

            Html5Qrcode.getCameras().then(cameras => {
                if(cameras && cameras.length) {
                    const cameraId = cameras[0].id;
                    scanner.start(
                        cameraId,
                        { 
                            fps: 10, 
                            qrbox: { width: 400, height: 150 } // rectangle
                        }
                        ,
                        (decodedText, decodedResult) => {
                            barcodeInput.value = decodedText;
                            scanner.stop().then(() => {
                                readerDiv.style.display = 'none'; // hide scanner after scan
                            });
                        },
                        errorMessage => {
                            console.log("Scan error:", errorMessage);
                        }
                    );
                } else {
                    alert("No camera found");
                }
            }).catch(err => console.error(err));
        });
    }
</script>
@endpush
