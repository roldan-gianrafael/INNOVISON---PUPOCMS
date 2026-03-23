<style>
    .voice-field-mic {
        position: fixed;
        z-index: 1200;
        width: 42px;
        height: 42px;
        border: none;
        border-radius: 999px;
        background: #8b0000;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        box-shadow: 0 10px 20px rgba(139, 0, 0, 0.25);
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .voice-field-mic.listening {
        background: #15803d;
    }

    .voice-field-mic:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>

<script>
    (function () {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            return;
        }

        const supportedSelector = [
            'textarea',
            'input[type="text"]',
            'input[type="email"]',
            'input[type="tel"]',
            'input[type="number"]',
            'input:not([type])'
        ].join(',');

        const micButton = document.createElement('button');
        micButton.type = 'button';
        micButton.className = 'voice-field-mic';
        micButton.textContent = 'Mic';
        micButton.setAttribute('aria-label', 'Use voice input');
        document.body.appendChild(micButton);

        const recognition = new SpeechRecognition();
        recognition.lang = document.documentElement.lang || 'en-US';
        recognition.interimResults = false;
        recognition.maxAlternatives = 1;

        let activeField = null;
        let isListening = false;

        function isEligibleField(field) {
            if (!field || !field.matches(supportedSelector)) {
                return false;
            }

            if (field.disabled || field.readOnly) {
                return false;
            }

            return !!field.closest('form');
        }

        function setMicPosition(field) {
            if (!isEligibleField(field)) {
                micButton.style.display = 'none';
                return;
            }

            const rect = field.getBoundingClientRect();
            const top = rect.top + (rect.height / 2) - 21;
            const left = Math.min(window.innerWidth - 54, rect.right - 50);

            micButton.style.top = Math.max(12, top) + 'px';
            micButton.style.left = Math.max(12, left) + 'px';
            micButton.style.display = 'inline-flex';
        }

        function updateButtonState() {
            micButton.classList.toggle('listening', isListening);
            micButton.textContent = isListening ? 'On' : 'Mic';
            micButton.disabled = !activeField;
        }

        function normalizeTranscript(text) {
            return String(text || '').trim();
        }

        function appendTranscript(field, transcript) {
            if (!transcript) {
                return;
            }

            const element = field;
            const start = typeof element.selectionStart === 'number' ? element.selectionStart : element.value.length;
            const end = typeof element.selectionEnd === 'number' ? element.selectionEnd : element.value.length;
            const currentValue = element.value || '';
            const spacer = currentValue && start > 0 && !/\s$/.test(currentValue.slice(0, start)) ? ' ' : '';

            element.value = currentValue.slice(0, start) + spacer + transcript + currentValue.slice(end);
            const caretPosition = (currentValue.slice(0, start) + spacer + transcript).length;

            if (typeof element.setSelectionRange === 'function') {
                element.setSelectionRange(caretPosition, caretPosition);
            }

            element.dispatchEvent(new Event('input', { bubbles: true }));
            element.dispatchEvent(new Event('change', { bubbles: true }));
        }

        recognition.addEventListener('start', function () {
            isListening = true;
            updateButtonState();
        });

        recognition.addEventListener('end', function () {
            isListening = false;
            updateButtonState();
            if (activeField) {
                setMicPosition(activeField);
            }
        });

        recognition.addEventListener('result', function (event) {
            const transcript = normalizeTranscript(event.results?.[0]?.[0]?.transcript);
            if (activeField) {
                appendTranscript(activeField, transcript);
            }
        });

        recognition.addEventListener('error', function () {
            isListening = false;
            updateButtonState();
        });

        document.addEventListener('focusin', function (event) {
            if (!isEligibleField(event.target)) {
                activeField = null;
                updateButtonState();
                micButton.style.display = 'none';
                return;
            }

            activeField = event.target;
            updateButtonState();
            setMicPosition(activeField);
        });

        document.addEventListener('click', function (event) {
            if (event.target === micButton) {
                return;
            }

            if (!event.target.closest('form')) {
                activeField = null;
                updateButtonState();
                micButton.style.display = 'none';
            }
        });

        window.addEventListener('scroll', function () {
            if (activeField) {
                setMicPosition(activeField);
            }
        }, true);

        window.addEventListener('resize', function () {
            if (activeField) {
                setMicPosition(activeField);
            }
        });

        micButton.addEventListener('click', function () {
            if (!activeField) {
                return;
            }

            if (isListening) {
                recognition.stop();
                return;
            }

            try {
                recognition.start();
            } catch (error) {
                console.warn('Voice recognition could not start.', error);
            }
        });
    })();
</script>
