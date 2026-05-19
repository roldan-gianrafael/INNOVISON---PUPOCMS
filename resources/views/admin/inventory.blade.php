@extends('layouts.admin')

@section('title', 'Inventory')

@push('styles')
<style>
    /* Card & Table */
    .card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
    }
    .inventory-summary-card {
        position: relative;
        overflow: hidden;
    }
    .inventory-summary-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 14px;
        right: 14px;
        height: 5px;
        background: #70131B;
        border-radius: 999px;
        pointer-events: none;
        z-index: 1;
    }
    .card,
    .card *:not(.status):not(.btn-add):not(.btn-icon) {
        color: #111827;
    }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th { text-align: left; padding: 12px 16px; border-bottom: 2px solid #f1f5f9; color: #000000; text-transform: uppercase; font-size: 12px; }
    td { padding: 16px; border-bottom: 1px solid #f8fafc; font-size: 14px; color: #111827; }

    /* Controls */
    .controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
        padding: 16px 18px;
        border-radius: 0 0 20px 20px;
        border: 0;
        border-bottom: 2px solid rgba(234, 215, 160, 0.9);
        background: linear-gradient(135deg, rgba(255, 253, 246, 0.76) 0%, rgba(255, 249, 231, 0.58) 42%, rgba(255, 255, 255, 0.82) 100%);
        box-shadow: 0 14px 26px rgba(112, 19, 27, 0.05);
    }
    .inventory-page-title {
        margin: 0;
        color: #000000;
        display: inline-flex;
        align-items: center;
        padding: 10px 18px;
        border-radius: 0 0 14px 14px;
        border: 0;
        border-bottom: 2px solid rgba(112, 19, 27, 0.72);
        background: transparent;
        box-shadow: none;
    }
    .inventory-page-title svg {
        width: 18px;
        height: 18px;
        margin-right: 10px;
        flex: 0 0 auto;
    }
    .inventory-toolbar-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-left: auto;
        flex-wrap: wrap;
    }
    .inventory-search-shell {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        justify-content: flex-end;
    }
    .inventory-search-wrap {
        width: 0;
        max-width: 100%;
        flex: 0 0 0;
        opacity: 0;
        overflow: hidden;
        pointer-events: none;
        transform: translateX(12px) scaleX(0.96);
        transform-origin: right center;
        transition:
            width .32s cubic-bezier(.22, 1, .36, 1),
            flex-basis .32s cubic-bezier(.22, 1, .36, 1),
            opacity .24s ease,
            transform .28s cubic-bezier(.22, 1, .36, 1);
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        padding: 0 !important;
        border-radius: 0 !important;
    }
    .inventory-search-shell.is-open .inventory-search-wrap {
        width: 320px;
        flex: 0 0 320px;
        opacity: 1;
        pointer-events: auto;
        transform: translateX(0) scaleX(1);
    }
    .inventory-search-input {
        width: 100%;
        min-height: 48px;
        height: 48px;
        padding: 12px 18px;
        border-radius: 0 0 14px 14px;
        border: 0 !important;
        border-bottom: 3px solid #8f2230 !important;
        color: #111827;
        background: transparent !important;
        box-shadow: none !important;
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        appearance: none;
        -webkit-appearance: none;
    }
    .inventory-search-input::placeholder {
        color: #7f1d2d;
        font-weight: 700;
    }
    .inventory-search-input:focus {
        outline: none;
        border-bottom-color: #70131B;
        box-shadow: none !important;
        transform: translateY(-1px);
    }
    .inventory-search-toggle {
        width: 50px !important;
        height: 50px !important;
        min-width: 50px !important;
        min-height: 50px !important;
        flex: 0 0 50px !important;
        padding: 0 !important;
        gap: 0 !important;
        border-radius: 999px !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        background: linear-gradient(135deg, #70131B, #8f2230) !important;
        border: 1px solid #8f2230 !important;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20) !important;
        outline: none !important;
    }
    .inventory-search-toggle svg {
        width: 28px !important;
        height: 28px !important;
        stroke-width: 2 !important;
        display: block;
    }
    .inventory-search-toggle:hover,
    .inventory-search-toggle:focus {
        border-color: #facc15 !important;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16) !important;
        outline: none !important;
    }
    .btn-add,
    .inventory-manage-btn,
    .inventory-btn-cancel {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #ffffff;
        padding: 11px 18px;
        border-radius: 999px;
        border: 1px solid #8f2230;
        font-weight: 800;
        cursor: pointer;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20);
        transition: color .08s linear, transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        z-index: 0;
    }
    .modal-actions-row .btn-add {
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20),
            0 18px 32px rgba(112, 19, 27, 0.18),
            0 30px 24px -18px rgba(250, 204, 21, 0.38);
    }
    .btn-add::after,
    .inventory-manage-btn::after,
    .inventory-btn-cancel::after {
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
    .btn-add:hover,
    .inventory-manage-btn:hover,
    .inventory-btn-cancel:hover {
        transform: translateY(-1px);
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16);
    }
    .modal-actions-row .btn-add:hover {
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16),
            0 22px 34px rgba(112, 19, 27, 0.20),
            0 34px 28px -20px rgba(250, 204, 21, 0.42);
    }
    .btn-add:hover::after,
    .inventory-manage-btn:hover::after,
    .inventory-btn-cancel:hover::after {
        transform: translateX(135%);
    }
    .inventory-manage-btn {
        text-decoration: none;
        white-space: nowrap;
    }
    .inventory-manage-btn::before {
        content: "IC";
        width: 28px;
        height: 28px;
        border-radius: 999px;
        background: #ffefb5;
        color: #70131B;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: 0.04em;
        flex: 0 0 auto;
        position: relative;
        z-index: 1;
    }

    /* Status Badges */
    .status { padding: 4px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; }
    .status.in { background: #dcfce7; color: #15803d; }
    .status.low { background: #fff7ed; color: #c2410c; }
    .status.out { background: #fee2e2; color: #b91c1c; }

    /* Action Buttons */
    .inventory-actions {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 96px;
        padding: 9px 14px;
        border-radius: 999px;
        border: 1px solid;
        cursor: pointer;
        font-size: 13px;
        font-weight: 800;
        line-height: 1;
        text-decoration: none;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease, color .18s ease;
        margin-right: 0;
        background: transparent;
    }
    .btn-icon svg {
        width: 15px;
        height: 15px;
        flex: 0 0 auto;
    }
    .btn-edit {
        background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
        color: #475569;
        border-color: rgba(112, 19, 27, 0.22);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
    }
    .btn-edit:hover {
        transform: translateY(-1px);
        color: #70131B;
        border-color: rgba(112, 19, 27, 0.46);
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.08),
            0 10px 22px rgba(112, 19, 27, 0.12);
    }
    .btn-delete {
        background: linear-gradient(180deg, #fff1f2 0%, #ffe4e6 100%);
        color: #b91c1c;
        border-color: rgba(220, 38, 38, 0.22);
        box-shadow: 0 8px 18px rgba(127, 29, 29, 0.08);
    }
    .btn-delete:hover {
        transform: translateY(-1px);
        background: linear-gradient(180deg, #fee2e2 0%, #fecaca 100%);
        border-color: rgba(220, 38, 38, 0.42);
        box-shadow:
            0 0 0 3px rgba(248, 113, 113, 0.12),
            0 10px 22px rgba(127, 29, 29, 0.14);
    }
    .inventory-row-highlight {
        background: #fff7cc;
        outline: 2px solid #f59e0b;
        box-shadow: inset 0 0 0 1px rgba(245, 158, 11, 0.25);
        transition: background 0.3s ease, outline-color 0.3s ease, box-shadow 0.3s ease;
    }
    .inventory-row-highlight-expired {
        background: #fee2e2;
        outline: 2px solid #dc2626;
        box-shadow: inset 0 0 0 1px rgba(220, 38, 38, 0.25);
        transition: background 0.3s ease, outline-color 0.3s ease, box-shadow 0.3s ease;
    }
    @keyframes inventoryHighlightPulse {
        0%, 100% { background: #fff7cc; }
        50% { background: #fde68a; }
    }
    @keyframes inventoryHighlightPulseExpired {
        0%, 100% { background: #fee2e2; }
        50% { background: #fecaca; }
    }

    /* Modal */
    .modal-overlay { 
        display: none; 
        position: fixed; 
        top: 0; left: 0; 
        width: 100%; 
        height: 100%; 
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 1000; 
        justify-content: center; 
        align-items: center; 
    }
    #itemModal .modal-box {
        background: rgba(255, 255, 255, 0.4) !important;
        width: 820px;
        max-width: min(94vw, 820px);
        max-height: min(88vh, 920px);
        border-left: 1px solid rgba(112, 19, 27, 0.12) !important;
        border-right: 1px solid rgba(112, 19, 27, 0.12) !important;
        border-top: 4px solid #66ff00 !important;
        border-bottom: 4px solid #70131B !important;
        border-radius: 18px !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        padding: 0;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    #itemModal .inventory-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 16px 20px;
        background: #70131B;
        border-bottom: 1px solid #eee;
        flex: 0 0 auto;
    }
    .inventory-modal-head-main {
        min-width: 0;
        flex: 1 1 auto;
    }
    .inventory-modal-title {
        margin: 0;
        color: #ffffff !important;
        font-size: 18px;
        font-weight: 800;
        line-height: 1.2;
    }
    .inventory-modal-copy {
        margin: 6px 0 0;
        color: #ffffff !important;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.45;
    }
    #itemModal .inventory-modal-body {
        padding: 24px;
        overflow-y: auto;
        background: transparent;
    }
    .modal-form-grid { 
        display: grid; 
        grid-template-columns: 1fr 1fr; 
        gap: 20px;
        align-items: start;
    }
    #itemModal .modal-form-panel {
        border: 1px solid rgba(112, 19, 27, 0.15);
        border-radius: 16px;
        background: #ffffff;
        padding: 18px;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,0.82),
            0 8px 18px rgba(112, 19, 27, 0.05);
    }
    .modal-panel-title {
        margin: 0 0 16px;
        font-size: 15px;
        font-weight: 800;
        color: #70131B;
        line-height: 1.3;
    }
    #itemModal .form-group {
        margin-bottom: 14px;
        display: flex;
        flex-direction: column;
        border: 1px solid rgba(112, 19, 27, 0.15);
        background: rgba(255, 255, 255, 0.46);
        border-radius: 12px;
        padding: 11px 12px;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,0.82),
            0 8px 18px rgba(112, 19, 27, 0.05);
    }
    .form-group label {
        display: block;
        margin-bottom: 4px;
        font-size: 0.74rem;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    #itemModal .form-control,
    #itemModal .form-select {
        width: 100%;
        min-height: 38px;
        padding: 8px 0 4px;
        border-radius: 0;
        border: 0;
        border-bottom: 1px solid #d9c8cd;
        color: #111827;
        background: transparent;
        box-shadow: none;
        font-weight: 700;
        transition: color .18s ease, box-shadow .18s ease, border-color .18s ease;
    }
    #itemModal .form-control:focus,
    #itemModal .form-select:focus {
        outline: none;
        border: 0;
        border-bottom: 1px solid #8f2230;
        background: transparent;
        box-shadow: none;
    }
    #itemModal .form-control[type="date"],
    #itemModal .form-control[type="number"],
    #itemModal .form-control[type="text"],
    #itemModal .form-select,
    #itemModal select.form-control {
        appearance: auto;
        -webkit-appearance: auto;
    }
    .form-control::placeholder {
        color: #6b7280;
        font-weight: 600;
    }

    .inventory-medicine-type-wrap {
        position: relative;
    }

    .inventory-medicine-type-select {
        position: absolute;
        width: 1px !important;
        height: 1px !important;
        opacity: 0;
        pointer-events: none;
        padding: 0 !important;
        border: 0 !important;
        margin: 0 !important;
    }

    .inventory-medicine-type-display {
        width: 100%;
        min-height: 52px;
        padding: 14px 52px 14px 16px;
        border: 1px solid rgba(127, 29, 29, 0.22);
        border-radius: 18px;
        font-size: 14px;
        color: #111111;
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, 0.10), transparent 36%),
            linear-gradient(180deg, #ffffff 0%, #fff8f6 100%);
        box-shadow:
            0 12px 22px rgba(15, 23, 42, 0.08),
            inset 0 1px 0 rgba(255,255,255,0.86);
        cursor: pointer;
        font-weight: 700;
        text-align: left;
        transition: all 0.2s ease;
    }

    .inventory-medicine-type-display:hover {
        border-color: rgba(139, 0, 0, 0.34);
        box-shadow:
            0 14px 24px rgba(15, 23, 42, 0.10),
            0 8px 18px rgba(139, 0, 0, 0.05),
            inset 0 1px 0 rgba(255,255,255,0.90);
        transform: translateY(-1px);
    }

    .inventory-medicine-type-display.is-open,
    .inventory-medicine-type-display:focus {
        outline: none;
        border-color: #8B0000;
        box-shadow:
            0 0 0 4px rgba(139, 0, 0, 0.06),
            0 14px 24px rgba(139, 0, 0, 0.10),
            inset 0 1px 0 rgba(255,255,255,0.88);
    }

    .inventory-medicine-type-wrap::after {
        content: "";
        position: absolute;
        top: 26px;
        right: 18px;
        width: 10px;
        height: 10px;
        border-right: 2px solid #8B0000;
        border-bottom: 2px solid #8B0000;
        transform: translateY(-65%) rotate(45deg);
        pointer-events: none;
        transition: transform 0.18s ease;
    }

    .inventory-medicine-type-wrap::before {
        content: "";
        position: absolute;
        top: 26px;
        right: 42px;
        transform: translateY(-50%);
        width: 1px;
        height: 24px;
        background: rgba(148, 163, 184, 0.24);
        pointer-events: none;
    }

    .inventory-medicine-type-wrap.is-open::after {
        transform: translateY(-20%) rotate(225deg);
    }

    .inventory-medicine-type-menu {
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
        max-height: 260px;
        overflow: hidden;
    }

    .inventory-medicine-type-wrap.is-open .inventory-medicine-type-menu {
        display: grid;
    }

    .inventory-medicine-type-search {
        width: 100%;
        min-height: 44px;
        padding: 12px 14px;
        border-radius: 14px;
        border: 1px solid rgba(127, 29, 29, 0.18);
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, 0.08), transparent 36%),
            linear-gradient(180deg, #ffffff 0%, #fff8f6 100%);
        color: #111111;
        font-size: 13px;
        font-weight: 700;
        outline: none;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.86);
    }

    .inventory-medicine-type-search::placeholder {
        color: #6b7280;
        font-weight: 700;
    }

    .inventory-medicine-type-search:focus {
        border-color: #8B0000;
        box-shadow:
            0 0 0 3px rgba(139, 0, 0, 0.06),
            inset 0 1px 0 rgba(255,255,255,0.88);
    }

    .inventory-medicine-type-options {
        display: grid;
        gap: 10px;
        max-height: 188px;
        overflow-y: auto;
        padding-right: 2px;
    }

    .inventory-medicine-type-option {
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
        box-shadow:
            0 12px 22px rgba(15, 23, 42, 0.08),
            inset 0 1px 0 rgba(255,255,255,0.82);
    }

    .inventory-medicine-type-option:hover,
    .inventory-medicine-type-option.is-selected {
        transform: translateY(-1px);
        border-color: #8B0000;
        background: linear-gradient(135deg, #8B0000, #70131B);
        color: #facc15;
        box-shadow: 0 12px 20px rgba(139, 0, 0, 0.16);
    }

    .inventory-medicine-type-empty {
        display: none;
        padding: 12px 14px;
        border-radius: 14px;
        border: 1px dashed rgba(127, 29, 29, 0.20);
        color: #6b7280;
        background: rgba(255, 255, 255, 0.76);
        font-size: 12px;
        font-weight: 800;
        text-align: center;
    }

    .inventory-medicine-type-menu.is-filter-empty .inventory-medicine-type-empty {
        display: block;
    }

    .inventory-subgroup {
        display: none;
        border-left: 3px solid #8B0000;
        padding-left: 15px;
        margin-top: 8px;
        margin-bottom: 4px;
    }
    #itemModal .inventory-subgroup .form-group {
        background: rgba(255, 255, 255, 0.52);
    }
    .inventory-inline-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .inventory-date-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        align-items: start;
    }
    #medicineExpiryField {
        display: none;
    }
    .modal-actions-row {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 22px;
    }
    .inventory-modal-close {
        width: 40px;
        height: 40px;
        min-width: 40px;
        min-height: 40px;
        padding: 0;
        border-radius: 999px;
        flex: 0 0 40px;
        margin-left: auto;
    }
    .inventory-modal-close svg {
        width: 18px;
        height: 18px;
        stroke-width: 2.2;
    }

    @media (max-width: 760px) {
        #itemModal .modal-box {
            max-width: 96vw;
            max-height: 90vh;
        }
        .inventory-modal-head {
            align-items: flex-start;
        }
        .modal-form-grid {
            grid-template-columns: 1fr;
        }
        .inventory-inline-grid {
            grid-template-columns: 1fr;
        }
        .inventory-date-grid {
            grid-template-columns: 1fr;
        }
        .inventory-modal-body {
            padding: 18px;
        }
    }

    html[data-theme="dark"] .inventory-page-title {
        color: #ffffff;
        border-bottom-color: rgba(143, 34, 48, 0.70);
        background: transparent;
        box-shadow: none;
    }

    html[data-theme="dark"] .controls {
        border-bottom-color: rgba(143, 34, 48, 0.70);
        background: linear-gradient(135deg, rgba(112, 19, 27, 0.68) 0%, rgba(86, 16, 26, 0.64) 48%, rgba(44, 14, 18, 0.72) 100%);
        box-shadow: 0 16px 28px rgba(0, 0, 0, 0.22);
    }

    html[data-theme="dark"] .inventory-search-input {
        background: transparent !important;
        color: #ffffff;
        border-bottom-color: rgba(143, 34, 48, 0.92);
        box-shadow: none !important;
    }

    html[data-theme="dark"] .inventory-search-input::placeholder {
        color: #e5e7eb;
    }

    html[data-theme="dark"] .inventory-summary-card::before {
        background: #facc15;
    }

    html[data-theme="dark"] .inventory-search-toggle {
        background: linear-gradient(135deg, #70131B, #8f2230) !important;
        border-color: rgba(250, 204, 21, 0.28) !important;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.16),
            0 12px 22px rgba(0, 0, 0, 0.24) !important;
    }

    @media (max-width: 920px) {
        .controls {
            flex-direction: column;
            align-items: stretch;
            border-radius: 0 0 18px 18px;
        }

        .inventory-toolbar-actions {
            width: 100%;
            justify-content: stretch;
            margin-left: 0;
        }

        .inventory-search-shell {
            width: 100%;
        }

        .inventory-search-wrap,
        .inventory-search-shell.is-open .inventory-search-wrap {
            width: 100%;
            flex: 1 1 100%;
        }

        .inventory-search-shell:not(.is-open) .inventory-search-wrap {
            width: 0;
            flex-basis: 0;
        }

        .btn-add {
            width: 100%;
        }
    }

    html[data-theme="dark"] #itemModal .modal-box {
        background: rgba(28, 20, 22, 0.34) !important;
        border-left: 1px solid rgba(143, 34, 48, 0.36) !important;
        border-right: 1px solid rgba(143, 34, 48, 0.36) !important;
        border-top: 4px solid #facc15 !important;
        border-bottom: 4px solid #facc15 !important;
        box-shadow:
            0 22px 38px rgba(0, 0, 0, 0.42),
            0 0 0 1px rgba(250, 204, 21, 0.06);
    }

    html[data-theme="dark"] #itemModal .inventory-modal-head {
        background: #4d0d17;
        border-bottom-color: rgba(250, 204, 21, 0.2);
    }

    html[data-theme="dark"] .inventory-modal-title,
    html[data-theme="dark"] .modal-panel-title {
        color: #ffffff !important;
    }

    html[data-theme="dark"] .inventory-modal-copy {
        color: rgba(255, 255, 255, 0.8);
    }

    html[data-theme="dark"] #itemModal .modal-form-panel {
        background: rgba(15, 23, 42, 0.78);
        border-color: rgba(250, 204, 21, 0.16);
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.04),
            0 10px 22px rgba(0, 0, 0, 0.24);
    }

    html[data-theme="dark"] #itemModal .form-group {
        background: rgba(31, 41, 55, 0.9);
        border-color: rgba(148, 163, 184, 0.26);
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.04),
            0 8px 18px rgba(0, 0, 0, 0.18);
    }

    html[data-theme="dark"] #itemModal .form-group label {
        color: #ffffff !important;
    }

    html[data-theme="dark"] #itemModal .form-control,
    html[data-theme="dark"] #itemModal .form-select,
    html[data-theme="dark"] #itemModal .form-control option {
        background: transparent;
        color: #ffffff !important;
        border-color: rgba(148, 163, 184, 0.36);
    }

    html[data-theme="dark"] #itemModal .form-control::placeholder {
        color: #94a3b8;
    }

    html[data-theme="dark"] .inventory-medicine-type-display {
        color: #f8fafc !important;
        border-color: rgba(250, 204, 21, 0.16);
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, 0.08), transparent 36%),
            linear-gradient(180deg, rgba(40, 26, 26, 0.98) 0%, rgba(23, 23, 23, 0.98) 100%);
        box-shadow:
            0 12px 22px rgba(0, 0, 0, 0.22),
            inset 0 1px 0 rgba(255,255,255,0.05);
    }

    html[data-theme="dark"] .inventory-medicine-type-display:hover,
    html[data-theme="dark"] .inventory-medicine-type-display:focus,
    html[data-theme="dark"] .inventory-medicine-type-display.is-open {
        border-color: #facc15;
        box-shadow:
            0 0 0 4px rgba(250, 204, 21, 0.14),
            0 14px 24px rgba(0, 0, 0, 0.26),
            inset 0 1px 0 rgba(255,255,255,0.06);
    }

    html[data-theme="dark"] .inventory-medicine-type-wrap::after {
        border-right-color: #facc15;
        border-bottom-color: #facc15;
    }

    html[data-theme="dark"] .inventory-medicine-type-wrap::before {
        background: rgba(250, 204, 21, 0.18);
    }

    html[data-theme="dark"] .inventory-medicine-type-menu {
        background: rgba(18, 18, 18, 0.96);
        border-color: rgba(250, 204, 21, 0.14);
        box-shadow: 0 18px 34px rgba(0, 0, 0, 0.34);
    }

    html[data-theme="dark"] .inventory-medicine-type-search {
        color: #f8fafc;
        border-color: rgba(250, 204, 21, 0.16);
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, 0.08), transparent 36%),
            linear-gradient(180deg, rgba(40, 26, 26, 0.98) 0%, rgba(23, 23, 23, 0.98) 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.05);
    }

    html[data-theme="dark"] .inventory-medicine-type-search::placeholder {
        color: rgba(248, 250, 252, 0.62);
    }

    html[data-theme="dark"] .inventory-medicine-type-search:focus {
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.14),
            inset 0 1px 0 rgba(255,255,255,0.06);
    }

    html[data-theme="dark"] .inventory-medicine-type-option {
        color: #f8fafc !important;
        border-color: rgba(250, 204, 21, 0.14);
        background: linear-gradient(180deg, rgba(40, 26, 26, 0.98) 0%, rgba(23, 23, 23, 0.98) 100%);
        box-shadow:
            0 12px 22px rgba(0, 0, 0, 0.22),
            inset 0 1px 0 rgba(255,255,255,0.04);
    }

    html[data-theme="dark"] .inventory-medicine-type-option:hover,
    html[data-theme="dark"] .inventory-medicine-type-option.is-selected {
        background: linear-gradient(135deg, #8B0000, #70131B);
        color: #facc15 !important;
        border-color: rgba(250, 204, 21, 0.28);
    }

    html[data-theme="dark"] .inventory-medicine-type-empty {
        color: #f8fafc;
        border-color: rgba(250, 204, 21, 0.18);
        background: rgba(255, 255, 255, 0.06);
    }

    html[data-theme="dark"] #medicineFields,
    html[data-theme="dark"] #medicineExpiryField {
        border-left-color: #facc15 !important;
    }

    html[data-theme="dark"] table td,
    html[data-theme="dark"] table td div,
    html[data-theme="dark"] table td small,
    html[data-theme="dark"] table td span:not(.status),
    html[data-theme="dark"] table td[style],
    html[data-theme="dark"] table td div[style],
    html[data-theme="dark"] table td small[style] {
        color: #ffffff !important;
    }

    html[data-theme="dark"] .btn-edit {
        background: linear-gradient(180deg, rgba(51, 65, 85, 0.92) 0%, rgba(30, 41, 59, 0.96) 100%);
        color: #f8fafc;
        border-color: rgba(250, 204, 21, 0.18);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.26);
    }

    html[data-theme="dark"] .btn-edit:hover {
        color: #ffffff;
        border-color: rgba(250, 204, 21, 0.34);
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.10),
            0 12px 24px rgba(0, 0, 0, 0.30);
    }

    html[data-theme="dark"] .btn-delete {
        background: linear-gradient(180deg, rgba(127, 29, 29, 0.92) 0%, rgba(69, 10, 10, 0.96) 100%);
        color: #fee2e2;
        border-color: rgba(248, 113, 113, 0.22);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.28);
    }

    html[data-theme="dark"] .btn-delete:hover {
        color: #ffffff;
        border-color: rgba(248, 113, 113, 0.40);
        box-shadow:
            0 0 0 3px rgba(248, 113, 113, 0.12),
            0 12px 24px rgba(0, 0, 0, 0.32);
    }
</style>
@endpush

@section('content')
    @php
        $role = \App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '');
        $canManageInventory = $role === \App\Models\User::ROLE_SUPERADMIN;
        $highlightItemId = (string) request()->query('highlight_item', '');
    @endphp

    <div class="controls">
        <h2 class="inventory-page-title"><x-outline-icon name="cube" />Clinic Inventory</h2>
        <div class="inventory-toolbar-actions">
            <div class="inventory-search-shell" id="inventorySearchShell">
                <div class="inventory-search-wrap">
                    <input type="text" id="inventorySearchInput" class="inventory-search-input" placeholder="Search by item, category, or unit...">
                </div>
                <button type="button" class="btn-add inventory-search-toggle" id="inventorySearchToggle" aria-label="Open search" aria-expanded="false" aria-controls="inventorySearchInput">
                    <x-outline-icon name="magnifying-glass" />
                </button>
            </div>
            @if($canManageInventory)
                <button class="btn-add" onclick="openModal()">+ Add New Item</button>
            @endif
        </div>
    </div>

    <div class="card inventory-summary-card">
        <table id="inventoryTable">
            <thead>
                <tr>    
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Unit</th>
                    <th>Quantity & Dates</th>
                    <th>Stock Status</th>
                    <th>{{ $canManageInventory ? 'Actions' : 'Access' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    @php
                        $isHighlightedItem = $highlightItemId !== '' && $highlightItemId === (string) $item->id;
                        $isExpiredMedicine = $item->category == 'Medicine' && $item->expiration_date && \Carbon\Carbon::parse($item->expiration_date)->isPast();
                        $highlightClass = $isHighlightedItem
                            ? ($isExpiredMedicine ? 'inventory-row-highlight-expired' : 'inventory-row-highlight')
                            : '';
                    @endphp
                    <tr
                        id="inventory-item-{{ $item->id }}"
                        data-inventory-row
                        class="{{ $highlightClass }}"
                    >
                        <td style="font-weight: 600;">{{ $item->name }}</td>
                        <td>
                            {{ $item->category }}
                            @if($item->category == 'Medicine' && $item->medicine_type)
                                <small style="display:block; color:#64748b; font-style: italic;">({{ $item->medicine_type }})</small>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 700;">{{ $item->unit ?: 'pcs' }}</div>
                            @if($item->category == 'Medicine' && $item->hasDispensingConversion())
                                <small style="display:block; color:#64748b; margin-top:4px;">
                                    Dispense as: {{ $item->dispensing_unit }} ({{ $item->units_per_stock_unit }} per {{ $item->unit }})
                                </small>
                            @endif
                        </td>
                        <td>
                            @php
                                $stockDisplay = rtrim(rtrim(number_format((float) $item->quantity, 2, '.', ''), '0'), '.');
                                $availableDispensing = $item->hasDispensingConversion()
                                    ? rtrim(rtrim(number_format($item->availableDispensingQuantity(), 2, '.', ''), '0'), '.')
                                    : null;
                            @endphp
                            <div style="font-weight: 700;">{{ $stockDisplay }} {{ $item->unit ?: 'pcs' }}</div>
                            @if($item->category == 'Medicine' && $item->hasDispensingConversion())
                                <small style="display:block; color:#64748b; margin-top:4px;">
                                    Available to dispense: {{ $availableDispensing }} {{ $item->dispensing_unit }}
                                </small>
                            @endif
                            <small style="display:block; color:#64748b; margin-top:4px;">
                                📅 Added: {{ $item->date_added ? \Carbon\Carbon::parse($item->date_added)->format('M d, Y') : 'N/A' }}
                            </small>
                            @if($item->category == 'Medicine' && $item->expiration_date)
                                <small style="display:block; color: {{ \Carbon\Carbon::parse($item->expiration_date)->isPast() ? '#b91c1c' : '#c2410c' }}; font-weight:600;">
                                    ⌛ Exp: {{ \Carbon\Carbon::parse($item->expiration_date)->format('M d, Y') }}
                                </small>
                            @endif
                        </td>
                        <td>
                            @if($item->quantity == 0)
                                <span class="status out">Out of Stock</span>
                            @elseif($item->quantity < 10)
                                <span class="status low">Low Stock</span>
                            @else
                                <span class="status in">In Stock</span>
                            @endif
                        </td>
                        <td>
                            @if($canManageInventory)
                                @php
                                    $editItemPayload = [
                                        'id' => $item->id,
                                        'name' => $item->name,
                                        'category' => $item->category,
                                        'quantity' => $item->quantity,
                                        'unit' => $item->unit,
                                        'medicine_type_id' => $item->medicine_type_id,
                                        'dispensing_unit' => $item->dispensing_unit,
                                        'units_per_stock_unit' => $item->units_per_stock_unit,
                                        'medicine_type' => $item->medicine_type,
                                        'date_added' => optional($item->date_added)->format('Y-m-d'),
                                        'expiration_date' => optional($item->expiration_date)->format('Y-m-d'),
                                    ];
                                @endphp
                                <div class="inventory-actions">
                                    <button class="btn-icon btn-edit" 
                                        onclick='editItem(@json($editItemPayload))'>
                                        <x-outline-icon name="pencil-square" />
                                        <span>Edit</span>
                                    </button>

                                    <form action="{{ url('/admin/inventory/'.$item->id) }}" method="POST" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon btn-delete" onclick="return confirm('Delete this item?')">
                                            <x-outline-icon name="trash" />
                                            <span>Delete</span>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span style="font-size: 12px; color: #64748b; font-weight: 700;">View Only</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align: center; padding: 30px; color: #888;">No items in inventory.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($canManageInventory)
        <div id="itemModal" class="modal-overlay">
            <div class="modal-box">
                <div class="inventory-modal-head">
                    <div class="inventory-modal-head-main">
                        <h3 id="modalTitle" class="inventory-modal-title">Add New Item</h3>
                        <p class="inventory-modal-copy">Provide inventory details and save to update clinic stock records.</p>
                    </div>
                    <button type="button" class="inventory-btn-cancel inventory-modal-close" onclick="closeModal()" aria-label="Close modal">
                        <x-outline-icon name="x-mark" />
                    </button>
                </div>

                <form id="itemForm" method="POST" action="{{ url('/admin/inventory/store') }}">
                    <div class="inventory-modal-body">
                        @csrf
                        <div id="methodField"></div>

                        <div class="modal-form-grid">
                            <div class="modal-form-panel">
                                <h4 class="modal-panel-title">Item Information</h4>

                                <div class="form-group">
                                    <label>Item Name</label>
                                    <input name="name" id="iName" class="form-control" required placeholder="e.g. Paracetamol">
                                </div>

                                <div class="form-group">
                                    <label>Category</label>
                                    <select name="category" id="iCategory" class="form-control" onchange="toggleMedicineFields()">
                                        <option value="Medicine">Medicine</option>
                                        <option value="Equipment">Equipment</option>
                                        <option value="Supplies">Supplies</option>
                                    </select>
                                </div>

                                <div id="medicineFields" class="inventory-subgroup">
                                    <div class="form-group">
                                        <label>Medicine Type</label>
                                        <div class="inventory-medicine-type-wrap" id="inventoryMedicineTypeWrap">
                                            <select name="medicine_type_id" id="iMedicineType" class="form-control inventory-medicine-type-select">
                                                <option value="">-- Select Type --</option>
                                                @foreach($medicineTypes as $medicineType)
                                                    <option value="{{ $medicineType->id }}">{{ $medicineType->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="inventory-medicine-type-display" id="inventoryMedicineTypeDisplay" aria-haspopup="listbox" aria-expanded="false">
                                                Select medicine type
                                            </button>
                                            <div class="inventory-medicine-type-menu" id="inventoryMedicineTypeMenu" role="listbox" aria-label="Medicine Type options">
                                                <input type="search" class="inventory-medicine-type-search" id="inventoryMedicineTypeSearch" placeholder="Search medicine type..." autocomplete="off">
                                                <div class="inventory-medicine-type-options">
                                                    @foreach($medicineTypes as $medicineType)
                                                        <button type="button" class="inventory-medicine-type-option" data-medicine-type-value="{{ $medicineType->id }}" data-medicine-type-name="{{ strtolower($medicineType->name) }}">{{ $medicineType->name }}</button>
                                                    @endforeach
                                                </div>
                                                <div class="inventory-medicine-type-empty" id="inventoryMedicineTypeEmpty">No medicine type found.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="modal-form-panel">
                                <h4 class="modal-panel-title">Stock Details</h4>

                                <div class="inventory-inline-grid">
                                    <div class="form-group">
                                        <label>Quantity</label>
                                        <input type="number" name="quantity" id="iQty" class="form-control" required min="0" step="0.01" placeholder="e.g. 100">
                                    </div>

                                    <div class="form-group">
                                        <label>Unit</label>
                                        <input type="text" name="unit" id="iUnit" class="form-control" list="inventoryUnitSuggestions" required placeholder="e.g. pcs, box, bottle, vial">
                                        <datalist id="inventoryUnitSuggestions">
                                            <option value="pcs">
                                            <option value="box">
                                            <option value="bottle">
                                            <option value="vial">
                                            <option value="ampule">
                                            <option value="tablet">
                                            <option value="capsule">
                                            <option value="pack">
                                            <option value="set">
                                            <option value="tube">
                                            <option value="sachet">
                                            <option value="roll">
                                            <option value="pair">
                                            <option value="ml">
                                            <option value="mg">
                                        </datalist>
                                    </div>
                                </div>

                                <div id="medicineDispensingFields" class="inventory-subgroup">
                                    <div class="inventory-inline-grid">
                                        <div class="form-group">
                                            <label>Dispensing Unit</label>
                                            <input type="text" name="dispensing_unit" id="iDispensingUnit" class="form-control" list="dispensingUnitSuggestions" placeholder="e.g. tablet, capsule, ml">
                                            <datalist id="dispensingUnitSuggestions">
                                                <option value="tablet">
                                                <option value="capsule">
                                                <option value="ml">
                                                <option value="dose">
                                                <option value="drop">
                                                <option value="puff">
                                                <option value="sachet">
                                            </datalist>
                                        </div>

                                        <div id="itemsPerUnitField" class="form-group" style="display:none;">
                                            <label>Items Per Unit</label>
                                            <input type="number" name="units_per_stock_unit" id="iUnitsPerStockUnit" class="form-control" min="1" step="1" placeholder="e.g. 10 tablets in 1 box">
                                        </div>
                                    </div>
                                </div>

                                <div class="inventory-date-grid">
                                    <div class="form-group">
                                        <label>Date Added</label>
                                        <input type="date" name="date_added" id="iDateAdded" class="form-control" required placeholder="mm/dd/yyyy">
                                    </div>

                                    <div id="medicineExpiryField" class="form-group">
                                        <label>Expiration Date</label>
                                        <input type="date" name="expiration_date" id="iExpDate" class="form-control" placeholder="mm/dd/yyyy">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-actions-row">
                            <button type="submit" class="btn-add">Save Item</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    const itemModal = document.getElementById('itemModal');
    const itemForm = document.getElementById('itemForm');
    const medicineFields = document.getElementById('medicineFields');
    const medicineDispensingFields = document.getElementById('medicineDispensingFields');
    const medicineExpiryField = document.getElementById('medicineExpiryField');
    const medicineSelect = document.getElementById('iMedicineType');
    const dispensingUnitInput = document.getElementById('iDispensingUnit');
    const itemsPerUnitField = document.getElementById('itemsPerUnitField');
    const unitsPerStockUnitInput = document.getElementById('iUnitsPerStockUnit');
    const expDateInput = document.getElementById('iExpDate');
    const highlightedRow = document.querySelector('.inventory-row-highlight');
    const highlightedExpiredRow = document.querySelector('.inventory-row-highlight-expired');
    const inventorySearchInput = document.getElementById('inventorySearchInput');
    const inventorySearchShell = document.getElementById('inventorySearchShell');
    const inventorySearchToggle = document.getElementById('inventorySearchToggle');
    const inventoryRows = Array.from(document.querySelectorAll('#inventoryTable tbody tr[data-inventory-row]'));
    const medicineTypeWrap = document.getElementById('inventoryMedicineTypeWrap');
    const medicineTypeDisplay = document.getElementById('inventoryMedicineTypeDisplay');
    const medicineTypeMenu = document.getElementById('inventoryMedicineTypeMenu');
    const medicineTypeSearch = document.getElementById('inventoryMedicineTypeSearch');
    const medicineTypeOptions = Array.from(document.querySelectorAll('.inventory-medicine-type-option'));
    const medicineTypeEmpty = document.getElementById('inventoryMedicineTypeEmpty');

    function syncMedicineTypeDisplay() {
        if (!medicineSelect || !medicineTypeDisplay) return;

        const selectedOption = medicineSelect.options[medicineSelect.selectedIndex];
        const selectedText = selectedOption && selectedOption.value ? selectedOption.text : 'Select medicine type';
        medicineTypeDisplay.textContent = selectedText;

        medicineTypeOptions.forEach(function(option) {
            option.classList.toggle('is-selected', option.dataset.medicineTypeValue === medicineSelect.value);
        });
    }

    function setMedicineTypeOpenState(isOpen) {
        if (!medicineTypeWrap || !medicineTypeDisplay) return;

        medicineTypeWrap.classList.toggle('is-open', isOpen);
        medicineTypeDisplay.classList.toggle('is-open', isOpen);
        medicineTypeDisplay.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

        if (isOpen && medicineTypeSearch) {
            medicineTypeSearch.value = '';
            filterMedicineTypeOptions('');
            setTimeout(function() {
                medicineTypeSearch.focus();
            }, 0);
        }
    }

    function filterMedicineTypeOptions(query) {
        const normalizedQuery = String(query || '').trim().toLowerCase();
        let visibleCount = 0;

        medicineTypeOptions.forEach(function(option) {
            const searchableName = option.dataset.medicineTypeName || option.textContent.toLowerCase();
            const isVisible = searchableName.includes(normalizedQuery);

            option.style.display = isVisible ? '' : 'none';
            if (isVisible) {
                visibleCount += 1;
            }
        });

        if (medicineTypeMenu) {
            medicineTypeMenu.classList.toggle('is-filter-empty', visibleCount === 0);
        }

        if (medicineTypeEmpty) {
            medicineTypeEmpty.style.display = visibleCount === 0 ? 'block' : '';
        }
    }

    function toggleDispensingFields() {
        if (!medicineDispensingFields) return;

        const unitValue = String(document.getElementById('iUnit').value || '').trim().toLowerCase();
        const shouldHideDispensing = unitValue === 'pcs';

        if (shouldHideDispensing) {
            medicineDispensingFields.style.display = 'none';
            if (itemsPerUnitField) {
                itemsPerUnitField.style.display = 'none';
            }
            if (dispensingUnitInput) {
                dispensingUnitInput.value = '';
            }
            if (unitsPerStockUnitInput) {
                unitsPerStockUnitInput.value = '';
            }
            return;
        }

        const category = document.getElementById('iCategory').value;
        medicineDispensingFields.style.display = category === 'Medicine' ? 'block' : 'none';
        if (itemsPerUnitField) {
            itemsPerUnitField.style.display = category === 'Medicine' ? 'flex' : 'none';
        }
    }

    function toggleMedicineFields() {
        const category = document.getElementById('iCategory').value;
        if (category === 'Medicine') {
            medicineFields.style.display = 'block';
            medicineExpiryField.style.display = 'block';
            medicineSelect.setAttribute('required', 'required');
            expDateInput.setAttribute('required', 'required');
            toggleDispensingFields();
        } else {
            medicineFields.style.display = 'none';
            if (medicineDispensingFields) {
                medicineDispensingFields.style.display = 'none';
            }
            if (itemsPerUnitField) {
                itemsPerUnitField.style.display = 'none';
            }
            medicineExpiryField.style.display = 'none';
            medicineSelect.removeAttribute('required');
            expDateInput.removeAttribute('required');
            medicineSelect.value = ''; 
            syncMedicineTypeDisplay();
            if (dispensingUnitInput) {
                dispensingUnitInput.value = '';
            }
            if (unitsPerStockUnitInput) {
                unitsPerStockUnitInput.value = '';
            }
            expDateInput.value = '';
        }
    }

    function openModal() {
        if (!itemModal) return;
        itemModal.style.display = 'flex';
        document.getElementById('modalTitle').innerText = 'Add New Item';
        document.getElementById('itemForm').action = "{{ url('/admin/inventory/store') }}";
        document.getElementById('methodField').innerHTML = ''; 
        
        // Reset inputs
        document.getElementById('iName').value = '';
        document.getElementById('iCategory').value = 'Medicine';
        document.getElementById('iQty').value = '';
        document.getElementById('iUnit').value = 'pcs';
        if (dispensingUnitInput) {
            dispensingUnitInput.value = '';
        }
        if (unitsPerStockUnitInput) {
            unitsPerStockUnitInput.value = '';
        }
        document.getElementById('iDateAdded').value = new Date().toISOString().split('T')[0]; // Set today as default
        document.getElementById('iExpDate').value = '';
        medicineSelect.value = '';
        
        toggleMedicineFields();
        syncMedicineTypeDisplay();
    }

    function editItem(item) {
        if (!itemModal) return;
        itemModal.style.display = 'flex';
        document.getElementById('modalTitle').innerText = 'Edit Item';
        document.getElementById('itemForm').action = "/admin/inventory/" + item.id;
        
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('iName').value = item.name || '';
        document.getElementById('iCategory').value = item.category || 'Medicine';
        document.getElementById('iQty').value = item.quantity ?? '';
        document.getElementById('iUnit').value = item.unit || 'pcs';
        if (dispensingUnitInput) {
            dispensingUnitInput.value = item.dispensing_unit || '';
        }
        if (unitsPerStockUnitInput) {
            unitsPerStockUnitInput.value = item.units_per_stock_unit || '';
        }
        document.getElementById('iDateAdded').value = item.date_added || '';
        
        toggleMedicineFields();
        if((item.category || '') === 'Medicine') {
            document.getElementById('iMedicineType').value = item.medicine_type_id || '';
            document.getElementById('iExpDate').value = item.expiration_date || '';
        }
        syncMedicineTypeDisplay();
        toggleDispensingFields();
    }

    function closeModal() {
        if (!itemModal) return;
        itemModal.style.display = 'none';
        setMedicineTypeOpenState(false);
    }

    if (medicineTypeDisplay && medicineTypeMenu && medicineSelect) {
        medicineTypeDisplay.addEventListener('click', function(event) {
            event.preventDefault();
            setMedicineTypeOpenState(!medicineTypeWrap.classList.contains('is-open'));
        });

        medicineTypeOptions.forEach(function(option) {
            option.addEventListener('click', function(event) {
                event.preventDefault();
                medicineSelect.value = option.dataset.medicineTypeValue || '';
                medicineSelect.dispatchEvent(new Event('change', { bubbles: true }));
                syncMedicineTypeDisplay();
                setMedicineTypeOpenState(false);
            });
        });

        medicineSelect.addEventListener('change', syncMedicineTypeDisplay);

        if (medicineTypeSearch) {
            medicineTypeSearch.addEventListener('input', function(event) {
                filterMedicineTypeOptions(event.target.value);
            });

            medicineTypeSearch.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    event.preventDefault();
                    setMedicineTypeOpenState(false);
                    medicineTypeDisplay.focus();
                } else if (event.key === 'Enter') {
                    event.preventDefault();
                    const firstVisibleOption = medicineTypeOptions.find(function(option) {
                        return option.style.display !== 'none';
                    });

                    if (firstVisibleOption) {
                        firstVisibleOption.click();
                    }
                }
            });
        }

        document.addEventListener('click', function(event) {
            if (medicineTypeWrap && !medicineTypeWrap.contains(event.target)) {
                setMedicineTypeOpenState(false);
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                setMedicineTypeOpenState(false);
            }
        });

        syncMedicineTypeDisplay();
    }

    if (itemForm && medicineSelect && medicineTypeDisplay) {
        itemForm.addEventListener('submit', function(event) {
            const category = document.getElementById('iCategory').value;

            if (category === 'Medicine' && !medicineSelect.value) {
                event.preventDefault();
                setMedicineTypeOpenState(true);
                medicineTypeDisplay.focus();
                medicineTypeDisplay.setCustomValidity('Please select a medicine type.');
                medicineTypeDisplay.reportValidity();
                setTimeout(function() {
                    medicineTypeDisplay.setCustomValidity('');
                }, 0);
            }
        });
    }

    const clearHighlightQueryParam = function (paramName) {
        const url = new URL(window.location.href);
        if (!url.searchParams.has(paramName)) {
            return;
        }
        url.searchParams.delete(paramName);
        window.history.replaceState({}, document.title, url.toString());
    };

    window.onclick = function(event) {
        if (itemModal && event.target == itemModal) {
            closeModal();
        }
    }

    if (highlightedRow) {
        setTimeout(function () {
            highlightedRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 180);
        setTimeout(function () {
            highlightedRow.classList.remove('inventory-row-highlight');
            clearHighlightQueryParam('highlight_item');
        }, 5000);
    }

    if (highlightedExpiredRow) {
        setTimeout(function () {
            highlightedExpiredRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 180);
        setTimeout(function () {
            highlightedExpiredRow.classList.remove('inventory-row-highlight-expired');
            clearHighlightQueryParam('highlight_item');
        }, 5000);
    }

    if (inventorySearchInput) {
        inventorySearchInput.addEventListener('input', function () {
            const searchTerm = this.value.trim().toLowerCase();

            inventoryRows.forEach(function (row) {
                const rowText = row.innerText.toLowerCase();
                row.style.display = rowText.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    if (inventorySearchShell && inventorySearchInput && inventorySearchToggle) {
        const setInventorySearchOpenState = function (isOpen) {
            inventorySearchShell.classList.toggle('is-open', isOpen);
            inventorySearchToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        };

        setInventorySearchOpenState(inventorySearchInput.value.trim() !== '');

        inventorySearchToggle.addEventListener('click', function () {
            const shouldOpen = !inventorySearchShell.classList.contains('is-open');
            setInventorySearchOpenState(shouldOpen);

            if (shouldOpen) {
                window.requestAnimationFrame(function () {
                    inventorySearchInput.focus();
                });
            }
        });
    }

    const unitInput = document.getElementById('iUnit');
    if (unitInput) {
        unitInput.addEventListener('input', toggleDispensingFields);
        unitInput.addEventListener('change', toggleDispensingFields);
    }
</script>
@endpush
