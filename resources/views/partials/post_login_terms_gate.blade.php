<style>
    .terms-gate-overlay {
        position: fixed;
        inset: 0;
        z-index: 5000;
        background: rgba(15, 23, 42, 0.62);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .terms-gate-modal {
        width: min(760px, 100%);
        background: #ffffff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        color: #1f2937;
    }

    .terms-gate-head {
        background: #8B0000;
        color: #ffffff;
        padding: 16px 20px;
    }

    .terms-gate-head h3 {
        margin: 0;
        font-size: 34px;
        line-height: 1.2;
        font-weight: 800;
    }

    .terms-gate-body {
        padding: 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .terms-gate-body p {
        margin: 0 0 16px;
        line-height: 1.65;
        font-size: 20px;
        color: #1f2937;
    }

    .terms-gate-body p:last-child {
        margin-bottom: 0;
    }

    .terms-gate-body a {
        color: #8B0000;
        font-weight: 700;
        text-decoration: underline;
    }

    .terms-gate-checkbox {
        margin-top: 20px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        color: #1f2937;
        font-size: 18px;
    }

    .terms-gate-checkbox input[type="checkbox"] {
        margin-top: 4px;
        width: 18px;
        height: 18px;
        accent-color: #8B0000;
        flex-shrink: 0;
    }

    .terms-gate-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 14px 20px;
        background: #f8fafc;
    }

    .terms-gate-btn {
        border: 1px solid transparent;
        border-radius: 8px;
        padding: 10px 16px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        min-width: 108px;
        font-family: inherit;
    }

    .terms-gate-btn-cancel {
        background: #6b7280;
        border-color: #6b7280;
        color: #ffffff;
    }

    .terms-gate-btn-continue {
        background: #8B0000;
        border-color: #8B0000;
        color: #ffffff;
    }

    .terms-gate-btn-continue:disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }

    body.terms-gate-lock {
        overflow: hidden;
    }

    @media (max-width: 640px) {
        .terms-gate-head h3 {
            font-size: 26px;
        }

        .terms-gate-body p {
            font-size: 17px;
        }

        .terms-gate-checkbox {
            font-size: 16px;
        }

        .terms-gate-actions {
            flex-direction: column;
        }

        .terms-gate-btn {
            width: 100%;
        }
    }
</style>

@if (session('show_terms_modal'))
    <div class="terms-gate-overlay" id="termsGateOverlay" role="dialog" aria-modal="true" aria-labelledby="termsGateTitle">
        <div class="terms-gate-modal">
            <div class="terms-gate-head">
                <h3 id="termsGateTitle">Terms and Conditions</h3>
            </div>
            <div class="terms-gate-body">
                <p>
                    By clicking <strong>I Agree</strong>, you consent to the collection, use, and processing of your personal data for legitimate purposes related to this service.
                </p>
                <p>
                    Your information will be handled in accordance with our
                    <a href="https://www.pup.edu.ph/privacy/" target="_blank" rel="noopener noreferrer">Privacy Policy</a>
                    and in compliance with the Data Privacy Act of 2012.
                </p>
                <p>
                    Please also review our
                    <a href="https://www.pup.edu.ph/terms/" target="_blank" rel="noopener noreferrer">Terms of Use</a>.
                </p>

                <label class="terms-gate-checkbox" for="termsGateAgree">
                    <input type="checkbox" id="termsGateAgree">
                    <span>I Agree and acknowledge the Terms and Conditions</span>
                </label>
            </div>
            <div class="terms-gate-actions">
                <button type="button" class="terms-gate-btn terms-gate-btn-cancel" id="termsGateCancelBtn">Cancel</button>
                <button type="button" class="terms-gate-btn terms-gate-btn-continue" id="termsGateContinueBtn" disabled>Continue</button>
            </div>
        </div>
    </div>

    <form id="termsGateLogoutForm" method="POST" action="{{ route('logout') }}" style="display:none;">
        @csrf
    </form>

    <script>
        (function () {
            const overlay = document.getElementById('termsGateOverlay');
            const agreeInput = document.getElementById('termsGateAgree');
            const continueBtn = document.getElementById('termsGateContinueBtn');
            const cancelBtn = document.getElementById('termsGateCancelBtn');
            const logoutForm = document.getElementById('termsGateLogoutForm');

            if (!overlay || !agreeInput || !continueBtn || !cancelBtn || !logoutForm) {
                return;
            }

            function syncContinueState() {
                continueBtn.disabled = !agreeInput.checked;
            }

            document.body.classList.add('terms-gate-lock');
            syncContinueState();

            agreeInput.addEventListener('change', syncContinueState);

            continueBtn.addEventListener('click', function () {
                if (!agreeInput.checked) {
                    return;
                }
                overlay.remove();
                document.body.classList.remove('terms-gate-lock');
            });

            cancelBtn.addEventListener('click', function () {
                logoutForm.submit();
            });
        })();
    </script>
@endif
