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
        border: 1px solid rgba(112, 19, 27, 0.46);
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #ffffff;
        box-shadow:
            inset 0 -3px 0 rgba(250, 204, 21, 0.72),
            0 10px 24px rgba(112, 19, 27, 0.18);
        overflow: hidden;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease, color .2s ease;
    }

    .intake-option-card:hover {
        background: #facc15 !important;
        background-image: none !important;
        color: #111111 !important;
        transform: translateY(-8px);
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.12),
            0 20px 30px rgba(139, 0, 0, 0.22);
        border-color: #facc15;
    }

    .intake-option-card:hover .intake-option-chip svg,
    .intake-option-card:hover .intake-option-icon-wrap svg {
        stroke: #ffffff;
        color: #ffffff;
    }

    .intake-option-card::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0) 38%);
        z-index: 0;
    }

    .intake-option-card::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, rgba(255, 248, 196, 0) 0%, rgba(250, 204, 21, 0.42) 48%, rgba(255, 248, 196, 0) 100%);
        transform: translateX(-130%);
        transition: transform .95s ease;
        pointer-events: none;
        z-index: 0;
    }

    .intake-option-card:hover::after {
        transform: translateX(130%);
    }

    .intake-option-card:hover .intake-option-title,
    .intake-option-card:hover .intake-option-copy {
        color: #70131B !important;
    }

    .intake-option-card:hover .intake-option-chip,
    .intake-option-card:hover .intake-option-icon-wrap {
        background: #70131B;
        color: #ffffff;
        border-color: rgba(112, 19, 27, 0.62);
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
        border: 1px solid rgba(255, 248, 196, 0.72);
        box-shadow: 0 8px 14px rgba(15, 23, 42, 0.14);
        transition: background .2s ease, color .2s ease, border-color .2s ease;
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
        transition: background .2s ease, color .2s ease, border-color .2s ease;
        background: rgba(255, 248, 196, 0.12);
        color: #ffffff;
        border: 1px solid rgba(255, 248, 196, 0.16);
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
        transition: color .2s ease;
    }

    .intake-option-copy {
        margin: 0;
        color: #475569;
        line-height: 1.55;
        position: relative;
        z-index: 1;
        transition: color .2s ease;
    }

    .intake-option-card .intake-option-title,
    .intake-option-card .intake-option-copy {
        color: #ffffff !important;
    }

    .intake-option-registration {
        background: linear-gradient(135deg, #70131B, #8f2230);
    }

    .intake-option-registration.is-active {
        border: 2px solid #facc15;
    }

    .intake-option-registration .intake-option-icon-wrap {
        background: rgba(255, 248, 196, 0.12);
        color: #ffffff;
    }

    .intake-option-registration .intake-option-chip {
        background: rgba(255, 248, 196, 0.12);
        color: #ffffff;
    }

    .intake-option-scan {
        background: linear-gradient(135deg, #70131B, #8f2230);
    }

    .intake-option-scan.is-active {
        border: 2px solid #facc15;
    }

    .intake-option-scan .intake-option-icon-wrap {
        background: rgba(255, 248, 196, 0.12);
        color: #ffffff;
    }

    .intake-option-scan .intake-option-chip {
        background: rgba(255, 248, 196, 0.12);
        color: #ffffff;
    }

    .intake-option-assisted {
        background: linear-gradient(135deg, #70131B, #8f2230);
    }

    .intake-option-assisted.is-active {
        border: 2px solid #facc15;
    }

    .intake-option-assisted .intake-option-icon-wrap {
        background: rgba(255, 248, 196, 0.12);
        color: #ffffff;
    }

    .intake-option-assisted .intake-option-chip {
        background: rgba(255, 248, 196, 0.12);
        color: #ffffff;
    }

    .intake-option-applicant {
        background: linear-gradient(135deg, #70131B, #8f2230);
    }

    .intake-option-applicant.is-active {
        border: 2px solid #facc15;
    }

    .intake-option-applicant .intake-option-icon-wrap {
        background: rgba(255, 248, 196, 0.12);
        color: #ffffff;
    }

    .intake-option-applicant .intake-option-chip {
        background: rgba(255, 248, 196, 0.12);
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
        background: linear-gradient(180deg, #8f2230 0%, #70131B 100%);
    }

    html[data-theme="dark"] .intake-option-card::before {
        background: none;
    }

    html[data-theme="dark"] .intake-option-title,
    html[data-theme="dark"] .intake-option-copy {
        color: #ffffff !important;
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

    html[data-theme="dark"] .intake-option-card:hover .intake-option-title,
    html[data-theme="dark"] .intake-option-card:hover .intake-option-copy {
        color: #70131B !important;
    }

    html[data-theme="dark"] .intake-option-card:hover {
        background: linear-gradient(135deg, #facc15, #fde68a);
        border-color: #facc15;
    }

    html[data-theme="dark"] .intake-option-card:hover .intake-option-chip,
    html[data-theme="dark"] .intake-option-card:hover .intake-option-icon-wrap {
        background: #70131B;
        color: #ffffff;
        border-color: rgba(112, 19, 27, 0.62);
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
        transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease, color .22s ease;
    }

    .registration-mode-btn::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(255,255,255,0.92), rgba(255,255,255,0.28) 42%, rgba(255,255,255,0.10));
        pointer-events: none;
        z-index: 0;
    }

    .registration-mode-btn::after {
        content: "";
        position: absolute;
        top: -42%;
        left: -130%;
        width: 120%;
        height: 185%;
        background: linear-gradient(115deg, rgba(250, 204, 21, 0) 0%, rgba(250, 204, 21, 0.5) 45%, rgba(250, 204, 21, 0) 100%);
        transform: skewX(-20deg);
        opacity: 0;
        pointer-events: none;
        transition: left .8s ease, opacity .18s ease;
        z-index: 0;
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

    .registration-mode-btn:hover::after {
        opacity: 1;
        left: 125%;
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
        transition: background .22s ease, color .22s ease, border-color .22s ease, transform .22s ease;
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
        transition: color .22s ease;
    }

    .registration-mode-btn p {
        margin: 0;
        color: #64748b;
        line-height: 1.6;
        font-size: .95rem;
        position: relative;
        z-index: 1;
        transition: color .22s ease;
    }

    .registration-mode-btn:hover .um-mode-icon {
        background: linear-gradient(145deg, #facc15, #fde68a);
        color: #111827;
        border-color: rgba(250, 204, 21, 0.62);
        transform: translateY(-2px) scale(1.04);
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
        color: #000000;
        letter-spacing: -0.02em;
    }

    .assisted-hero-copy p {
        margin: 8px 0 0;
        color: #000000;
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
        color: #000000;
    }

    .assisted-status-chip.ready {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #000000;
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
        color: #000000;
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
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, 0.10), transparent 34%),
            linear-gradient(180deg, #fff7f7 0%, #fef2f2 100%);
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,0.94),
            0 10px 20px rgba(127, 29, 29, 0.04);
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
        color: #000000 !important;
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
        color: #000000 !important;
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
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, 0.10), transparent 34%),
            linear-gradient(180deg, #fff7f7 0%, #fef2f2 100%);
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,0.94),
            0 10px 20px rgba(127, 29, 29, 0.04);
        min-width: 0;
    }

    .assisted-panel-body > input.form-control {
        width: 100%;
    }

    .assisted-panel-body .form-control::placeholder {
        color: #000000;
        font-weight: 600;
        opacity: 0.72;
    }

    .assisted-intake-shell .assisted-role-display,
    .assisted-intake-shell .assisted-gender-display,
    .assisted-intake-shell .assisted-role-option,
    .assisted-intake-shell .assisted-gender-option,
    .assisted-intake-shell > .assisted-panel > .assisted-panel-body > div[style*="background:#fff7ed"] strong,
    .assisted-intake-shell > .assisted-panel > .assisted-panel-body > div[style*="background:#fff7ed"] p,
    #registerForm .text-center.mt-3 a {
        color: #000000 !important;
    }

    .assisted-submit-btn {
        width: 100%;
        border: none;
        padding: 14px 28px;
        border-radius: 14px;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        color: #ffffff;
        background: linear-gradient(135deg, #8B0000, #70131B);
        box-shadow:
            0 0 0 3px rgba(139, 0, 0, 0.10),
            0 16px 28px rgba(112, 19, 27, 0.20);
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, color 0.2s ease;
    }

    .assisted-submit-btn:hover {
        background: #facc15;
        color: #8B0000;
        transform: translateY(-2px);
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.12),
            0 18px 30px rgba(139, 0, 0, 0.22);
    }

    .assisted-submit-btn:disabled {
        cursor: not-allowed;
        opacity: 0.85;
        transform: none;
    }

    html[data-theme="dark"] .assisted-panel {
        background: linear-gradient(180deg, rgba(18, 18, 18, 0.98), rgba(28, 18, 18, 0.98));
        border-color: rgba(250, 204, 21, 0.14);
        box-shadow:
            0 22px 46px rgba(0, 0, 0, 0.34),
            0 0 0 1px rgba(139, 0, 0, 0.18);
    }

    html[data-theme="dark"] .assisted-hero-copy h3,
    html[data-theme="dark"] .assisted-hero-copy p,
    html[data-theme="dark"] .assisted-panel-body > .mb-3 label,
    html[data-theme="dark"] .assisted-panel-body > .mb-2 label,
    html[data-theme="dark"] .assisted-field-label,
    html[data-theme="dark"] .assisted-section-divider span,
    html[data-theme="dark"] .assisted-intake-shell .assisted-role-display,
    html[data-theme="dark"] .assisted-intake-shell .assisted-gender-display,
    html[data-theme="dark"] .assisted-intake-shell .assisted-role-option,
    html[data-theme="dark"] .assisted-intake-shell .assisted-gender-option,
    html[data-theme="dark"] .assisted-intake-shell > .assisted-panel > .assisted-panel-body > div[style*="background:#fff7ed"] strong,
    html[data-theme="dark"] .assisted-intake-shell > .assisted-panel > .assisted-panel-body > div[style*="background:#fff7ed"] p,
    html[data-theme="dark"] #registerForm .text-center.mt-3 a {
        color: #f8fafc !important;
    }

    html[data-theme="dark"] .assisted-status-chip.pending,
    html[data-theme="dark"] .assisted-status-chip.ready {
        color: #f8fafc;
        border-color: rgba(250, 204, 21, 0.24);
        background: rgba(255, 255, 255, 0.08);
    }

    html[data-theme="dark"] .assisted-panel-body > .mb-3,
    html[data-theme="dark"] .assisted-panel-body > .mb-2,
    html[data-theme="dark"] .assisted-panel-body > .assisted-pair-row,
    html[data-theme="dark"] .assisted-panel-body > .assisted-field-card,
    html[data-theme="dark"] .assisted-panel-body > input.form-control,
    html[data-theme="dark"] .assisted-panel-body > .assisted-callout,
    html[data-theme="dark"] .assisted-panel-body > div[style*="background:#fff7ed"] {
        border-color: rgba(250, 204, 21, 0.14);
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, 0.08), transparent 34%),
            linear-gradient(180deg, rgba(47, 24, 24, 0.92) 0%, rgba(30, 18, 18, 0.98) 100%);
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,0.04),
            0 12px 24px rgba(0, 0, 0, 0.18);
    }

    html[data-theme="dark"] .assisted-highlight-card {
        border-color: rgba(250, 204, 21, 0.18) !important;
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, 0.14), transparent 34%),
            linear-gradient(180deg, rgba(74, 24, 31, 0.96) 0%, rgba(52, 18, 23, 0.98) 100%) !important;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,0.05),
            0 12px 24px rgba(0, 0, 0, 0.20);
    }

    html[data-theme="dark"] .assisted-panel-body > .mb-3 .form-control,
    html[data-theme="dark"] .assisted-panel-body > .mb-2 .form-control,
    html[data-theme="dark"] .assisted-panel-body > .assisted-pair-row .form-control,
    html[data-theme="dark"] .assisted-panel-body > .assisted-field-card .form-control,
    html[data-theme="dark"] .assisted-panel-body > input.form-control,
    html[data-theme="dark"] .assisted-intake-shell .assisted-role-display,
    html[data-theme="dark"] .assisted-intake-shell .assisted-gender-display {
        color: #f8fafc !important;
        border-color: rgba(250, 204, 21, 0.16);
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, 0.08), transparent 36%),
            linear-gradient(180deg, rgba(40, 26, 26, 0.98) 0%, rgba(23, 23, 23, 0.98) 100%);
        box-shadow:
            0 12px 22px rgba(0, 0, 0, 0.22),
            inset 0 1px 0 rgba(255,255,255,0.05);
    }

    html[data-theme="dark"] .assisted-panel-body .form-control::placeholder {
        color: rgba(248, 250, 252, 0.62);
    }

    html[data-theme="dark"] .assisted-panel-body .form-control:focus,
    html[data-theme="dark"] .assisted-panel-body select.form-control:focus,
    html[data-theme="dark"] .assisted-intake-shell .assisted-role-display:focus,
    html[data-theme="dark"] .assisted-intake-shell .assisted-gender-display:focus,
    html[data-theme="dark"] .assisted-intake-shell .assisted-role-display.is-open,
    html[data-theme="dark"] .assisted-intake-shell .assisted-gender-display.is-open {
        border-color: #facc15;
        box-shadow:
            0 0 0 4px rgba(250, 204, 21, 0.14),
            0 14px 24px rgba(0, 0, 0, 0.26),
            inset 0 1px 0 rgba(255,255,255,0.06);
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, 0.10), transparent 36%),
            linear-gradient(180deg, rgba(54, 34, 34, 0.98) 0%, rgba(28, 20, 20, 0.98) 100%);
    }

    html[data-theme="dark"] .assisted-role-wrap::after,
    html[data-theme="dark"] .assisted-gender-wrap::after {
        border-right-color: #facc15;
        border-bottom-color: #facc15;
    }

    html[data-theme="dark"] .assisted-role-wrap::before,
    html[data-theme="dark"] .assisted-gender-wrap::before {
        background: rgba(250, 204, 21, 0.18);
    }

    html[data-theme="dark"] .assisted-role-menu,
    html[data-theme="dark"] .assisted-gender-menu {
        background: rgba(18, 18, 18, 0.96);
        border-color: rgba(250, 204, 21, 0.14);
        box-shadow: 0 18px 34px rgba(0, 0, 0, 0.34);
    }

    html[data-theme="dark"] .assisted-role-option,
    html[data-theme="dark"] .assisted-gender-option {
        color: #f8fafc !important;
        border-color: rgba(250, 204, 21, 0.14);
        background: linear-gradient(180deg, rgba(40, 26, 26, 0.98) 0%, rgba(23, 23, 23, 0.98) 100%);
        box-shadow: 0 12px 22px rgba(0, 0, 0, 0.22), inset 0 1px 0 rgba(255,255,255,0.04);
    }

    html[data-theme="dark"] .assisted-role-option:hover,
    html[data-theme="dark"] .assisted-role-option.is-selected,
    html[data-theme="dark"] .assisted-gender-option:hover,
    html[data-theme="dark"] .assisted-gender-option.is-selected {
        background: linear-gradient(135deg, #8B0000, #70131B);
        color: #facc15 !important;
        border-color: rgba(250, 204, 21, 0.28);
    }

    html[data-theme="dark"] .assisted-submit-btn {
        background: linear-gradient(135deg, #8B0000, #70131B);
        color: #ffffff;
        box-shadow:
            0 0 0 3px rgba(139, 0, 0, 0.16),
            0 16px 28px rgba(0, 0, 0, 0.30);
    }

    html[data-theme="dark"] .assisted-submit-btn:hover {
        background: #facc15;
        color: #111111;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.16),
            0 18px 30px rgba(0, 0, 0, 0.32);
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

    html[data-theme="dark"] .assisted-intake-shell > .assisted-panel > .assisted-panel-body > .mb-3,
    html[data-theme="dark"] .assisted-intake-shell > .assisted-panel > .assisted-panel-body > .mb-2,
    html[data-theme="dark"] .assisted-intake-shell > .assisted-panel > .assisted-panel-body > .assisted-pair-row,
    html[data-theme="dark"] .assisted-intake-shell > .assisted-panel > .assisted-panel-body > .assisted-field-card,
    html[data-theme="dark"] .assisted-intake-shell > .assisted-panel > .assisted-panel-body > input.form-control,
    html[data-theme="dark"] .assisted-intake-shell > .assisted-panel > .assisted-panel-body > div[style*="background:#fff7ed"] {
        border-color: rgba(250, 204, 21, 0.14) !important;
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, 0.08), transparent 34%),
            linear-gradient(180deg, rgba(47, 24, 24, 0.92) 0%, rgba(30, 18, 18, 0.98) 100%) !important;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,0.04),
            0 12px 24px rgba(0, 0, 0, 0.18) !important;
    }

    html[data-theme="dark"] .assisted-intake-shell > .assisted-panel > .assisted-panel-body > .mb-3.assisted-highlight-card {
        border-color: rgba(250, 204, 21, 0.18) !important;
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, 0.14), transparent 34%),
            linear-gradient(180deg, rgba(74, 24, 31, 0.96) 0%, rgba(52, 18, 23, 0.98) 100%) !important;
    }

    html[data-theme="dark"] .assisted-intake-shell > .assisted-panel > .assisted-panel-body > .assisted-pair-row .assisted-field-card,
    html[data-theme="dark"] .assisted-intake-shell > .assisted-panel > .assisted-panel-body > .assisted-field-card {
        border-color: rgba(250, 204, 21, 0.14) !important;
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, 0.08), transparent 34%),
            linear-gradient(180deg, rgba(47, 24, 24, 0.92) 0%, rgba(30, 18, 18, 0.98) 100%) !important;
    }

    html[data-theme="dark"] .assisted-intake-shell .assisted-section-divider span {
        background: rgba(20, 20, 20, 0.9) !important;
        border-color: rgba(250, 204, 21, 0.18) !important;
        color: #f8fafc !important;
    }

    html[data-theme="dark"] .assisted-intake-shell > .assisted-panel > .assisted-panel-body .form-control,
    html[data-theme="dark"] .assisted-intake-shell .assisted-role-display,
    html[data-theme="dark"] .assisted-intake-shell .assisted-gender-display {
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, 0.08), transparent 36%),
            linear-gradient(180deg, rgba(40, 26, 26, 0.98) 0%, rgba(23, 23, 23, 0.98) 100%) !important;
        color: #f8fafc !important;
        border-color: rgba(250, 204, 21, 0.16) !important;
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

    html[data-theme="dark"] .registration-kicker {
        color: #fde68a;
    }

    html[data-theme="dark"] .registration-head h3,
    html[data-theme="dark"] .registration-head p,
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

    html[data-theme="dark"] .registration-mode-btn::after {
        background: linear-gradient(115deg, rgba(250, 204, 21, 0) 0%, rgba(250, 204, 21, 0.46) 45%, rgba(250, 204, 21, 0) 100%);
    }

    html[data-theme="dark"] .registration-mode-btn .eyebrow {
        background: rgba(193, 138, 16, 0.22);
        color: #ffd86b;
    }

    html[data-theme="dark"] .registration-mode-btn h3,
    html[data-theme="dark"] .registration-mode-btn p {
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

    /* --- Applicant Reference Panel --- */
    .applicant-ref-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        align-items: stretch;
    }
    .applicant-ref-col {
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding: 20px;
        border-radius: 16px;
        border: 1px solid rgba(112, 19, 27, 0.13);
        background: linear-gradient(180deg, #ffffff, #f8fafc);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.9), 0 10px 24px rgba(15, 23, 42, 0.05);
    }
    .applicant-ref-kicker {
        margin: 0 0 3px;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #8b0000;
    }
    .applicant-ref-title {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
    }
    .applicant-ref-copy {
        margin: 4px 0 0;
        font-size: 13px;
        color: #64748b;
        line-height: 1.5;
    }
    .applicant-ref-input {
        width: 100%;
        min-height: 52px;
        padding: 14px 16px;
        border: 1px solid rgba(112, 19, 27, 0.18);
        border-radius: 14px;
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        background: linear-gradient(180deg, #ffffff, #fff8f6);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06), inset 0 1px 0 rgba(255,255,255,0.9);
        transition: border-color .2s ease, box-shadow .2s ease;
        margin-bottom: 0;
        outline: none;
    }
    .applicant-ref-input:focus {
        border-color: rgba(112, 19, 27, 0.42);
        box-shadow: 0 0 0 3px rgba(112, 19, 27, 0.08), 0 8px 18px rgba(15, 23, 42, 0.08);
    }
    .applicant-ref-status {
        padding: 10px 12px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.5;
        display: none;
    }
    .applicant-ref-status.info    { display:block; background:#eff6ff; border:1px solid #bfdbfe; color:#1d4ed8; }
    .applicant-ref-status.success { display:block; background:#ecfdf5; border:1px solid #a7f3d0; color:#047857; }
    .applicant-ref-status.error   { display:block; background:#fff1f2; border:1px solid #fecdd3; color:#be123c; }
    .applicant-ref-mode {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 14px;
        align-items: center;
    }

    .applicant-ref-copy {
        width: 100%;
        max-width: 460px;
        text-align: center;
    }

    .applicant-ref-copy .applicant-ref-kicker {
        margin: 0 0 6px;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #8b0000;
    }

    .applicant-ref-copy h4 {
        margin: 0 0 8px;
        font-size: 20px;
        font-weight: 900;
        color: #111827;
    }

    .applicant-ref-copy p {
        margin: 0;
        font-size: 13px;
        line-height: 1.55;
        color: #64748b;
    }

    .applicant-ref-lookup-row {
        display: flex;
        flex-direction: column;
        gap: 12px;
        width: 100%;
    }

    .applicant-ref-instruction {
        position: relative;
        min-height: 88px;
        padding: 14px 15px;
        border-radius: 16px;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        color: #9a3412;
        font-size: 0;
        line-height: 1.5;
        box-shadow: 0 8px 18px rgba(180, 83, 9, 0.10);
    }

    .applicant-ref-panel { position: relative; }

    .applicant-ref-instruction strong {
        display: block;
        margin-bottom: 4px;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .applicant-ref-instruction > strong:not(.applicant-ref-help-title) {
        display: none;
    }

    .applicant-ref-help-title {
        display: block;
        margin-bottom: 5px;
        color: #9a3412;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .applicant-ref-help-copy {
        display: block;
        color: #9a3412;
        font-size: 12px;
        line-height: 1.5;
    }

    .applicant-ref-help-copy strong {
        display: inline;
        margin: 0;
        font-size: inherit;
        letter-spacing: 0;
        text-transform: none;
    }

    .applicant-ref-toggle-btn,
    .applicant-ref-action-btn {
        width: 100%;
        min-height: 46px;
        border-radius: 14px;
        border: 1px solid transparent;
        font-size: 14px;
        font-weight: 900;
        cursor: pointer;
        transition: transform .18s ease, box-shadow .18s ease, background .18s ease, color .18s ease, border-color .18s ease, filter .18s ease;
    }

    .applicant-ref-toggle-btn {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px 18px;
        overflow: hidden;
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #facc15;
        border-color: rgba(112, 19, 27, 0.46);
        box-shadow: 0 12px 24px rgba(112, 19, 27, 0.22);
    }

    .applicant-ref-toggle-btn:hover,
    .applicant-ref-toggle-btn:focus {
        transform: translateY(-1px);
        background: linear-gradient(135deg, #facc15, #fde68a);
        color: #70131B;
        border-color: #facc15;
        box-shadow: 0 14px 24px rgba(112, 19, 27, 0.18);
        outline: none;
    }

    .applicant-ref-toggle-btn::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, rgba(255, 255, 255, 0) 0%, rgba(255, 248, 196, 0.45) 50%, rgba(255, 255, 255, 0) 100%);
        transform: translateX(-140%);
        transition: transform .95s ease;
        pointer-events: none;
    }

    .applicant-ref-toggle-btn:hover::after,
    .applicant-ref-toggle-btn:focus::after {
        transform: translateX(140%);
    }

    .applicant-ref-toggle-btn svg,
    .applicant-ref-toggle-btn span {
        position: relative;
        z-index: 1;
    }

    .applicant-ref-toggle-btn svg {
        width: 18px;
        height: 18px;
        flex: 0 0 auto;
    }

    .applicant-ref-panel {
        width: 100%;
        display: none;
        flex-direction: column;
        gap: 12px;
        align-items: stretch;
        max-width: 760px;
    }

    .applicant-ref-panel.is-visible {
        display: flex;
    }

    .applicant-ref-field label {
        display: block;
        margin: 0 0 6px;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
    }

    .applicant-ref-input {
        width: 100%;
        min-height: 48px;
        padding: 12px 14px;
        border: 1px solid rgba(112, 19, 27, 0.15);
        border-radius: 12px;
        background: linear-gradient(180deg, #ffffff, #fff8f6);
        color: #111827;
        font-size: 14px;
        font-weight: 700;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06), inset 0 1px 0 rgba(255,255,255,0.9);
        transition: border-color .2s ease, box-shadow .2s ease;
        outline: none;
    }
    .applicant-ref-input:focus {
        border-color: rgba(112, 19, 27, 0.42);
        box-shadow: 0 0 0 3px rgba(112, 19, 27, 0.08), 0 8px 18px rgba(15, 23, 42, 0.08);
    }

    .applicant-ref-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .applicant-ref-cancel-btn {
        background: #f1f5f9;
        color: #334155;
        border-color: #cbd5e1;
    }

    .applicant-ref-cancel-btn:hover,
    .applicant-ref-cancel-btn:focus {
        background: #e2e8f0;
        border-color: #b8c2d3;
        outline: none;
    }

    .applicant-ref-find-btn {
        background: linear-gradient(135deg, #7f1d1d, #991b1b 55%, #b91c1c);
        color: #ffffff;
        box-shadow: 0 12px 24px rgba(127, 29, 29, 0.24);
    }

    .applicant-ref-find-btn:hover,
    .applicant-ref-find-btn:focus {
        transform: translateY(-1px);
        box-shadow: 0 16px 28px rgba(127, 29, 29, 0.3);
        outline: none;
    }

    .applicant-ref-result {
        display: none;
        padding: 12px 14px;
        border-radius: 12px;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #047857;
        font-size: 12px;
        font-weight: 800;
        line-height: 1.5;
    }

    .applicant-ref-result strong {
        display: block;
        margin-top: 2px;
        color: #064e3b;
        font-size: 13px;
    }

    .applicant-lookup-details {
        display: none;
        width: 100%;
        padding: 14px;
        border-radius: 14px;
        border: 1px solid rgba(112, 19, 27, 0.14);
        background: linear-gradient(180deg, #ffffff, #fff8f6);
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.06);
    }

    .applicant-lookup-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px 14px;
    }

    .applicant-lookup-item {
        min-width: 0;
    }

    .applicant-lookup-label {
        margin: 0 0 3px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
    }

    .applicant-lookup-value {
        margin: 0;
        font-size: 13px;
        font-weight: 800;
        color: #111827;
        word-break: break-word;
    }

    .applicant-upload-wrap {
        display: none;
        width: 100%;
        gap: 10px;
    }

    .applicant-upload-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        min-height: 46px;
        padding: 0 16px;
        border-radius: 14px;
        border: 1px solid rgba(112, 19, 27, 0.42);
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #facc15;
        font-size: 13px;
        font-weight: 900;
        cursor: pointer;
        transition: transform .18s ease, background .18s ease, color .18s ease, box-shadow .18s ease;
        box-shadow: 0 10px 22px rgba(112, 19, 27, 0.18);
    }

    .applicant-upload-btn:hover {
        background: linear-gradient(135deg, #facc15, #fde68a);
        color: #70131B;
        transform: translateY(-1px);
    }

    .applicant-upload-note {
        font-size: 11px;
        line-height: 1.45;
        color: #64748b;
    }

    html[data-theme="dark"] .applicant-lookup-details {
        background: rgba(15, 23, 42, 0.94);
        border-color: rgba(250, 204, 21, 0.14);
    }

    html[data-theme="dark"] .applicant-lookup-label {
        color: #94a3b8;
    }

    html[data-theme="dark"] .applicant-lookup-value {
        color: #f8fafc;
    }

    html[data-theme="dark"] .applicant-upload-note {
        color: #cbd5e1;
    }

    @media (max-width: 640px) {
        .applicant-ref-actions {
            grid-template-columns: 1fr;
        }
    }
    html[data-theme="dark"] .applicant-ref-col {
        background: linear-gradient(180deg, rgba(18,18,18,0.98), rgba(28,18,18,0.98));
        border-color: rgba(250, 204, 21, 0.14);
    }
    html[data-theme="dark"] .applicant-ref-title { color: #f8fafc; }
    html[data-theme="dark"] .applicant-ref-copy  { color: #94a3b8; }
    html[data-theme="dark"] .applicant-ref-input {
        background: rgba(30, 41, 59, 0.9);
        border-color: rgba(148, 163, 184, 0.24);
        color: #f1f5f9;
    }
    @media (max-width: 640px) {
        .applicant-ref-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
@php
    $role = \App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '');
    $basePrefix = $role === \App\Models\User::ROLE_ADMIN ? '/assistant' : '/admin';
    $currentMode = in_array($mode ?? '', ['scan', 'assisted', 'registration', 'applicant'], true) ? $mode : '';
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
    <button onclick="location.href='{{ url($basePrefix . '/walkin') }}?mode=scan'" class="btn-toast-action">Open OCR / Manual ID</button>
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
                    <p class="intake-option-copy">Open registration options for IDP account creation or assisted manual profile setup.</p>
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
                    <h3 class="intake-option-title">OCR / Manual ID</h3>
                    <p class="intake-option-copy">Use OCR ID scanning or manual student number entry to identify an existing school user and continue directly to consultation.</p>
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

            {{-- Applicants card — opens the reference number modal --}}
            <a href="#" class="intake-option-link" id="openApplicantRefModal">
                <div class="intake-option-card intake-option-applicant {{ $currentMode === 'applicant' ? 'is-active' : '' }}">
                    <span class="intake-option-chip" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                        </svg>
                    </span>
                    <span class="intake-option-icon-wrap" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                    </span>
                    <h3 class="intake-option-title">Applicants</h3>
                    <p class="intake-option-copy">Enter a reference number to look up the applicant record.</p>
                </div>
            </a>

        </div>
    </div>

    {{-- Applicants Modal: Reference Number Lookup --}}
    <div class="applicant-modal-backdrop" id="applicantRefModal">
        <div class="applicant-modal-shell" style="width:min(480px,100%)">
            <div class="applicant-modal-head">
                <div class="applicant-modal-head-main">
                    <div class="applicant-modal-head-badge">AP</div>
                    <div class="applicant-modal-head-copy">
                        <h3>Applicants</h3>
                        <p>Enter the applicant's reference number to look up the record.</p>
                    </div>
                </div>
                <button type="button" class="applicant-modal-close" id="closeApplicantRefModal" aria-label="Close modal">
                    <x-outline-icon name="x-mark" />
                </button>
            </div>

            <div class="applicant-modal-body" style="display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:220px; gap:18px;">
                <div class="applicant-ref-mode" id="applicantRefDefault">
                    <div class="applicant-ref-copy">
                        <p class="applicant-ref-kicker">Proceed</p>
                        <h4>Reference Lookup</h4>
                        <p>Use the reference number to open the applicant record.</p>
                    </div>

                    <button type="button" id="btnShowApplicantRefInput" class="applicant-ref-toggle-btn" style="max-width:360px;">
                        <x-outline-icon name="magnifying-glass" />
                        <span>Input Reference Number</span>
                    </button>
                </div>

                <div class="applicant-ref-panel" id="applicantRefEntry">
                    <div class="applicant-ref-lookup-row">
                    <div class="applicant-ref-instruction">
                        <span class="applicant-ref-help-copy">Find the reference number in the <strong>Admission System</strong> under the applicant's profile or registration form.</span>
                    </div>
                    <div class="applicant-ref-field">
                        <label for="applicantRefInput">Reference Number</label>
                        <input type="text" id="applicantRefInput" class="applicant-ref-input" placeholder="Enter reference number">
                    </div>
                    </div>

                    <div id="applicantRefStatus" class="ocr-status"></div>

                    <div id="applicantFoundCard" class="applicant-ref-result">
                        Applicant located
                        <strong id="applicantFoundName"></strong>
                    </div>

                    <div id="applicantLookupDetails" class="applicant-lookup-details">
                        <div class="applicant-lookup-grid">
                            <div class="applicant-lookup-item">
                                <p class="applicant-lookup-label">Reference Number</p>
                                <p class="applicant-lookup-value" id="applicantLookupRef">-</p>
                            </div>
                            <div class="applicant-lookup-item">
                                <p class="applicant-lookup-label">Student ID</p>
                                <p class="applicant-lookup-value" id="applicantLookupStudentId">-</p>
                            </div>
                            <div class="applicant-lookup-item">
                                <p class="applicant-lookup-label">Course</p>
                                <p class="applicant-lookup-value" id="applicantLookupCourse">-</p>
                            </div>
                            <div class="applicant-lookup-item">
                                <p class="applicant-lookup-label">Year / Section</p>
                                <p class="applicant-lookup-value" id="applicantLookupYearSection">-</p>
                            </div>
                            <div class="applicant-lookup-item">
                                <p class="applicant-lookup-label">Date of Birth</p>
                                <p class="applicant-lookup-value" id="applicantLookupDob">-</p>
                            </div>
                            <div class="applicant-lookup-item">
                                <p class="applicant-lookup-label">Email</p>
                                <p class="applicant-lookup-value" id="applicantLookupEmail">-</p>
                            </div>
                        </div>
                    </div>

                    <form id="applicantAssessmentUploadForm" class="applicant-upload-wrap" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="reference_number" id="applicantAssessmentReferenceNumber">
                        <input type="hidden" name="student_number" id="applicantAssessmentStudentNumber">
                        <input type="file" name="medical_assessment_copy" id="applicantAssessmentUploadInput" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/*" style="display:none;">
                        <button type="button" id="btnUploadAssessmentCopy" class="applicant-upload-btn">
                            <x-outline-icon name="arrow-up-tray" />
                            Upload Medical Assessment Copy
                        </button>
                        <div class="applicant-upload-note">Optional digital copy for clinic records only.</div>
                    </form>

                    <div class="applicant-ref-actions">
                        <button type="button" id="btnCancelApplicantRef" class="applicant-ref-action-btn applicant-ref-cancel-btn">Cancel</button>
                        <button type="button" id="btnFindApplicant" class="applicant-ref-action-btn applicant-ref-find-btn">Find</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- OCR / Manual ID shared modal (used by OCR/Manual ID card and legacy assessment flow) --}}
    <div class="applicant-modal-backdrop" id="applicantScanModal">
        <div class="applicant-modal-shell">
            <div class="applicant-modal-head">
                <div class="applicant-modal-head-main">
                    <div id="headerIcon" class="applicant-modal-head-badge">AP</div>
                    <div class="applicant-modal-head-copy">
                        <h3 id="headerTitle">OCR Ready</h3>
                        <span id="scanMethodBadge" class="scan-method-badge">OCR Active</span>
                        <p id="headerSubtitle">Start with OCR ID scanning, or use manual student number entry when the card cannot be captured clearly.</p>
                    </div>
                </div>
                <div class="applicant-modal-head-actions" style="display:none;">
                    <button type="button" id="btnSwitchScanMode" class="btn-scan-switch">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                        </svg>
                        <span>OCR Scan Active</span>
                    </button>
                </div>
                <button type="button" class="applicant-modal-close" id="closeApplicantScanModal" aria-label="Close applicant scan modal">
                    <x-outline-icon name="x-mark" />
                </button>
            </div>
            <div class="applicant-modal-body">
                <div class="applicant-modal-grid">
                    {{-- Left Column: Scanner (No Frame) --}}
                    <div id="scanForm" style="display: contents;">
                        <div id="scanStage" class="scan-stage">
                            <div id="scanner-container-scan" class="scan-surface" style="position:relative;">
                                <p id="scanInlineNote" class="scan-inline-note">OCR mode is active. Align the physical ID inside the frame and continue once student number and name are matched.</p>
                                <div id="barcodeScanPanel">
                                    <div id="scan-loading">
                                        <div class="spinner"></div>
                                        <p style="margin-top:10px;color:#8B0000;font-weight:bold;font-size:12px;">Verifying...</p>
                                    </div>
                                    <div id="readerScan" class="scanner-box">
                                        <div class="scan-line-overlay"></div>
                                        <div class="ocr-guide"></div>
                                        <div class="ocr-guide-label">Align Student Number and Name</div>
                                    </div>
                                    <div class="ocr-actions">
                                        <button type="button" id="btnRunAiOcr" class="btn-ocr btn-ocr-primary" style="background:linear-gradient(135deg,#1d4ed8,#2563eb 55%,#3b82f6);box-shadow:0 12px 24px rgba(37,99,235,0.22);">Reading ID Number</button>
                                        <button type="button" id="btnRetryOcr" class="btn-ocr btn-ocr-secondary">Clear OCR Result</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Detected Data & Manual Entry --}}
                    <div class="applicant-modal-panel">
                        <div id="applicantOcrReviewPanel">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #e2e8f0;">
                                <div style="display:flex;align-items:flex-start;gap:12px;">
                                    <span style="width:42px;height:42px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#7f1d1d,#991b1b 58%,#b91c1c);color:#ffffff;box-shadow:0 12px 24px rgba(127,29,29,0.18);flex-shrink:0;">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" style="width:20px;height:20px;stroke:currentColor;stroke-width:1.9;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5h16.5m-16.5 4.5h10.5m-10.5 4.5h7.5M17.25 5.25h2.25A1.5 1.5 0 0 1 21 6.75v10.5a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 17.25V6.75a1.5 1.5 0 0 1 1.5-1.5h2.25"/>
                                        </svg>
                                    </span>
                                    <div>
                                        <p style="margin:0;font-size:11px;font-weight:900;letter-spacing:0.08em;text-transform:uppercase;color:#8b0000;">Detected Data</p>
                                        <p style="margin:4px 0 0;font-size:12px;color:#64748b;line-height:1.5;">Captured from OCR scan and arranged for final review.</p>
                                    </div>
                                </div>
                                <span style="display:inline-flex;align-items:center;padding:7px 10px;border-radius:999px;background:#ecfdf5;border:1px solid #a7f3d0;color:#047857;font-size:11px;font-weight:800;letter-spacing:0.04em;text-transform:uppercase;white-space:nowrap;">OCR Result</span>
                            </div>
                            <div style="display:grid;gap:12px;">
                                <div style="display:grid;grid-template-columns:120px minmax(0,1fr);gap:12px;align-items:center;padding:12px 14px;border-radius:14px;background:linear-gradient(180deg,#fdfefe,#f8fafc);border:1px solid #e2e8f0;">
                                    <p class="ocr-result-label" style="margin:0;color:#334155;">Full Name</p>
                                    <input type="text" id="ocr_student_name" class="form-control" readonly style="margin-bottom:0;background:#ffffff;color:#0f172a;border:1px solid #cbd5e1;box-shadow:inset 0 1px 0 rgba(255,255,255,0.95);font-weight:700;cursor:default;">
                                </div>
                                <div style="display:grid;grid-template-columns:120px minmax(0,1fr);gap:12px;align-items:center;padding:12px 14px;border-radius:14px;background:linear-gradient(180deg,#fdfefe,#f8fafc);border:1px solid #e2e8f0;">
                                    <p class="ocr-result-label" style="margin:0;color:#334155;">ID Number</p>
                                    <input type="text" id="ocr_student_number" class="form-control" readonly style="margin-bottom:0;background:#ffffff;color:#0f172a;border:1px solid #cbd5e1;box-shadow:inset 0 1px 0 rgba(255,255,255,0.95);font-weight:700;cursor:default;">
                                </div>
                            </div>
                            <div id="ocrStatus" class="ocr-status info" style="display:block;">AI verification could not finish right now.</div>
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                <div id="ocrConfidenceText" class="ocr-meta">Student no. confidence: 10%</div>
                                <div id="ocrLockBadge" class="ocr-lock-badge" style="display:none;">Locked on ID</div>
                            </div>
                            <div class="ocr-actions" style="margin-top:14px;">
                                <button type="button" id="btnConfirmOcr" class="btn-ocr btn-ocr-secondary" disabled>Confirm &amp; Continue</button>
                            </div>
                            <div class="manual-input-stack">
                                <p class="manual-toggle-label">Type Student Number Manually</p>
                                <form id="walkinFormManual">
                                    <input type="text" id="student_id_manual" placeholder="Enter student number" class="form-control" style="margin-bottom:10px;" required>
                                    <button type="submit" id="manualFindBtn" class="manual-find-btn" style="display:none;">Find</button>
                                </form>
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

        <a href="{{ url()->current() }}?mode=assisted" class="um-mode-btn registration-mode-btn">
            <span class="um-mode-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75h3A2.25 2.25 0 0 1 21 9v7.5A2.25 2.25 0 0 1 18.75 18.75H5.25A2.25 2.25 0 0 1 3 16.5V9a2.25 2.25 0 0 1 2.25-2.25h3m1.5 0a2.25 2.25 0 0 1 4.5 0m-4.5 0h4.5m-6 5.25h6m-6 3h3.75" />
                </svg>
            </span>
            <h3>Manual Registration</h3>
            <p>Capture the patient record on their behalf when illness or urgency makes self-registration impractical.</p>
        </a>
    </div>
</div>
@endif



@if(in_array($currentMode, ['scan', 'assisted', 'applicant'], true))
<div class="{{ $currentMode === 'assisted' ? '' : 'card p-4 shadow-sm walkin-strip-card' }}" style="{{ $currentMode === 'assisted' ? 'max-width: 1180px; margin: 20px auto; padding: 0; background: transparent; box-shadow: none; border: none;' : 'border-radius: 15px; border: none; max-width: 550px; margin: 20px auto;' }}">
    
    @if($currentMode !== 'assisted')
    <div id="dynamicHeader" class="mode-header bg-scan">
        <div id="headerIcon" class="mode-header-badge">{{ $currentMode === 'applicant' ? 'AP' : 'SB' }}</div>
        <div class="mode-header-copy">
            <h3 id="headerTitle" style="margin: 0; font-weight: 800; text-transform: uppercase; font-size: 1rem; letter-spacing: 1px;">
                {{ $currentMode === 'applicant' ? 'Applicant Scan Ready' : 'OCR Ready' }}
            </h3>
            <p id="headerSubtitle">
                {{ $currentMode === 'applicant' ? 'Choose OCR scanning or manual ID entry to identify the applicant record.' : 'Choose OCR scanning or manual ID entry to identify the patient.' }}
            </p>
        </div>
    </div>
    @endif

    <div id="scanForm" style="{{ $currentMode === 'assisted' ? 'display:none;' : '' }}">
        <div id="scanStage" class="scan-stage">
            <div class="scan-method-bar">
                <div>
                <p id="scanMethodTitle" class="scan-method-title">OCR ID Scan</p>
                <p id="scanMethodNote" class="scan-method-note">Use the camera to extract the student number from the physical ID card, or enter it manually.</p>
                <span id="scanMethodBadge" class="scan-method-badge">OCR Active</span>
                </div>
                <button type="button" id="btnSwitchScanMode" class="btn-scan-switch" style="display:none;">OCR Scan Active</button>
            </div>

            <div id="scanner-container-scan" class="scan-surface" style="position: relative;">
                <p id="scanInlineNote" class="scan-inline-note">OCR mode is active. Align the physical ID inside the frame, or type the student number manually.</p>
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
                        <button type="button" id="btnRunAiOcr" class="btn-ocr btn-ocr-primary" style="background:linear-gradient(135deg, #1d4ed8, #2563eb 55%, #3b82f6); box-shadow:0 12px 24px rgba(37,99,235,0.22);">Reading ID Number</button>
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
                                <h3>Clinic Patient Registration</h3>
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
            
            <button type="button" id="confirmBtn" class="assisted-submit-btn mt-2">
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
    let scanMethod = 'ocr';
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
            return intakeTarget === 'assessment' ? 'applicant record' : 'consultation form';
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
            return true;
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
                    $('#btnRunAiOcr').prop('disabled', false).text('Reading ID Number');
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
            scanMethod = 'ocr';
            const isApplicantFlow = intakeTarget === 'assessment';
            $('#scanMethodTitle').text('OCR ID Scan');
            $('#scanMethodNote').text(
                isApplicantFlow
                        ? 'Use the live camera feed to extract the applicant student number from the ID card, then review the applicant record.'
                        : 'Use the live camera feed to extract the printed student number from the physical ID card, then fill the saved name from records.'
            );
            $('#scanMethodBadge').text('OCR Active');
            $('#btnSwitchScanMode').hide();
            $('#btnSwitchScanMode span').text('OCR Scan Active');
            $('#headerTitle').text('OCR Ready');
            $('#headerSubtitle').text(
                isApplicantFlow
                        ? 'Choose OCR ID scanning or manual ID entry to identify the applicant record.'
                        : ''
            );
            $('#headerIcon').text(isApplicantFlow ? 'AP' : 'SB');
            $('#scanInlineNote').text(
                isApplicantFlow
                        ? 'OCR mode is active. Align the physical ID inside the frame and continue once student number and name are matched.'
                        : 'OCR mode is active. Align the physical ID inside the frame and the system will keep reading the student number live, then match the saved name automatically.'
            );
            $('#barcodeScanPanel').show();
            $('#btnShowManual').show();
            $('#manualInputArea').toggle($('#manualInputArea').is(':visible'));
            const keepResultPanelVisible = intakeTarget === 'assessment';
            $('#ocrResultPanel').toggle($('#ocrResultPanel').is(':visible') || keepResultPanelVisible);
            $('#applicantOcrReviewPanel').show();
        }

        function openIntakeScanModal(target = 'consultation') {
            intakeTarget = target;
            scanMethod = 'ocr';
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
                // Start camera AFTER modal is visible
                setTimeout(() => {
                    startMainScanner();
                }, 100);
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
            scanMethod = 'ocr';
            updateScanModeUI();
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
            const form = document.getElementById('formRegisterStudent');
            const role = $('#reg_user_type').val();
            const email = $('#reg_email').val().trim();

            if(!role) { alert("Please select a User Role!"); return; }

            if (!email) {
                $('#reg_email')[0].reportValidity();
                return;
            }

            if (form && !form.checkValidity()) {
                form.reportValidity();
                return;
            }

            $(this).prop('disabled', true).text('PROCESSING...');
            
            const formData = {
                _token: "{{ csrf_token() }}",
                role: role,
                user_role: role,
                user_type: role,
                student_number: $('#reg_student_id').val(),
                first_name: $('#reg_first_name').val(),
                last_name: $('#reg_last_name').val(),
                email: email,
                dob: $('#reg_dob').val(),
                gender: $('#reg_gender').val(),
                contact_no: $('#reg_contact_no').val(),
                barcode: $('#reg_barcode').val() || $('#reg_student_id').val()
            };

            $.ajax({
                url: "{{ url($basePrefix . '/walkin/register') }}",
                method: 'POST',
                data: formData,
                headers: {
                    Accept: 'application/json'
                }
            }).done(function(res) {
                if(res.redirect_url) window.location.href = res.redirect_url;
                else window.location.reload();
            }).fail(function(xhr) {
                $('#confirmBtn').prop('disabled', false).text('SAVE ASSISTED INTAKE');
                let errorMsg = "Assisted intake failed.";
                if(xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors)[0][0];
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.status === 419) {
                    errorMsg = "Your session expired. Please refresh the page and try again.";
                } else if (xhr.status === 409) {
                    errorMsg = "This account already exists.";
                }
                $('#notification').html(`<p style="color:#991b1b; font-size:12px; font-weight:bold; background:#fee2e2; padding:10px; border-radius:8px; border:1px solid #fecaca;">${$('<div>').text(errorMsg).html()}</p>`);
            });
        });
    });

    // --- Applicants Modal: Reference Number Lookup ---
    (function () {
        const backdrop        = document.getElementById('applicantRefModal');
        const openBtn         = document.getElementById('openApplicantRefModal');
        const closeBtn        = document.getElementById('closeApplicantRefModal');
        const defaultPane     = document.getElementById('applicantRefDefault');
        const entryPane       = document.getElementById('applicantRefEntry');
        const showEntryBtn    = document.getElementById('btnShowApplicantRefInput');
        const cancelEntryBtn  = document.getElementById('btnCancelApplicantRef');
        const refInput        = document.getElementById('applicantRefInput');
        const refStatus       = document.getElementById('applicantRefStatus');
        const findBtn         = document.getElementById('btnFindApplicant');
        const foundCard       = document.getElementById('applicantFoundCard');
        const foundName       = document.getElementById('applicantFoundName');
        const lookupDetails   = document.getElementById('applicantLookupDetails');
        const lookupRef       = document.getElementById('applicantLookupRef');
        const lookupStudentId = document.getElementById('applicantLookupStudentId');
        const lookupCourse    = document.getElementById('applicantLookupCourse');
        const lookupYearSec   = document.getElementById('applicantLookupYearSection');
        const lookupDob       = document.getElementById('applicantLookupDob');
        const lookupEmail     = document.getElementById('applicantLookupEmail');
        const uploadForm      = document.getElementById('applicantAssessmentUploadForm');
        const uploadInput     = document.getElementById('applicantAssessmentUploadInput');
        const uploadButton    = document.getElementById('btnUploadAssessmentCopy');
        const uploadRefInput  = document.getElementById('applicantAssessmentReferenceNumber');
        const uploadStudentNo = document.getElementById('applicantAssessmentStudentNumber');
        let currentLookupRef  = '';
        const getStudentUrl   = '{{ url($basePrefix . '/walkin/get-student') }}';

        function setEntryMode(isActive) {
            if (defaultPane) defaultPane.style.display = isActive ? 'none' : 'flex';
            if (entryPane) entryPane.classList.toggle('is-visible', isActive);
            if (!isActive) resetLookupState();
            if (isActive && refInput) {
                setTimeout(() => refInput.focus(), 0);
            }
        }

        function resetLookupState() {
            if (refStatus) { refStatus.className = 'ocr-status'; refStatus.textContent = ''; }
            if (foundCard) foundCard.style.display = 'none';
            if (foundName) foundName.textContent = '';
            if (lookupDetails) lookupDetails.style.display = 'none';
            if (uploadForm) uploadForm.style.display = 'none';
            if (uploadRefInput) uploadRefInput.value = '';
            if (uploadStudentNo) uploadStudentNo.value = '';
            if (uploadInput) uploadInput.value = '';
            currentLookupRef = '';
        }

        function openApplicantsModal() {
            if (!backdrop) return;
            backdrop.classList.add('show');
            setEntryMode(false);
            if (refInput) refInput.value = '';
        }

        function closeApplicantsModal() {
            if (backdrop) backdrop.classList.remove('show');
            setEntryMode(false);
            if (refInput) refInput.value = '';
        }

        function setStatus(type, msg) {
            if (!refStatus) return;
            refStatus.className = 'ocr-status ' + type;
            refStatus.textContent = msg;
        }

        function showLookupDetails(data, fallbackRef) {
            if (!lookupDetails) {
                return;
            }

            const studentNumber = data.student_number || data.student_id || fallbackRef || '-';
            const yearSection = [data.year || '', data.section || ''].filter(Boolean).join(' / ') || 'N/A';

            if (lookupRef) lookupRef.textContent = data.student_number || fallbackRef || '-';
            if (lookupStudentId) lookupStudentId.textContent = data.student_id || 'N/A';
            if (lookupCourse) lookupCourse.textContent = data.course || 'N/A';
            if (lookupYearSec) lookupYearSec.textContent = yearSection;
            if (lookupDob) lookupDob.textContent = data.dob || 'N/A';
            if (lookupEmail) lookupEmail.textContent = data.email || 'N/A';

            lookupDetails.style.display = 'block';
            if (uploadForm) uploadForm.style.display = 'grid';
            if (uploadRefInput) uploadRefInput.value = data.student_number || fallbackRef || '';
            if (uploadStudentNo) uploadStudentNo.value = data.student_id || studentNumber || '';
        }

        function uploadAssessmentCopy(file) {
            if (!uploadForm || !uploadRefInput || !file) {
                return;
            }

            const formData = new FormData(uploadForm);
            formData.set('medical_assessment_copy', file);
            formData.set('reference_number', uploadRefInput.value || currentLookupRef || '');
            formData.set('student_number', uploadStudentNo ? uploadStudentNo.value : '');

            setStatus('info', 'Uploading medical assessment copy...');

            fetch("{{ route('admin.medical_assessment_upload') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async function (response) {
                const payload = await response.json().catch(function () { return {}; });
                if (!response.ok) {
                    throw new Error(payload.message || 'Upload failed.');
                }

                setStatus('success', payload.message || 'Medical assessment copy uploaded successfully.');
                if (uploadInput) uploadInput.value = '';
                return payload;
            })
            .catch(function () {
                setStatus('error', 'Unable to upload right now. Please try again.');
            });
        }

        function doLookup() {
            const ref = (refInput ? refInput.value : '').trim();
            if (!ref) {
                setStatus('error', 'Please enter a reference number first.');
                return;
            }

            setStatus('info', 'Looking up applicant...');
            if (foundCard) foundCard.style.display = 'none';

            fetch(getStudentUrl + '?student_id=' + encodeURIComponent(ref) + '&preview_only=true', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'preview' || data.status === 'found') {
                    const applicantName = data.student_name || '';
                    currentLookupRef = data.student_number || ref;
                    setStatus('success', applicantName ? 'Applicant found: ' + applicantName + '.' : 'Applicant found.');
                    if (foundCard && foundName) {
                        foundName.textContent = applicantName || ref;
                        foundCard.style.display = 'block';
                    }
                    showLookupDetails(data, ref);
                } else {
                    setStatus('error', data.message || 'No applicant found with that reference number.');
                }
            })
            .catch(() => setStatus('error', 'Unable to look up right now. Please try again.'));
        }

        if (openBtn) openBtn.addEventListener('click', function (e) { e.preventDefault(); openApplicantsModal(); });
        if (closeBtn) closeBtn.addEventListener('click', closeApplicantsModal);
        if (backdrop) backdrop.addEventListener('click', function (e) { if (e.target === backdrop) closeApplicantsModal(); });
        if (showEntryBtn) showEntryBtn.addEventListener('click', function () { setEntryMode(true); });
        if (cancelEntryBtn) cancelEntryBtn.addEventListener('click', function () {
            if (refInput) refInput.value = '';
            setEntryMode(false);
        });
        if (findBtn) findBtn.addEventListener('click', doLookup);
        if (uploadButton && uploadInput) {
            uploadButton.addEventListener('click', function () {
                if (!currentLookupRef) {
                    setStatus('error', 'Find the applicant first before uploading a copy.');
                    return;
                }

                uploadInput.click();
            });

            uploadInput.addEventListener('change', function () {
                const file = this.files && this.files[0] ? this.files[0] : null;
                if (file) {
                    uploadAssessmentCopy(file);
                }
            });
        }
        if (refInput) {
            refInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    doLookup();
                }
            });
        }

    })();

</script>
@endpush
