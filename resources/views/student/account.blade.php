@extends('layouts.student')

@section('title', 'My Account')

@push('styles')
<style>
    /* --- HERO PROFILE SECTION --- */
    .profile-hero {
        background: linear-gradient(135deg, #8B0000 0%, #5a0f15 100%);
        border-radius: 16px;
        padding: 40px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 10px 20px rgba(139, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 30px;
        flex-wrap: wrap;
    }

    .hero-avatar {
        width: 100px;
        height: 100px;
        background: rgba(255,255,255,0.2);
        border: 2px solid rgba(255,255,255,0.4);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        font-weight: 800;
        color: #fff;
        overflow: hidden;
    }

    .hero-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .hero-info { flex: 1; }
    .hero-name { font-size: 32px; font-weight: 800; margin: 0; line-height: 1.2; color: white; }
    .hero-course { font-size: 16px; opacity: 0.9; margin-top: 5px; font-weight: 500; }
    .hero-badge {
        background: #ffc107;
        color: #70131B;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        margin-left: 10px;
        vertical-align: middle;
    }

    /* Stats Row inside Hero */
    .hero-stats {
        display: flex;
        gap: 40px;
        margin-top: 10px;
        padding-top: 20px;
        border-top: 1px solid rgba(255,255,255,0.2);
    }
    .stat-item { text-align: left; }
    .stat-val { font-size: 24px; font-weight: 700; display: block; }
    .stat-label { font-size: 12px; text-transform: uppercase; opacity: 0.8; letter-spacing: 0.5px; }

    /* --- LAYOUT GRID --- */
    .account-layout {
        max-width: 960px;
        margin: 0 auto;
    }

    .page-intro {
        margin-bottom: 18px;
    }

    .page-intro-title {
        margin: 0;
        font-size: 28px;
        color: #600000;
        font-weight: 800;
    }

    .page-intro-text {
        margin: 6px 0 0;
        font-size: 14px;
        color: #6b7b7d;
        line-height: 1.5;
    }

    /* --- APPOINTMENT CARDS --- */
    .section-title {
        color: #20343a;
        font-size: 20px;
        font-weight: 800;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .appt-card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 16px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
        border-left: 5px solid #cbd5e1;
        transition: transform 0.2s;
    }
    .appt-card:hover { transform: translateY(-3px); }

    .appt-card.approved { border-left-color: #10b981; }
    .appt-card.pending { border-left-color: #f59e0b; }
    .appt-card.cancelled { border-left-color: #ef4444; }
    .appt-card.completed { border-left-color: #3b82f6; }
    .appt-card.missed { border-left-color: #c2410c; }
    .appt-card.expired { border-left-color: #6b7280; }

    .appt-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
    .appt-service { font-size: 18px; font-weight: 700; color: #334155; }
    .appt-date { font-size: 14px; color: #64748b; font-weight: 600; text-align: right; }
    
    .appt-notes {
        background: #f8fafc;
        padding: 12px;
        border-radius: 8px;
        font-size: 14px;
        color: #475569;
        margin-bottom: 15px;
        border: 1px dashed #cbd5e1;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .status-badge.approved { background: #dcfce7; color: #15803d; }
    .status-badge.pending { background: #fffbeb; color: #b45309; }
    .status-badge.cancelled { background: #fee2e2; color: #b91c1c; }
    .status-badge.completed { background: #dbeafe; color: #1e40af; }
    .status-badge.missed { background: #ffedd5; color: #9a3412; }
    .status-badge.expired { background: #f3f4f6; color: #4b5563; }

    .profile-grid-3,
    .profile-grid-2 {
        display: grid;
        gap: 12px;
        margin-bottom: 15px;
    }
    .profile-grid-3,
    .profile-grid-2 { grid-template-columns: 1fr; }

    /* --- SIDEBAR WIDGETS --- */
    .widget-card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
    }

    .profile-form-section {
        position: relative;
        overflow: visible;
        --field-bottom: #8f2230;
        --field-bottom-focus: #f59e0b;
        margin-bottom: 18px;
        padding: 18px 16px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: linear-gradient(180deg, #ffffff 0%, #fffdf6 100%);
    }
    .profile-form-section::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: #70131B;
        opacity: 0.95;
    }
    .profile-form-section.accent-gold::before {
        background: #facc15;
    }
    .profile-form-section.accent-maroon::before {
        background: #70131B;
    }
    .profile-form-section.accent-maroon {
        --field-bottom: #8f2230;
        --field-bottom-focus: #f59e0b;
    }
    .profile-form-section.accent-gold {
        --field-bottom: #d4a60a;
        --field-bottom-focus: #b45309;
    }

    .profile-sections-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        align-items: stretch;
        margin-bottom: 18px;
    }

    .profile-column-stack {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .profile-sections-grid .profile-form-section {
        margin-bottom: 0;
        height: 100%;
        box-shadow:
            0 12px 24px rgba(112, 19, 27, 0.08),
            0 4px 10px rgba(15, 23, 42, 0.06);
    }
    .profile-frame-equal {
        height: 280px;
        display: flex;
        flex-direction: column;
    }

    .profile-frame-equal .profile-grid-3,
    .profile-frame-equal .profile-grid-2 {
        margin-bottom: 10px;
    }

    .profile-frame-equal .profile-info-row:last-child {
        margin-bottom: 0;
    }

    .profile-form-section-title {
        margin: 4px 0 14px;
        font-size: 15px;
        font-weight: 900;
        letter-spacing: 0.02em;
        color: #70131B;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }
    .profile-form-section-title svg {
        width: 16px;
        height: 16px;
        flex: 0 0 auto;
    }

    .profile-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .profile-card-heading {
        min-width: 0;
    }
    .profile-card-title {
        margin: 0;
        font-size: 28px;
        color: #600000;
        font-weight: 800;
        line-height: 1.1;
    }

    .profile-card-description {
        margin: 6px 0 0;
        font-size: 14px;
        color: #6b7b7d;
        line-height: 1.5;
    }

    .profile-edit-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        position: relative;
        overflow: hidden;
        white-space: nowrap;
        padding: 11px 18px;
        border: 1px solid #8f2230;
        border-radius: 999px;
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #ffffff;
        font-size: 14px;
        font-weight: 800;
        letter-spacing: 0.01em;
        cursor: pointer;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20);
        transition: color .08s linear, transform .18s ease, box-shadow .18s ease, background .18s ease;
        z-index: 0;
    }

    .profile-edit-btn::after {
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

    .profile-edit-btn:hover {
        transform: translateY(-1px);
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16);
        color: #ffffff;
        background: linear-gradient(135deg, #70131B, #8f2230);
    }

    .profile-edit-btn:hover::after {
        transform: translateX(135%);
    }

    #profileActionBar {
        margin-top: 14px;
    }

    #saveAction {
        display: none;
        gap: 10px;
        justify-content: flex-end;
        align-items: center;
    }

    .profile-action-btn {
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        position: relative;
        overflow: hidden;
        white-space: nowrap;
        padding: 11px 18px;
        border-radius: 999px;
        border: 1px solid #8f2230;
        color: #ffffff;
        font-size: 14px;
        font-weight: 800;
        letter-spacing: 0.01em;
        cursor: pointer;
        transition: color .08s linear, transform .18s ease, box-shadow .18s ease, background .18s ease;
        z-index: 0;
    }

    .profile-action-btn::after {
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

    .profile-action-btn:hover {
        transform: translateY(-1px);
    }

    .profile-action-btn:hover::after {
        transform: translateX(135%);
    }

    .profile-action-btn.save {
        background: linear-gradient(135deg, #70131B, #8f2230);
        border-color: #8f2230;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20);
    }

    .profile-action-btn.save:hover {
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16);
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #ffffff;
    }

    .profile-action-btn.cancel {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        border-color: #6b7280;
        box-shadow:
            0 0 0 3px rgba(100, 116, 139, 0.16),
            0 10px 22px rgba(15, 23, 42, 0.18);
        color: #ffffff;
    }

    .profile-action-btn.cancel:hover {
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(15, 23, 42, 0.22);
        background: linear-gradient(135deg, #4b5563, #374151);
        color: #ffffff;
    }

    .input-label { font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: 6px; text-transform: uppercase; display: block; }

    .profile-grid-3 > div,
    .profile-grid-2 > div,
    .profile-info-row {
        display: grid;
        grid-template-columns: minmax(150px, 42%) minmax(0, 1fr);
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: linear-gradient(180deg, #ffffff 0%, #fffdf6 100%);
        transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
    }

    .profile-grid-3 > div.is-editing,
    .profile-grid-2 > div.is-editing,
    .profile-info-row.is-editing {
        border-color: rgba(250, 204, 21, 0.95);
        background: linear-gradient(180deg, #fffbea 0%, #fff7d1 100%);
        box-shadow: 0 0 0 3px rgba(250, 204, 21, 0.22);
    }

    .profile-info-row {
        margin-bottom: 12px;
    }

    .profile-grid-3 > div .input-label,
    .profile-grid-2 > div .input-label,
    .profile-info-row .input-label {
        margin: 0;
        font-size: 14px;
        font-weight: 800;
        letter-spacing: 0.01em;
        text-transform: none;
        color: #111827;
    }
    
    .form-control { 
        width: 100%; 
        padding: 10px 14px; 
        border: 1px solid #cbd5e1; 
        border-radius: 8px; 
        font-size: 14px; 
        color: #334155; 
        transition: 0.2s; 
        background: #fff; 
    }
    .profile-grid-3 > div .form-control,
    .profile-grid-2 > div .form-control,
    .profile-info-row .form-control {
        font-size: 15px;
        color: #111827;
        text-align: right;
        font-weight: 700;
        letter-spacing: 0.01em;
        min-height: 44px;
        padding: 10px 16px;
        border: none !important;
        border-bottom: 2px solid var(--field-bottom) !important;
        border-radius: 12px 12px 10px 10px;
        background: rgba(255, 255, 255, 0.55);
        box-shadow: none !important;
        transition: border-color 0.18s ease, background 0.18s ease, transform 0.18s ease;
    }
    .metric-field {
        position: relative;
        display: flex;
        align-items: center;
    }
    .metric-field .form-control {
        padding-right: 16px;
        text-align: right !important;
        border-bottom-color: var(--field-bottom) !important;
    }
    .metric-field .form-control:focus {
        border-bottom-color: var(--field-bottom-focus) !important;
        background: rgba(255, 251, 235, 0.85);
        transform: translateY(-1px);
    }
    .soft-field .form-control {
        border-bottom-color: var(--field-bottom) !important;
    }
    .soft-field .form-control:focus {
        border-bottom-color: var(--field-bottom-focus) !important;
        background: rgba(255, 251, 235, 0.85);
        transform: translateY(-1px);
    }
    .widget-card .voice-field-wrap input[type="text"],
    .widget-card .voice-field-wrap input[type="email"],
    .widget-card .voice-field-wrap input[type="tel"],
    .widget-card .voice-field-wrap input[type="number"],
    .widget-card .voice-field-wrap input[type="search"],
    .widget-card .voice-field-wrap input:not([type]),
    .widget-card .voice-field-wrap textarea {
        padding-right: 16px !important;
        padding-left: 44px !important;
    }
    .widget-card .voice-field-inline-mic {
        left: 10px !important;
        right: auto !important;
    }
    .profile-info-row textarea.form-control {
        text-align: left;
        min-height: 44px;
        resize: none;
    }
    .profile-grid-3 > div .form-control:disabled,
    .profile-grid-2 > div .form-control:disabled,
    .profile-info-row .form-control:disabled {
        color: #111827;
        background: #fffef7;
        border-bottom-color: var(--field-bottom) !important;
        opacity: 1;
    }
    .profile-grid-3 > div .form-control:focus,
    .profile-grid-2 > div .form-control:focus,
    .profile-info-row .form-control:focus {
        border-bottom-color: var(--field-bottom-focus) !important;
        background: rgba(255, 251, 235, 0.85);
        transform: translateY(-1px);
    }
    .form-control:focus { border-color: #8B0000; box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.05); outline: none; }
    .form-control:disabled { background: #f8fafc; color: #94a3b8; cursor: not-allowed; }

    html[data-theme="dark"] .profile-grid-3 > div,
    html[data-theme="dark"] .profile-grid-2 > div,
    html[data-theme="dark"] .profile-info-row {
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.94) 100%);
        border-color: rgba(148, 163, 184, 0.3);
    }
    html[data-theme="dark"] .profile-grid-3 > div.is-editing,
    html[data-theme="dark"] .profile-grid-2 > div.is-editing,
    html[data-theme="dark"] .profile-info-row.is-editing {
        border-color: rgba(250, 204, 21, 0.62);
        background: linear-gradient(180deg, rgba(133, 77, 14, 0.35) 0%, rgba(146, 64, 14, 0.24) 100%);
        box-shadow: 0 0 0 3px rgba(250, 204, 21, 0.18);
    }
    html[data-theme="dark"] .profile-form-section {
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.92) 0%, rgba(30, 41, 59, 0.9) 100%);
        border-color: rgba(148, 163, 184, 0.3);
    }
    html[data-theme="dark"] .profile-form-section.accent-maroon {
        --field-bottom: #fca5a5;
        --field-bottom-focus: #fde047;
    }
    html[data-theme="dark"] .profile-form-section.accent-gold {
        --field-bottom: #facc15;
        --field-bottom-focus: #fde047;
    }
    html[data-theme="dark"] .profile-sections-grid .profile-form-section {
        box-shadow:
            0 14px 28px rgba(0, 0, 0, 0.28),
            0 4px 14px rgba(250, 204, 21, 0.08);
    }
    html[data-theme="dark"] .profile-form-section-title {
        color: #f8fafc;
    }
    html[data-theme="dark"] .profile-card-title {
        color: #ffffff;
    }
    html[data-theme="dark"] .profile-card-description {
        color: #cbd5e1;
    }
    html[data-theme="dark"] .profile-edit-btn {
        background: linear-gradient(135deg, #70131B, #8f2230);
        border-color: #8f2230;
        color: #ffffff;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 12px 24px rgba(0, 0, 0, 0.28);
    }
    html[data-theme="dark"] .profile-action-btn.save {
        background: linear-gradient(135deg, #70131B, #8f2230);
        border-color: #8f2230;
    }
    html[data-theme="dark"] .profile-action-btn.cancel {
        background: linear-gradient(135deg, #475569, #334155);
        border-color: #64748b;
        color: #ffffff;
    }

    html[data-theme="dark"] .profile-grid-3 > div .input-label,
    html[data-theme="dark"] .profile-grid-2 > div .input-label,
    html[data-theme="dark"] .profile-info-row .input-label,
    html[data-theme="dark"] .profile-grid-3 > div .form-control,
    html[data-theme="dark"] .profile-grid-2 > div .form-control,
    html[data-theme="dark"] .profile-info-row .form-control {
        color: #f8fafc;
    }
    html[data-theme="dark"] .profile-grid-3 > div .form-control:disabled,
    html[data-theme="dark"] .profile-grid-2 > div .form-control:disabled,
    html[data-theme="dark"] .profile-info-row .form-control:disabled {
        background: rgba(30, 41, 59, 0.92);
        border-bottom-color: var(--field-bottom) !important;
        color: #f8fafc;
    }
    html[data-theme="dark"] .profile-grid-3 > div .form-control:focus,
    html[data-theme="dark"] .profile-grid-2 > div .form-control:focus,
    html[data-theme="dark"] .profile-info-row .form-control:focus {
        background: rgba(51, 65, 85, 0.78);
        border-bottom-color: var(--field-bottom-focus) !important;
    }

    /* --- NOTIFICATIONS --- */
    .alert-success { background: #dcfce7; color: #15803d; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 600; border: 1px solid #bbf7d0; }
    .health-submit-overlay {
        position: fixed;
        inset: 0;
        z-index: 1200;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 42, 0.62);
        backdrop-filter: blur(10px);
        animation: overlayFadeIn 0.28s ease;
    }
    .health-submit-overlay.is-hiding {
        animation: overlayFadeOut 0.3s ease forwards;
    }
    .health-submit-overlay-card {
        text-align: center;
        padding: 30px 24px;
        max-width: 420px;
    }
    .health-submit-title {
        font-size: 28px;
        font-weight: 800;
        color: #ffffff;
        margin: 0 0 22px;
        letter-spacing: -0.02em;
    }
    .health-submit-circle {
        width: 132px;
        height: 132px;
        margin: 0 auto 18px;
        border-radius: 999px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        border: 3px solid rgba(255, 255, 255, 0.92);
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.16);
        transform: scale(0.84);
        animation: submitCircleResolve 0.9s ease forwards;
        animation-delay: 0.92s;
    }
    .health-submit-ring {
        position: absolute;
        inset: -9px;
        width: calc(100% + 18px);
        height: calc(100% + 18px);
        transform: rotate(-90deg);
    }
    .health-submit-ring circle {
        fill: none;
        stroke: #16a34a;
        stroke-width: 7;
        stroke-linecap: round;
        stroke-dasharray: 358;
        stroke-dashoffset: 358;
        animation: submitRingDraw 0.9s ease forwards;
    }
    .health-submit-check {
        position: absolute;
        width: 54px;
        height: 54px;
        opacity: 0;
        transform: scale(0.6);
        animation: submitCheckReveal 0.34s ease forwards;
        animation-delay: 1.18s;
    }
    .health-submit-subtext {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.84);
        margin: 0;
    }
    @keyframes overlayFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes overlayFadeOut {
        from { opacity: 1; }
        to { opacity: 0; visibility: hidden; }
    }
    @keyframes submitRingDraw {
        0% {
            stroke-dashoffset: 358;
        }
        100% {
            stroke-dashoffset: 0;
        }
    }
    @keyframes submitCircleResolve {
        0% {
            transform: scale(0.84);
            background: #ffffff;
            border-color: rgba(255, 255, 255, 0.92);
        }
        55% {
            transform: scale(1.04);
            background: #22c55e;
            border-color: #22c55e;
        }
        100% {
            transform: scale(1);
            background: #16a34a;
            border-color: #16a34a;
        }
    }
    @keyframes submitCheckReveal {
        0% {
            opacity: 0;
            transform: scale(0.6);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }
    .notif-item { display: flex; gap: 10px; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
    .notif-item:last-child { border-bottom: none; }
    .notif-icon { font-size: 16px; }
    .notif-text { font-size: 13px; color: #334155; line-height: 1.4; }
    .notif-time { display: block; font-size: 11px; color: #94a3b8; margin-top: 4px; }
    .notif-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 14px;
    }
    .notif-panel-title {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: #600000;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .notif-panel-title svg {
        width: 18px;
        height: 18px;
        flex: 0 0 auto;
    }
    .notif-mark-btn {
        border: 1px solid #8f2230;
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #ffffff;
        border-radius: 999px;
        padding: 9px 14px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 20px rgba(112, 19, 27, 0.18);
    }
    .notif-mark-btn:hover {
        transform: translateY(-1px);
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 12px 22px rgba(112, 19, 27, 0.16);
    }
    .notif-mark-btn:disabled {
        cursor: not-allowed;
        opacity: 0.6;
        box-shadow: none;
    }
    .notif-list {
        display: grid;
        gap: 10px;
    }
    .notif-record {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 14px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        text-decoration: none;
        transition: transform 0.16s ease, border-color 0.16s ease, box-shadow 0.16s ease, background 0.16s ease;
    }
    .notif-record:hover {
        transform: translateY(-1px);
        border-color: #f5d0d0;
        box-shadow: 0 10px 20px rgba(112, 19, 27, 0.08);
    }
    .notif-record.is-unread {
        border-color: #f5d0d0;
        background: #fff7f7;
    }
    .notif-record-dot {
        width: 10px;
        height: 10px;
        margin-top: 6px;
        border-radius: 999px;
        background: #cbd5e1;
        flex: 0 0 auto;
    }
    .notif-record.is-unread .notif-record-dot {
        background: #8B0000;
    }
    .notif-record-content {
        flex: 1;
        min-width: 0;
    }
    .notif-record-message {
        display: block;
        font-size: 14px;
        line-height: 1.5;
        color: #1f2937;
        font-weight: 600;
    }
    .notif-record.is-unread .notif-record-message {
        font-weight: 800;
    }
    .notif-record-time {
        display: block;
        margin-top: 5px;
        font-size: 12px;
        color: #64748b;
    }
    .notif-empty {
        padding: 18px;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        color: #64748b;
        text-align: center;
        background: #ffffff;
    }
    html[data-theme="dark"] .notif-panel-title {
        color: #ffffff;
    }
    html[data-theme="dark"] .notif-mark-btn {
        background: linear-gradient(135deg, #70131B, #8f2230);
        border-color: #8f2230;
    }
    html[data-theme="dark"] .notif-record {
        background: rgba(15, 23, 42, 0.9);
        border-color: rgba(148, 163, 184, 0.24);
    }
    html[data-theme="dark"] .notif-record.is-unread {
        background: rgba(127, 29, 45, 0.20);
        border-color: rgba(248, 113, 113, 0.35);
    }
    html[data-theme="dark"] .notif-record-message {
        color: #f8fafc;
    }
    html[data-theme="dark"] .notif-record-time,
    html[data-theme="dark"] .notif-empty {
        color: #cbd5e1;
    }
    html[data-theme="dark"] .notif-empty {
        border-color: rgba(148, 163, 184, 0.3);
        background: rgba(15, 23, 42, 0.72);
    }

    /* --- BARCODE WIDGET --- */
    .barcode-status-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    .barcode-status-card::before {
        content: "";
        position: absolute;
        top: 0; left: 0; width: 4px; height: 100%;
    }
    .barcode-status-card.linked::before { background: #10b981; }
    .barcode-status-card.not-linked::before { background: #f59e0b; }

    .barcode-icon-box { font-size: 24px; margin-bottom: 10px; }
    .barcode-label { font-size: 11px; text-transform: uppercase; font-weight: 800; color: #64748b; letter-spacing: 0.5px; }
    .barcode-value { font-size: 16px; font-weight: 700; color: #1e293b; display: block; margin: 4px 0; }
    .btn-barcode-action { display: inline-block; margin-top: 10px; font-size: 12px; font-weight: 700; text-decoration: none; color: #8B0000; }
    .btn-barcode-action:hover { text-decoration: underline; }

    @media (max-width: 900px) {
        .account-layout { grid-template-columns: 1fr; }
        .hero-stats { gap: 20px; flex-wrap: wrap; }
    }

    @media (max-width: 760px) {
        .profile-hero {
            padding: 24px 18px;
            gap: 18px;
        }
        .hero-name {
            font-size: 24px;
        }
        .profile-grid-3,
        .profile-grid-2 {
            grid-template-columns: 1fr;
        }
        .profile-sections-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .profile-frame-equal {
            height: auto;
            min-height: 0;
        }
        .profile-card-head {
            flex-direction: column;
            align-items: stretch;
        }
        .profile-edit-btn {
            width: 100%;
        }
        #saveAction {
            flex-direction: column;
            align-items: stretch;
        }
        .profile-action-btn {
            width: 100%;
        }
        .profile-column-stack {
            gap: 12px;
        }
        .profile-grid-3 > div,
        .profile-grid-2 > div,
        .profile-info-row {
            grid-template-columns: 1fr;
            gap: 8px;
        }
        .profile-grid-3 > div .form-control,
        .profile-grid-2 > div .form-control,
        .profile-info-row .form-control {
            text-align: left;
        }
    }

  
    /* --- HEALTH PROFILE STATUS WIDGET --- */
    .health-status-card {
        background: var(--card-bg, #fff);
        border-radius: 14px;
        padding: 26px;
        box-shadow: 0 8px 22px rgba(139, 0, 0, 0.1);
        border: 1px solid #fce7e7;
        margin-bottom: 24px;
        color: var(--text-main, #1e293b);
    }
    .health-status-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }
    .health-status-title {
        font-size: 18px;
        font-weight: 800;
        color: #8B0000;
        margin: 0;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        letter-spacing: 0.01em;
    }
    .health-status-title svg {
        width: 20px;
        height: 20px;
        flex: 0 0 auto;
    }
    .health-status-summary {
        margin-bottom: 12px;
    }
    .health-status-state {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }
    .health-status-state svg {
        width: 14px;
        height: 14px;
        flex: 0 0 auto;
    }
    .health-status-state.issued { background: #dcfce7; color: #166534; }
    .health-status-state.pending { background: #fffbeb; color: #92400e; }
    .health-status-state.incomplete { background: #fef2f2; color: #991b1b; }
    .health-status-message {
        font-size: 15px;
        color: var(--text-main, #1e293b);
        margin: 10px 0 0;
        line-height: 1.55;
    }
    .health-status-steps {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
        margin: 14px 0 16px;
    }
    .health-step {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #f8fafc;
        padding: 10px;
        text-align: center;
        color: #64748b;
    }
    .health-step.is-complete {
        border-color: #bbf7d0;
        background: #ecfdf5;
        color: #166534;
    }
    .health-step.is-active {
        border-color: #fde68a;
        background: #fffbeb;
        color: #92400e;
    }
    .health-step svg {
        width: 16px;
        height: 16px;
        display: block;
        margin: 0 auto 5px;
    }
    .health-step-label {
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .health-status-sync {
        margin-top: 10px;
        padding: 12px 14px;
        border-radius: 10px;
        font-size: 13px;
        line-height: 1.5;
        text-align: left;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }
    .health-status-sync svg {
        width: 16px;
        height: 16px;
        flex: 0 0 auto;
        margin-top: 1px;
    }
    .health-status-sync.syncing {
        background: #eff6ff;
        color: #1d4ed8;
    }
    .health-status-sync.synced {
        background: #ecfdf5;
        color: #166534;
    }
    .health-status-sync.failed,
    .health-status-sync.missing {
        background: #fef2f2;
        color: #991b1b;
    }
    .health-status-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }
    .health-status-link {
        font-size: 13px;
        color: #475569;
        text-decoration: none;
        text-align: center;
        padding: 11px 12px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        flex: 1 1 220px;
    }
    .health-status-link svg {
        width: 15px;
        height: 15px;
        flex: 0 0 auto;
    }
    .health-status-note {
        font-size: 13px;
        color: var(--text-light, #64748b);
        margin-top: 12px;
        display: block;
    }
    .btn-print-form {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 12px 14px;
        flex: 1 1 220px;
        background: #8B0000;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 700;
        font-size: 15px;
        transition: 0.3s;
    }
    .btn-print-form svg {
        width: 16px;
        height: 16px;
        flex: 0 0 auto;
    }
    .btn-print-form:hover {
        background: #5a0f15;
        box-shadow: 0 4px 12px rgba(139, 0, 0, 0.2);
        color: white;
    }
    .btn-print-form.approved { background: #059669; }
    .btn-print-form.pending { background: #8f2724; }
    .btn-print-form.incomplete { background: #800000; }
    .btn-print-form.disabled {
        background: #dd4b4b;
        cursor: not-allowed;
        font-size: 13px;
        opacity: 0.85;
    }
    html[data-theme="dark"] .health-status-card {
        border-color: var(--border);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.35);
    }
    html[data-theme="dark"] .health-status-title {
        color: #fca5a5;
    }
    html[data-theme="dark"] .health-step {
        background: rgba(17, 24, 39, 0.72);
        border-color: #374151;
        color: #cbd5e1;
    }
    html[data-theme="dark"] .health-step.is-complete {
        background: rgba(22, 101, 52, 0.2);
        border-color: rgba(34, 197, 94, 0.35);
        color: #bbf7d0;
    }
    html[data-theme="dark"] .health-step.is-active {
        background: rgba(146, 64, 14, 0.24);
        border-color: rgba(250, 204, 21, 0.42);
        color: #fde68a;
    }
    html[data-theme="dark"] .health-status-link {
        background: rgba(30, 41, 59, 0.75);
        border-color: #475569;
        color: #e2e8f0;
    }
    html[data-theme="dark"] .health-status-link,
    html[data-theme="dark"] .health-status-note {
        color: var(--text-light, #a9b4c4);
    }
</style>
@endpush

@section('content')
@php
    $linkedAccessLevel = strtolower(trim((string) optional($linkedAdminProfile)->access_level));
    $linkedRoleLabel = in_array($linkedAccessLevel, ['clinic_staff', 'designee', 'superadmin', 'super_admin'], true)
        ? (str_contains($linkedAccessLevel, 'faculty') ? 'Faculty' : 'Admin')
        : null;
    $accountProfileData = $accountProfileData ?? [];
    $accountView = in_array(($accountView ?? 'profile'), ['profile', 'health-record', 'notifications'], true) ? $accountView : 'profile';
    $showOfficeField = in_array($linkedAccessLevel, ['clinic_staff', 'designee', 'superadmin', 'super_admin', 'faculty'], true) || str_contains($linkedAccessLevel, 'faculty');
    $displayStudentNumber = trim((string) ($accountProfileData['student_number'] ?? $user->student_number ?? ''));
    $displayCourse = trim((string) ($accountProfileData['course_college'] ?? $user->course ?? ''));
    $heightRaw = old('height', $accountProfileData['height'] ?? $user->height ?? '');
    $weightRaw = old('weight', $accountProfileData['weight'] ?? $user->weight ?? '');
    preg_match('/\d+(?:\.\d+)?/', (string) $heightRaw, $heightMatch);
    preg_match('/\d+(?:\.\d+)?/', (string) $weightRaw, $weightMatch);
    $heightDisplay = $heightMatch[0] ?? trim((string) $heightRaw);
    $weightDisplay = $weightMatch[0] ?? trim((string) $weightRaw);
@endphp
<div class="container" style="padding: 0 20px 40px;">

    @if(session('health_profile_submitted'))
        <div class="health-submit-overlay" id="healthSubmitOverlay">
            <div class="health-submit-overlay-card">
                <h2 class="health-submit-title">Health Profile Submitted</h2>
                <div class="health-submit-circle" aria-hidden="true">
                    <svg class="health-submit-ring" viewBox="0 0 120 120" aria-hidden="true">
                        <circle cx="60" cy="60" r="54"></circle>
                    </svg>
                    <svg class="health-submit-check" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 6L9 17l-5-5"></path>
                    </svg>
                </div>
                <p class="health-submit-subtext">Your record has been received and added to your account.</p>
            </div>
        </div>
    @endif

    @if(session('success') && !session('health_profile_submitted'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div style="background:#fee2e2; color:#b91c1c; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center; font-size:14px; border:1px solid #fecaca;">
            {{ $errors->first() }}
        </div>
    @endif

    @if($accountView === 'profile')
    <div class="profile-hero">
        <div class="hero-avatar">
            @php
                $healthProfile = \App\Models\HealthProfile::where('user_id', $user->id)->first();
            @endphp
            @if(!empty($healthProfile?->student_photo))
                <img src="{{ asset('storage/' . $healthProfile->student_photo) }}" alt="Student 2x2 Picture">
            @else
                {{ strtoupper(substr($user->name, 0, 1)) }}
            @endif
        </div>
        <div class="hero-info">
            <h1 class="hero-name">{{ $user->name }} <span class="hero-badge">Active</span></h1>
            <div class="hero-course" @if($linkedRoleLabel) style="display: none;" @endif>
                {{ $user->student_number }} • {{ $user->course ?? 'BS Information Technology' }}
            </div>
            @if($linkedRoleLabel)
                <div class="hero-course">
                    {{ $user->student_number }} - {{ $linkedRoleLabel }}
                </div>
            @endif

            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-val">{{ $pendingCount ?? 0 }}</span>
                    <span class="stat-label">Pending</span>
                </div>
                <div class="stat-item">
                    <span class="stat-val">{{ $approvedCount ?? 0 }}</span>
                    <span class="stat-label">Approved</span>
                </div>
                <div class="stat-item">
                    <span class="stat-val">{{ $completedCount ?? 0 }}</span>
                    <span class="stat-label">Completed</span>
                </div>
                <div class="stat-item">
                    <span class="stat-val">{{ $cancelledCount ?? 0 }}</span>
                    <span class="stat-label">Cancelled</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="account-layout">
        @if(session('health_profile_submitted'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('healthSubmitOverlay');
    if (!overlay) {
        return;
    }

    window.setTimeout(() => {
        overlay.classList.add('is-hiding');
        window.setTimeout(() => overlay.remove(), 320);
    }, 3500);
});
</script>
@endif

@if($accountView === 'profile')
{{-- Full Profile Widget --}}
            <div class="widget-card">
    <div class="profile-card-head">
        <div class="profile-card-heading">
            <h1 class="profile-card-title">Personal Information</h1>
            <p class="profile-card-description">Review your personal account details and keep your clinic information up to date.</p>
        </div>
        <button type="button" id="editBtn" class="profile-edit-btn" onclick="enableEditing()">Edit Profile</button>
    </div>
    
    <form action="{{ route('student.updateContact') }}" method="POST">
        @csrf
        @if(!empty($linkedAdminProfile))
            <input type="hidden" name="admin_profile_id" value="{{ $linkedAdminProfile->admin_id }}">
        @endif
        
        @if(empty($linkedAdminProfile))
            <div class="profile-sections-grid">
                <div class="profile-column-stack">
                    <section class="profile-form-section accent-maroon profile-frame-equal">
                        <h3 class="profile-form-section-title"><x-outline-icon name="document-text" />Academic Information</h3>
                        <div class="profile-grid-3">
                            <div>
                                <label class="input-label">Course</label>
                                <input type="text" class="form-control" value="{{ $accountProfileData['course_college'] ?? $user->course }}" readonly style="background-color: #f8fafc;">
                            </div>
                            <div>
                                <label class="input-label">Year</label>
                                <input type="text" name="year" class="form-control" value="{{ old('year', $user->year) }}" disabled>
                            </div>
                            <div>
                                <label class="input-label">Section</label>
                                <input type="text" name="section" class="form-control" value="{{ old('section', $user->section) }}" disabled>
                            </div>
                        </div>
                    </section>

                    <section class="profile-form-section accent-gold">
                        <h3 class="profile-form-section-title"><x-outline-icon name="information-circle" />Personal Details</h3>
                        <div class="profile-grid-2">
                            <div>
                                <label class="input-label">Gender</label>
                                <input type="text" class="form-control" value="{{ $accountProfileData['sex'] ?? $user->gender }}" readonly style="background-color: #f8fafc;">
                            </div>
                            <div>
                                <label class="input-label">Birthday (DOB)</label>
                                <input type="text" class="form-control" value="{{ $accountProfileData['birthday'] ?? $user->DOB }}" readonly style="background-color: #f8fafc;">
                            </div>
                        </div>
                        <div class="profile-grid-2">
                            <div>
                                <label class="input-label">Height (cm)</label>
                                <div class="metric-field">
                                    <input type="text" name="height" class="form-control editable-input" inputmode="decimal" value="{{ $heightDisplay }}" disabled>
                                </div>
                            </div>
                            <div>
                                <label class="input-label">Weight (kg)</label>
                                <div class="metric-field">
                                    <input type="text" name="weight" class="form-control editable-input" inputmode="decimal" value="{{ $weightDisplay }}" disabled>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="profile-column-stack">
                    <section class="profile-form-section accent-maroon profile-frame-equal">
                        <h3 class="profile-form-section-title"><x-outline-icon name="clock" />Contact Information</h3>
                        <div class="profile-info-row">
                            <label class="input-label">Contact Number</label>
                            <input type="text" name="contact_no" class="form-control" value="{{ old('contact_no', $user->contact_no) }}" disabled>
                        </div>
                        <div class="profile-info-row">
                            <label class="input-label">Address</label>
                            <textarea class="form-control" rows="2" disabled>{{ old('home_address', $accountProfileData['home_address'] ?? '') }}</textarea>
                        </div>
                    </section>

                    <section class="profile-form-section accent-gold">
                        <h3 class="profile-form-section-title"><x-outline-icon name="exclamation-triangle" />Emergency Contact</h3>
                        <div class="profile-grid-2">
                            <div class="soft-field">
                                <label class="input-label">Emergency Contact Person</label>
                                <input type="text" name="guardian_name" class="form-control editable-input" value="{{ old('guardian_name', $accountProfileData['guardian_name'] ?? '') }}" disabled>
                            </div>
                            <div class="soft-field">
                                <label class="input-label">Emergency Contact Number</label>
                                <input type="text" name="cellphone" class="form-control editable-input" value="{{ old('cellphone', $accountProfileData['cellphone'] ?? '') }}" disabled>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        @endif

        @if(!empty($linkedAdminProfile))
            <div class="profile-sections-grid">
            <section class="profile-form-section accent-maroon">
                <h3 class="profile-form-section-title"><x-outline-icon name="information-circle" />Personal Information</h3>
                <div class="profile-grid-2">
                    <div>
                        <label class="input-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $accountProfileData['first_name'] ?? $linkedAdminProfile->first_name) }}" disabled>
                    </div>
                    <div>
                        <label class="input-label">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $accountProfileData['middle_name'] ?? $linkedAdminProfile->middle_name) }}" disabled>
                    </div>
                </div>

                <div class="profile-grid-2">
                    <div>
                        <label class="input-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $accountProfileData['last_name'] ?? $linkedAdminProfile->last_name) }}" disabled>
                    </div>
                    <div>
                        <label class="input-label">Suffix Name</label>
                        <input type="text" name="suffix_name" class="form-control" value="{{ old('suffix_name', $accountProfileData['suffix_name'] ?? $linkedAdminProfile->suffix_name) }}" disabled>
                    </div>
                </div>

                <div class="profile-grid-2">
                    <div>
                        <label class="input-label">Birthday</label>
                        <input type="date" name="birthday" class="form-control" value="{{ old('birthday', $accountProfileData['birthday'] ?? $linkedAdminProfile->birthday) }}" disabled>
                    </div>
                    <div>
                        <label class="input-label">Gender</label>
                        <input type="text" name="gender" class="form-control" value="{{ old('gender', $accountProfileData['sex'] ?? $linkedAdminProfile->gender) }}" disabled>
                    </div>
                </div>

                <div class="profile-grid-2">
                    <div>
                        <label class="input-label">Age</label>
                        <input type="number" name="age" class="form-control" value="{{ old('age', $accountProfileData['age'] ?? $linkedAdminProfile->age) }}" disabled>
                    </div>
                    <div>
                        <label class="input-label">Civil Status</label>
                        <input type="text" name="civil_status" class="form-control editable-input" value="{{ old('civil_status', $accountProfileData['civil_status'] ?? $linkedAdminProfile->civil_status) }}" disabled>
                    </div>
                </div>
            </section>

            <section class="profile-form-section accent-gold">
                <h3 class="profile-form-section-title"><x-outline-icon name="clock" />Contact Information</h3>
                <div class="profile-grid-2">
                    <div>
                        <label class="input-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $accountProfileData['email'] ?? $linkedAdminProfile->email) }}" disabled>
                    </div>
                    <div>
                        <label class="input-label">Contact Number</label>
                        <input type="text" name="contact_no" class="form-control editable-input" value="{{ old('contact_no', $accountProfileData['contact_number'] ?? $user->contact_no) }}" disabled>
                    </div>
                </div>
                <div class="profile-info-row">
                    <label class="input-label">Address</label>
                    <textarea name="address" class="form-control editable-input" rows="2" disabled>{{ old('address', $accountProfileData['home_address'] ?? $linkedAdminProfile->address) }}</textarea>
                </div>

                <div class="profile-grid-2">
                    <div class="soft-field">
                        <label class="input-label">Emergency Contact Person</label>
                        <input type="text" name="emergency_contact_person" class="form-control editable-input" value="{{ old('emergency_contact_person', $accountProfileData['guardian_name'] ?? $linkedAdminProfile->emergency_contact_person) }}" disabled>
                    </div>
                    <div class="soft-field">
                        <label class="input-label">Emergency Contact Number</label>
                        <input type="text" name="emergency_contact_no" class="form-control editable-input" value="{{ old('emergency_contact_no', $accountProfileData['cellphone'] ?? $linkedAdminProfile->emergency_contact_no) }}" disabled>
                    </div>
                </div>

                @if($showOfficeField)
                <div class="profile-grid-2">
                    <div>
                        <label class="input-label">Office</label>
                        <input type="text" name="office" class="form-control editable-input" value="{{ old('office', $accountProfileData['office'] ?? $linkedAdminProfile->office) }}" disabled>
                    </div>
                </div>
                @endif
            </section>
            </div>
        @endif

        <div id="profileActionBar">
            <div id="saveAction">
                <button type="submit" class="profile-action-btn save">
                    Save Changes
                </button>
                <button type="button" class="profile-action-btn cancel" onclick="window.location.reload()">
                    Cancel
                </button>
            </div>
        </div>
    </form>
</div>
@elseif($accountView === 'health-record')
    @php
        $healthFormSubmitted = $hasSubmittedHealthProfile ?? ($user->healthProfile !== null);
        $status = $user->healthProfile->clearance_status ?? 'For Verification';
        $statusNormalized = strtolower(trim((string) $status));
        $isIssuedStatus = $statusNormalized === 'issued';
        $isRejectedStatus = $statusNormalized === 'rejected';
        $isPendingStatus = !$isIssuedStatus && !$isRejectedStatus;
        $puptasSyncStatus = optional($user->healthProfile)->puptas_sync_status;
        $puptasSyncMessage = trim((string) optional($user->healthProfile)->puptas_sync_message);
        $puptasSyncedAt = optional(optional($user->healthProfile)->puptas_synced_at)->format('M d, Y g:i A');
    @endphp
    <div class="page-intro">
        <h1 class="page-intro-title">Health Record</h1>
        <p class="page-intro-text">Check the status of your submitted health profile, review clinic approval, and view your uploaded documents.</p>
    </div>
    <div class="health-status-card">
        <div class="health-status-head">
            <span class="health-status-title">
                <x-outline-icon name="clipboard-document-list" />
                Health Information Record
            </span>
        </div>

        <div class="health-status-steps">
            <div class="health-step {{ $healthFormSubmitted ? 'is-complete' : '' }}">
                <x-outline-icon name="document-text" />
                <div class="health-step-label">Submitted</div>
            </div>
            <div class="health-step {{ $healthFormSubmitted ? ($isIssuedStatus ? 'is-complete' : 'is-active') : '' }}">
                <x-outline-icon name="clock" />
                <div class="health-step-label">Verification</div>
            </div>
            <div class="health-step {{ $isIssuedStatus ? 'is-complete' : '' }}">
                <x-outline-icon name="check" />
                <div class="health-step-label">Issued</div>
            </div>
        </div>

        @if($healthFormSubmitted)
            @if($isIssuedStatus)
                <div class="health-status-summary">
                    <span class="health-status-state issued"><x-outline-icon name="check" /> Approved</span>
                    <p class="health-status-message">Your health profile is approved and already available in your health record.</p>
                </div>

                @if($puptasSyncStatus === 'synced')
                    <div class="health-status-sync synced">
                        <x-outline-icon name="check" />
                        <span>
                            <strong>PUPTAS sync complete.</strong>
                            @if($puptasSyncedAt)
                                Synced on {{ $puptasSyncedAt }}.
                            @endif
                        </span>
                    </div>
                @elseif($puptasSyncStatus === 'syncing')
                    <div class="health-status-sync syncing">
                        <x-outline-icon name="clock" />
                        <span>
                            <strong>PUPTAS sync in progress.</strong>
                            {{ $puptasSyncMessage !== '' ? $puptasSyncMessage : 'Your approved clearance is being prepared for PUPTAS sync.' }}
                        </span>
                    </div>
                @elseif($puptasSyncStatus === 'failed')
                    <div class="health-status-sync failed">
                        <x-outline-icon name="exclamation-triangle" />
                        <span>
                            <strong>PUPTAS sync failed.</strong>
                            {{ $puptasSyncMessage !== '' ? $puptasSyncMessage : 'The approved clearance has not been accepted by PUPTAS yet.' }}
                        </span>
                    </div>
                @elseif($puptasSyncStatus === 'missing_student_number')
                    <div class="health-status-sync missing">
                        <x-outline-icon name="information-circle" />
                        <span>
                            <strong>PUPTAS sync is waiting for a valid student number.</strong>
                            {{ $puptasSyncMessage !== '' ? $puptasSyncMessage : 'The clinic approval is complete, but the admission sync cannot finish until the school student number is resolved.' }}
                        </span>
                    </div>
                @endif

                <div class="health-status-actions">
                    <a href="https://puptas.undraftedbsit2027.com/applicant-dashboard" class="btn-print-form approved">
                        <x-outline-icon name="eye" />
                        View Admission System
                    </a>
                    <a href="{{ url('/student/account?view=health-record') }}" class="health-status-link">
                        <x-outline-icon name="document-text" />
                        View Record Details
                    </a>
                </div>
                <span class="health-status-note">Valid for Academic Year 2025-2026</span>
            @else
                <div class="health-status-summary">
                    <span class="health-status-state pending"><x-outline-icon name="clock" /> For Verification</span>
                    <p class="health-status-message">Your profile has been submitted and is currently <strong>awaiting medical review</strong>.</p>
                </div>

                <div class="health-status-actions">
                    <a href="{{ url('/student/account?view=health-record') }}" class="btn-print-form pending">
                        <x-outline-icon name="eye" />
                        View Submitted Record
                    </a>
                    <button class="btn-print-form disabled" disabled>
                        <x-outline-icon name="clock" />
                        Approval Required
                    </button>
                </div>
                <span class="health-status-note">Clinic approval is required before your record can be marked as issued.</span>
            @endif
        @else
            <div class="health-status-summary">
                <span class="health-status-state incomplete"><x-outline-icon name="x-mark" /> Not Completed</span>
                <p class="health-status-message">You haven't completed your health profile yet.</p>
            </div>
            <a href="{{ route('health.form') }}" class="btn-print-form incomplete">
                <x-outline-icon name="document-text" />
                Complete Form Now
            </a>
            <span class="health-status-note">Required for clinic consultations.</span>
        @endif
    </div>
@else
    <div class="page-intro">
        <h1 class="page-intro-title">Notifications</h1>
        <p class="page-intro-text">Stay updated with appointment changes, health record progress, and important clinic activity.</p>
    </div>
    <div class="widget-card">
        <div class="notif-panel-head">
            <h2 class="notif-panel-title">
                <x-outline-icon name="bell" />
                Notifications
            </h2>
            @if(collect($notifications ?? [])->isNotEmpty())
                <form action="{{ route('student.notifications.read_all') }}" method="POST">
                    @csrf
                    <button type="submit" class="notif-mark-btn">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        <div class="notif-list">
            @forelse(collect($notifications ?? []) as $notif)
                <a href="{{ route('student.notifications.open', ['notificationId' => $notif['id']]) }}"
                   class="notif-record {{ !empty($notif['is_unread']) ? 'is-unread' : '' }}">
                    <span class="notif-record-dot"></span>
                    <span class="notif-record-content">
                        <span class="notif-record-message">
                            {{ $notif['message'] ?? 'Notification available.' }}
                        </span>
                        <span class="notif-record-time">
                            {{ $notif['time'] ?? 'Just now' }}
                        </span>
                    </span>
                </a>
            @empty
                <div class="notif-empty">
                    No notifications available right now.
                </div>
            @endforelse
        </div>
    </div>
@endif
        </div>
    </div>
</div>

<script>
function enableEditing() {
    const form = document.querySelector('form[action="{{ route('student.updateContact') }}"]');
    if (form) {
        form.classList.add('profile-is-editing');
    }
    const inputs = form
        ? form.querySelectorAll('[name="height"], [name="weight"], [name="guardian_name"], [name="cellphone"], [name="emergency_contact_person"], [name="emergency_contact_no"]')
        : [];
    
    inputs.forEach(input => {
        input.disabled = false;
        input.readOnly = false;
        input.style.borderColor = '#8B0000'; 
        input.style.backgroundColor = document.documentElement.getAttribute('data-theme') === 'dark'
            ? 'rgba(30, 41, 59, 0.92)'
            : '#fff';

        const editableRow = input.closest('.profile-info-row, .profile-grid-2 > div, .profile-grid-3 > div');
        if (editableRow) {
            editableRow.classList.add('is-editing');
        }
    });

    document.getElementById('editBtn').style.display = 'none';
    document.getElementById('saveAction').style.display = 'flex';
}
</script>
@endsection




