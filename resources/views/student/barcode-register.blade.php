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

    .btn-upload { background: #0f766e; color: #fff; }
    .btn-upload:hover { background: #0d5f58; }

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

    .field-error {
        background: #fee2e2;
        color: #b91c1c;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 15px;
        border: 1px solid #fecaca;
        font-size: 13px;
    }

    .scan-helper {
        color: #64748b;
        margin-bottom: 12px;
        font-size: 13px;
    }

    .scan-status {
        display: none;
        margin-bottom: 10px;
        padding: 10px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
    }

    .scan-status.info {
        display: block;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .scan-status.success {
        display: block;
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .scan-status.error {
        display: block;
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .upload-input {
        display: none;
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

    @error('barcode')
        <div class="field-error">{{ $message }}</div>
    @enderror

    <form method="POST" action="{{ route('barcode.store') }}">
        @csrf
        <input type="text" name="barcode" id="barcode" 
               class="barcode-input {{ $user->barcode ? 'input-success' : '' }}"
               readonly placeholder="Scan result will appear here..."
               value="{{ $user->barcode ?? '' }}">

        @if(!$user->barcode)
            <button type="button" class="btn-scan" id="start-scan">
                Start Camera Scan
            </button>

            <p class="scan-helper">Or upload a clear photo of your ID barcode for instant validation.</p>
            <label for="barcode-image-input" class="btn-scan btn-upload">
                Upload Barcode Image
            </label>
            <input type="file" id="barcode-image-input" class="upload-input" accept="image/png,image/jpeg,image/jpg,image/webp">

            <div id="scan-status" class="scan-status" aria-live="polite"></div>

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
    const uploadInput = document.getElementById('barcode-image-input');
    const statusBox = document.getElementById('scan-status');
    const validateUrl = "{{ route('barcode.validate') }}";
    const csrfToken = "{{ csrf_token() }}";

    let scanner = null;
    let isScannerRunning = false;
    let isProcessing = false;

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
        fps: 12,
        qrbox: { width: 400, height: 150 },
        aspectRatio: 1.777778
    };

    if (supportedFormats.length) {
        scannerConfig.formatsToSupport = supportedFormats;
    }

    function createScannerInstance() {
        if (supportedFormats.length) {
            return new Html5Qrcode('reader', { formatsToSupport: supportedFormats });
        }

        return new Html5Qrcode('reader');
    }

    function setStatus(message, type = 'info') {
        if (!statusBox) {
            return;
        }

        if (!message) {
            statusBox.textContent = '';
            statusBox.className = 'scan-status';
            return;
        }

        statusBox.textContent = message;
        statusBox.className = `scan-status ${type}`;
    }

    async function stopScanner() {
        if (!scanner || !isScannerRunning) {
            readerDiv.style.display = 'none';
            return;
        }

        try {
            await scanner.stop();
        } catch (error) {
            console.warn(error);
        } finally {
            isScannerRunning = false;
            readerDiv.style.display = 'none';
        }
    }

    async function validateScannedBarcode(decodedValue) {
        const barcode = (decodedValue || '').trim();
        if (!barcode) {
            barcodeInput.value = '';
            barcodeInput.classList.remove('input-success');
            setStatus('No readable barcode value was detected.', 'error');
            return;
        }

        setStatus('Validating barcode...', 'info');

        try {
            const response = await fetch(validateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ barcode })
            });

            const payload = await response.json().catch(() => ({}));
            if (!response.ok || !payload.valid) {
                throw new Error(payload.message || 'Barcode validation failed.');
            }

            const finalBarcode = (payload.barcode || barcode).trim();
            barcodeInput.value = finalBarcode;
            barcodeInput.classList.add('input-success');
            setStatus(payload.message || 'Barcode validated successfully.', 'success');
        } catch (error) {
            barcodeInput.value = '';
            barcodeInput.classList.remove('input-success');
            setStatus(error.message || 'Unable to validate barcode right now.', 'error');
        }
    }

    async function scanUploadedImage(file) {
        if (!file) {
            return;
        }

        await stopScanner();

        if (!scanner) {
            scanner = createScannerInstance();
        }

        readerDiv.style.display = 'block';
        setStatus('Reading uploaded image...', 'info');

        try {
            const decodedText = await scanner.scanFile(file, true);
            await validateScannedBarcode(decodedText);
        } catch (error) {
            barcodeInput.value = '';
            barcodeInput.classList.remove('input-success');
            setStatus('No readable 1D barcode found in the uploaded image. Try a clearer photo.', 'error');
        } finally {
            readerDiv.style.display = 'none';
            if (typeof scanner.clear === 'function') {
                try {
                    await scanner.clear();
                } catch (error) {
                    console.warn(error);
                }
            }
        }
    }

    if (startButton) {
        startButton.addEventListener('click', async function () {
            if (isScannerRunning || isProcessing) {
                return;
            }

            if (!scanner) {
                scanner = createScannerInstance();
            }

            try {
                readerDiv.style.display = 'block';
                setStatus('Opening camera scanner...', 'info');

                const cameras = await Html5Qrcode.getCameras();
                if (!cameras || !cameras.length) {
                    setStatus('No camera was found on this device.', 'error');
                    readerDiv.style.display = 'none';
                    return;
                }

                const preferredCamera = cameras.find((camera) => {
                    const label = (camera.label || '').toLowerCase();
                    return label.includes('back') || label.includes('rear') || label.includes('environment');
                }) || cameras[0];

                await scanner.start(
                    preferredCamera.id,
                    scannerConfig,
                    async (decodedText) => {
                        if (isProcessing) {
                            return;
                        }

                        isProcessing = true;
                        await stopScanner();
                        await validateScannedBarcode(decodedText);
                        isProcessing = false;
                    },
                    () => {}
                );

                isScannerRunning = true;
                setStatus('Scanner ready. Align the 1D barcode inside the frame.', 'info');
            } catch (error) {
                isScannerRunning = false;
                readerDiv.style.display = 'none';
                setStatus('Unable to start camera scanner. Check camera permission and try again.', 'error');
                console.error(error);
            }
        });
    }

    if (uploadInput) {
        uploadInput.addEventListener('change', async function (event) {
            const file = event.target.files && event.target.files[0];
            await scanUploadedImage(file);
            uploadInput.value = '';
        });
    }
</script>
@endpush
