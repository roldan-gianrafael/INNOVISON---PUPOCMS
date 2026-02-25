@extends('layouts.student')

@section('title', 'Register Barcode')

@push('styles')
<style>
    /* --- MAIN CARD CONTAINER --- */
    .barcode-card {
        max-width: 500px;
        margin: 50px auto;
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        text-align: center;
    }

    /* --- TYPOGRAPHY & HEADINGS --- */
    .barcode-card h2 {
        margin-bottom: 20px;
        font-weight: 800;
    }
    .barcode-card h2.not-registered { color: #8B0000; }
    .barcode-card h2.registered { color: #15803d; }
    
    .student-info-box {
        background: #f8fafc;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        text-align: left;
        border: 1px solid #e2e8f0;
    }
    .student-info-box p {
        margin: 5px 0;
        font-size: 14px;
        color: #334155;
    }

    /* --- INPUTS --- */
    .barcode-input {
        width: 100%;
        padding: 12px 15px;
        font-size: 18px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        margin-bottom: 15px;
        text-align: center;
        transition: all 0.3s ease;
    }
    .barcode-input:focus {
        border-color: #8B0000;
        outline: none;
        box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.1);
    }
    .input-success {
        background: #f0fdf4 !important;
        border-color: #bbf7d0 !important;
        color: #15803d !important;
    }

    /* --- BUTTONS --- */
    .btn-scan, .btn-submit, .btn-reset {
        width: 100%;
        padding: 12px;
        margin-bottom: 10px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-scan { background: #f59e0b; color: #fff; }
    .btn-scan:hover { background: #d97706; }

    .btn-submit { background: #a31b1b; color: #fff; }
    .btn-submit:hover { background: #821515; }

    .btn-reset {
        background: #fff1f2;
        color: #be123c;
        border: 1px solid #fecdd3;
        margin-top: 15px;
    }
    .btn-reset:hover { background: #ffe4e6; }

    /* --- SCANNER & MESSAGES --- */
    #reader {
        width: 100%;
        margin: 20px auto;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        overflow: hidden;
        display: none;
    }

    .submitted-message {
        background: #dcfce7;
        color: #15803d;
        padding: 10px;
        border-radius: 8px;
        font-weight: 600;
        margin-bottom: 15px;
        border: 1px solid #bbf7d0;
    }
</style>
@endpush

@section('content')
<div class="barcode-card">

    {{-- HEADER LOGIC --}}
    @if($user->barcode)
        <h2 class="registered">✅ Barcode Linked</h2>
        <p style="color: #64748b; margin-bottom: 20px;">Your barcode is already linked to your account.</p>
    @else
        <h2 class="not-registered">Register Your Barcode</h2>
        <p style="color: #64748b; margin-bottom: 20px;">Scan your physical ID to link it with your account.</p>
    @endif

    <div class="student-info-box">
        <p><strong>Student Name:</strong> {{ $user->name }}</p>
        <p><strong>Student ID:</strong> {{ $user->student_id ?? 'N/A' }}</p>
    </div>

    @if(session('success'))
        <div class="submitted-message">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('barcode.store') }}">
        @csrf
        <input type="text" name="barcode" id="barcode" 
               class="barcode-input {{ $user->barcode ? 'input-success' : '' }}"
               readonly placeholder="Scan result will appear here..."
               value="{{ $user->barcode ?? '' }}">

        @if(!$user->barcode)
            <button type="button" class="btn-scan" id="start-scan">
                📷 Start Scanning
            </button>
            <button type="submit" class="btn-submit">
                Submit Registration
            </button>
        @endif
    </form>

    @if($user->barcode)
        <form method="POST" action="{{ route('barcode.reset') }}" onsubmit="return confirm('Are you sure you want to unlink this barcode?')">
            @csrf
            <button type="submit" class="btn-reset">
                Unlink / Reset Barcode
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
            readerDiv.style.display = 'block';
            scanner = new Html5Qrcode("reader");

            Html5Qrcode.getCameras().then(cameras => {
                if(cameras && cameras.length) {
                    const cameraId = cameras[0].id;
                    scanner.start(
                        cameraId,
                        { fps: 10, qrbox: { width: 400, height: 150 } },
                        (decodedText) => {
                            barcodeInput.value = decodedText;
                            scanner.stop().then(() => {
                                readerDiv.style.display = 'none';
                            });
                        },
                        (err) => { console.warn(err); }
                    );
                } else {
                    alert("No camera found");
                }
            }).catch(err => console.error(err));
        });
    }
</script>
@endpush