@extends('layouts.admin')
@section('title', 'Patient Intake')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .notification-toast {
        position: fixed; top: 25px; right: 25px;
        background: linear-gradient(135deg, #15803d, #166534); color: #ffffff; padding: 15px 20px;
        border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        z-index: 10000; display: flex; align-items: center;
        justify-content: space-between; min-width: 380px;
        gap: 16px;
        animation: slideInRight 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .toast-copy {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #ffffff;
    }
    .toast-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 48px;
        height: 28px;
        padding: 0 10px;
        border-radius: 999px;
        background: rgba(255,255,255,0.18);
        border: 1px solid rgba(255,255,255,0.28);
        color: #ffffff;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        flex-shrink: 0;
    }
    .toast-title {
        display: block;
        font-size: 14px;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.2;
    }
    .toast-subtitle {
        display: block;
        font-size: 11px;
        opacity: 0.95;
        color: rgba(255,255,255,0.92);
        margin-top: 2px;
    }
    .btn-toast-action {
        background: rgba(255,255,255,0.25); border: 1px solid rgba(255,255,255,0.4);
        color: #ffffff; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;
        flex-shrink: 0;
    }

    .mode-header {
        padding: 22px 24px; color: white; display: flex; align-items: center;
        justify-content: center; gap: 12px; border-radius: 12px 12px 0 0;
        margin: -25px -25px 25px -25px;
        transition: background 0.4s ease;
    }
    .bg-scan { background: linear-gradient(135deg, #7f1d1d, #991b1b 55%, #b91c1c); }
    .bg-register { background: linear-gradient(135deg, #1e293b, #334155 58%, #475569); }

    .mode-header-badge {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: rgba(255,255,255,0.16);
        border: 1px solid rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.08em;
    }

    .mode-header-copy {
        text-align: left;
    }

    .mode-header-copy h3 {
        color: #ffffff;
    }

    .mode-header-copy p {
        margin: 4px 0 0;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.96);
        opacity: 1;
        line-height: 1.45;
        letter-spacing: 0.01em;
    }

    #dynamicHeader #headerTitle,
    #dynamicHeader #headerSubtitle {
        color: #ffffff;
    }

    .scanner-box {
        width: 100% !important; max-width: 480px; aspect-ratio: 16 / 9;
        margin: 0 auto; background: radial-gradient(circle at top, #1f2937 0%, #0f172a 58%, #020617 100%);
        border: 2px solid #cbd5e1;
        border-radius: 16px; overflow: hidden; position: relative;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.04), 0 20px 40px rgba(15, 23, 42, 0.18);
    }
    .scanner-box video { object-fit: cover !important; }
    .scanner-box::before {
        content: '';
        position: absolute;
        inset: 16px;
        border: 1px dashed rgba(255,255,255,0.18);
        border-radius: 12px;
        pointer-events: none;
        z-index: 2;
    }

    .scan-stage {
        transform-style: preserve-3d;
        transform-origin: center;
        transition: transform 0.55s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.25s ease;
    }

    .scan-stage.is-flipping {
        transform: rotateY(180deg) scale(0.98);
        opacity: 0.82;
    }

    .scan-line-overlay {
        position: absolute;
        left: 8%;
        width: 84%;
        height: 4px;
        border-radius: 999px;
        background: linear-gradient(90deg, rgba(16,185,129,0) 0%, rgba(52,211,153,0.95) 20%, rgba(167,243,208,1) 50%, rgba(52,211,153,0.95) 80%, rgba(16,185,129,0) 100%);
        z-index: 10;
        box-shadow: 0 0 14px rgba(110, 231, 183, 0.95), 0 0 28px rgba(16, 185, 129, 0.45);
        animation: scan-animation 2.1s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }
    .scan-line-overlay::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        top: -22px;
        height: 48px;
        background: linear-gradient(180deg, rgba(16,185,129,0) 0%, rgba(52,211,153,0.16) 45%, rgba(16,185,129,0) 100%);
        filter: blur(4px);
        pointer-events: none;
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

    .scan-method-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
        padding: 14px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: linear-gradient(135deg, #fffaf9, #f8fafc);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    }

    .scan-method-title {
        margin: 0;
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
    }

    .scan-method-note {
        margin: 4px 0 0;
        font-size: 12px;
        color: #64748b;
        line-height: 1.5;
    }

    .btn-scan-switch {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        border-radius: 999px;
        padding: 10px 14px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        white-space: nowrap;
        box-shadow: 0 6px 14px rgba(15, 23, 42, 0.06);
    }

    .walkin-strip-card {
        position: relative;
        overflow: hidden;
    }

    .walkin-strip-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 14px;
        right: 14px;
        height: 5px;
        background: #70131B;
        border-radius: 999px;
        pointer-events: none;
        z-index: 2;
    }

    .scan-method-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        color: #9a3412;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .intake-heading-kicker {
        margin: 0 0 8px;
        font-size: 16px;
        font-weight: 800;
        letter-spacing: 1px;
        color: #8b0000;
        text-transform: uppercase;
    }

    .intake-heading-title {
        margin: 0;
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
    }

    .intake-heading-copy {
        margin: 10px 0 0;
        color: #475569;
        max-width: 680px;
    }

    .intake-options-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-top: 24px;
    }

    .intake-option-link {
        text-decoration: none;
        color: inherit;
    }

    .intake-option-card {
        position: relative;
        height: 100%;
        padding: 20px;
        border-radius: 16px;
        border: 1px solid rgba(112, 19, 27, 0.42);
        box-shadow:
            inset 0 -3px 0 rgba(112, 19, 27, 0.82),
            0 10px 24px rgba(15, 23, 42, 0.05);
        overflow: hidden;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    .intake-option-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 28px rgba(15, 23, 42, 0.12);
    }

    .intake-option-card::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(255,255,255,0.48) 0%, rgba(255,255,255,0) 38%);
    }

    .intake-option-card::after {
        content: "";
        position: absolute;
        top: -42%;
        left: -130%;
        width: 120%;
        height: 185%;
        background: linear-gradient(115deg, rgba(250, 204, 21, 0) 0%, rgba(250, 204, 21, 0.46) 45%, rgba(250, 204, 21, 0) 100%);
        transform: skewX(-20deg);
        opacity: 0;
        transition: left .8s ease, opacity .18s ease;
        pointer-events: none;
        z-index: 0;
    }

    .intake-option-card:hover::after {
        opacity: 1;
        left: 125%;
    }

    .intake-option-chip {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 28px;
        height: 28px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
        border: 1px solid rgba(255, 255, 255, 0.55);
        box-shadow: 0 8px 14px rgba(15, 23, 42, 0.14);
    }

    .intake-option-chip svg {
        width: 14px;
        height: 14px;
        stroke: currentColor;
        stroke-width: 2.2;
        fill: none;
    }

    .intake-option-icon-wrap {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        position: relative;
        z-index: 1;
        animation: intakeFloat 3.8s ease-in-out infinite;
    }

    .intake-option-icon-wrap::after {
        content: "";
        position: absolute;
        left: 10%;
        right: 10%;
        bottom: -10px;
        height: 14px;
        border-radius: 999px;
        filter: blur(8px);
        opacity: .6;
        z-index: -1;
        background: radial-gradient(circle, rgba(0, 0, 0, 0.44) 0%, rgba(0, 0, 0, 0.22) 48%, transparent 86%);
    }

    .intake-option-icon-wrap svg {
        width: 24px;
        height: 24px;
        stroke: currentColor;
        stroke-width: 2.1;
        fill: none;
    }

    .intake-option-title {
        margin: 0 0 8px;
        font-size: 18px;
        font-weight: 800;
        color: #111827;
        position: relative;
        z-index: 1;
    }

    .intake-option-copy {
        margin: 0;
        color: #475569;
        line-height: 1.55;
        position: relative;
        z-index: 1;
    }

    .intake-option-registration {
        background: linear-gradient(135deg, #fff8f1, #ffffff);
    }

    .intake-option-registration.is-active {
        border: 2px solid #8B0000;
        background: linear-gradient(135deg, #fff5f5, #ffffff);
    }

    .intake-option-registration .intake-option-icon-wrap {
        background: #8B0000;
        color: #ffffff;
    }

    .intake-option-registration .intake-option-chip {
        background: #8B0000;
        color: #ffffff;
    }

    .intake-option-scan {
        background: linear-gradient(135deg, #f8fafc, #ffffff);
    }

    .intake-option-scan.is-active {
        border: 2px solid #8B0000;
        background: linear-gradient(135deg, #fff5f5, #ffffff);
    }

    .intake-option-scan .intake-option-icon-wrap {
        background: #0f172a;
        color: #ffffff;
    }

    .intake-option-scan .intake-option-chip {
        background: #0f172a;
        color: #ffffff;
    }

    .intake-option-assisted {
        background: linear-gradient(135deg, #f8fafc, #ffffff);
    }

    .intake-option-assisted.is-active {
        border: 2px solid #8B0000;
        background: linear-gradient(135deg, #eef2ff, #ffffff);
    }

    .intake-option-assisted .intake-option-icon-wrap {
        background: #334155;
        color: #ffffff;
    }

    .intake-option-assisted .intake-option-chip {
        background: #334155;
        color: #ffffff;
    }

    .intake-option-applicant {
        background: linear-gradient(135deg, #fffdf3, #ffffff);
    }

    .intake-option-applicant.is-active {
        border: 2px solid #8B0000;
        background: linear-gradient(135deg, #fff7cc, #ffffff);
    }

    .intake-option-applicant .intake-option-icon-wrap {
        background: #70131B;
        color: #ffffff;
    }

    .intake-option-applicant .intake-option-chip {
        background: #70131B;
        color: #ffffff;
    }

    html[data-theme="dark"] .intake-heading-title,
    html[data-theme="dark"] .intake-heading-copy {
        color: #ffffff;
    }

    html[data-theme="dark"] .intake-heading-kicker {
        color: #ffffff;
    }

    html[data-theme="dark"] .scan-method-title,
    html[data-theme="dark"] .scan-method-note {
        color: #ffffff;
    }

    html[data-theme="dark"] .intake-option-card {
        border-color: rgba(250, 204, 21, 0.62);
        box-shadow:
            inset 0 -3px 0 rgba(250, 204, 21, 0.92),
            0 14px 26px rgba(0, 0, 0, 0.22);
        background: #70131B;
    }

    html[data-theme="dark"] .intake-option-card::after {
        background: linear-gradient(115deg, rgba(163, 53, 72, 0) 0%, rgba(163, 53, 72, 0.62) 45%, rgba(163, 53, 72, 0) 100%);
    }

    html[data-theme="dark"] .intake-option-card::before {
        background: none;
    }

    html[data-theme="dark"] .intake-option-title,
    html[data-theme="dark"] .intake-option-copy {
        color: #ffffff;
    }

    html[data-theme="dark"] .intake-option-registration,
    html[data-theme="dark"] .intake-option-scan,
    html[data-theme="dark"] .intake-option-assisted,
    html[data-theme="dark"] .intake-option-applicant {
        background: #70131B;
    }

    html[data-theme="dark"] .intake-option-registration.is-active,
    html[data-theme="dark"] .intake-option-scan.is-active,
    html[data-theme="dark"] .intake-option-assisted.is-active,
    html[data-theme="dark"] .intake-option-applicant.is-active {
        background: #70131B;
    }

    html[data-theme="dark"] .intake-option-registration.is-active,
    html[data-theme="dark"] .intake-option-scan.is-active,
    html[data-theme="dark"] .intake-option-assisted.is-active,
    html[data-theme="dark"] .intake-option-applicant.is-active {
        border-color: #facc15;
    }

    .scan-surface {
        padding: 16px;
        border-radius: 16px;
        background: linear-gradient(180deg, #ffffff, #f8fafc);
        border: 1px solid #e2e8f0;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.9), 0 12px 30px rgba(15, 23, 42, 0.05);
    }

    .scan-inline-note {
        margin: 0 0 14px;
        padding: 10px 12px;
        border-radius: 10px;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        color: #9a3412;
        font-size: 12px;
        line-height: 1.5;
    }

    .ocr-guide {
        position: absolute;
        inset: 14px;
        border: 2px solid rgba(255,255,255,0.7);
        border-radius: 18px;
        z-index: 12;
        pointer-events: none;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.12);
    }

    .ocr-guide::before,
    .ocr-guide::after {
        content: '';
        position: absolute;
        width: 52px;
        height: 52px;
        border: 3px solid #f8fafc;
        border-radius: 12px;
    }

    .ocr-guide::before {
        top: -2px;
        left: -2px;
        border-right: 0;
        border-bottom: 0;
    }

    .ocr-guide::after {
        right: -2px;
        bottom: -2px;
        border-left: 0;
        border-top: 0;
    }

    .ocr-guide-label {
        position: absolute;
        left: 50%;
        top: 18px;
        transform: translateX(-50%);
        z-index: 13;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.72);
        color: #ffffff;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        white-space: nowrap;
        pointer-events: none;
    }

    .ocr-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 16px;
    }

    .btn-ocr {
        border: none;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.03em;
        cursor: pointer;
        transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
    }

    .btn-ocr:hover,
    .btn-ocr:focus {
        transform: translateY(-1px);
        filter: brightness(1.02);
        outline: none;
    }

    .btn-ocr-primary {
        background: linear-gradient(135deg, #0f172a, #1e293b 58%, #334155);
        color: #ffffff;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.16);
    }

    .btn-ocr-secondary {
        background: linear-gradient(135deg, #7f1d1d, #991b1b 55%, #b91c1c);
        color: #ffffff;
        box-shadow: 0 12px 24px rgba(127, 29, 29, 0.22);
    }

    .btn-ocr:disabled,
    .manual-find-btn:disabled {
        opacity: 0.65;
        cursor: not-allowed;
        transform: none;
        filter: none;
    }

    .ocr-result-panel {
        margin-top: 18px;
        padding: 16px;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #ffffff, #f8fafc);
    }

    .ocr-result-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
    }

    .ocr-result-label {
        margin: 0 0 6px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #475569;
    }

    .ocr-result-help {
        margin: 0 0 12px;
        font-size: 12px;
        color: #64748b;
        line-height: 1.55;
    }

    .ocr-status {
        margin-top: 12px;
        padding: 12px 14px;
        border-radius: 12px;
        font-size: 12px;
        line-height: 1.55;
        display: none;
    }

    .ocr-status.info {
        display: block;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
    }

    .ocr-status.success {
        display: block;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #047857;
    }

    .ocr-status.error {
        display: block;
        background: #fff1f2;
        border: 1px solid #fecdd3;
        color: #be123c;
    }

    html[data-theme="dark"] .walkin-strip-card::before {
        background: #facc15;
    }

    .ocr-meta {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
        font-size: 11px;
        font-weight: 700;
    }

    .ocr-lock-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-left: 8px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #047857;
        font-size: 11px;
        font-weight: 800;
    }

    .manual-find-btn {
        min-width: 180px;
        min-height: 48px;
        padding: 0 26px;
        border: none;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #7f1d1d, #991b1b 55%, #b91c1c);
        color: #ffffff;
        font-weight: 800;
        font-size: 0.92rem;
        letter-spacing: 0.03em;
        box-shadow: 0 12px 24px rgba(127, 29, 29, 0.24);
        transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
    }

    .manual-find-btn:hover,
    .manual-find-btn:focus {
        transform: translateY(-1px);
        box-shadow: 0 16px 28px rgba(127, 29, 29, 0.3);
        filter: brightness(1.03);
        outline: none;
    }

    .manual-find-btn:active {
        transform: translateY(0);
        box-shadow: 0 8px 18px rgba(127, 29, 29, 0.22);
    }

    .applicant-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 1300;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 26px 18px;
        background: rgba(15, 23, 42, 0.52);
        backdrop-filter: blur(10px);
    }

    .applicant-modal-backdrop.show {
        display: flex;
    }

    .applicant-modal-shell {
        width: min(1180px, 100%);
        max-height: calc(100vh - 40px);
        overflow: hidden;
        border-radius: 24px;
        background: linear-gradient(180deg, rgba(255,255,255,0.96), rgba(248,250,252,0.98));
        box-shadow: 0 26px 60px rgba(15, 23, 42, 0.24);
        border: 1px solid rgba(255,255,255,0.62);
        border-bottom: 4px solid #70131B;
    }

    .applicant-modal-head {
        position: relative;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px 14px;
        background: linear-gradient(135deg, #7f1d1d, #991b1b 55%, #b91c1c);
        color: #ffffff;
    }

    .applicant-modal-head-main {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        min-width: 0;
    }

    .applicant-modal-head-actions {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-width: max-content;
        z-index: 1;
    }

    .applicant-modal-head-badge {
        width: 46px;
        height: 46px;
        flex: 0 0 auto;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.24);
        color: #ffffff;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.08em;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.16);
    }

    .applicant-modal-head-copy {
        min-width: 0;
    }

    .applicant-modal-head h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #ffffff !important;
    }

    .applicant-modal-head p {
        margin: 6px 0 0;
        color: rgba(255, 255, 255, 0.92) !important;
        font-size: 12px;
        line-height: 1.55;
        max-width: 760px;
    }

    .applicant-modal-head-copy,
    .applicant-modal-head-copy h3,
    .applicant-modal-head-copy p {
        color: #ffffff !important;
    }

    .applicant-modal-head-copy .scan-method-badge {
        margin-top: 8px;
        margin-bottom: 0;
    }

    .applicant-modal-head-actions .btn-scan-switch {
        position: relative;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.96);
        color: #70131B;
        border-color: rgba(255, 255, 255, 0.86);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
        min-height: 44px;
        padding: 11px 18px;
        font-size: 13px;
        gap: 8px;
        transition: color .08s linear, transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease;
    }

    .applicant-modal-head-actions .btn-scan-switch svg {
        width: 15px;
        height: 15px;
        flex: 0 0 auto;
        stroke-width: 2.2;
    }

    .applicant-modal-head-actions .btn-scan-switch::after {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(120deg,
                rgba(255, 248, 196, 0) 0%,
                rgba(255, 239, 181, 0.14) 22%,
                rgba(255, 239, 181, 0.52) 48%,
                rgba(255, 239, 181, 0.14) 72%,
                rgba(255, 248, 196, 0) 100%);
        transform: translateX(-135%);
        transition: transform 1.2s ease;
        pointer-events: none;
    }

    .applicant-modal-head-actions .btn-scan-switch:hover,
    .applicant-modal-head-actions .btn-scan-switch:focus {
        transform: translateY(-1px);
        border-color: #facc15;
        background: #fff8e1;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16);
        outline: none;
    }

    .applicant-modal-head-actions .btn-scan-switch:hover::after,
    .applicant-modal-head-actions .btn-scan-switch:focus::after {
        transform: translateX(135%);
    }

    .applicant-modal-head-actions .scan-method-badge {
        margin-top: 0;
        background: rgba(255, 244, 214, 0.96);
        border-color: rgba(254, 215, 170, 0.94);
        color: #9a3412;
    }

    .applicant-modal-close {
        width: 40px;
        height: 40px;
        min-width: 40px;
        min-height: 40px;
        padding: 0;
        flex: 0 0 40px;
        border-radius: 999px;
        position: relative;
        overflow: hidden;
        border: 1px solid #8f2230;
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20);
        cursor: pointer;
        transition: color .08s linear, transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        z-index: 0;
    }

    .applicant-modal-close::after {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(120deg,
                rgba(255, 248, 196, 0) 0%,
                rgba(255, 239, 181, 0.14) 22%,
                rgba(255, 239, 181, 0.52) 48%,
                rgba(255, 239, 181, 0.14) 72%,
                rgba(255, 248, 196, 0) 100%);
        transform: translateX(-135%);
        transition: transform 1.5s ease;
        z-index: -1;
    }

    .applicant-modal-close svg {
        width: 18px;
        height: 18px;
        stroke-width: 2.2;
        flex: 0 0 auto;
    }

    .applicant-modal-close:hover,
    .applicant-modal-close:focus {
        transform: translateY(-1px);
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16);
        outline: none;
    }

    .applicant-modal-close:hover::after,
    .applicant-modal-close:focus::after {
        transform: translateX(135%);
    }

    .applicant-modal-body {
        padding: 18px;
        overflow: auto;
        max-height: calc(100vh - 158px);
    }

    .applicant-modal-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(320px, 0.85fr);
        gap: 18px;
        align-items: start;
    }

    .applicant-modal-panel {
        border-radius: 18px;
        border: 1px solid rgba(148, 163, 184, 0.16);
        background: rgba(255,255,255,0.88);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.06);
        padding: 16px;
    }

    .applicant-modal-panel .scan-method-bar {
        margin-bottom: 14px;
    }

    .applicant-modal-panel .scan-method-title,
    .applicant-modal-panel .scan-method-note {
        color: #000000 !important;
    }

    .applicant-modal-panel .ocr-actions {
        margin-top: 14px;
    }

    .applicant-modal-panel .ocr-result-panel {
        display: block;
        margin-top: 0;
        background: transparent;
        border: none;
        padding: 0;
        box-shadow: none;
    }

    .applicant-modal-panel .manual-input-stack {
        margin-top: 16px;
        padding-top: 14px;
        border-top: 1px dashed #cbd5e1;
    }

    .applicant-modal-panel .manual-input-stack .manual-find-btn {
        width: 100%;
    }

    .applicant-modal-panel .manual-toggle-label {
        margin: 0 0 10px;
        font-size: 12px;
        font-weight: 700;
        color: #7f1d1d;
    }

    .applicant-biosync-visual {
        position: relative;
        min-height: 360px;
        border-radius: 18px;
        overflow: hidden;
        background: radial-gradient(circle at top, #eff6ff 0%, #dbeafe 38%, #eef2ff 100%);
        border: 1px dashed #93c5fd;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        padding: 28px 24px;
        text-align: center;
    }

    .applicant-biosync-ring {
        position: relative;
        width: 190px;
        height: 190px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle, rgba(255,255,255,0.95) 0%, rgba(219,234,254,0.9) 52%, rgba(191,219,254,0.72) 100%);
        box-shadow:
            inset 0 0 0 1px rgba(59, 130, 246, 0.16),
            0 22px 42px rgba(59, 130, 246, 0.14);
    }

    .applicant-biosync-ring::before,
    .applicant-biosync-ring::after {
        content: "";
        position: absolute;
        border-radius: 999px;
        inset: -14px;
        border: 1px solid rgba(59, 130, 246, 0.18);
        animation: biosyncPulse 2.6s ease-in-out infinite;
    }

    .applicant-biosync-ring::after {
        inset: -28px;
        animation-delay: 1.2s;
    }

    .applicant-biosync-fingerprint {
        width: 104px;
        height: 104px;
        color: #2563eb;
        display: block;
    }

    .applicant-biosync-scanline {
        position: absolute;
        left: 20%;
        right: 20%;
        top: 50%;
        height: 4px;
        border-radius: 999px;
        background: linear-gradient(90deg, rgba(37,99,235,0) 0%, rgba(37,99,235,0.95) 50%, rgba(37,99,235,0) 100%);
        box-shadow: 0 0 14px rgba(37,99,235,0.34);
        animation: biosyncScan 2s ease-in-out infinite;
    }

    .applicant-biosync-copy {
        margin-top: 28px;
        max-width: 320px;
    }

    .applicant-biosync-copy h4 {
        margin: 0 0 8px;
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
    }

    .applicant-biosync-copy p {
        margin: 0;
        font-size: 13px;
        line-height: 1.65;
        color: #475569;
    }

    html[data-theme="dark"] .applicant-modal-shell {
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(17, 24, 39, 0.96));
        border-color: rgba(148, 163, 184, 0.16);
    }

    html[data-theme="dark"] .applicant-modal-panel {
        background: rgba(15, 23, 42, 0.88);
        border-color: rgba(148, 163, 184, 0.16);
        box-shadow: 0 18px 34px rgba(0, 0, 0, 0.24);
    }

    html[data-theme="dark"] .applicant-modal-panel .scan-method-bar,
    html[data-theme="dark"] .applicant-modal-panel .scan-surface {
        background: linear-gradient(180deg, rgba(17, 24, 39, 0.96), rgba(15, 23, 42, 0.94));
        border-color: rgba(148, 163, 184, 0.18);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.04), 0 12px 30px rgba(0, 0, 0, 0.20);
    }

    html[data-theme="dark"] .applicant-modal-panel .scan-method-title,
    html[data-theme="dark"] .applicant-modal-panel .scan-method-note,
    html[data-theme="dark"] .applicant-modal-panel .scan-inline-note {
        color: #f8fafc !important;
    }

    html[data-theme="dark"] .applicant-modal-panel .scan-inline-note {
        background: rgba(112, 19, 27, 0.32);
        border-color: rgba(250, 204, 21, 0.22);
    }

    html[data-theme="dark"] .applicant-modal-panel .btn-scan-switch {
        background: rgba(15, 23, 42, 0.86);
        color: #f8fafc;
        border-color: rgba(250, 204, 21, 0.28);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.22);
    }

    html[data-theme="dark"] .applicant-modal-panel .scan-method-badge {
        background: rgba(250, 204, 21, 0.16);
        border-color: rgba(250, 204, 21, 0.28);
        color: #fde68a;
    }

    html[data-theme="dark"] .applicant-modal-head-actions .btn-scan-switch {
        background: rgba(15, 23, 42, 0.86);
        color: #f8fafc;
        border-color: rgba(250, 204, 21, 0.28);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.22);
    }

    html[data-theme="dark"] .applicant-modal-head-actions .btn-scan-switch:hover,
    html[data-theme="dark"] .applicant-modal-head-actions .btn-scan-switch:focus {
        background: rgba(112, 19, 27, 0.92);
        color: #fef3c7;
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(0, 0, 0, 0.28);
    }

    html[data-theme="dark"] .applicant-modal-head-actions .scan-method-badge {
        background: rgba(250, 204, 21, 0.16);
        border-color: rgba(250, 204, 21, 0.28);
        color: #fde68a;
    }

    html[data-theme="dark"] .applicant-modal-panel .manual-input-stack {
        border-top-color: rgba(148, 163, 184, 0.18);
    }

    html[data-theme="dark"] .applicant-modal-panel .manual-toggle-label {
        color: #facc15;
    }

    html[data-theme="dark"] .applicant-biosync-visual {
        background: radial-gradient(circle at top, rgba(30,41,59,0.98) 0%, rgba(15,23,42,0.94) 38%, rgba(17,24,39,0.96) 100%);
        border-color: rgba(96, 165, 250, 0.22);
    }

    html[data-theme="dark"] .applicant-biosync-ring {
        background: radial-gradient(circle, rgba(30,41,59,0.98) 0%, rgba(30,64,175,0.18) 52%, rgba(30,41,59,0.92) 100%);
        box-shadow:
            inset 0 0 0 1px rgba(96, 165, 250, 0.18),
            0 22px 42px rgba(0, 0, 0, 0.26);
    }

    html[data-theme="dark"] .applicant-biosync-fingerprint {
        color: #93c5fd;
    }

    html[data-theme="dark"] .applicant-biosync-copy h4 {
        color: #f8fafc;
    }

    html[data-theme="dark"] .applicant-biosync-copy p {
        color: #cbd5e1;
    }

    @keyframes biosyncPulse {
        0%, 100% { transform: scale(1); opacity: 0.45; }
        50% { transform: scale(1.04); opacity: 0.9; }
    }

    @keyframes biosyncScan {
        0% { top: 28%; opacity: 0.72; }
        50% { top: 72%; opacity: 1; }
        100% { top: 28%; opacity: 0.72; }
    }

    .registration-hub {
        max-width: 980px;
    }

    .registration-head {
        margin-bottom: 10px;
    }

    .registration-head-main {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }

    .registration-head-copy {
        flex: 1 1 420px;
        min-width: 280px;
    }

    .registration-kicker {
        margin: 0 0 8px;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #8b0000;
    }

    .registration-head h3 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 900;
        color: #111827;
    }

    .registration-head p {
        margin: 8px 0 0;
        color: #475569;
    }

    .registration-mode-picker {
        display: flex;
        justify-content: center;
        gap: 18px;
        margin: 18px 0 20px;
        flex-wrap: wrap;
    }

    .registration-mode-btn {
        width: min(360px, 100%);
        min-height: 280px;
        border: 1px solid rgba(234, 179, 8, 0.42);
        border-radius: 28px;
        padding: 26px 24px 30px;
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 48%, #e5e7eb 100%);
        box-shadow:
            0 0 0 1px rgba(250, 204, 21, 0.12),
            0 22px 36px rgba(234, 179, 8, 0.18),
            0 48px 60px -36px rgba(202, 138, 4, 0.36);
        text-align: center;
        text-decoration: none;
        color: inherit;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    }

    .registration-mode-btn::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(255,255,255,0.92), rgba(255,255,255,0.28) 42%, rgba(255,255,255,0.10));
        pointer-events: none;
    }

    .registration-mode-btn::after {
        content: "";
        position: absolute;
        left: 10%;
        right: 10%;
        bottom: -22px;
        height: 36px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(148, 163, 184, 0.34) 0%, rgba(148, 163, 184, 0.14) 42%, transparent 78%);
        filter: blur(12px);
        pointer-events: none;
    }

    .registration-mode-btn:hover {
        transform: translateY(-4px);
        border-color: rgba(234, 179, 8, 0.62);
        box-shadow:
            0 0 0 1px rgba(250, 204, 21, 0.22),
            0 26px 42px rgba(234, 179, 8, 0.22),
            0 54px 76px -38px rgba(202, 138, 4, 0.42);
        text-decoration: none;
        color: inherit;
    }

    .registration-mode-btn .um-mode-icon {
        width: 68px;
        height: 68px;
        margin: 12px auto 10px;
        border-radius: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(145deg, rgba(254, 240, 138, 0.98), rgba(250, 204, 21, 0.9));
        border: 1px solid rgba(234, 179, 8, 0.34);
        color: #111827;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.96), 0 18px 28px rgba(234, 179, 8, 0.18);
        position: relative;
        z-index: 1;
        animation: umModeFloat 3.8s ease-in-out infinite;
    }

    .registration-mode-btn .um-mode-icon::after {
        content: "";
        position: absolute;
        left: 12%;
        right: 12%;
        bottom: -13px;
        height: 14px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(0, 0, 0, 0.42) 0%, rgba(0, 0, 0, 0.2) 48%, transparent 86%);
        filter: blur(7px);
        z-index: -1;
        pointer-events: none;
    }

    .registration-mode-btn .um-mode-icon svg {
        width: 30px;
        height: 30px;
        stroke: currentColor;
        stroke-width: 2;
        fill: none;
    }

    .registration-mode-btn .um-mode-icon img {
        width: 46px;
        height: 46px;
        object-fit: contain;
        display: block;
    }

    .registration-mode-btn h3 {
        margin: 14px 0 8px;
        font-size: 1.24rem;
        font-weight: 900;
        color: #0f172a;
        position: relative;
        z-index: 1;
    }

    .registration-mode-btn p {
        margin: 0;
        color: #64748b;
        line-height: 1.6;
        font-size: .95rem;
        position: relative;
        z-index: 1;
    }

    .registration-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: flex-end;
        margin-top: 8px;
    }

    .registration-actions .btn {
        min-width: 132px;
        width: auto !important;
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
        overflow: hidden;
        border-radius: 999px;
        border: 1px solid rgba(112, 19, 27, 0.3);
        background: rgba(255, 255, 255, 0.96);
        color: #70131B;
        font-weight: 800;
        padding: 10px 16px;
        white-space: nowrap;
        box-shadow: 0 0 0 2px rgba(112, 19, 27, 0.09), 0 10px 20px rgba(15, 23, 42, 0.08);
    }

    .registration-actions .btn::after {
        content: "";
        position: absolute;
        top: -40%;
        left: -130%;
        width: 120%;
        height: 180%;
        background: linear-gradient(115deg, rgba(250, 204, 21, 0) 0%, rgba(250, 204, 21, 0.46) 45%, rgba(250, 204, 21, 0) 100%);
        transform: skewX(-20deg);
        transition: left 1.5s ease;
        pointer-events: none;
    }

    .registration-actions .btn:hover::after {
        left: 125%;
    }

    .registration-actions .btn:hover,
    .registration-actions .btn:focus {
        color: #70131B;
        border-color: rgba(112, 19, 27, 0.48);
        background: #ffffff;
    }

    .assisted-intake-shell {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 22px;
        align-items: start;
    }

    .assisted-panel {
        position: relative;
        border-radius: 24px;
        border: 1px solid rgba(148, 163, 184, 0.22);
        background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,250,252,0.94));
        box-shadow: 0 20px 44px rgba(15, 23, 42, 0.08), inset 0 1px 0 rgba(255,255,255,0.9);
        overflow: hidden;
    }

    .assisted-panel::before {
        content: "";
        position: absolute;
        top: 0;
        left: 26px;
        right: 26px;
        height: 4px;
        border-radius: 999px;
        background: linear-gradient(90deg, #70131B 0%, #a11d33 54%, #facc15 100%);
    }

    .assisted-panel-body {
        padding: 22px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .assisted-hero {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 18px;
        grid-column: 1 / -1;
    }

    .assisted-hero-badge {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        background: linear-gradient(145deg, #70131B, #8f2230);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 16px 30px rgba(112, 19, 27, 0.22);
        flex-shrink: 0;
    }

    .assisted-hero-badge svg {
        width: 28px;
        height: 28px;
        stroke: currentColor;
        stroke-width: 2;
        fill: none;
    }

    .assisted-hero-copy h3 {
        margin: 0;
        font-size: 1.45rem;
        font-weight: 900;
        color: #111827;
        letter-spacing: -0.02em;
    }

    .assisted-hero-copy p {
        margin: 8px 0 0;
        color: #64748b;
        line-height: 1.6;
        font-size: .95rem;
    }

    .assisted-status-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 14px;
    }

    .assisted-status-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .assisted-status-chip.pending {
        background: #fff7ed;
        border: 1px solid #fdba74;
        color: #9a3412;
    }

    .assisted-status-chip.ready {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #047857;
    }

    .assisted-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .assisted-section-divider {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 6px 0 12px;
    }

    .assisted-section-divider::before,
    .assisted-section-divider::after {
        content: "";
        flex: 1 1 auto;
        height: 1px;
        background: linear-gradient(90deg, rgba(148, 163, 184, 0), rgba(148, 163, 184, 0.42), rgba(148, 163, 184, 0));
    }

    .assisted-section-divider span {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.18);
        background: rgba(255, 255, 255, 0.82);
        color: #7f1d1d;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
        white-space: nowrap;
    }

    .assisted-section-divider svg {
        width: 14px;
        height: 14px;
        stroke: currentColor;
        fill: none;
        stroke-width: 1.9;
    }

    .assisted-panel-body > .mb-3,
    .assisted-panel-body > .mb-2,
    .assisted-panel-body > .assisted-pair-row,
    .assisted-panel-body > .assisted-field-card,
    .assisted-panel-body > input.form-control,
    .assisted-panel-body > .assisted-callout,
    .assisted-panel-body > div[style*="background:#fff7ed"] {
        margin-bottom: 14px !important;
        padding: 14px;
        border-radius: 18px;
        border: 1px solid rgba(127, 29, 29, 0.16);
        background: linear-gradient(180deg, #ffffff, #f8fafc);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.92);
    }

    .assisted-highlight-card {
        border: 1px solid rgba(127, 29, 29, 0.12) !important;
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, 0.12), transparent 34%),
            linear-gradient(180deg, #fff4f4 0%, #fef2f2 100%) !important;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,0.94),
            0 10px 20px rgba(127, 29, 29, 0.05);
    }

    .assisted-highlight-card label {
        color: #7f1d1d !important;
    }

    .assisted-panel-body > .mb-3,
    .assisted-panel-body > .assisted-pair-row:first-of-type {
        grid-column: 1 / -1;
    }

    .assisted-panel-body > .mb-3 label,
    .assisted-panel-body > .mb-2 label,
    .assisted-field-label {
        display: block;
        margin: 0 0 9px;
        font-size: 13px !important;
        font-weight: 700 !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b !important;
    }

    .assisted-panel-body > .mb-3 .form-control,
    .assisted-panel-body > .mb-2 .form-control,
    .assisted-panel-body > .assisted-pair-row .form-control,
    .assisted-panel-body > .assisted-field-card .form-control,
    .assisted-panel-body > input.form-control {
        width: 100%;
        min-height: 56px;
        padding: 16px 18px;
        border: 1px solid rgba(127, 29, 29, 0.22);
        border-radius: 18px;
        font-size: 14px;
        color: #111111;
        font-weight: 700;
        margin-bottom: 0 !important;
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, 0.10), transparent 36%),
            linear-gradient(180deg, #ffffff 0%, #fff8f6 100%);
        box-shadow:
            0 12px 22px rgba(15, 23, 42, 0.08),
            inset 0 1px 0 rgba(255,255,255,0.86);
        transition: all 0.2s ease;
    }

    .assisted-pair-row {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px !important;
        align-items: stretch;
    }

    .assisted-field-card {
        padding: 14px;
        border-radius: 18px;
        border: 1px solid rgba(127, 29, 29, 0.16);
        background: linear-gradient(180deg, #ffffff, #f8fafc);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.92);
        min-width: 0;
    }

    .assisted-panel-body > input.form-control {
        width: 100%;
    }

    .assisted-panel-body .form-control::placeholder {
        color: #94a3b8;
        font-weight: 600;
    }

    .assisted-panel-body .form-control:hover {
        border-color: rgba(139, 0, 0, 0.34);
        box-shadow:
            0 14px 24px rgba(15, 23, 42, 0.10),
            0 8px 18px rgba(139, 0, 0, 0.05),
            inset 0 1px 0 rgba(255,255,255,0.90);
        transform: translateY(-1px);
    }

    .assisted-panel-body .form-control:focus,
    .assisted-panel-body select.form-control:focus {
        border-color: #8B0000;
        box-shadow:
            0 0 0 4px rgba(139, 0, 0, 0.06),
            0 14px 24px rgba(139, 0, 0, 0.10),
            inset 0 1px 0 rgba(255,255,255,0.88);
        background: #ffffff;
        outline: none;
        transform: translateY(-1px);
    }

    .assisted-panel-body select.form-control {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        padding-right: 44px;
        background-image:
            linear-gradient(45deg, transparent 50%, #70131B 50%),
            linear-gradient(135deg, #70131B 50%, transparent 50%);
        background-position:
            calc(100% - 20px) calc(50% - 3px),
            calc(100% - 14px) calc(50% - 3px);
        background-size: 6px 6px, 6px 6px;
        background-repeat: no-repeat;
    }

    .assisted-panel-body input[type="date"].form-control {
        letter-spacing: 0.01em;
        color: #1e293b;
    }

    .assisted-panel-body input[type="date"].form-control::-webkit-calendar-picker-indicator {
        opacity: 0.7;
        cursor: pointer;
        filter: sepia(1) saturate(6) hue-rotate(330deg);
    }

    .assisted-field-card .assisted-gender-wrap {
        width: 100%;
    }

    .assisted-role-wrap {
        position: relative;
    }

    .assisted-role-select {
        position: absolute;
        opacity: 0;
        pointer-events: none;
        width: 0;
        height: 0;
        padding: 0;
        border: 0;
        margin: 0;
    }

    .assisted-role-display {
        width: 100%;
        min-height: 52px;
        padding: 14px 52px 14px 16px;
        border: 1px solid rgba(148, 163, 184, 0.20);
        border-radius: 18px;
        font-size: 14px;
        color: #111111;
        background: linear-gradient(180deg, #ffffff 0%, #fff8f6 100%);
        box-shadow: 0 12px 22px rgba(15, 23, 42, 0.08), inset 0 1px 0 rgba(255,255,255,0.86);
        cursor: pointer;
        font-weight: 600;
        text-align: left;
        transition: all 0.2s ease;
    }

    .assisted-role-display:hover {
        border-color: rgba(139, 0, 0, 0.28);
        box-shadow: 0 10px 18px rgba(139, 0, 0, 0.05), inset 0 1px 0 rgba(255,255,255,0.86);
    }

    .assisted-role-display.is-open,
    .assisted-role-display:focus {
        outline: none;
        border-color: #8B0000;
        box-shadow: 0 0 0 4px rgba(139, 0, 0, 0.06), 0 10px 18px rgba(139, 0, 0, 0.08);
    }

    .assisted-role-wrap::after {
        content: "";
        position: absolute;
        top: 50%;
        right: 18px;
        width: 10px;
        height: 10px;
        border-right: 2px solid #8B0000;
        border-bottom: 2px solid #8B0000;
        transform: translateY(-65%) rotate(45deg);
        pointer-events: none;
        transition: transform 0.18s ease;
    }

    .assisted-role-wrap::before {
        content: "";
        position: absolute;
        top: 50%;
        right: 42px;
        transform: translateY(-50%);
        width: 1px;
        height: 24px;
        background: rgba(148, 163, 184, 0.24);
        pointer-events: none;
    }

    .assisted-role-wrap.is-open::after {
        transform: translateY(-20%) rotate(225deg);
    }

    .assisted-role-menu {
        position: absolute;
        top: calc(100% + 10px);
        left: 0;
        right: 0;
        display: none;
        gap: 10px;
        padding: 14px;
        border-radius: 18px;
        border: 1px solid rgba(139, 0, 0, 0.12);
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.14);
        z-index: 80;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    .assisted-role-wrap.is-open .assisted-role-menu {
        display: grid;
    }

    .assisted-role-option {
        width: 100%;
        border: 1px solid rgba(148, 163, 184, 0.22);
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        color: #1e293b;
        border-radius: 999px;
        padding: 12px 14px;
        font-size: 13px;
        font-weight: 800;
        text-align: left;
        cursor: pointer;
        transition: all 0.18s ease;
        box-shadow: 0 12px 22px rgba(15, 23, 42, 0.08), 0 1px 0 rgba(255,255,255,0.82) inset;
    }

    .assisted-role-option:hover {
        transform: translateY(-1px);
        border-color: #8B0000;
        background: linear-gradient(135deg, #8B0000, #70131B);
        color: #facc15;
        box-shadow: 0 12px 20px rgba(139, 0, 0, 0.16);
    }

    .assisted-role-option.is-selected {
        background: linear-gradient(135deg, #8B0000, #70131B);
        color: #ffffff;
        border-color: #8B0000;
        box-shadow: 0 14px 24px rgba(139, 0, 0, 0.18);
    }

    .assisted-gender-wrap {
        position: relative;
    }

    .assisted-gender-select {
        position: absolute;
        opacity: 0;
        pointer-events: none;
        width: 0;
        height: 0;
        padding: 0;
        border: 0;
        margin: 0;
    }

    .assisted-gender-display {
        width: 100%;
        min-height: 52px;
        padding: 14px 52px 14px 16px;
        border: 1px solid rgba(148, 163, 184, 0.20);
        border-radius: 18px;
        font-size: 14px;
        color: #111111;
        background: linear-gradient(180deg, #ffffff 0%, #fff8f6 100%);
        box-shadow: 0 12px 22px rgba(15, 23, 42, 0.08), inset 0 1px 0 rgba(255,255,255,0.86);
        cursor: pointer;
        font-weight: 600;
        text-align: left;
        transition: all 0.2s ease;
    }

    .assisted-gender-display:hover {
        border-color: rgba(139, 0, 0, 0.28);
        box-shadow: 0 10px 18px rgba(139, 0, 0, 0.05), inset 0 1px 0 rgba(255,255,255,0.86);
    }

    .assisted-gender-display.is-open,
    .assisted-gender-display:focus {
        outline: none;
        border-color: #8B0000;
        box-shadow: 0 0 0 4px rgba(139, 0, 0, 0.06), 0 10px 18px rgba(139, 0, 0, 0.08);
    }

    .assisted-gender-wrap::after {
        content: "";
        position: absolute;
        top: 50%;
        right: 18px;
        width: 10px;
        height: 10px;
        border-right: 2px solid #8B0000;
        border-bottom: 2px solid #8B0000;
        transform: translateY(-65%) rotate(45deg);
        pointer-events: none;
        transition: transform 0.18s ease;
    }

    .assisted-gender-wrap::before {
        content: "";
        position: absolute;
        top: 50%;
        right: 42px;
        transform: translateY(-50%);
        width: 1px;
        height: 24px;
        background: rgba(148, 163, 184, 0.24);
        pointer-events: none;
    }

    .assisted-gender-wrap.is-open::after {
        transform: translateY(-20%) rotate(225deg);
    }

    @media (max-width: 767.98px) {
        .assisted-pair-row {
            grid-template-columns: 1fr;
        }
    }

    .assisted-gender-menu {
        position: absolute;
        top: calc(100% + 10px);
        left: 0;
        right: 0;
        display: none;
        gap: 10px;
        padding: 14px;
        border-radius: 18px;
        border: 1px solid rgba(139, 0, 0, 0.12);
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.14);
        z-index: 80;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    .assisted-gender-wrap.is-open .assisted-gender-menu {
        display: grid;
    }

    .assisted-gender-option {
        width: 100%;
        border: 1px solid rgba(148, 163, 184, 0.22);
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        color: #1e293b;
        border-radius: 999px;
        padding: 12px 14px;
        font-size: 13px;
        font-weight: 800;
        text-align: left;
        cursor: pointer;
        transition: all 0.18s ease;
        box-shadow: 0 12px 22px rgba(15, 23, 42, 0.08), 0 1px 0 rgba(255,255,255,0.82) inset;
    }

    .assisted-gender-option:hover {
        transform: translateY(-1px);
        border-color: #8B0000;
        background: linear-gradient(135deg, #8B0000, #70131B);
        color: #facc15;
        box-shadow: 0 12px 20px rgba(139, 0, 0, 0.16);
    }

    .assisted-gender-option.is-selected {
        background: linear-gradient(135deg, #8B0000, #70131B);
        color: #ffffff;
        border-color: #8B0000;
        box-shadow: 0 14px 24px rgba(139, 0, 0, 0.18);
    }

    .assisted-field,
    .assisted-field-full {
        padding: 14px;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #ffffff, #f8fafc);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.92);
    }

    .assisted-field-full {
        grid-column: 1 / -1;
    }

    .assisted-field-label {
        display: block;
        margin: 0 0 9px;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: #64748b;
    }

    .assisted-field .form-control,
    .assisted-field select,
    .assisted-field-full .form-control,
    .assisted-field-full select {
        margin-bottom: 0;
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        min-height: 48px;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.95);
    }

    .assisted-inline-identity {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 14px;
    }

    .assisted-summary-card {
        display: grid;
        gap: 14px;
        max-width: 360px;
        width: 100%;
        justify-self: end;
    }

    .assisted-summary-card .assisted-panel-body {
        display: block;
    }

    .assisted-intake-shell > .assisted-panel > .assisted-panel-body {
        display: block;
        padding: 28px 30px;
        max-width: 880px;
        margin: 0 auto;
        width: 100%;
    }

    .assisted-intake-shell > .assisted-panel > .assisted-panel-body > .mb-3,
    .assisted-intake-shell > .assisted-panel > .assisted-panel-body > .mb-2,
    .assisted-intake-shell > .assisted-panel > .assisted-panel-body > .d-flex.gap-2,
    .assisted-intake-shell > .assisted-panel > .assisted-panel-body > input.form-control,
    .assisted-intake-shell > .assisted-panel > .assisted-panel-body > div[style*="background:#fff7ed"] {
        margin-bottom: 16px !important;
    }

    .assisted-intake-shell > .assisted-panel > .assisted-panel-body > .mb-3,
    .assisted-intake-shell > .assisted-panel > .assisted-panel-body > .mb-2,
    .assisted-intake-shell > .assisted-panel > .assisted-panel-body > .d-flex.gap-2,
    .assisted-intake-shell > .assisted-panel > .assisted-panel-body > input.form-control,
    .assisted-intake-shell > .assisted-panel > .assisted-panel-body > div[style*="background:#fff7ed"] {
        padding: 16px;
    }

    .assisted-intake-shell > .assisted-panel > .assisted-panel-body > .mb-3 label,
    .assisted-intake-shell > .assisted-panel > .assisted-panel-body > .mb-2 label {
        font-size: 12px !important;
    }

    .assisted-summary-block {
        padding: 18px;
        border-radius: 20px;
        border: 1px solid rgba(112, 19, 27, 0.10);
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, 0.16), transparent 34%),
            linear-gradient(180deg, #fffdf8 0%, #fff9fb 48%, #ffffff 100%);
        box-shadow: 0 18px 32px rgba(15, 23, 42, 0.06);
    }

    .assisted-summary-kicker {
        margin: 0 0 8px;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #8b0000;
    }

    .assisted-summary-title {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 900;
        color: #111827;
    }

    .assisted-summary-copy {
        margin: 8px 0 0;
        color: #64748b;
        line-height: 1.6;
        font-size: .93rem;
    }

    .assisted-preview-list {
        display: grid;
        gap: 10px;
        margin-top: 14px;
    }

    .assisted-preview-item {
        display: grid;
        grid-template-columns: 110px minmax(0, 1fr);
        gap: 12px;
        align-items: center;
        padding: 11px 12px;
        border-radius: 14px;
        background: rgba(255,255,255,0.9);
        border: 1px solid rgba(203, 213, 225, 0.7);
    }

    .assisted-preview-item small {
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
    }

    .assisted-preview-item strong {
        color: #111827;
        font-size: 14px;
    }

    .assisted-callout {
        padding: 16px 18px;
        border-radius: 18px;
        background: linear-gradient(180deg, #fff7ed, #fffaf3);
        border: 1px dashed #fdba74;
    }

    .assisted-callout strong {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        color: #9a3412;
    }

    .assisted-callout p {
        margin: 0;
        font-size: 12px;
        color: #7c2d12;
        line-height: 1.6;
    }

    .assisted-footer {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px dashed #cbd5e1;
    }

    .assisted-save-btn {
        width: 100%;
        min-height: 56px;
        border: none;
        border-radius: 16px;
        background: linear-gradient(135deg, #15803d, #166534 54%, #14532d);
        color: #ffffff;
        font-weight: 900;
        letter-spacing: 0.04em;
        box-shadow: 0 18px 28px rgba(22, 101, 52, 0.24);
    }

    .assisted-footer-link {
        margin-top: 12px;
        text-align: center;
    }

    .assisted-footer-link a {
        font-size: 12px;
        color: #64748b;
        text-decoration: none;
    }

    @media (max-width: 900px) {
        .assisted-intake-shell,
        .assisted-inline-identity {
            grid-template-columns: 1fr;
        }

        .assisted-summary-card {
            max-width: none;
            justify-self: stretch;
        }
    }

    @media (max-width: 640px) {
        .assisted-grid {
            grid-template-columns: 1fr;
        }

        .assisted-preview-item {
            grid-template-columns: 1fr;
            gap: 6px;
        }
    }

    .fingerprint-hub {
        max-width: 980px;
    }

    .fingerprint-hero {
        display: grid;
        grid-template-columns: auto 1fr;
        align-items: center;
        gap: 18px;
        margin-bottom: 16px;
    }

    .fingerprint-orbit {
        width: 94px;
        height: 94px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(250, 204, 21, 0.35) 0%, rgba(112, 19, 27, 0.12) 56%, transparent 100%);
        display: grid;
        place-items: center;
        animation: fingerPulse 2.8s ease-in-out infinite;
    }

    .fingerprint-core {
        width: 70px;
        height: 70px;
        border-radius: 20px;
        background: linear-gradient(145deg, #70131B, #8f2230);
        color: #ffffff;
        border: 1px solid rgba(250, 204, 21, 0.4);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 14px 28px rgba(112, 19, 27, 0.3);
    }

    .fingerprint-core i {
        font-size: 30px;
    }

    .fingerprint-core svg {
        width: 34px;
        height: 34px;
        stroke: #ffffff;
        stroke-width: 2;
        fill: none;
    }

    .fingerprint-kicker {
        margin: 0 0 8px;
        font-size: 12px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        font-weight: 900;
        color: #8b0000;
    }

    .fingerprint-copy h3 {
        margin: 0;
        font-size: 1.55rem;
        font-weight: 900;
        color: #111827;
    }

    .fingerprint-copy p {
        margin: 8px 0 0;
        color: #475569;
        line-height: 1.6;
    }

    .fingerprint-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 12px;
    }

    .fingerprint-step {
        border: 1px solid rgba(148, 163, 184, 0.26);
        background: linear-gradient(180deg, rgba(255,255,255,0.96), rgba(248,250,252,0.92));
        border-radius: 14px;
        padding: 14px;
        box-shadow: 0 12px 22px rgba(15, 23, 42, 0.05);
    }

    .fingerprint-step span {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 900;
        color: #7c2d12;
    }

    .fingerprint-step strong {
        display: block;
        margin-top: 8px;
        color: #0f172a;
    }

    .fingerprint-step p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
    }

    .fingerprint-status {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 4px;
    }

    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
    }

    .status-chip.pending {
        background: #fff7ed;
        border: 1px solid #fdba74;
        color: #9a3412;
    }

    .status-note {
        color: #64748b;
        font-size: 13px;
    }

    html[data-theme="dark"] .registration-kicker,
    html[data-theme="dark"] .fingerprint-kicker {
        color: #fde68a;
    }

    html[data-theme="dark"] .registration-head h3,
    html[data-theme="dark"] .registration-head p,
    html[data-theme="dark"] .fingerprint-copy h3,
    html[data-theme="dark"] .fingerprint-copy p,
    html[data-theme="dark"] .status-note {
        color: #ffffff;
    }

    html[data-theme="dark"] .registration-mode-btn {
        background: #70131B;
        border-color: rgba(255, 214, 102, 0.5);
        box-shadow: 0 0 0 1px rgba(255, 214, 102, 0.16), 0 24px 38px rgba(95, 0, 18, 0.34), 0 52px 72px -38px rgba(193, 138, 16, 0.56);
    }

    html[data-theme="dark"] .registration-mode-btn::before {
        background: none;
    }

    html[data-theme="dark"] .registration-mode-btn .eyebrow {
        background: rgba(193, 138, 16, 0.22);
        color: #ffd86b;
    }

    html[data-theme="dark"] .registration-mode-btn h3,
    html[data-theme="dark"] .registration-mode-btn p {
        color: #ffffff;
    }

    html[data-theme="dark"] .fingerprint-step {
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.96), rgba(17, 24, 39, 0.92));
        border-color: rgba(148, 163, 184, 0.22);
    }

    html[data-theme="dark"] .fingerprint-step strong,
    html[data-theme="dark"] .fingerprint-step p {
        color: #ffffff;
    }

    html[data-theme="dark"] .status-chip.pending {
        background: rgba(234, 179, 8, 0.16);
        border-color: rgba(250, 204, 21, 0.5);
        color: #fde68a;
    }

    html[data-theme="dark"] .registration-actions .btn {
        background: rgba(255, 255, 255, 0.96);
        color: #70131B;
        border-color: rgba(250, 204, 21, 0.42);
    }

    @media (max-width: 860px) {
        .fingerprint-hero {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .fingerprint-orbit {
            margin: 0 auto;
        }

        .fingerprint-grid {
            grid-template-columns: 1fr;
        }

        .registration-actions {
            justify-content: flex-start;
        }

        .registration-head-main {
            flex-direction: column;
        }
    }

    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    @keyframes umModeFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    @keyframes intakeFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    @keyframes fingerPulse {
        0%, 100% { transform: scale(1); opacity: 0.95; }
        50% { transform: scale(1.05); opacity: 1; }
    }
    @keyframes scan-animation {
        0% { top: 16%; opacity: 0.9; }
        50% { top: 78%; opacity: 1; }
        100% { top: 16%; opacity: 0.9; }
    }
    @keyframes slideInRight { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>
@endpush

@section('content')
@php
    $role = \App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '');
    $basePrefix = $role === \App\Models\User::ROLE_ADMIN ? '/assistant' : '/admin';
    $currentMode = in_array($mode ?? '', ['assisted', 'registration', 'fingerprint'], true) ? $mode : '';
    $idpBaseUrl = rtrim((string) config('services.idp.base_url', ''), '/');
    $idpClientId = trim((string) config('services.idp.client_id', ''));
    $portalRegisterUrl = ($idpBaseUrl !== '' && $idpClientId !== '')
        ? $idpBaseUrl . '/login?' . http_build_query(['client_id' => $idpClientId])
        : route('login');
    $idpRegistrationLink = 'https://identity-provider.isaxbsit2027.com/register?client_id=7112646b-c785-4306-b00f-87d29ad54fb2';
@endphp

@if(session('consultation_done'))
<div id="successToast" class="notification-toast">
    <div class="toast-copy">
        <span class="toast-badge">Done</span>
        <div>
            <strong class="toast-title">Consultation Done!</strong>
            <span class="toast-subtitle">Record saved successfully.</span>
        </div>
    </div>
    <button onclick="location.href='{{ url($basePrefix . '/walkin') }}?mode=scan'" class="btn-toast-action">Open Scan / Bio</button>
</div>
@endif

<div style="max-width: 980px; margin: 20px auto;">
    @if($currentMode === '')
    <div class="card p-4 shadow-sm walkin-strip-card" style="border-radius: 18px; border: none; margin-bottom: 20px;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:18px; flex-wrap:wrap;">
            <div>
                <p class="intake-heading-kicker">Patient Intake</p>
                <h2 class="intake-heading-title">Choose how you want to begin the clinic intake flow</h2>
            </div>
            <a href="{{ url($basePrefix . '/appointments') }}" class="btn" style="background:#f8fafc; border:1px solid #cbd5e1; color:#334155; font-weight:700; border-radius:12px; white-space:nowrap;">
                BACK TO APPOINTMENTS
            </a>
        </div>

        <div class="intake-options-grid">
            <a href="{{ url()->current() }}?mode=registration" class="intake-option-link">
                <div class="intake-option-card intake-option-registration {{ $currentMode === 'registration' ? 'is-active' : '' }}">
                    <span class="intake-option-chip" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </span>
                    <span class="intake-option-icon-wrap" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h6m0 0v6m0-6-7.5 7.5M9 6H6.75A2.25 2.25 0 0 0 4.5 8.25v9A2.25 2.25 0 0 0 6.75 19.5h9A2.25 2.25 0 0 0 18 17.25V15" />
                        </svg>
                    </span>
                    <h3 class="intake-option-title">Registration</h3>
                    <p class="intake-option-copy">Open registration options for IDP account creation or fingerprint enrollment through BioSync.</p>
                </div>
            </a>

            <a href="#" class="intake-option-link" id="openScanLookupModal">
                <div class="intake-option-card intake-option-scan {{ $currentMode === 'scan' ? 'is-active' : '' }}">
                    <span class="intake-option-chip" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 7.5V6A1.5 1.5 0 0 1 6 4.5h1.5m10.5 3V6A1.5 1.5 0 0 0 16.5 4.5H15m3 12V18a1.5 1.5 0 0 1-1.5 1.5H15m-9-3V18A1.5 1.5 0 0 0 7.5 19.5H9" />
                        </svg>
                    </span>
                    <span class="intake-option-icon-wrap" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5A1.5 1.5 0 0 1 4.5 6h15A1.5 1.5 0 0 1 21 7.5v9A1.5 1.5 0 0 1 19.5 18h-15A1.5 1.5 0 0 1 3 16.5v-9Zm3 3h12m-12 3h7.5" />
                        </svg>
                    </span>
                    <h3 class="intake-option-title">Scan / Bio</h3>
                    <p class="intake-option-copy">Use OCR ID scanning, BioSync, or manual student number entry to identify an existing school user and continue directly to consultation.</p>
                </div>
            </a>

            <a href="{{ url()->current() }}?mode=assisted" class="intake-option-link">
                <div class="intake-option-card intake-option-assisted {{ $currentMode === 'assisted' ? 'is-active' : '' }}">
                    <span class="intake-option-chip" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </span>
                    <span class="intake-option-icon-wrap" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.983 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.072M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.552-.645-6.46-1.766l-.084-.049a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </span>
                    <h3 class="intake-option-title">Assisted Intake</h3>
                    <p class="intake-option-copy">Let clinic staff capture the patient record on their behalf when illness or urgency makes self-registration impractical.</p>
                </div>
            </a>

            <a href="#" class="intake-option-link" id="openApplicantScanModal">
                <div class="intake-option-card intake-option-applicant {{ $currentMode === 'applicant' ? 'is-active' : '' }}">
                    <span class="intake-option-chip" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                        </svg>
                    </span>
                    <span class="intake-option-icon-wrap" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3a6 6 0 0 0-6 6v1.5m12-1.5A6 6 0 0 0 12 3m6 6v4.5m-12-4.5v3m3-5.25A3.75 3.75 0 0 1 12 5.25m0 0A3.75 3.75 0 0 1 15 6.75m-3 12.75v-4.5m-3 4.5c0-1.95 1.05-3.75 3-4.5m3 4.5c0-1.95-1.05-3.75-3-4.5" />
                        </svg>
                    </span>
                    <h3 class="intake-option-title">Applicants</h3>
                    <p class="intake-option-copy">Use OCR ID scanning, BioSync, or manual student number entry, then proceed directly to Medical Assessment.</p>
                </div>
            </a>
        </div>
    </div>

    <div class="applicant-modal-backdrop" id="applicantScanModal">
        <div class="applicant-modal-shell">
            <div class="applicant-modal-head">
                <div class="applicant-modal-head-main">
                    <div id="headerIcon" class="applicant-modal-head-badge">AP</div>
                    <div class="applicant-modal-head-copy">
                        <h3 id="headerTitle">BioSync Ready</h3>
                        <span id="scanMethodBadge" class="scan-method-badge">BioSync Active</span>
                        <p id="headerSubtitle">Start with BioSync fingerprint identification, or switch to OCR ID scanning when you need to capture the applicant card instead.</p>
                    </div>
                </div>
                <div class="applicant-modal-head-actions">
                    <button type="button" id="btnSwitchScanMode" class="btn-scan-switch">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                        </svg>
                        <span>Switch to OCR Scan</span>
                    </button>
                </div>
                <button type="button" class="applicant-modal-close" id="closeApplicantScanModal" aria-label="Close applicant scan modal">
                    <x-outline-icon name="x-mark" />
                </button>
            </div>
            <div class="applicant-modal-body">
                <div class="applicant-modal-grid">
                    <div class="applicant-modal-panel">
                        <div id="scanForm">
                            <div id="scanStage" class="scan-stage">
                                <div id="scanner-container-scan" class="scan-surface" style="position: relative;">
                                    <p id="scanInlineNote" class="scan-inline-note">OCR mode is active. Align the physical ID inside the frame and continue once student number and name are matched for Medical Assessment.</p>
                                    <div id="barcodeScanPanel">
                                        <div id="scan-loading">
                                            <div class="spinner"></div>
                                            <p style="margin-top:10px; color:#8B0000; font-weight:bold; font-size: 12px;">Verifying...</p>
                                        </div>
                                        <div id="readerScan" class="scanner-box">
                                            <div class="scan-line-overlay"></div>
                                            <div class="ocr-guide"></div>
                                            <div class="ocr-guide-label">Align Student Number and Name</div>
                                        </div>

                                        <div class="ocr-actions">
                                            <button type="button" id="btnRunAiOcr" class="btn-ocr btn-ocr-primary" style="background:linear-gradient(135deg, #1d4ed8, #2563eb 55%, #3b82f6); box-shadow:0 12px 24px rgba(37,99,235,0.22);">AI Read Student No.</button>
                                            <button type="button" id="btnRetryOcr" class="btn-ocr btn-ocr-secondary">Clear OCR Result</button>
                                        </div>
                                    </div>

                                    <div id="bioSyncPendingPanel" style="display:none; background:linear-gradient(180deg, #f8fafc, #eef2ff); border:1px dashed #cbd5e1; border-radius:12px; padding:30px 22px;">
                                        <div style="display:flex; align-items:center; gap:14px; margin-bottom:18px;">
                                            <div style="width:60px; height:60px; border-radius:18px; background:#dbeafe; color:#1d4ed8; display:flex; align-items:center; justify-content:center; font-weight:900; box-shadow:0 10px 20px rgba(59,130,246,0.12); flex-shrink:0;">BIO</div>
                                            <div style="text-align:left;">
                                                <h4 style="margin:0; font-size:18px; color:#0f172a; font-weight:800;">BioSync Pending</h4>
                                            </div>
                                        </div>
                                        <div style="display:grid; gap:10px;">
                                            <div style="display:grid; grid-template-columns:110px minmax(0, 1fr); gap:12px; align-items:center;">
                                                <span style="font-size:12px; font-weight:800; letter-spacing:0.04em; text-transform:uppercase; color:#475569;">Full Name</span>
                                                <input type="text" class="form-control" value="Waiting for BioSync" readonly style="margin-bottom:0; background:#ffffff;">
                                            </div>
                                            <div style="display:grid; grid-template-columns:110px minmax(0, 1fr); gap:12px; align-items:center;">
                                                <span style="font-size:12px; font-weight:800; letter-spacing:0.04em; text-transform:uppercase; color:#475569;">ID Number</span>
                                                <input type="text" class="form-control" value="Waiting for BioSync" readonly style="margin-bottom:0; background:#ffffff;">
                                            </div>
                                            <div style="display:grid; grid-template-columns:110px minmax(0, 1fr); gap:12px; align-items:center;">
                                                <span style="font-size:12px; font-weight:800; letter-spacing:0.04em; text-transform:uppercase; color:#475569;">Role</span>
                                                <input type="text" class="form-control" value="Waiting for BioSync" readonly style="margin-bottom:0; background:#ffffff;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="applicant-modal-panel">
                        <div id="applicantOcrReviewPanel">
                            <div id="ocrResultPanel" class="ocr-result-panel" style="background:linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); border:1px solid #dbe3ef; box-shadow:0 16px 34px rgba(15, 23, 42, 0.08), inset 0 1px 0 rgba(255,255,255,0.92);">
                                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:14px; margin-bottom:16px; padding-bottom:12px; border-bottom:1px solid #e2e8f0;">
                                    <div style="display:flex; align-items:flex-start; gap:12px;">
                                        <span style="width:42px; height:42px; border-radius:14px; display:inline-flex; align-items:center; justify-content:center; background:linear-gradient(135deg, #7f1d1d, #991b1b 58%, #b91c1c); color:#ffffff; box-shadow:0 12px 24px rgba(127,29,29,0.18); flex-shrink:0;">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" style="width:20px; height:20px; stroke:currentColor; stroke-width:1.9;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5h16.5m-16.5 4.5h10.5m-10.5 4.5h7.5M17.25 5.25h2.25A1.5 1.5 0 0 1 21 6.75v10.5a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 17.25V6.75a1.5 1.5 0 0 1 1.5-1.5h2.25" />
                                            </svg>
                                        </span>
                                        <div>
                                            <p style="margin:0; font-size:11px; font-weight:900; letter-spacing:0.08em; text-transform:uppercase; color:#8b0000;">Detected Data</p>
                                            <p style="margin:4px 0 0; font-size:12px; color:#64748b; line-height:1.5;">Captured from OCR scan and arranged for final review.</p>
                                        </div>
                                    </div>
                                    <span style="display:inline-flex; align-items:center; padding:7px 10px; border-radius:999px; background:#ecfdf5; border:1px solid #a7f3d0; color:#047857; font-size:11px; font-weight:800; letter-spacing:0.04em; text-transform:uppercase; white-space:nowrap;">OCR Result</span>
                                </div>
                                <div style="display:grid; gap:12px;">
                                    <div style="display:grid; grid-template-columns: 120px minmax(0, 1fr); gap:12px; align-items:center; padding:12px 14px; border-radius:14px; background:linear-gradient(180deg, #fdfefe, #f8fafc); border:1px solid #e2e8f0;">
                                        <p class="ocr-result-label" style="margin:0; color:#334155;">Full Name</p>
                                        <input type="text" id="ocr_student_name" class="form-control" readonly style="margin-bottom:0; background:#ffffff; color:#0f172a; border:1px solid #cbd5e1; box-shadow:inset 0 1px 0 rgba(255,255,255,0.95); font-weight:700; cursor:default;">
                                    </div>
                                    <div style="display:grid; grid-template-columns: 120px minmax(0, 1fr); gap:12px; align-items:center; padding:12px 14px; border-radius:14px; background:linear-gradient(180deg, #fdfefe, #f8fafc); border:1px solid #e2e8f0;">
                                        <p class="ocr-result-label" style="margin:0; color:#334155;">ID Number</p>
                                        <input type="text" id="ocr_student_number" class="form-control" readonly style="margin-bottom:0; background:#ffffff; color:#0f172a; border:1px solid #cbd5e1; box-shadow:inset 0 1px 0 rgba(255,255,255,0.95); font-weight:700; cursor:default;">
                                    </div>
                                </div>

                                <div id="ocrStatus" class="ocr-status info" style="display:block;">AI verification could not finish right now.</div>
                                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                    <div id="ocrConfidenceText" class="ocr-meta">Student no. confidence: 10%</div>
                                    <div id="ocrLockBadge" class="ocr-lock-badge" style="display:none;">Locked on ID</div>
                                </div>

                                <div class="ocr-actions" style="margin-top:14px;">
                                    <button type="button" id="btnConfirmOcr" class="btn-ocr btn-ocr-secondary" disabled>Confirm & Continue</button>
                                </div>
                            </div>

                            <div class="manual-input-stack">
                                <p class="manual-toggle-label">Type Student Number Manually</p>
                                <form id="walkinFormManual">
                                    <input type="text" id="student_id_manual" placeholder="Enter student number" class="form-control" style="margin-bottom:10px;" required>
                                    <button type="submit" id="manualFindBtn" class="manual-find-btn" style="display:none;">Find</button>
                                </form>
                            </div>
                        </div>

                        <div id="applicantBioSyncInfoPanel" class="applicant-biosync-visual" style="display:none;">
                            <div class="applicant-biosync-ring">
                                <svg class="applicant-biosync-fingerprint" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0 1 19.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 0 0 4.5 10.5a7.464 7.464 0 0 1-1.15 3.993m1.989 3.559A11.209 11.209 0 0 0 8.25 10.5a3.75 3.75 0 1 1 7.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 0 1-3.6 9.75m6.633-4.596a18.666 18.666 0 0 1-2.485 5.33" />
                                </svg>
                                <div class="applicant-biosync-scanline"></div>
                            </div>
                            <div class="applicant-biosync-copy">
                                <h4>BioSync Pending</h4>
                                <p>The fingerprint preview is ready here. Identity details will appear in the left-side BioSync section once the integration is connected.</p>
                            </div>
                        </div>

                        <canvas id="ocrCanvas" style="display:none;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

@if($currentMode === 'registration')
<div class="card p-4 shadow-sm walkin-strip-card registration-hub" style="border-radius: 16px; border: none; margin: 20px auto;">
    <div class="registration-head">
        <div class="registration-head-main">
            <div class="registration-head-copy">
                <p class="registration-kicker">Patient Intake</p>
                <h3>Registration Options</h3>
                <p>Choose an onboarding path for applicant registration and identity setup.</p>
            </div>
            <div class="registration-actions">
                <a href="{{ url($basePrefix . '/walkin') }}" class="btn">Back to Intake Options</a>
                <a href="{{ url($basePrefix . '/appointments') }}" class="btn">Back to Appointments</a>
            </div>
        </div>
    </div>

    <div class="um-mode-picker registration-mode-picker">
        <a href="{{ $idpRegistrationLink }}" target="_blank" rel="noopener noreferrer" class="um-mode-btn registration-mode-btn">
            <span class="um-mode-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h6m0 0v6m0-6-7.5 7.5M9 6H6.75A2.25 2.25 0 0 0 4.5 8.25v9A2.25 2.25 0 0 0 6.75 19.5h9A2.25 2.25 0 0 0 18 17.25V15" />
                </svg>
            </span>
            <h3>Register via IDP</h3>
            <p>Open the official Identity Provider registration form and complete applicant account enrollment.</p>
        </a>

        <a href="{{ url()->current() }}?mode=fingerprint" class="um-mode-btn registration-mode-btn">
            <span class="um-mode-icon" aria-hidden="true">
                <img width="100" height="100" src="https://img.icons8.com/bubbles/100/fingerprint.png" alt="fingerprint" />
            </span>
            <h3>Register Fingerprint</h3>
            <p>Proceed to the BioSync enrollment interface to capture and bind fingerprint data for clinic use.</p>
        </a>
    </div>
</div>
@endif

@if($currentMode === 'fingerprint')
<div class="card p-4 shadow-sm walkin-strip-card fingerprint-hub" style="border-radius: 16px; border: none; margin: 20px auto;">
    <div class="fingerprint-hero">
        <div class="fingerprint-orbit">
            <span class="fingerprint-core">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0 1 19.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 0 0 4.5 10.5a7.464 7.464 0 0 1-1.15 3.993m1.989 3.559A11.209 11.209 0 0 0 8.25 10.5a3.75 3.75 0 1 1 7.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 0 1-3.6 9.75m6.633-4.596a18.666 18.666 0 0 1-2.485 5.33" />
                </svg>
            </span>
        </div>
        <div class="fingerprint-copy">
            <p class="fingerprint-kicker">BioSync Enrollment</p>
            <h3>Register Fingerprint</h3>
            <p>Capture applicant biometrics for identity matching and secure clinic verification. This interface is ready for the BioSync device integration flow.</p>
        </div>
    </div>

    <div class="fingerprint-grid">
        <div class="fingerprint-step">
            <span><i class="fa-solid fa-plug-circle-bolt"></i> Step 1</span>
            <strong>Connect Reader</strong>
            <p>Ensure the BioSync fingerprint scanner is connected and recognized by this workstation.</p>
        </div>
        <div class="fingerprint-step">
            <span><i class="fa-solid fa-hand-pointer"></i> Step 2</span>
            <strong>Capture Fingerprint</strong>
            <p>Ask the applicant to press their finger until the enrollment quality meets the required threshold.</p>
        </div>
        <div class="fingerprint-step">
            <span><i class="fa-solid fa-shield-heart"></i> Step 3</span>
            <strong>Save Template</strong>
            <p>Bind the biometric template to the applicant profile and confirm enrollment success.</p>
        </div>
    </div>

    <div class="fingerprint-status">
        <span class="status-chip pending"><i class="fa-solid fa-circle-notch fa-spin"></i> Waiting for scanner handshake</span>
        <span class="status-note">Device bridge can be connected here once BioSync runtime service is available.</span>
    </div>

    <div class="registration-actions">
        <a href="{{ url()->current() }}?mode=registration" class="btn">Back</a>
        <button type="button" class="btn" disabled>Start Enrollment</button>
    </div>
</div>
@endif

@if(in_array($currentMode, ['scan', 'assisted', 'applicant'], true))
<div class="card p-4 shadow-sm walkin-strip-card" style="border-radius: 15px; border: none; max-width: {{ $currentMode === 'assisted' ? '1180px' : '550px' }}; margin: 20px auto;">
    
    @if($currentMode !== 'assisted')
    <div id="dynamicHeader" class="mode-header bg-scan">
        <div id="headerIcon" class="mode-header-badge">{{ $currentMode === 'applicant' ? 'AP' : 'SB' }}</div>
        <div class="mode-header-copy">
            <h3 id="headerTitle" style="margin: 0; font-weight: 800; text-transform: uppercase; font-size: 1rem; letter-spacing: 1px;">
                {{ $currentMode === 'applicant' ? 'Applicant Scan Ready' : 'OCR Ready' }}
            </h3>
            <p id="headerSubtitle">
                {{ $currentMode === 'applicant' ? 'Choose OCR scanning or BioSync to identify the applicant and continue to Medical Assessment.' : 'Choose barcode scanning or BioSync mode to identify the patient.' }}
            </p>
        </div>
    </div>
    @endif

    <div id="scanForm" style="{{ $currentMode === 'assisted' ? 'display:none;' : '' }}">
        <div id="scanStage" class="scan-stage">
            <div class="scan-method-bar">
                <div>
                <p id="scanMethodTitle" class="scan-method-title">Scan Barcode</p>
                <p id="scanMethodNote" class="scan-method-note">Use the camera to capture the patient barcode, or switch to BioSync mode for identity matching.</p>
                <span id="scanMethodBadge" class="scan-method-badge">Barcode Active</span>
                </div>
                <button type="button" id="btnSwitchScanMode" class="btn-scan-switch">Switch to BioSync</button>
            </div>

            <div id="scanner-container-scan" class="scan-surface" style="position: relative;">
                <p id="scanInlineNote" class="scan-inline-note">Barcode mode is active. Point the camera at the patient barcode, or switch to BioSync for the upcoming biometric flow.</p>
                <div id="barcodeScanPanel">
                    <div id="scan-loading">
                        <div class="spinner"></div>
                        <p style="margin-top:10px; color:#8B0000; font-weight:bold; font-size: 12px;">Verifying...</p>
                    </div>
                    <div id="readerScan" class="scanner-box">
                        <div class="scan-line-overlay"></div>
                        <div class="ocr-guide"></div>
                        <div class="ocr-guide-label">Align Student Number and Name</div>
                    </div>

                    <div class="ocr-actions">
                        <button type="button" id="btnRunAiOcr" class="btn-ocr btn-ocr-primary" style="background:linear-gradient(135deg, #1d4ed8, #2563eb 55%, #3b82f6); box-shadow:0 12px 24px rgba(37,99,235,0.22);">AI Read Student No.</button>
                        <button type="button" id="btnRetryOcr" class="btn-ocr btn-ocr-secondary">Clear OCR Result</button>
                    </div>

                    <div id="ocrResultPanel" class="ocr-result-panel" style="display:none;">
                        <div style="display:grid; gap:12px;">
                            <div style="display:grid; grid-template-columns: 120px minmax(0, 1fr); gap:12px; align-items:center;">
                                <p class="ocr-result-label" style="margin:0;">Full Name</p>
                                <input type="text" id="ocr_student_name" class="form-control" placeholder="Enter full name" style="margin-bottom:0;">
                            </div>
                            <div style="display:grid; grid-template-columns: 120px minmax(0, 1fr); gap:12px; align-items:center;">
                                <p class="ocr-result-label" style="margin:0;">ID Number</p>
                                <input type="text" id="ocr_student_number" class="form-control" placeholder="Enter ID number" style="margin-bottom:0;">
                            </div>
                        </div>

                        <div id="ocrStatus" class="ocr-status info" style="display:block;">Live OCR is ready. Hold the ID steady inside the frame so we can detect the student number and fill the saved name from records.</div>
                        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                            <div id="ocrConfidenceText" class="ocr-meta">OCR confidence will appear here after analysis.</div>
                            <div id="ocrLockBadge" class="ocr-lock-badge" style="display:none;">Locked on ID</div>
                        </div>

                        <div class="ocr-actions" style="margin-top:14px;">
                            <button type="button" id="btnConfirmOcr" class="btn-ocr btn-ocr-secondary" disabled>Confirm & Continue</button>
                        </div>
                    </div>

                    <canvas id="ocrCanvas" style="display:none;"></canvas>
                </div>

                <div id="bioSyncPendingPanel" style="display:none; background:linear-gradient(180deg, #f8fafc, #eef2ff); border:1px dashed #cbd5e1; border-radius:12px; padding:30px 22px; text-align:center;">
                    <div style="width:60px; height:60px; margin:0 auto 14px; border-radius:18px; background:#dbeafe; color:#1d4ed8; display:flex; align-items:center; justify-content:center; font-weight:900; box-shadow:0 10px 20px rgba(59,130,246,0.12);">BIO</div>
                    <h4 style="margin:0 0 8px; font-size:18px; color:#0f172a; font-weight:800;">BioSync Pending</h4>
                    <p style="margin:0; color:#64748b; line-height:1.6; font-size:13px;">This mode is reserved for the upcoming BioSync integration. For now, please switch back to barcode scanning or use assisted intake.</p>
                </div>
            </div>
        
            <div class="text-center mt-3">
                <button type="button" id="btnShowManual" style="background:none; border:none; color:#8B0000; text-decoration:underline; cursor:pointer; font-weight:600; font-size: 0.85rem;">
                    Type Student Number Manually
                </button>
            </div>

            <div id="manualInputArea" style="display:none;" class="mt-3">
                <form id="walkinFormManual" class="d-flex gap-2">
                    <input type="text" id="student_id_manual" placeholder="Enter student number" class="form-control" style="margin-bottom:0;" required>
                    <button type="submit" class="manual-find-btn">Find</button>
                </form>
            </div>

            <div class="mt-4 pt-3" style="border-top: 1px dashed #cbd5e1;">
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="{{ url($basePrefix . '/walkin') }}" class="btn w-100 py-2" style="flex:1 1 180px; background: #ffffff; border: 1px solid #cbd5e1; color: #475569; font-weight: 700; font-size: 0.8rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none;">
                     BACK TO INTAKE OPTIONS
                </a>
                <a href="{{ url($basePrefix . '/appointments') }}" class="btn w-100 py-2" style="flex:1 1 180px; background: #f8fafc; border: 1px solid #cbd5e1; color: #475569; font-weight: 600; font-size: 0.8rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none;">
                 BACK TO APPOINTMENTS LIST
                </a>
                </div>
            </div>
        </div>
    </div>

    <div id="registerForm" style="{{ $currentMode === 'assisted' ? '' : 'display:none;' }}">
        <form id="formRegisterStudent">
            @csrf

            <div class="assisted-intake-shell">
                <section class="assisted-panel">
                    <div class="assisted-panel-body">
                        <div class="assisted-hero">
                            <div class="assisted-hero-badge" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75V4.875c0-1.036-.84-1.875-1.875-1.875h-3.75c-1.035 0-1.875.84-1.875 1.875V6.75m7.5 0h1.125c1.035 0 1.875.84 1.875 1.875v9.75c0 1.035-.84 1.875-1.875 1.875H7.125c-1.035 0-1.875-.84-1.875-1.875v-9.75c0-1.035.84-1.875 1.875-1.875H8.25m7.5 0h-7.5m3.75 3v3.75m-1.875-1.875h3.75" />
                                </svg>
                            </div>
                            <div class="assisted-hero-copy">
                                <h3>Clinic Concierge Intake</h3>
                                <div class="assisted-status-row" style="margin-bottom:10px;">
                                    <span class="assisted-status-chip pending">Temporary Record</span>
                                    <span class="assisted-status-chip ready">Ready for Consultation</span>
                                </div>
                                <p>Capture the patient identity details here, then continue to the consultation form for clinical notes and assessment.</p>
                            </div>
                        </div>

            <div class="mb-3 assisted-highlight-card">
                <label style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase;">Student Number / Reference ID</label>
                <input type="text" id="reg_student_id" class="form-control mb-0" placeholder="Enter student number or reference ID" required>
                <input type="hidden" id="reg_barcode">
            </div>
            
            <div class="mb-2">
                <label style="font-size: 11px; font-weight: 700; color: #475569;">PATIENT ROLE</label>
                <div class="assisted-role-wrap" id="assistedRoleWrap">
                    <select id="reg_user_type" class="form-control assisted-role-select" required>
                        <option value="" disabled selected>-- Choose Patient Role --</option>
                        <option value="Guest">Guest</option>
                        <option value="Dependent">Dependent</option>
                        <option value="Student">Student</option>
                        <option value="Faculty">Faculty</option>
                        <option value="Admin">Admin</option>
                    </select>
                    <button type="button" class="assisted-role-display" id="assistedRoleDisplay" aria-haspopup="listbox" aria-expanded="false">
                        Select patient role
                    </button>
                    <div class="assisted-role-menu" id="assistedRoleMenu" role="listbox" aria-label="Patient Role options">
                        <button type="button" class="assisted-role-option" data-role-value="Guest">Guest</button>
                        <button type="button" class="assisted-role-option" data-role-value="Dependent">Dependent</button>
                        <button type="button" class="assisted-role-option" data-role-value="Student">Student</button>
                        <button type="button" class="assisted-role-option" data-role-value="Faculty">Faculty</button>
                        <button type="button" class="assisted-role-option" data-role-value="Admin">Admin</button>
                    </div>
                </div>
            </div>

            <div class="assisted-section-divider" aria-hidden="true">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.125A7.125 7.125 0 1 0 8.25 7.5a5.625 5.625 0 1 1 6.75 11.625Zm0 0h3.375m-3.375 0v-3.375" />
                    </svg>
                    Identity Details
                </span>
            </div>
            
            <div class="assisted-pair-row">
                <div class="assisted-field-card">
                    <label class="assisted-field-label" for="reg_first_name">First Name</label>
                    <input type="text" id="reg_first_name" placeholder="Enter first name" class="form-control" required>
                </div>
                <div class="assisted-field-card">
                    <label class="assisted-field-label" for="reg_last_name">Last Name</label>
                    <input type="text" id="reg_last_name" placeholder="Enter last name" class="form-control" required>
                </div>
            </div>

            <div class="assisted-pair-row">
                <div class="assisted-field-card">
                    <label class="assisted-field-label" for="reg_dob">Birthday</label>
                    <input type="date" id="reg_dob" class="form-control" aria-label="Birthday">
                </div>
                <div class="assisted-field-card">
                    <label class="assisted-field-label" for="reg_gender">Sex / Gender</label>
                    <div class="assisted-gender-wrap" id="assistedGenderWrap">
                    <select id="reg_gender" class="form-control assisted-gender-select">
                        <option value="">Sex / Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    <button type="button" class="assisted-gender-display" id="assistedGenderDisplay" aria-haspopup="listbox" aria-expanded="false">
                        Select sex / gender
                    </button>
                    <div class="assisted-gender-menu" id="assistedGenderMenu" role="listbox" aria-label="Sex / Gender options">
                        <button type="button" class="assisted-gender-option" data-gender-value="Male">Male</button>
                        <button type="button" class="assisted-gender-option" data-gender-value="Female">Female</button>
                        <button type="button" class="assisted-gender-option" data-gender-value="Other">Other</button>
                    </div>
                </div>
                </div>
            </div>

            <div class="assisted-section-divider" aria-hidden="true">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 8.25v7.5a2.25 2.25 0 0 1-2.25 2.25H4.5a2.25 2.25 0 0 1-2.25-2.25v-7.5m19.5 0-8.69 5.216a2.25 2.25 0 0 1-2.122 0L2.25 8.25m19.5 0A2.25 2.25 0 0 0 19.5 6H4.5a2.25 2.25 0 0 0-2.25 2.25" />
                    </svg>
                    Contact Details
                </span>
            </div>

            <div class="assisted-field-card">
                <label class="assisted-field-label" for="reg_contact_no">Contact Number</label>
                <input type="text" id="reg_contact_no" placeholder="Enter contact number" class="form-control">
            </div>

            <div class="assisted-field-card">
                <label class="assisted-field-label" for="reg_email">Email Address</label>
                <input type="email" id="reg_email" placeholder="Enter email address" class="form-control" required>
            </div>

            <div style="background:#fff7ed; border:1px dashed #fdba74; border-radius:10px; padding:12px 14px; margin-bottom:10px;">
                <strong style="display:block; font-size:12px; color:#9a3412; margin-bottom:4px;">No password needed for assisted intake</strong>
                <p style="margin:0; font-size:12px; color:#7c2d12; line-height:1.5;">A valid email address is required for assisted intake before proceeding to consultation.</p>
            </div>
            
                    </div>
                </section>

            <div id="notification" style="margin: 10px 0;"></div>
            
            <button type="button" id="confirmBtn" class="btn btn-success w-100 fw-bold py-3 mt-2" style="border-radius: 8px; background: #15803d; border: none; color: white;">
                SAVE ASSISTED INTAKE
            </button>
            
            <div class="text-center mt-3">
                <a href="{{ url($basePrefix . '/walkin') }}" style="font-size: 12px; color: #64748b; text-decoration: none;">Back to intake options</a>
            </div>
            </div>
        </form>
    </div>
</div>
@endif
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<script type="text/javascript">
    let mainScanner;
    let ocrWorkerPromise = null;
    let currentVideoTrack = null;
    let liveOcrInterval = null;
    let ocrInFlight = false;
    let lastOcrSignature = '';
    let ocrLockCount = 0;
    let lastStudentNumberCandidate = '';
    let studentNumberStableCount = 0;
    let lastStudentNameCandidate = '';
    let studentNameStableCount = 0;
    let manualStudentNumberEdited = false;
    let manualStudentNameEdited = false;
    let lastPreviewedStudentNumber = '';
    let lastPreviewedStudentName = '';
    let ocrNameLocked = false;
    let aiAssistCooldown = false;
    let autoProceedInFlight = false;
    let lastAutoProceedKey = '';
    const initialMode = @json($currentMode);
    let intakeTarget = 'consultation';
    let scanMethod = 'biosync';
    const liveOcrIntervalMs = 900;
    const ocrCanvasScale = 1;
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
    const ocrSkipWords = ['POLYTECHNIC', 'UNIVERSITY', 'OF', 'THE', 'PHILIPPINES', 'TAGUIG', 'CAMPUS', 'STUDENT', 'IDENTIFICATION', 'CARD', 'ID', 'NO', 'NUMBER'];

    if (supportedFormats.length) {
        scannerConfig.formatsToSupport = supportedFormats;
    }

    function createScanner(targetId) {
        if (supportedFormats.length) {
            return new Html5Qrcode(targetId, { formatsToSupport: supportedFormats });
        }

        return new Html5Qrcode(targetId);
    }

    $(document).ready(function() {
        const applicantScanModal = document.getElementById('applicantScanModal');
        const openScanLookupModalBtn = document.getElementById('openScanLookupModal');
        const openApplicantScanModalBtn = document.getElementById('openApplicantScanModal');
        const closeApplicantScanModalBtn = document.getElementById('closeApplicantScanModal');
        const assistedRoleSelect = document.getElementById('reg_user_type');
        const assistedRoleDisplay = document.getElementById('assistedRoleDisplay');
        const assistedRoleMenu = document.getElementById('assistedRoleMenu');
        const assistedRoleOptions = Array.from(document.querySelectorAll('.assisted-role-option'));
        const assistedRoleWrap = assistedRoleDisplay ? assistedRoleDisplay.closest('.assisted-role-wrap') : null;
        const assistedGenderSelect = document.getElementById('reg_gender');
        const assistedGenderDisplay = document.getElementById('assistedGenderDisplay');
        const assistedGenderMenu = document.getElementById('assistedGenderMenu');
        const assistedGenderOptions = Array.from(document.querySelectorAll('.assisted-gender-option'));
        const assistedGenderWrap = assistedGenderDisplay ? assistedGenderDisplay.closest('.assisted-gender-wrap') : null;

        updateScanModeUI();

        function getDestinationLabel() {
            return intakeTarget === 'assessment' ? 'medical assessment form' : 'consultation form';
        }

        function syncAssistedRoleDisplay() {
            if (!assistedRoleSelect || !assistedRoleDisplay) return;

            const selectedValue = assistedRoleSelect.value || '';
            const selectedText = selectedValue
                ? (assistedRoleSelect.options[assistedRoleSelect.selectedIndex]?.text || selectedValue)
                : 'Select patient role';

            assistedRoleDisplay.textContent = selectedText;

            assistedRoleOptions.forEach(function (option) {
                option.classList.toggle('is-selected', option.dataset.roleValue === selectedValue);
            });
        }

        function setAssistedRoleOpenState(isOpen) {
            if (!assistedRoleWrap || !assistedRoleDisplay) return;

            assistedRoleWrap.classList.toggle('is-open', isOpen);
            assistedRoleDisplay.classList.toggle('is-open', isOpen);
            assistedRoleDisplay.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        }

        function syncAssistedGenderDisplay() {
            if (!assistedGenderSelect || !assistedGenderDisplay) return;

            const selectedValue = assistedGenderSelect.value || '';
            const selectedText = selectedValue
                ? (assistedGenderSelect.options[assistedGenderSelect.selectedIndex]?.text || selectedValue)
                : 'Select sex / gender';

            assistedGenderDisplay.textContent = selectedText;

            assistedGenderOptions.forEach(function (option) {
                option.classList.toggle('is-selected', option.dataset.genderValue === selectedValue);
            });
        }

        function setAssistedGenderOpenState(isOpen) {
            if (!assistedGenderWrap || !assistedGenderDisplay) return;

            assistedGenderWrap.classList.toggle('is-open', isOpen);
            assistedGenderDisplay.classList.toggle('is-open', isOpen);
            assistedGenderDisplay.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        }

        function getScannerVideoElement() {
            return document.querySelector('#readerScan video');
        }

        function isOcrMode() {
            return scanMethod !== 'biosync';
        }

        function attachVideoTrack() {
            const video = getScannerVideoElement();
            if (!video || !video.srcObject) {
                return;
            }

            const tracks = video.srcObject.getVideoTracks ? video.srcObject.getVideoTracks() : [];
            currentVideoTrack = tracks.length ? tracks[0] : null;
        }

        function stopLiveOcr() {
            if (liveOcrInterval) {
                window.clearInterval(liveOcrInterval);
                liveOcrInterval = null;
            }
        }

        function startLiveOcr() {
            if (!isOcrMode() || liveOcrInterval) {
                return;
            }

            liveOcrInterval = window.setInterval(function () {
                const video = getScannerVideoElement();

                if (!isOcrMode() || ocrInFlight || !video || video.readyState < 2) {
                    return;
                }

                captureAndAnalyzeId(true);
            }, liveOcrIntervalMs);
        }

        function startMainScanner() {
            if (!mainScanner) {
                mainScanner = createScanner("readerScan");
                mainScanner.start(
                    { facingMode: "environment" },
                    scannerConfig,
                    (decodedText) => {
                        if (isOcrMode()) {
                            return;
                        }

                        verifyUser(decodedText);
                    }
                ).then(() => {
                    attachVideoTrack();
                    startLiveOcr();
                }).catch(err => console.warn(err));
            }
        }

        function stopMainScanner() {
            stopLiveOcr();
            if (mainScanner) {
                mainScanner.stop().catch(() => {}).finally(() => {
                    mainScanner = null;
                    currentVideoTrack = null;
                });
            } else {
                currentVideoTrack = null;
            }
        }

        function buildStatus(message, type = 'info', extra = '') {
            const $status = $('#ocrStatus');
            $status.removeClass('info success error').addClass(type).html(`${message}${extra ? `<div class="ocr-meta">${extra}</div>` : ''}`);
        }

        function normalizeSpaces(value) {
            return (value || '').replace(/\s+/g, ' ').trim();
        }

        function cleanOcrLine(value) {
            return normalizeSpaces((value || '').replace(/[^\w\s-]/g, ' '));
        }

        function extractStudentNumber(text, focusedText = '') {
            const normalized = `${focusedText} ${text}`.replace(/\s+/g, ' ').toUpperCase();
            const patterns = [
                /\b20\d{2}\s*-\s*\d{5}\s*-\s*[A-Z]{2}\s*-\s*\d\b/,
                /\b20\d{2}-\d{3}-\d{3}\b/,
                /\b\d{4}-\d{3}-\d{3}\b/,
                /\b\d{4}\s*-\s*\d{3}\s*-\s*\d{3}\b/,
                /\b20\d{2}\d{6}\b/,
                /\b20\d{2}\d{5}[A-Z]{2}\d\b/
            ];

            for (const pattern of patterns) {
                const match = normalized.match(pattern);
                if (match) {
                    const compact = match[0].replace(/\s+/g, '').toUpperCase();

                    if (/^20\d{2}\d{5}[A-Z]{2}\d$/.test(compact)) {
                        return `${compact.slice(0, 4)}-${compact.slice(4, 9)}-${compact.slice(9, 11)}-${compact.slice(11, 12)}`;
                    }

                    if (/^20\d{10}$/.test(compact)) {
                        return `${compact.slice(0, 4)}-${compact.slice(4, 7)}-${compact.slice(7, 10)}`;
                    }

                    return compact;
                }
            }

            return '';
        }

        function isLikelyNameLine(line, allowSingleToken = false) {
            const cleaned = line.replace(/[^A-Za-z\s]/g, '').trim();
            const upper = cleaned.toUpperCase();
            const tokens = upper.split(' ').filter(Boolean);

            if (!tokens.length) {
                return false;
            }

            if (!allowSingleToken && tokens.length < 2) {
                return false;
            }

            if (tokens.length > 5) {
                return false;
            }

            if (tokens.some(token => ocrSkipWords.includes(token))) {
                return false;
            }

            if (tokens.some(token => token.length < 2 && !['R', 'J'].includes(token))) {
                return false;
            }

            if (!tokens.every(token => /^[A-Z]+$/.test(token))) {
                return false;
            }

            return true;
        }

        function extractStudentName(text, focusedText = '') {
            const lines = `${focusedText}\n${text}`
                .split(/\r?\n/)
                .map(line => cleanOcrLine(line))
                .filter(Boolean);
            const uniqueLines = [];

            lines.forEach((line) => {
                const upper = line.toUpperCase();
                if (!uniqueLines.includes(upper) && !/\d/.test(upper)) {
                    uniqueLines.push(upper);
                }
            });

            for (let i = 0; i < uniqueLines.length - 1; i += 1) {
                const topLine = uniqueLines[i];
                const nextLine = uniqueLines[i + 1];

                if (isLikelyNameLine(topLine, false) && isLikelyNameLine(nextLine, true)) {
                    return `${topLine} ${nextLine}`.replace(/\s+/g, ' ').trim();
                }
            }

            const singleLineCandidate = uniqueLines.find(line => isLikelyNameLine(line, false));
            if (singleLineCandidate) {
                return singleLineCandidate;
            }

            const twoSingleTokenLines = uniqueLines.filter(line => isLikelyNameLine(line, true)).slice(0, 2);
            if (twoSingleTokenLines.length === 2) {
                return twoSingleTokenLines.join(' ');
            }

            return '';
        }

        function updateDetectedFields(studentNumber, studentName, confidence, isLocked = false, allowNameAutofill = false) {
            if (studentNumber && (!manualStudentNumberEdited || $('#ocr_student_number').val().trim() === '')) {
                $('#ocr_student_number').val(studentNumber);
            }

            if (allowNameAutofill && studentName && !ocrNameLocked && (!manualStudentNameEdited || $('#ocr_student_name').val().trim() === '')) {
                $('#ocr_student_name').val(studentName);
                ocrNameLocked = true;
            }

            $('#ocrConfidenceText').text(confidence ? `Student no. confidence: ${confidence}%` : 'Student number confidence will appear here after analysis.');
            $('#ocrResultPanel').show();
            $('#btnConfirmOcr').prop('disabled', !($('#ocr_student_number').val().trim() && $('#ocr_student_name').val().trim()));
            $('#ocrLockBadge').toggle(isLocked);
        }

        function requestMatchedNamePreview(studentNumber, extractedName = '', onApplied = null) {
            const normalizedStudentNumber = (studentNumber || '').trim();
            if (!normalizedStudentNumber || lastPreviewedStudentNumber === normalizedStudentNumber) {
                return;
            }

            lastPreviewedStudentNumber = normalizedStudentNumber;

            $.get("{{ url($basePrefix . '/walkin/get-student') }}", {
                student_id: normalizedStudentNumber,
                student_name: extractedName,
                preview_only: 1,
                intake_target: intakeTarget
            }, function(res) {
                if (res.status !== 'preview' || !res.student_name) {
                    return;
                }

                $('#ocr_student_name').val(res.student_name);
                $('#btnConfirmOcr').prop('disabled', !($('#ocr_student_number').val().trim() && $('#ocr_student_name').val().trim()));
                $('#ocrLockBadge').show().text('Matched in records');

                if (typeof onApplied === 'function') {
                    onApplied(res);
                }
            });
        }

        function attemptAutoProceed(studentNumber, studentName) {
            const normalizedStudentNumber = (studentNumber || '').trim();
            const normalizedStudentName = (studentName || '').trim();
            const autoProceedKey = `${normalizedStudentNumber}|${normalizedStudentName}`;

            if (!normalizedStudentNumber || !normalizedStudentName || autoProceedInFlight || lastAutoProceedKey === autoProceedKey) {
                return;
            }

            autoProceedInFlight = true;
            lastAutoProceedKey = autoProceedKey;
            buildStatus(`Student number and name matched. Opening the ${getDestinationLabel()} now.`, 'success', 'Auto proceed');
            verifyUser(normalizedStudentNumber, normalizedStudentName, true);
        }

        function requestStudentNumberPreviewByName(studentName, onApplied = null) {
            const normalizedStudentName = (studentName || '').trim();
            if (!normalizedStudentName || lastPreviewedStudentName === normalizedStudentName) {
                return;
            }

            lastPreviewedStudentName = normalizedStudentName;

            $.get("{{ url($basePrefix . '/walkin/get-student') }}", {
                student_id: '',
                student_name: normalizedStudentName,
                preview_only: 1,
                intake_target: intakeTarget
            }, function(res) {
                if (res.status !== 'preview' || !res.student_number) {
                    return;
                }

                $('#ocr_student_number').val(res.student_number);
                if (res.student_name && (!manualStudentNameEdited || $('#ocr_student_name').val().trim() === '')) {
                    $('#ocr_student_name').val(res.student_name);
                }
                $('#btnConfirmOcr').prop('disabled', !($('#ocr_student_number').val().trim() && $('#ocr_student_name').val().trim()));
                $('#ocrLockBadge').show().text('Matched by name');

                if (typeof onApplied === 'function') {
                    onApplied(res);
                }
            });
        }

        async function getOcrWorker() {
            if (!ocrWorkerPromise) {
                ocrWorkerPromise = (async () => {
                    const worker = await Tesseract.createWorker('eng');

                    if (typeof worker.setParameters === 'function') {
                        await worker.setParameters({
                            preserve_interword_spaces: '1',
                        });
                    }

                    return worker;
                })();
            }

            return ocrWorkerPromise;
        }

        function capturePreparedIdCanvas(preprocess = true) {
            const video = getScannerVideoElement();
            if (!video || video.readyState < 2) {
                return null;
            }

            const canvas = document.getElementById('ocrCanvas');
            const context = canvas.getContext('2d', { willReadFrequently: true });
            const width = video.videoWidth || 1280;
            const height = video.videoHeight || 720;
            const outputWidth = Math.max(720, Math.floor(width * ocrCanvasScale));
            const outputHeight = Math.max(405, Math.floor(height * ocrCanvasScale));

            canvas.width = outputWidth;
            canvas.height = outputHeight;
            context.drawImage(video, 0, 0, width, height, 0, 0, outputWidth, outputHeight);

            if (!preprocess) {
                return canvas;
            }

            const imageData = context.getImageData(0, 0, outputWidth, outputHeight);
            const data = imageData.data;

            for (let i = 0; i < data.length; i += 4) {
                const grayscale = (data[i] * 0.299) + (data[i + 1] * 0.587) + (data[i + 2] * 0.114);
                const softened = Math.max(0, Math.min(255, ((grayscale - 128) * 1.28) + 128 + 10));
                data[i] = softened;
                data[i + 1] = softened;
                data[i + 2] = softened;
            }

            context.putImageData(imageData, 0, 0);

            return canvas;
        }

        function captureStudentNumberCanvas() {
            const sourceCanvas = capturePreparedIdCanvas(true);
            if (!sourceCanvas) {
                return null;
            }

            const width = sourceCanvas.width;
            const height = sourceCanvas.height;
            const numberZoneCanvas = document.createElement('canvas');
            const zoneWidth = Math.floor(width * 0.72);
            const zoneHeight = Math.floor(height * 0.18);
            const zoneX = Math.floor((width - zoneWidth) / 2);
            const zoneY = Math.floor(height * 0.48);

            numberZoneCanvas.width = zoneWidth;
            numberZoneCanvas.height = zoneHeight;
            numberZoneCanvas.getContext('2d', { willReadFrequently: true }).drawImage(
                sourceCanvas,
                zoneX,
                zoneY,
                zoneWidth,
                zoneHeight,
                0,
                0,
                zoneWidth,
                zoneHeight
            );

            return numberZoneCanvas;
        }

        async function captureAndAnalyzeId(isAutoPass = false) {
            const canvas = capturePreparedIdCanvas(true);
            const studentNumberCanvas = captureStudentNumberCanvas();
            if (!canvas || !studentNumberCanvas) {
                if (!isAutoPass) {
                    buildStatus('Camera preview is not ready yet. Please wait a moment, then try again.', 'error');
                }
                return;
            }
            ocrInFlight = true;
            $('#btnRunOcr').prop('disabled', true).text(isAutoPass ? 'Live OCR Running...' : 'Analyzing ID...');

            if (!isAutoPass) {
                buildStatus('Analyzing the camera image and extracting the student data now.', 'info');
            }

            try {
                const worker = await getOcrWorker();
                const [fullResult, numberResult] = await Promise.all([
                    worker.recognize(canvas),
                    worker.recognize(studentNumberCanvas),
                ]);
                const fullText = fullResult.data.text || '';
                const numberText = numberResult.data.text || '';
                const studentNumber = extractStudentNumber(`${numberText} ${fullText}`, numberText);
                const studentName = extractStudentName(fullText, fullText);
                const confidence = Math.round(((numberResult.data.confidence || 0) * 0.8) + ((fullResult.data.confidence || 0) * 0.2));
                const hasNumberCandidate = studentNumber !== '';
                const hasNameCandidate = studentName !== '';

                if (hasNumberCandidate && studentNumber === lastStudentNumberCandidate) {
                    studentNumberStableCount += 1;
                } else if (hasNumberCandidate) {
                    lastStudentNumberCandidate = studentNumber;
                    studentNumberStableCount = 1;
                } else {
                    lastStudentNumberCandidate = '';
                    studentNumberStableCount = 0;
                }

                if (hasNameCandidate && studentName === lastStudentNameCandidate) {
                    studentNameStableCount += 1;
                } else if (hasNameCandidate) {
                    lastStudentNameCandidate = studentName;
                    studentNameStableCount = 1;
                } else {
                    lastStudentNameCandidate = '';
                    studentNameStableCount = 0;
                }

                const allowNameAutofill = hasNameCandidate && studentNameStableCount >= 2;
                const stableStudentNumber = hasNumberCandidate && studentNumberStableCount >= 2 ? studentNumber : '';
                const stableStudentName = allowNameAutofill ? studentName : '';
                const signature = `${stableStudentNumber}|${stableStudentName}|${confidence}`;
                const isStableCandidate = stableStudentNumber !== '' || stableStudentName !== '';

                if (isStableCandidate && signature === lastOcrSignature) {
                    ocrLockCount += 1;
                } else if (isStableCandidate) {
                    ocrLockCount = 1;
                } else {
                    ocrLockCount = 0;
                }

                const isLocked = ocrLockCount >= 2 && isStableCandidate;

                updateDetectedFields(stableStudentNumber || studentNumber, stableStudentName, confidence, isLocked, allowNameAutofill);

                if (stableStudentNumber) {
                    requestMatchedNamePreview(stableStudentNumber, stableStudentName || studentName, function(preview) {
                        if (preview.name_matches === false) {
                            buildStatus('Student number matched an official record, but the scanned name still looks different. Please review the card before continuing.', 'info', 'Record name applied');
                        } else {
                            buildStatus('Student number matched an official record and the system applied the saved name automatically. Please review before continuing.', 'success', 'Record name applied');
                            attemptAutoProceed(
                                stableStudentNumber,
                                ($('#ocr_student_name').val() || preview.student_name || stableStudentName || studentName)
                            );
                        }
                    });

                    if (signature !== lastOcrSignature || !isAutoPass) {
                        buildStatus(
                            isLocked
                                ? 'Live OCR locked onto the card. Please review the extracted student number and name before continuing.'
                                : allowNameAutofill
                                    ? 'Live OCR found a stable student number and a usable name guess. Please review the extracted fields below.'
                                    : 'Live OCR found a stable student number. The system is matching the saved name now.',
                            'success',
                            `Student no. confidence ${confidence}%`
                        );
                        lastOcrSignature = signature;
                    }
                } else if (stableStudentName) {
                    if (signature !== lastOcrSignature) {
                        buildStatus(
                            'The name is stable now. Keep the ID steady and we will keep reading the student number.',
                            'info',
                            `Student no. confidence ${confidence}%`
                        );
                        lastOcrSignature = signature;
                    }
                } else if (isAutoPass && !aiAssistCooldown) {
                    aiAssistCooldown = true;
                    window.setTimeout(function() {
                        aiAssistCooldown = false;
                    }, 7000);
                    verifyWithAi(true);
                } else if (!isAutoPass) {
                    buildStatus('OCR could not confidently read the student number yet. You can keep the card steady, use AI student-number reading, or type it manually.', 'error', `Student no. confidence ${confidence}%`);
                }
            } catch (error) {
                if (!isAutoPass) {
                    buildStatus('OCR analysis failed on this capture. Please try again or use manual entry.', 'error');
                }
            } finally {
                ocrInFlight = false;
                $('#btnRunOcr').prop('disabled', false).text('Capture & Analyze ID');
            }
        }

        function verifyWithAi(isAutoAssist = false) {
            const canvas = capturePreparedIdCanvas(false);
            if (!canvas) {
                buildStatus('Camera preview is not ready yet. Please wait a moment, then try AI student-number reading again.', 'error');
                return;
            }

            $('#btnRunAiOcr').prop('disabled', true).text('AI Reading...');
            buildStatus(
                isAutoAssist
                    ? 'Live OCR needs help, so we are sending the current camera image to AI to extract the student number.'
                    : 'Sending the current camera image to AI to extract the student number.',
                'info'
            );

            $.ajax({
                url: "{{ url($basePrefix . '/walkin/verify-id-ai') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    image_data: canvas.toDataURL('image/jpeg', 0.84),
                },
                success: function(res) {
                    const studentNumber = (res.student_number || '').trim();
                    const studentName = (res.student_name || '').trim();
                    const note = (res.confidence_note || 'AI student-number reading completed.').trim();

                    if (studentNumber) {
                        $('#ocr_student_number').val(studentNumber);
                    }

                    $('#ocrResultPanel').show();
                    $('#btnConfirmOcr').prop('disabled', !(studentNumber && $('#ocr_student_name').val().trim()));
                    $('#ocrLockBadge').show().text('AI Read');

                    if (studentNumber) {
                        requestMatchedNamePreview(studentNumber, '', function(preview) {
                            if (preview && preview.student_name) {
                                buildStatus('AI read the student number and the system filled the saved name from records. Please review before continuing.', 'success', 'AI + records');
                                attemptAutoProceed(studentNumber, preview.student_name);
                                return;
                            }

                            if (studentName) {
                                $('#ocr_student_name').val(studentName);
                            }

                            $('#btnConfirmOcr').prop('disabled', !(studentNumber && $('#ocr_student_name').val().trim()));
                            buildStatus(note, 'success', 'AI assist');
                            attemptAutoProceed(studentNumber, $('#ocr_student_name').val().trim());
                        });
                    } else if (studentName) {
                        $('#ocr_student_name').val(studentName);
                        $('#btnConfirmOcr').prop('disabled', !(studentNumber && $('#ocr_student_name').val().trim()));
                        buildStatus(note, 'info', 'AI assist');
                    } else {
                        buildStatus(note, 'error', 'AI assist');
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || {};
                    buildStatus(response.message || 'AI student-number reading could not complete right now. Please keep using OCR or manual review.', 'error');
                },
                complete: function() {
                    $('#btnRunAiOcr').prop('disabled', false).text('AI Read Student No.');
                }
            });
        }

        function verifyUser(id, studentName = '', autoProceed = false) {
            $('#scan-loading').css('display', 'flex');
            $('#notification').html('');
            $.get("{{ url($basePrefix . '/walkin/get-student') }}", { student_id: id, student_name: studentName, intake_target: intakeTarget }, function(res) {
                $('#scan-loading').hide();
                autoProceedInFlight = false;
                if (res.status === 'found') {
                    window.location.href = res.redirect_url;
                } else if (res.status === 'name_mismatch') {
                    const candidateName = res.candidate && res.candidate.name ? res.candidate.name : 'Saved patient name';
                    const candidateNumber = res.candidate && res.candidate.student_number ? res.candidate.student_number : id;
                    buildStatus(`We found ${candidateNumber}, but the extracted name did not match the saved record (${candidateName}). Please review the OCR result before continuing.`, 'error');
                    $('#ocrResultPanel').show();
                } else {
                    const statusText = res.lookup_status ? ` (PUPTAS status ${res.lookup_status})` : '';
                    const failureMessage = res.message
                        ? `${res.message}${statusText}`
                        : `No patient matched ${id} locally or in PUPTAS${statusText}.`;

                    if (autoProceed) {
                        buildStatus(`Auto proceed stopped: ${failureMessage}`, 'info');
                        return;
                    }

                    $('#notification').html(`<p style="color:#991b1b; font-size:12px; font-weight:700; background:#fff1f2; padding:10px 12px; border-radius:10px; border:1px solid #fecdd3; margin-bottom:12px;">${failureMessage}</p>`);

                    if(mainScanner) {
                        mainScanner.stop().then(() => {
                            mainScanner = null;
                            currentVideoTrack = null;
                            stopLiveOcr();
                            if (confirm(`${failureMessage}\n\nOpen Assisted Intake instead?`)) {
                                showRegisterUI(id);
                            } else { window.location.reload(); }
                        });
                    }
                }
            }).fail(() => {
                $('#scan-loading').hide();
                autoProceedInFlight = false;
            });
        }

        function showRegisterUI(scannedId = '') {
            $('#scanForm').hide();
            $('#registerForm').show();
            $('#headerTitle').text('Assisted Intake Ready');
            $('#headerIcon').text('ASSIST');
            if(scannedId) {
                $('#reg_barcode').val(scannedId);
                $('#reg_student_id').val(scannedId);
            }
        }

        function updateScanModeUI() {
            const isBioSync = scanMethod === 'biosync';
            const isApplicantFlow = intakeTarget === 'assessment';
            $('#scanMethodTitle').text(isBioSync ? 'BioSync Fingerprint' : 'OCR ID Scan');
            $('#scanMethodNote').text(
                isBioSync
                    ? 'Start with the fingerprint-first BioSync panel, then switch to OCR if the applicant needs card-based identification instead.'
                    : isApplicantFlow
                        ? 'Use the live camera feed to extract the applicant student number from the ID card, then proceed to Medical Assessment.'
                        : 'Use the live camera feed to extract the printed student number from the physical ID card, then fill the saved name from records.'
            );
            $('#scanMethodBadge').text(isBioSync ? 'BioSync Active' : 'OCR Active');
            $('#btnSwitchScanMode span').text(isBioSync ? 'Switch to OCR Scan' : 'Switch to BioSync');
            $('#headerTitle').text(isBioSync ? 'BioSync Ready' : 'OCR Ready');
            $('#headerSubtitle').text(
                isBioSync
                    ? 'Start with BioSync fingerprint identification, or switch to OCR ID scanning when you need to capture the applicant card instead.'
                    : isApplicantFlow
                        ? 'Choose OCR ID scanning or BioSync mode to identify the applicant and proceed to Medical Assessment.'
                        : ''
            );
            $('#headerIcon').text(isBioSync ? 'BIO' : (isApplicantFlow ? 'AP' : 'SB'));
            $('#scanInlineNote').text(
                isBioSync
                    ? 'BioSync fingerprint mode is selected first. When the scanner flow is ready, this panel will be the primary identification step before OCR.'
                    : isApplicantFlow
                        ? 'OCR mode is active. Align the physical ID inside the frame and continue once student number and name are matched for Medical Assessment.'
                        : 'OCR mode is active. Align the physical ID inside the frame and the system will keep reading the student number live, then match the saved name automatically.'
            );
            $('#barcodeScanPanel').toggle(!isBioSync);
            $('#bioSyncPendingPanel').toggle(isBioSync);
            $('#btnShowManual').toggle(!isBioSync);
            $('#manualInputArea').toggle(!isBioSync && $('#manualInputArea').is(':visible'));
            const keepResultPanelVisible = intakeTarget === 'assessment';
            $('#ocrResultPanel').toggle(!isBioSync && ($('#ocrResultPanel').is(':visible') || keepResultPanelVisible));
            $('#applicantOcrReviewPanel').toggle(!isBioSync);
            $('#applicantBioSyncInfoPanel').toggle(isBioSync);

            if (isBioSync) {
                stopMainScanner();
            } else {
                startMainScanner();
            }
        }

        function openIntakeScanModal(target = 'consultation') {
            intakeTarget = target;
            scanMethod = 'biosync';
            manualStudentNumberEdited = false;
            manualStudentNameEdited = false;
            $('#student_id_manual').val('');
            $('#ocr_student_number').val('');
            $('#ocr_student_name').val('');
            $('#ocrLockBadge').hide();
            $('#btnConfirmOcr').prop('disabled', true);
            $('#manualInputArea').show();
            buildStatus('AI verification could not finish right now.', 'info');
            $('#ocrConfidenceText').text('Student no. confidence: 10%');
        updateScanModeUI();
        syncAssistedRoleDisplay();
        syncAssistedGenderDisplay();
            if (applicantScanModal) {
                applicantScanModal.classList.add('show');
            }
        }

        function closeApplicantScanModal() {
            if (applicantScanModal) {
                applicantScanModal.classList.remove('show');
            }
            stopMainScanner();
        }

        if (openScanLookupModalBtn) {
            openScanLookupModalBtn.addEventListener('click', function (event) {
                event.preventDefault();
                openIntakeScanModal('consultation');
            });
        }

        if (openApplicantScanModalBtn) {
            openApplicantScanModalBtn.addEventListener('click', function (event) {
                event.preventDefault();
                openIntakeScanModal('assessment');
            });
        }

        if (closeApplicantScanModalBtn) {
            closeApplicantScanModalBtn.addEventListener('click', closeApplicantScanModal);
        }

        if (applicantScanModal) {
            applicantScanModal.addEventListener('click', function (event) {
                if (event.target === applicantScanModal) {
                    closeApplicantScanModal();
                }
            });
        }

        if (assistedRoleSelect && assistedRoleDisplay && assistedRoleWrap) {
            assistedRoleDisplay.addEventListener('click', function () {
                const shouldOpen = !assistedRoleWrap.classList.contains('is-open');
                setAssistedRoleOpenState(shouldOpen);
            });

            assistedRoleOptions.forEach(function (option) {
                option.addEventListener('click', function () {
                    const value = option.dataset.roleValue || '';
                    assistedRoleSelect.value = value;
                    syncAssistedRoleDisplay();
                    setAssistedRoleOpenState(false);
                });
            });

            document.addEventListener('click', function (event) {
                if (!assistedRoleWrap.contains(event.target)) {
                    setAssistedRoleOpenState(false);
                }
            });
        }

        if (assistedGenderSelect && assistedGenderDisplay && assistedGenderWrap) {
            assistedGenderDisplay.addEventListener('click', function () {
                const shouldOpen = !assistedGenderWrap.classList.contains('is-open');
                setAssistedGenderOpenState(shouldOpen);
            });

            assistedGenderOptions.forEach(function (option) {
                option.addEventListener('click', function () {
                    const value = option.dataset.genderValue || '';
                    assistedGenderSelect.value = value;
                    syncAssistedGenderDisplay();
                    setAssistedGenderOpenState(false);
                });
            });

            document.addEventListener('click', function (event) {
                if (!assistedGenderWrap.contains(event.target)) {
                    setAssistedGenderOpenState(false);
                }
            });
        }

        $('#btnShowManual').on('click', function() {
            $('#manualInputArea').toggle();
        });

        $('#student_id_manual').on('input', function() {
            const hasValue = $(this).val().trim() !== '';
            $('#manualFindBtn').toggle(hasValue);
        });

        $('#btnSwitchScanMode').on('click', function() {
            const $scanStage = $('#scanStage');
            if ($scanStage.hasClass('is-flipping')) {
                return;
            }

            $scanStage.addClass('is-flipping');

            window.setTimeout(function () {
            scanMethod = scanMethod === 'biosync' ? 'ocr' : 'biosync';
            updateScanModeUI();
            }, 180);

            window.setTimeout(function () {
                $scanStage.removeClass('is-flipping');
            }, 560);
        });

        $('#btnRunAiOcr').on('click', function() {
            verifyWithAi();
        });

        $('#btnConfirmOcr').on('click', function() {
            const studentNumber = $('#ocr_student_number').val().trim();
            const studentName = $('#ocr_student_name').val().trim();

            if (!studentNumber || !studentName) {
                buildStatus('Please review both extracted fields first. We need both the student number and the student name for confirmation.', 'error');
                return;
            }

            verifyUser(studentNumber, studentName);
        });

        $('#btnRetryOcr').on('click', function() {
            $('#ocr_student_number').val('');
            $('#ocr_student_name').val('');
            $('#btnConfirmOcr').prop('disabled', true);
            $('#ocrConfidenceText').text('Student number confidence will appear here after analysis.');
            lastOcrSignature = '';
            ocrLockCount = 0;
            lastStudentNumberCandidate = '';
            studentNumberStableCount = 0;
            lastStudentNameCandidate = '';
            studentNameStableCount = 0;
            manualStudentNumberEdited = false;
            manualStudentNameEdited = false;
            lastPreviewedStudentNumber = '';
            lastPreviewedStudentName = '';
            ocrNameLocked = false;
            autoProceedInFlight = false;
            lastAutoProceedKey = '';
            $('#ocrLockBadge').hide();
            buildStatus('We cleared the last OCR result. Capture the ID again when you are ready.', 'info');
        });

        $('#ocr_student_number').on('input', function() {
            manualStudentNumberEdited = true;
            lastPreviewedStudentNumber = '';
            lastAutoProceedKey = '';
            const hasBoth = $('#ocr_student_number').val().trim() !== '' && $('#ocr_student_name').val().trim() !== '';
            $('#btnConfirmOcr').prop('disabled', !hasBoth);
        });

        $('#ocr_student_name').on('input', function() {
            manualStudentNameEdited = true;
            lastPreviewedStudentName = '';
            ocrNameLocked = true;
            lastAutoProceedKey = '';
            const hasBoth = $('#ocr_student_number').val().trim() !== '' && $('#ocr_student_name').val().trim() !== '';
            $('#btnConfirmOcr').prop('disabled', !hasBoth);
        });

        $('#walkinFormManual').on('submit', function(e) {
            e.preventDefault();
            verifyUser($('#student_id_manual').val());
        });

        $('#confirmBtn').on('click', function() {
            const role = $('#reg_user_type').val();

            if(!role) { alert("Please select a User Role!"); return; }

            $(this).prop('disabled', true).text('PROCESSING...');
            
            const formData = {
                _token: "{{ csrf_token() }}",
                role: role,
                user_role: role,
                user_type: role,
                student_number: $('#reg_student_id').val(),
                first_name: $('#reg_first_name').val(),
                last_name: $('#reg_last_name').val(),
                email: $('#reg_email').val(),
                dob: $('#reg_dob').val(),
                gender: $('#reg_gender').val(),
                contact_no: $('#reg_contact_no').val(),
                barcode: $('#reg_barcode').val() || $('#reg_student_id').val()
            };

            $.post("{{ url($basePrefix . '/walkin/register') }}", formData, function(res) {
                if(res.redirect_url) window.location.href = res.redirect_url;
                else window.location.reload();
            }).fail(function(xhr) {
                $('#confirmBtn').prop('disabled', false).text('CONFIRM REGISTRATION');
                let errorMsg = "Assisted intake failed.";
                if(xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors)[0][0];
                }
                $('#notification').html(`<p style="color:red; font-size:12px; font-weight:bold; background:#fee2e2; padding:10px; border-radius:8px;">${errorMsg}</p>`);
            });
        });
    });
</script>
@endpush
