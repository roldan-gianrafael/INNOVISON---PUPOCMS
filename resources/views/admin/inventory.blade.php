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

    .inventory-toolbar-actions > .btn-add {
        min-height: 50px;
        height: 50px;
        padding-top: 0;
        padding-bottom: 0;
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
        background: #facc15 !important;
        color: #111827 !important;
        border-color: #facc15 !important;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16) !important;
        outline: none !important;
    }
    .inventory-search-toggle:hover svg,
    .inventory-search-toggle:focus svg {
        color: #111827 !important;
        stroke: currentColor !important;
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
        background: #facc15;
        color: #111827;
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
        position: relative;
    }
    .inventory-actions-dropdown {
        position: relative;
        display: inline-flex;
        align-items: stretch;
    }
    .inventory-actions-toggle {
        min-width: 120px;
        padding: 10px 16px;
    }
    .inventory-actions-menu {
        position: absolute;
        right: calc(100% + 10px);
        top: 50%;
        transform: translateY(-50%);
        display: none;
        flex-direction: column;
        gap: 8px;
        width: min(220px, 100vw);
        padding: 10px;
        background: #ffffff;
        border: 1px solid rgba(112, 19, 27, 0.12);
        border-radius: 18px;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.14);
        z-index: 20;
    }
    .inventory-actions-dropdown.is-open .inventory-actions-menu {
        display: flex;
    }
    .inventory-actions-menu-item {
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        background: #f8fafc;
        color: #111827;
        border: 1px solid rgba(112, 19, 27, 0.12);
        border-radius: 14px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        transition: background .18s ease, transform .18s ease, color .18s ease, border-color .18s ease;
        text-align: left;
    }
    .inventory-actions-menu-item:hover {
        background: #facc15;
        color: #111827;
        border-color: #facc15;
        transform: translateY(-1px);
    }
    .inventory-actions-menu-item.btn-delete {
        background: linear-gradient(180deg, #fff1f2 0%, #ffe4e6 100%);
        color: #b91c1c;
        border-color: rgba(220, 38, 38, 0.22);
    }
    .inventory-actions-menu-item.btn-delete:hover {
        background: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
    }
    .inventory-filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin: 0 0 14px;
        align-items: center;
    }
    @keyframes filterPillSlideIn {
        from { opacity: 0; transform: translateX(-12px) scale(0.92); }
        to   { opacity: 1; transform: translateX(0)    scale(1); }
    }
    .inventory-filter-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border: 1px solid rgba(127, 29, 45, 0.18);
        background: #ffffff;
        color: #70131B;
        border-radius: 999px;
        min-height: 34px;
        padding: 0 16px;
        font-size: 12px;
        font-weight: 900;
        cursor: pointer;
        white-space: nowrap;
        transition: background .18s ease, color .18s ease, border-color .18s ease, transform .18s ease, box-shadow .18s ease;
    }
    .inventory-filter-pill:hover {
        background: #facc15;
        color: #111827;
        border-color: #facc15;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(250, 204, 21, 0.28);
    }
    .inventory-filter-pill.is-active {
        background: #facc15;
        color: #111827;
        border-color: #facc15;
        box-shadow: 0 6px 16px rgba(250, 204, 21, 0.28);
    }
    /* All button is always yellow with white text */
    #inventoryFilterAllBtn,
    #inventoryFilterAllBtn.is-active {
        background: #facc15;
        color: #ffffff;
        border-color: #facc15;
        box-shadow: 0 6px 16px rgba(250, 204, 21, 0.32);
    }
    #inventoryFilterAllBtn:hover {
        background: #fde047;
        color: #ffffff;
        border-color: #fde047;
        transform: translateY(-1px);
    }
    html[data-theme="dark"] #inventoryFilterAllBtn,
    html[data-theme="dark"] #inventoryFilterAllBtn.is-active {
        background: #facc15;
        color: #111827;
        border-color: #facc15;
    }
    html[data-theme="dark"] .inventory-filter-pill.is-active {
        background: #facc15;
        color: #111827;
        border-color: #facc15;
    }
    /* Arrow icon inside the All pill */
    .inventory-filter-all-arrow {
        width: 12px;
        height: 12px;
        flex: 0 0 auto;
        transition: transform .22s cubic-bezier(.22, 1, .36, 1);
    }
    .inventory-filter-bar.is-expanded #inventoryFilterAllBtn .inventory-filter-all-arrow {
        transform: rotate(90deg);
    }
    /* Option pills hidden until bar is expanded */
    .inventory-filter-option {
        display: none;
        opacity: 0;
    }
    .inventory-filter-bar.is-expanded .inventory-filter-option {
        display: inline-flex;
        animation: filterPillSlideIn .32s cubic-bezier(.22, 1, .36, 1) forwards;
    }
    .inventory-filter-bar.is-expanded .inventory-filter-option:nth-child(2) { animation-delay:  0ms; }
    .inventory-filter-bar.is-expanded .inventory-filter-option:nth-child(3) { animation-delay: 55ms; }
    .inventory-filter-bar.is-expanded .inventory-filter-option:nth-child(4) { animation-delay: 110ms; }
    .inventory-filter-bar.is-expanded .inventory-filter-option:nth-child(5) { animation-delay: 165ms; }
    .inventory-filter-bar.is-expanded .inventory-filter-option:nth-child(6) { animation-delay: 220ms; }
    html[data-theme="dark"] .inventory-filter-pill {
        background: rgba(30, 41, 59, 0.72);
        color: #ffffff !important;
        border-color: rgba(250, 204, 21, 0.16);
    }
    html[data-theme="dark"] .inventory-filter-pill:hover {
        background: #facc15;
        color: #111827 !important;
        border-color: #facc15;
    }
    html[data-theme="dark"] .inventory-filter-pill.is-active {
        background: #facc15;
        color: #111827 !important;
        border-color: #facc15;
    }
    html[data-theme="dark"] #inventoryFilterAllBtn,
    html[data-theme="dark"] #inventoryFilterAllBtn.is-active {
        background: #facc15;
        color: #111827 !important;
        border-color: #facc15;
    }
    html[data-theme="dark"] .inventory-meta-pill {
        background: rgba(250, 204, 21, 0.14);
        border-color: rgba(250, 204, 21, 0.28);
        color: #fde68a !important;
    }
    .inventory-meta-pill {
        display: inline-flex;
        align-items: center;
        min-height: 24px;
        padding: 0 8px;
        border-radius: 999px;
        background: #fff7ed;
        color: #9a3412;
        border: 1px solid #fed7aa;
        font-size: 11px;
        font-weight: 800;
        margin-top: 4px;
    }
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 42px;
        min-height: 42px;
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
        background: #facc15;
        color: #111827;
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
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
        background: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
        box-shadow:
            0 0 0 3px rgba(248, 113, 113, 0.18),
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
        padding: clamp(12px, 2vw, 28px);
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 1000; 
        justify-content: center; 
        align-items: center; 
    }
    .modal-box {
        background: rgba(255, 255, 255, 0.95);
        width: min(100%, 920px);
        max-width: 100%;
        height: min(920px, calc(100dvh - clamp(24px, 4vw, 56px)));
        max-height: min(920px, calc(100dvh - clamp(24px, 4vw, 56px)));
        border: 1px solid rgba(112, 19, 27, 0.14);
        border-radius: 22px;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.16);
        overflow: hidden;
        padding: 0;
        display: flex;
        flex-direction: column;
        position: relative;
        min-width: 320px;
        min-height: 0;
        backdrop-filter: blur(4px);
    }
    #itemModal .modal-box {
        background: rgba(255, 255, 255, 0.42) !important;
        width: min(100%, 1040px);
        max-width: 100%;
        height: min(920px, calc(100dvh - clamp(24px, 4vw, 56px)));
        max-height: min(920px, calc(100dvh - clamp(24px, 4vw, 56px)));
        border-left: 1px solid rgba(112, 19, 27, 0.12) !important;
        border-right: 1px solid rgba(112, 19, 27, 0.12) !important;
        border-top: 4px solid #66ff00 !important;
        border-bottom: 4px solid #70131B !important;
        border-radius: 18px !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
    #restockModal .modal-box,
    #historyModal .modal-box {
        border-left: 1px solid rgba(112, 19, 27, 0.12) !important;
        border-right: 1px solid rgba(112, 19, 27, 0.12) !important;
        border-top: 4px solid #66ff00 !important;
        border-bottom: 4px solid #70131B !important;
        border-radius: 18px !important;
    }
    .modal-box .inventory-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: clamp(12px, 1.4vw, 18px) clamp(14px, 1.6vw, 22px);
        background: linear-gradient(135deg, #70131B, #8f2230);
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        flex: 0 0 auto;
        position: sticky;
        top: 0;
        z-index: 10;
        backdrop-filter: blur(8px);
        overflow: hidden;
    }
    .item-modal-head-icon {
        position: absolute;
        right: 56px;
        top: 50%;
        transform: translateY(-50%);
        width: 72px;
        height: 72px;
        color: rgba(255, 255, 255, 0.07);
        pointer-events: none;
        flex: 0 0 auto;
        z-index: 0;
    }
    .item-modal-head-icon svg {
        width: 100%;
        height: 100%;
    }
    .modal-box .inventory-modal-head-main,
    .modal-box .inventory-modal-head > button {
        position: relative;
        z-index: 1;
    }
    .modal-box .inventory-modal-head-main {
        min-width: 0;
        flex: 1 1 auto;
        color: #ffffff;
    }
    .modal-box .inventory-modal-title,
    .modal-box .inventory-modal-copy {
        color: #ffffff !important;
    }
    .modal-box .inventory-modal-body {
        flex: 1 1 auto;
        overflow-y: auto;
        padding: clamp(18px, 2.2vw, 26px);
        min-height: 0;
        background: transparent;
        overscroll-behavior: contain;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    .modal-box .inventory-modal-body::-webkit-scrollbar {
        width: 0;
        height: 0;
    }
    .modal-box .inventory-modal-body::-webkit-scrollbar-track {
        background: transparent;
    }
    .modal-box .inventory-modal-body::-webkit-scrollbar-thumb {
        background: transparent;
    }
    .inventory-modal-preview,
    .inventory-modal-summary-row {
        display: grid;
        gap: 10px;
        margin-top: 14px;
    }
    .inventory-modal-preview {
        padding: 16px;
        border-radius: 18px;
        background: #f8fafc;
        border: 1px solid rgba(112, 19, 27, 0.1);
    }
    .inventory-modal-preview .preview-row,
    .inventory-modal-summary-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }
    .inventory-modal-preview .preview-label,
    .inventory-modal-summary-card .summary-label {
        color: #475569;
        font-size: 13px;
        font-weight: 700;
    }
    .inventory-modal-preview .preview-row strong,
    .inventory-modal-summary-card .summary-value {
        color: #111827;
        font-size: 14px;
        font-weight: 900;
    }
    .form-note {
        display: block;
        margin-top: 6px;
        font-size: 12px;
        color: #6b7280;
        line-height: 1.4;
    }
    .inventory-modal-summary-row {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .inventory-modal-summary-card {
        padding: 14px 16px;
        border-radius: 16px;
        background: #ffffff;
        border: 1px solid rgba(112, 19, 27, 0.12);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
    }
    .inventory-history-list {
        display: grid;
        gap: 14px;
    }
    .history-card {
        padding: 16px;
        border-radius: 18px;
        background: #ffffff;
        border: 1px solid rgba(112, 19, 27, 0.12);
        box-shadow: 0 12px 20px rgba(15, 23, 42, 0.06);
    }
    .history-card-head {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 10px;
    }
    .history-card-type {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 999px;
        background: #f8fafc;
        color: #70131B;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .history-card-body {
        display: grid;
        gap: 8px;
    }
    .history-card-quantity {
        color: #111827;
        font-size: 16px;
        font-weight: 800;
    }
    .history-card-stock,
    .history-card-note,
    .history-card-meta {
        color: #475569;
        font-size: 13px;
        line-height: 1.4;
    }
    .history-card-meta {
        font-weight: 700;
    }
    .inventory-modal-copy {
        margin: 6px 0 0;
        color: #ffffff !important;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.45;
    }
    #itemModal .inventory-modal-body {
        padding: clamp(16px, 2.6vw, 26px);
        overflow-y: auto;
        min-height: 0;
        background: transparent;
        overscroll-behavior: contain;
    }
    .modal-form-grid { 
        display: grid; 
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); 
        gap: clamp(14px, 2vw, 22px);
        align-items: start;
    }
    #itemModal .modal-form-panel {
        border: 1px solid rgba(112, 19, 27, 0.15);
        border-radius: clamp(12px, 1.8vw, 16px);
        background: #ffffff;
        padding: clamp(14px, 2vw, 18px);
        min-width: 0;
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

    .inventory-category-wrap,
    .inventory-medicine-type-wrap {
        position: relative;
    }

    .inventory-category-select,
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

    .inventory-category-display,
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

    .inventory-category-display:hover,
    .inventory-medicine-type-display:hover {
        border-color: rgba(139, 0, 0, 0.34);
        box-shadow:
            0 14px 24px rgba(15, 23, 42, 0.10),
            0 8px 18px rgba(139, 0, 0, 0.05),
            inset 0 1px 0 rgba(255,255,255,0.90);
        transform: translateY(-1px);
    }

    .inventory-category-display.is-open,
    .inventory-category-display:focus,
    .inventory-medicine-type-display.is-open,
    .inventory-medicine-type-display:focus {
        outline: none;
        border-color: #8B0000;
        box-shadow:
            0 0 0 4px rgba(139, 0, 0, 0.06),
            0 14px 24px rgba(139, 0, 0, 0.10),
            inset 0 1px 0 rgba(255,255,255,0.88);
    }

    .inventory-category-wrap::after,
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

    .inventory-category-wrap::before,
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

    .inventory-category-wrap.is-open::after,
    .inventory-medicine-type-wrap.is-open::after {
        transform: translateY(-20%) rotate(225deg);
    }

    .inventory-category-menu,
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

    .inventory-category-wrap.is-open .inventory-category-menu,
    .inventory-medicine-type-wrap.is-open .inventory-medicine-type-menu {
        display: grid;
    }

    .inventory-medicine-type-menu.is-open {
        display: grid;
    }

    #itemModal .inventory-medicine-type-wrap.is-open .inventory-medicine-type-menu,
    body > .inventory-medicine-type-menu.is-open {
        position: fixed;
        right: auto;
        z-index: 2200;
        width: min(420px, calc(100vw - 32px));
        max-height: min(360px, calc(100vh - 32px));
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

    .inventory-category-option,
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

    .inventory-category-option:hover,
    .inventory-category-option.is-selected,
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
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }
    .inventory-date-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        align-items: start;
    }
    #medicineExpiryField {
        display: none;
    }
    #itemModal form {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
    }
    .modal-actions-row {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin: 0;
        padding: 18px clamp(18px, 2.2vw, 26px);
        background: transparent;
        border-top: none;
        flex: 0 0 auto;
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

    @media (max-width: 980px) {
        #itemModal .modal-box {
            width: min(100%, 760px);
        }

        .modal-form-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .modal-overlay {
            align-items: stretch;
            padding: 10px;
        }

        #itemModal .modal-box {
            width: 100%;
            max-height: calc(100dvh - 20px);
            border-radius: 14px !important;
        }

        #itemModal .inventory-modal-head {
            align-items: flex-start;
            gap: 12px;
        }

        .inventory-inline-grid {
            grid-template-columns: 1fr;
        }

        .inventory-date-grid {
            grid-template-columns: 1fr;
        }

        #itemModal .inventory-modal-body {
            padding: 14px;
        }

        #itemModal .form-group {
            padding: 10px;
        }

        .modal-actions-row {
            position: static;
            margin: 18px 0 0;
            padding: 12px 14px 0;
            background: transparent;
            border-top: none;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
        }
    }

    @media (max-width: 420px) {
        .modal-overlay {
            padding: 0;
        }

        #itemModal .modal-box {
            max-height: 100dvh;
            border-radius: 0 !important;
            border-left: 0 !important;
            border-right: 0 !important;
        }

        .inventory-modal-title {
            font-size: 16px;
        }

        .inventory-modal-copy {
            font-size: 12px;
        }

        .inventory-category-display,
        .inventory-medicine-type-display {
            min-height: 48px;
            padding-top: 12px;
            padding-bottom: 12px;
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
    html[data-theme="dark"] .inventory-filter-btn {
        background: rgba(15, 23, 42, 0.92);
        color: #f8fafc;
        border-color: rgba(250, 204, 21, 0.18);
    }
    html[data-theme="dark"] .inventory-filter-btn:hover,
    html[data-theme="dark"] .inventory-filter-btn.is-active {
        background: #facc15;
        color: #111827;
        border-color: #facc15;
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

    html[data-theme="dark"] #restockModal .modal-box,
    html[data-theme="dark"] #historyModal .modal-box {
        border-left: 1px solid rgba(143, 34, 48, 0.36) !important;
        border-right: 1px solid rgba(143, 34, 48, 0.36) !important;
        border-top: 4px solid #facc15 !important;
        border-bottom: 4px solid #facc15 !important;
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

    html[data-theme="dark"] .inventory-actions-menu {
        background: rgba(15, 23, 42, 0.97);
        border-color: rgba(250, 204, 21, 0.14);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.42);
    }
    html[data-theme="dark"] .inventory-actions-menu-item {
        background: rgba(30, 41, 59, 0.92);
        color: #f1f5f9;
        border-color: rgba(148, 163, 184, 0.16);
    }
    html[data-theme="dark"] .inventory-actions-menu-item:hover {
        background: #facc15;
        color: #111827;
        border-color: #facc15;
    }
    html[data-theme="dark"] .inventory-actions-menu-item.btn-delete {
        background: rgba(127, 29, 29, 0.42);
        color: #fca5a5;
        border-color: rgba(248, 113, 113, 0.22);
    }
    html[data-theme="dark"] .inventory-actions-menu-item.btn-delete:hover {
        background: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
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

    html[data-theme="dark"] .modal-actions-row {
        background: rgba(15, 23, 42, 0.92);
        border-top-color: rgba(250, 204, 21, 0.16);
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

    html[data-theme="dark"] .inventory-category-display,
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

    html[data-theme="dark"] .inventory-category-display:hover,
    html[data-theme="dark"] .inventory-category-display:focus,
    html[data-theme="dark"] .inventory-category-display.is-open,
    html[data-theme="dark"] .inventory-medicine-type-display:hover,
    html[data-theme="dark"] .inventory-medicine-type-display:focus,
    html[data-theme="dark"] .inventory-medicine-type-display.is-open {
        border-color: #facc15;
        box-shadow:
            0 0 0 4px rgba(250, 204, 21, 0.14),
            0 14px 24px rgba(0, 0, 0, 0.26),
            inset 0 1px 0 rgba(255,255,255,0.06);
    }

    html[data-theme="dark"] .inventory-category-wrap::after,
    html[data-theme="dark"] .inventory-medicine-type-wrap::after {
        border-right-color: #facc15;
        border-bottom-color: #facc15;
    }

    html[data-theme="dark"] .inventory-category-wrap::before,
    html[data-theme="dark"] .inventory-medicine-type-wrap::before {
        background: rgba(250, 204, 21, 0.18);
    }

    html[data-theme="dark"] .inventory-category-menu,
    html[data-theme="dark"] .inventory-medicine-type-menu,
    html[data-theme="dark"] body > .inventory-medicine-type-menu {
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

    html[data-theme="dark"] .inventory-category-option,
    html[data-theme="dark"] .inventory-medicine-type-option {
        color: #f8fafc !important;
        border-color: rgba(250, 204, 21, 0.14);
        background: linear-gradient(180deg, rgba(40, 26, 26, 0.98) 0%, rgba(23, 23, 23, 0.98) 100%);
        box-shadow:
            0 12px 22px rgba(0, 0, 0, 0.22),
            inset 0 1px 0 rgba(255,255,255,0.04);
    }

    html[data-theme="dark"] .inventory-category-option:hover,
    html[data-theme="dark"] .inventory-category-option.is-selected,
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
        background: #facc15;
        color: #111827;
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 12px 24px rgba(0, 0, 0, 0.30);
    }

    html[data-theme="dark"] .btn-delete {
        background: linear-gradient(180deg, rgba(127, 29, 29, 0.92) 0%, rgba(69, 10, 10, 0.96) 100%);
        color: #fee2e2;
        border-color: rgba(248, 113, 113, 0.22);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.28);
    }

    html[data-theme="dark"] .btn-delete:hover {
        background: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
        box-shadow:
            0 0 0 3px rgba(248, 113, 113, 0.18),
            0 12px 24px rgba(0, 0, 0, 0.32);
    }

    /* --- Restock modal header — two-column layout with big frames on the right --- */
    #restockModal .inventory-modal-head {
        align-items: stretch;
        gap: 16px;
    }
    .restock-head-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 10px;
        flex: 0 0 auto;
    }
    .restock-stock-frames {
        display: flex;
        gap: 10px;
        flex: 1 1 auto;
        align-items: stretch;
    }
    .restock-stock-frame {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-width: 108px;
        padding: 14px 16px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.10);
        border: 1px solid rgba(255, 255, 255, 0.20);
        gap: 5px;
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        transition: background .2s ease, border-color .2s ease;
    }
    .restock-stock-frame-after {
        background: rgba(250, 204, 21, 0.16);
        border-color: rgba(250, 204, 21, 0.38);
    }
    .restock-frame-label {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgba(255, 255, 255, 0.72) !important;
        white-space: nowrap;
    }
    .restock-frame-value {
        font-size: 22px;
        font-weight: 900;
        color: #ffffff !important;
        line-height: 1.15;
        text-align: center;
        word-break: break-all;
    }
    .restock-stock-frame-after .restock-frame-value {
        color: #facc15 !important;
    }
    @media (max-width: 600px) {
        #restockModal .inventory-modal-head {
            flex-wrap: wrap;
        }
        .restock-head-right {
            width: 100%;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
        .restock-stock-frames {
            flex: 1 1 auto;
        }
    }

    /* --- Restock modal — form field styling (mirrors #itemModal) --- */
    #restockModal .form-group {
        margin-bottom: 14px;
        display: flex;
        flex-direction: column;
        border: 1px solid rgba(112, 19, 27, 0.13);
        background: rgba(255, 255, 255, 0.72);
        border-radius: 12px;
        padding: 11px 12px;
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.95),
            0 2px 4px rgba(112, 19, 27, 0.04),
            0 8px 16px rgba(112, 19, 27, 0.08),
            0 18px 32px rgba(112, 19, 27, 0.06);
        transition: box-shadow .2s ease, border-color .2s ease, transform .2s ease;
    }
    #restockModal .form-group:focus-within {
        border-color: rgba(112, 19, 27, 0.30);
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.95),
            0 2px 4px rgba(112, 19, 27, 0.06),
            0 10px 24px rgba(112, 19, 27, 0.13),
            0 22px 40px rgba(112, 19, 27, 0.08);
        transform: translateY(-1px);
    }
    #restockModal .form-group label {
        font-size: 0.74rem;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 4px;
    }
    #restockModal .form-control,
    #restockModal textarea.form-control {
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
        resize: none;
        transition: color .18s ease, border-color .18s ease;
    }
    #restockModal .form-control:focus,
    #restockModal textarea.form-control:focus {
        outline: none;
        border-bottom: 1px solid #8f2230;
        background: transparent;
        box-shadow: none;
    }
    #restockModal .form-control::placeholder,
    #restockModal textarea.form-control::placeholder {
        color: #6b7280;
        font-weight: 600;
    }
    html[data-theme="dark"] #restockModal .form-group {
        background: rgba(31, 41, 55, 0.92);
        border-color: rgba(148, 163, 184, 0.22);
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.05),
            0 2px 4px rgba(0, 0, 0, 0.12),
            0 8px 20px rgba(0, 0, 0, 0.22),
            0 18px 36px rgba(0, 0, 0, 0.16);
    }
    html[data-theme="dark"] #restockModal .form-group:focus-within {
        border-color: rgba(250, 204, 21, 0.32);
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.05),
            0 2px 4px rgba(0, 0, 0, 0.14),
            0 10px 26px rgba(0, 0, 0, 0.28),
            0 22px 42px rgba(0, 0, 0, 0.20);
        transform: translateY(-1px);
    }
    html[data-theme="dark"] #restockModal .form-group label {
        color: #ffffff !important;
    }
    html[data-theme="dark"] #restockModal .form-control,
    html[data-theme="dark"] #restockModal textarea.form-control {
        color: #ffffff;
        border-bottom-color: rgba(148, 163, 184, 0.36);
    }
    html[data-theme="dark"] #restockModal .form-control:focus,
    html[data-theme="dark"] #restockModal textarea.form-control:focus {
        border-bottom-color: #facc15;
    }
    html[data-theme="dark"] #restockModal .form-control::placeholder,
    html[data-theme="dark"] #restockModal textarea.form-control::placeholder {
        color: #94a3b8;
    }
    @media (max-width: 760px) {
        #restockModal .form-group { padding: 10px; }
    }

    /* --- Restock quick-add preset buttons --- */
    .restock-quick-btns {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 14px;
    }
    .restock-quick-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 16px;
        border-radius: 999px;
        border: 1px solid rgba(112, 19, 27, 0.22);
        background: linear-gradient(180deg, #fff8f6 0%, #fff1ee 100%);
        color: #70131B;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        transition: background .16s ease, color .16s ease, border-color .16s ease, transform .16s ease, box-shadow .16s ease;
        box-shadow: 0 4px 10px rgba(112, 19, 27, 0.08);
    }
    .restock-quick-label {
        font-size: 11px;
        font-weight: 800;
        color: #70131B;
        align-self: center;
        text-transform: uppercase;
        letter-spacing: .06em;
        flex: 0 0 auto;
    }
    .restock-quick-btn:hover {
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #facc15;
        border-color: #70131B;
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(112, 19, 27, 0.18);
    }
    html[data-theme="dark"] .restock-quick-btn {
        background: rgba(112, 19, 27, 0.22);
        color: #ffffff;
        border-color: #facc15;
    }
    html[data-theme="dark"] .restock-quick-btn:hover {
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #facc15;
        border-color: #facc15;
    }
    html[data-theme="dark"] .restock-quick-label {
        color: #ffffff;
    }

    /* --- History stat bar (Total In / Total Out / Net) --- */
    .history-stat-bar {
        display: flex;
        gap: 8px;
        margin-top: 12px;
        flex-wrap: wrap;
    }
    .history-stat-chip {
        flex: 1 1 0;
        min-width: 80px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 8px 10px;
        border-radius: 14px;
        border: 1px solid;
        gap: 2px;
    }
    .history-stat-chip-label {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        opacity: 0.72;
    }
    .history-stat-chip-value {
        font-size: 15px;
        font-weight: 900;
        line-height: 1.2;
    }
    .history-stat-chip.chip-in {
        background: rgba(21, 128, 61, 0.72);
        border-color: rgba(255, 255, 255, 0.18);
        color: #ffffff;
    }
    .history-stat-chip.chip-out {
        background: rgba(185, 28, 28, 0.72);
        border-color: rgba(255, 255, 255, 0.18);
        color: #ffffff;
    }
    .history-stat-chip.chip-net-pos {
        background: rgba(29, 78, 216, 0.72);
        border-color: rgba(255, 255, 255, 0.18);
        color: #ffffff;
    }
    .history-stat-chip.chip-net-neg {
        background: rgba(180, 83, 9, 0.72);
        border-color: rgba(255, 255, 255, 0.18);
        color: #ffffff;
    }
    html[data-theme="dark"] .history-stat-chip.chip-in      { background: rgba(21,128,61,0.72);   border-color: rgba(255,255,255,0.14); color: #ffffff; }
    html[data-theme="dark"] .history-stat-chip.chip-out     { background: rgba(185,28,28,0.72);   border-color: rgba(255,255,255,0.14); color: #ffffff; }
    html[data-theme="dark"] .history-stat-chip.chip-net-pos { background: rgba(29,78,216,0.72);   border-color: rgba(255,255,255,0.14); color: #ffffff; }
    html[data-theme="dark"] .history-stat-chip.chip-net-neg { background: rgba(180,83,9,0.72);    border-color: rgba(255,255,255,0.14); color: #ffffff; }

    /* --- History card color-coded by type --- */
    .history-card {
        border-left-width: 4px !important;
        border-left-style: solid !important;
    }
    .history-card[data-movement-type="restock"]   { border-left-color: #16a34a !important; }
    .history-card[data-movement-type="dispensed"],
    .history-card[data-movement-type="dispense"],
    .history-card[data-movement-type="used"],
    .history-card[data-movement-type="consumed"]  { border-left-color: #dc2626 !important; }
    .history-card[data-movement-type="created"]   { border-left-color: #2563eb !important; }
    .history-card[data-movement-type="adjusted"],
    .history-card[data-movement-type="adjustment"]{ border-left-color: #d97706 !important; }

    .history-card-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .history-card-type-badge svg {
        width: 13px;
        height: 13px;
        flex: 0 0 auto;
    }
    .badge-restock   { background: #dcfce7; color: #15803d; }
    .badge-dispensed,
    .badge-dispense,
    .badge-used,
    .badge-consumed  { background: #fee2e2; color: #b91c1c; }
    .badge-created   { background: #dbeafe; color: #1d4ed8; }
    .badge-adjusted,
    .badge-adjustment{ background: #fef3c7; color: #b45309; }
    .badge-default   { background: #f1f5f9; color: #475569; }

    html[data-theme="dark"] .badge-restock    { background: rgba(21,128,61,0.22);  color: #86efac; }
    html[data-theme="dark"] .badge-dispensed,
    html[data-theme="dark"] .badge-dispense,
    html[data-theme="dark"] .badge-used,
    html[data-theme="dark"] .badge-consumed   { background: rgba(185,28,28,0.22);  color: #fca5a5; }
    html[data-theme="dark"] .badge-created    { background: rgba(29,78,216,0.22);  color: #93c5fd; }
    html[data-theme="dark"] .badge-adjusted,
    html[data-theme="dark"] .badge-adjustment { background: rgba(180,83,9,0.22);   color: #fcd34d; }
    html[data-theme="dark"] .badge-default    { background: rgba(71,85,105,0.22);  color: #94a3b8; }
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
        <div class="inventory-filter-bar" id="inventoryFilterBar" aria-label="Inventory filters">
            <button type="button" class="inventory-filter-pill is-active" data-inventory-filter="all" id="inventoryFilterAllBtn">
                <span id="inventoryFilterAllLabel">All</span>
                <svg class="inventory-filter-all-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </button>
            <button type="button" class="inventory-filter-pill inventory-filter-option" data-inventory-filter="medicine">Medicines</button>
            <button type="button" class="inventory-filter-pill inventory-filter-option" data-inventory-filter="supplies">Supplies</button>
            <button type="button" class="inventory-filter-pill inventory-filter-option" data-inventory-filter="equipment">Equipment</button>
            <button type="button" class="inventory-filter-pill inventory-filter-option" data-inventory-filter="low">Low Stock</button>
            <button type="button" class="inventory-filter-pill inventory-filter-option" data-inventory-filter="out">Out of Stock</button>
        </div>
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
                    @php
                        $effectiveQty     = $item->hasDispensingConversion() ? $item->availableDispensingQuantity() : (float) $item->quantity;
                        $effectiveMinUnit = $item->hasDispensingConversion() ? $item->dispensing_unit : ($item->unit ?: 'pcs');
                    @endphp
                    <tr
                        id="inventory-item-{{ $item->id }}"
                        data-inventory-row
                        data-category="{{ strtolower($item->category) }}"
                        data-stock="{{ (float) $item->quantity }}"
                        data-effective-qty="{{ $effectiveQty }}"
                        data-minimum-stock="{{ (float) ($item->minimum_stock ?: 10) }}"
                        class="{{ $highlightClass }}"
                    >
                        <td style="font-weight: 600;">{{ $item->name }}</td>
                        <td>
                            {{ $item->category }}
                            @if($item->category == 'Medicine' && $item->medicine_type)
                                <small style="display:block; color:#64748b; font-style: italic;">({{ $item->medicine_type }})</small>
                            @endif
                            @if($item->batch_number)
                                <span class="inventory-meta-pill">Batch: {{ $item->batch_number }}</span>
                            @endif
                            @if($item->supplier_source)
                                <small style="display:block; color:#64748b; margin-top:4px;">Source: {{ $item->supplier_source }}</small>
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
                            <small style="display:block; color:#64748b; margin-top:4px;">
                                Starting stock: {{ rtrim(rtrim(number_format((float) ($item->starting_stock ?: 0), 2, '.', ''), '0'), '.') }} {{ $item->unit ?: 'pcs' }}
                            </small>
                            <small style="display:block; color:#64748b; margin-top:4px;">
                                Minimum stock: {{ rtrim(rtrim(number_format((float) ($item->minimum_stock ?: 10), 2, '.', ''), '0'), '.') }} {{ $effectiveMinUnit }}
                            </small>
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
                            @elseif($effectiveQty <= (float) ($item->minimum_stock ?: 10))
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
                                        'minimum_stock' => $item->minimum_stock,
                                        'batch_number' => $item->batch_number,
                                        'supplier_source' => $item->supplier_source,
                                        'medicine_type_id' => $item->medicine_type_id,
                                        'dispensing_unit' => $item->dispensing_unit,
                                        'units_per_stock_unit' => $item->units_per_stock_unit,
                                        'medicine_type' => $item->medicine_type,
                                        'date_added' => optional($item->date_added)->format('Y-m-d'),
                                        'expiration_date' => optional($item->expiration_date)->format('Y-m-d'),
                                        'movements' => $item->movements->take(10)->map(function ($movement) {
                                            return [
                                                'type' => $movement->type,
                                                'quantity' => $movement->quantity,
                                                'stock_before' => $movement->stock_before,
                                                'stock_after' => $movement->stock_after,
                                                'unit' => $movement->unit,
                                                'batch_number' => $movement->batch_number,
                                                'supplier_source' => $movement->supplier_source,
                                                'notes' => $movement->notes,
                                                'user_name' => optional($movement->user)->name,
                                                'created_at' => optional($movement->created_at)->format('M d, Y g:i A'),
                                            ];
                                        })->values(),
                                    ];
                                @endphp
                                <div class="inventory-actions">
                                    <div class="inventory-actions-dropdown">
                                        <button type="button" class="btn-icon btn-edit inventory-actions-toggle" onclick="toggleInventoryActionMenu(event)">
                                            <x-outline-icon name="bars-3" />
                                            <span>Actions</span>
                                        </button>
                                        <div class="inventory-actions-menu" role="menu">
                                            <button type="button" class="inventory-actions-menu-item" onclick='closeInventoryActionMenus(); openRestockModal(@json($editItemPayload));'>
                                                <span>Restock</span>
                                            </button>
                                            <button type="button" class="inventory-actions-menu-item" onclick='closeInventoryActionMenus(); editItem(@json($editItemPayload));'>
                                                <span>Edit</span>
                                            </button>
                                            <button type="button" class="inventory-actions-menu-item" onclick='closeInventoryActionMenus(); openHistoryModal(@json($editItemPayload));'>
                                                <span>History</span>
                                            </button>
                                            <form action="{{ url('/admin/inventory/'.$item->id) }}" method="POST" style="margin:0;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="inventory-actions-menu-item btn-delete" onclick="return confirm('Delete this item?')">
                                                    <span>Delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
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
                        <h3 id="modalTitle" class="inventory-modal-title" style="font-size:clamp(17px,1.6vw,22px); margin:0; font-weight:900;">Add New Item</h3>
                        <p class="inventory-modal-copy" style="margin:5px 0 0; font-size:13.5px; line-height:1.5;">Provide inventory details and save to update clinic stock records.</p>
                    </div>
                    <div class="item-modal-head-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                            <line x1="12" y1="22.08" x2="12" y2="12"/>
                        </svg>
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
                                    <div class="inventory-category-wrap" id="inventoryCategoryWrap">
                                        <select name="category" id="iCategory" class="form-control inventory-category-select" onchange="toggleMedicineFields()">
                                            <option value="Medicine">Medicine</option>
                                            <option value="Equipment">Equipment</option>
                                            <option value="Supplies">Supplies</option>
                                        </select>
                                        <button type="button" class="inventory-category-display" id="inventoryCategoryDisplay" aria-haspopup="listbox" aria-expanded="false">
                                            Medicine
                                        </button>
                                        <div class="inventory-category-menu" id="inventoryCategoryMenu" role="listbox" aria-label="Category options">
                                            <button type="button" class="inventory-category-option" data-category-value="Medicine">Medicine</button>
                                            <button type="button" class="inventory-category-option" data-category-value="Equipment">Equipment</button>
                                            <button type="button" class="inventory-category-option" data-category-value="Supplies">Supplies</button>
                                        </div>
                                    </div>
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
                                        <label>Minimum Stock &mdash; <span id="iMinStockUnitLabel" style="font-weight:800; color:#70131B; text-transform:none;">pcs</span></label>
                                        <input type="number" name="minimum_stock" id="iMinimumStock" class="form-control" min="0" step="0.01" placeholder="e.g. 10">
                                        <span class="form-note" id="iMinStockNote">Value is in the stock unit shown above.</span>
                                    </div>
                                </div>

                                <div class="inventory-inline-grid">
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

                                    <div class="form-group">
                                        <label>Batch / Lot Number</label>
                                        <input type="text" name="batch_number" id="iBatchNumber" class="form-control" placeholder="e.g. LOT-2026-05">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Supplier / Source</label>
                                    <input type="text" name="supplier_source" id="iSupplierSource" class="form-control" placeholder="e.g. Campus supply office, donated, purchased">
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

        <div id="restockModal" class="modal-overlay">
            <div class="modal-box">
                <div class="inventory-modal-head">
                    <div class="inventory-modal-head-main">
                        <h3 class="inventory-modal-title">Restock Item</h3>
                        <p class="inventory-modal-copy" id="restockItemName">Add stock without overwriting the item record.</p>
                    </div>
                    <div class="restock-head-right">
                        <button type="button" class="inventory-btn-cancel inventory-modal-close" onclick="closeRestockModal()" aria-label="Close restock modal">
                            <x-outline-icon name="x-mark" />
                        </button>
                        <div class="restock-stock-frames">
                            <div class="restock-stock-frame">
                                <span class="restock-frame-label">Current Stock</span>
                                <strong class="restock-frame-value" id="restockCurrentStock">—</strong>
                            </div>
                            <div class="restock-stock-frame restock-stock-frame-after">
                                <span class="restock-frame-label">After Restock</span>
                                <strong class="restock-frame-value" id="restockPreviewLine">—</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="restockForm" method="POST" action="#">
                    @csrf
                    <div class="inventory-modal-body">
                        <div class="restock-quick-btns" id="restockQuickBtns" aria-label="Quick add presets">
                            <span class="restock-quick-label">Quick Add:</span>
                            <button type="button" class="restock-quick-btn" data-preset="5">+5</button>
                            <button type="button" class="restock-quick-btn" data-preset="10">+10</button>
                            <button type="button" class="restock-quick-btn" data-preset="25">+25</button>
                            <button type="button" class="restock-quick-btn" data-preset="50">+50</button>
                            <button type="button" class="restock-quick-btn" data-preset="100">+100</button>
                        </div>
                        <div class="inventory-inline-grid">
                            <div class="form-group">
                                <label>Quantity to Add &mdash; <span id="restockUnitLabel" style="font-weight:800;color:#70131B;text-transform:none;">pcs</span></label>
                                <input type="number" name="restock_quantity" id="restockQuantity" class="form-control" min="0.01" step="0.01" required placeholder="e.g. 5">
                                <small class="form-note">Or click a preset above to fill quickly.</small>
                            </div>
                            <div class="form-group">
                                <label>Restock Date</label>
                                <input type="date" name="restock_date" id="restockDate" class="form-control">
                            </div>
                        </div>
                        <div class="inventory-inline-grid">
                            <div class="form-group">
                                <label>Batch / Lot Number</label>
                                <input type="text" name="batch_number" id="restockBatchNumber" class="form-control" placeholder="Optional">
                            </div>
                            <div class="form-group">
                                <label>Supplier / Source</label>
                                <input type="text" name="supplier_source" id="restockSupplierSource" class="form-control" placeholder="Optional">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="restock_notes" class="form-control" rows="3" placeholder="Optional restock note"></textarea>
                        </div>
                        <div class="modal-actions-row">
                            <button type="submit" class="btn-add">Save Restock</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="historyModal" class="modal-overlay">
            <div class="modal-box">
                <div class="inventory-modal-head">
                    <div class="inventory-modal-head-main">
                        <h3 class="inventory-modal-title">Stock Movement History</h3>
                        <p class="inventory-modal-copy" id="historyItemName">Recent inventory activity.</p>
                        <div class="history-stat-bar">
                            <div class="history-stat-chip chip-in">
                                <span class="history-stat-chip-label">Total In</span>
                                <span class="history-stat-chip-value" id="historyTotalIn">+0</span>
                            </div>
                            <div class="history-stat-chip chip-out">
                                <span class="history-stat-chip-label">Total Out</span>
                                <span class="history-stat-chip-value" id="historyTotalOut">0</span>
                            </div>
                            <div class="history-stat-chip chip-net-pos" id="historyNetChip">
                                <span class="history-stat-chip-label">Net Change</span>
                                <span class="history-stat-chip-value" id="historyNetChange">0</span>
                            </div>
                            <div class="history-stat-chip chip-in" style="background:rgba(255,255,255,0.18);border-color:rgba(255,255,255,0.22);color:#ffffff;">
                                <span class="history-stat-chip-label">Movements</span>
                                <span class="history-stat-chip-value" id="historyMovementCount">0</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="inventory-btn-cancel inventory-modal-close" onclick="closeHistoryModal()" aria-label="Close history modal">
                        <x-outline-icon name="x-mark" />
                    </button>
                </div>
                <div class="inventory-modal-body">
                    <div id="historyList" class="inventory-history-list"></div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    const itemModal = document.getElementById('itemModal');
    const restockModal = document.getElementById('restockModal');
    const historyModal = document.getElementById('historyModal');
    const itemForm = document.getElementById('itemForm');
    const restockForm = document.getElementById('restockForm');
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
    const inventoryFilterToggle = document.getElementById('inventoryFilterToggle');
    const inventoryFilterMenu = document.getElementById('inventoryFilterMenu');
    const inventoryFilterItems = Array.from(document.querySelectorAll('.inventory-filter-pill'));
    let activeInventoryFilter = 'all';
    const categorySelect = document.getElementById('iCategory');
    const categoryWrap = document.getElementById('inventoryCategoryWrap');
    const categoryDisplay = document.getElementById('inventoryCategoryDisplay');
    const categoryOptions = Array.from(document.querySelectorAll('.inventory-category-option'));
    const medicineTypeWrap = document.getElementById('inventoryMedicineTypeWrap');
    const medicineTypeDisplay = document.getElementById('inventoryMedicineTypeDisplay');
    const medicineTypeMenu = document.getElementById('inventoryMedicineTypeMenu');
    const medicineTypeSearch = document.getElementById('inventoryMedicineTypeSearch');
    const medicineTypeOptions = Array.from(document.querySelectorAll('.inventory-medicine-type-option'));
    const medicineTypeEmpty = document.getElementById('inventoryMedicineTypeEmpty');
    const medicineTypeMenuHome = medicineTypeMenu ? medicineTypeMenu.parentElement : null;
    const restockQuantityInput = document.getElementById('restockQuantity');
    const restockCurrentStockDisplay = document.getElementById('restockCurrentStock');
    const restockPreviewLine = document.getElementById('restockPreviewLine');
    const historyMovementCount = document.getElementById('historyMovementCount');
    const historyNetChange = document.getElementById('historyNetChange');
    const historyList = document.getElementById('historyList');
    let restockCurrentQuantity = 0;
    let restockCurrentUnit = 'pcs';

    function updateRestockPreview() {
        if (!restockCurrentStockDisplay || !restockPreviewLine || !restockQuantityInput) return;
        const added = Number(restockQuantityInput.value) || 0;
        const newQty = restockCurrentQuantity + added;
        restockCurrentStockDisplay.textContent = `${restockCurrentQuantity} ${restockCurrentUnit}`;
        restockPreviewLine.textContent = added > 0
            ? `${newQty} ${restockCurrentUnit}`
            : '—';
    }

    if (restockQuantityInput) {
        restockQuantityInput.addEventListener('input', updateRestockPreview);
    }

    function syncCategoryDisplay() {
        if (!categorySelect || !categoryDisplay) return;

        const selectedOption = categorySelect.options[categorySelect.selectedIndex];
        categoryDisplay.textContent = selectedOption ? selectedOption.text : 'Select category';

        categoryOptions.forEach(function(option) {
            option.classList.toggle('is-selected', option.dataset.categoryValue === categorySelect.value);
        });
    }

    function setCategoryOpenState(isOpen) {
        if (!categoryWrap || !categoryDisplay) return;

        categoryWrap.classList.toggle('is-open', isOpen);
        categoryDisplay.classList.toggle('is-open', isOpen);
        categoryDisplay.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

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
        if (medicineTypeMenu) {
            medicineTypeMenu.classList.toggle('is-open', isOpen);
        }

        if (isOpen && medicineTypeSearch) {
            if (medicineTypeMenu && medicineTypeMenu.parentElement !== document.body) {
                document.body.appendChild(medicineTypeMenu);
            }
            positionMedicineTypeMenu();
            medicineTypeSearch.value = '';
            filterMedicineTypeOptions('');
            setTimeout(function() {
                positionMedicineTypeMenu();
                medicineTypeSearch.focus();
            }, 0);
        } else if (medicineTypeMenu) {
            medicineTypeMenu.style.left = '';
            medicineTypeMenu.style.top = '';
            medicineTypeMenu.style.width = '';
            medicineTypeMenu.style.maxHeight = '';
            if (medicineTypeMenuHome && medicineTypeMenu.parentElement !== medicineTypeMenuHome) {
                medicineTypeMenuHome.appendChild(medicineTypeMenu);
            }
        }
    }

    function positionMedicineTypeMenu() {
        if (!medicineTypeDisplay || !medicineTypeMenu || !medicineTypeWrap.classList.contains('is-open')) return;

        const triggerRect = medicineTypeDisplay.getBoundingClientRect();
        const viewportPadding = 12;
        const menuGap = 6;
        const width = Math.min(triggerRect.width, window.innerWidth - (viewportPadding * 2));
        const left = Math.min(Math.max(triggerRect.left, viewportPadding), window.innerWidth - width - viewportPadding);
        const top = triggerRect.bottom + menuGap;
        const spaceBelow = window.innerHeight - top - viewportPadding;
        const maxHeight = Math.max(160, Math.min(320, spaceBelow));

        medicineTypeMenu.style.left = `${left}px`;
        medicineTypeMenu.style.width = `${width}px`;
        medicineTypeMenu.style.maxHeight = `${maxHeight}px`;
        medicineTypeMenu.style.top = `${top}px`;
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

        const category = categorySelect.value;
        medicineDispensingFields.style.display = category === 'Medicine' ? 'block' : 'none';
        if (itemsPerUnitField) {
            itemsPerUnitField.style.display = category === 'Medicine' ? 'flex' : 'none';
        }
    }

    function toggleMedicineFields() {
        const category = categorySelect.value;
        syncCategoryDisplay();

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
        categorySelect.value = 'Medicine';
        document.getElementById('iQty').value = '';
        document.getElementById('iMinimumStock').value = '10';
        document.getElementById('iUnit').value = 'pcs';
        document.getElementById('iBatchNumber').value = '';
        document.getElementById('iSupplierSource').value = '';
        if (dispensingUnitInput) {
            dispensingUnitInput.value = '';
        }
        if (unitsPerStockUnitInput) {
            unitsPerStockUnitInput.value = '';
        }
        document.getElementById('iDateAdded').value = new Date().toISOString().split('T')[0]; // Set today as default
        document.getElementById('iExpDate').value = '';
        medicineSelect.value = '';
        
        syncCategoryDisplay();
        toggleMedicineFields();
        syncMedicineTypeDisplay();
        syncMinStockUnitLabel();
    }

    function editItem(item) {
        closeInventoryActionMenus();
        if (!itemModal) return;
        itemModal.style.display = 'flex';
        document.getElementById('modalTitle').innerText = 'Edit Item';
        document.getElementById('itemForm').action = "/admin/inventory/" + item.id;
        
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('iName').value = item.name || '';
        categorySelect.value = item.category || 'Medicine';
        document.getElementById('iQty').value = item.quantity ?? '';
        document.getElementById('iMinimumStock').value = item.minimum_stock ?? '10';
        document.getElementById('iUnit').value = item.unit || 'pcs';
        document.getElementById('iBatchNumber').value = item.batch_number || '';
        document.getElementById('iSupplierSource').value = item.supplier_source || '';
        if (dispensingUnitInput) {
            dispensingUnitInput.value = item.dispensing_unit || '';
        }
        if (unitsPerStockUnitInput) {
            unitsPerStockUnitInput.value = item.units_per_stock_unit || '';
        }
        document.getElementById('iDateAdded').value = item.date_added || '';
        
        syncCategoryDisplay();
        toggleMedicineFields();
        if((item.category || '') === 'Medicine') {
            document.getElementById('iMedicineType').value = item.medicine_type_id || '';
            document.getElementById('iExpDate').value = item.expiration_date || '';
        }
        syncMedicineTypeDisplay();
        toggleDispensingFields();
        syncMinStockUnitLabel();
    }

    function closeModal() {
        if (!itemModal) return;
        itemModal.style.display = 'none';
        setCategoryOpenState(false);
        setMedicineTypeOpenState(false);
    }

    function openRestockModal(item) {
        closeInventoryActionMenus();
        if (!restockModal || !restockForm) return;
        restockModal.style.display = 'flex';
        restockForm.action = `/admin/inventory/${item.id}/restock`;
        document.getElementById('restockItemName').textContent = `Add stock to ${item.name || 'this item'}. Current stock: ${item.quantity ?? 0} ${item.unit || 'pcs'}.`;
        restockCurrentQuantity = Number(item.quantity || 0);
        restockCurrentUnit = item.unit || 'pcs';
        document.getElementById('restockQuantity').value = '';
        document.getElementById('restockDate').value = new Date().toISOString().split('T')[0];
        document.getElementById('restockBatchNumber').value = item.batch_number || '';
        document.getElementById('restockSupplierSource').value = item.supplier_source || '';

        const unitLabel = document.getElementById('restockUnitLabel');
        if (unitLabel) unitLabel.textContent = restockCurrentUnit;

        updateRestockPreview();
        if (restockQuantityInput) restockQuantityInput.focus();
    }

    // Wire quick-add preset buttons
    document.querySelectorAll('.restock-quick-btn[data-preset]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (!restockQuantityInput) return;
            const preset = Number(btn.dataset.preset);
            restockQuantityInput.value = preset;
            restockQuantityInput.dispatchEvent(new Event('input'));
            restockQuantityInput.focus();
        });
    });

    function closeRestockModal() {
        if (!restockModal) return;
        restockModal.style.display = 'none';
    }

    const MOVEMENT_TYPE_META = {
        restock:    { badgeClass: 'badge-restock',    icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg>' },
        dispensed:  { badgeClass: 'badge-dispensed',  icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg>' },
        dispense:   { badgeClass: 'badge-dispense',   icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg>' },
        used:       { badgeClass: 'badge-used',       icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg>' },
        consumed:   { badgeClass: 'badge-consumed',   icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg>' },
        created:    { badgeClass: 'badge-created',    icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 19H19M5 5h14"/></svg>' },
        adjusted:   { badgeClass: 'badge-adjusted',   icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>' },
        adjustment: { badgeClass: 'badge-adjustment', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>' },
    };

    function openHistoryModal(item) {
        closeInventoryActionMenus();
        if (!historyModal || !historyList || !historyMovementCount || !historyNetChange) return;
        historyModal.style.display = 'flex';
        document.getElementById('historyItemName').textContent = item.name ? `Recent activity for ${item.name}.` : 'Recent inventory activity.';

        const movements = Array.isArray(item.movements) ? item.movements : [];
        const unit = item.unit || 'pcs';

        let totalIn = 0, totalOut = 0;
        movements.forEach(function(m) {
            const q = Number(m.quantity) || 0;
            if (q > 0) totalIn += q; else totalOut += q;
        });
        const net = totalIn + totalOut;

        historyMovementCount.textContent = movements.length;

        const totalInEl  = document.getElementById('historyTotalIn');
        const totalOutEl = document.getElementById('historyTotalOut');
        const netChip    = document.getElementById('historyNetChip');
        if (totalInEl)  totalInEl.textContent  = `+${totalIn} ${unit}`;
        if (totalOutEl) totalOutEl.textContent  = `${totalOut} ${unit}`;
        historyNetChange.textContent = `${net >= 0 ? '+' : ''}${net} ${unit}`;
        if (netChip) {
            netChip.className = 'history-stat-chip ' + (net >= 0 ? 'chip-net-pos' : 'chip-net-neg');
        }

        if (!movements.length) {
            historyList.innerHTML = '<div class="history-card" data-movement-type="default" style="border-left-color:#cbd5e1!important;"><div style="color:#64748b;font-weight:700;">No movement history yet. This item has not been restocked or used.</div></div>';
            return;
        }

        historyList.innerHTML = movements.map(function(movement) {
            const typeKey  = (movement.type || '').toLowerCase();
            const typeMeta = MOVEMENT_TYPE_META[typeKey] || { badgeClass: 'badge-default', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/></svg>' };
            const quantity = Number(movement.quantity || 0);
            const signedQuantity = quantity > 0 ? `+${quantity}` : `${quantity}`;
            const metaParts = [];
            if (movement.user_name)    metaParts.push(`By ${movement.user_name}`);
            if (movement.batch_number) metaParts.push(`Batch ${movement.batch_number}`);
            if (movement.supplier_source) metaParts.push(movement.supplier_source);
            const unitLabel = movement.unit || unit;

            return `
                <div class="history-card" data-movement-type="${typeKey}">
                    <div class="history-card-head">
                        <span class="history-card-type-badge ${typeMeta.badgeClass}">
                            ${typeMeta.icon}
                            ${movement.type || 'Movement'}
                        </span>
                        <span style="font-size:12px;font-weight:700;color:#64748b;">${movement.created_at || ''}</span>
                    </div>
                    <div class="history-card-body">
                        <div class="history-card-quantity">${signedQuantity} ${unitLabel}</div>
                        <div class="history-card-stock">${movement.stock_before ?? 0} ${unitLabel} &rarr; ${movement.stock_after ?? 0} ${unitLabel}</div>
                        <div class="history-card-note">${movement.notes || 'No notes.'}</div>
                        ${metaParts.length ? `<div class="history-card-meta">${metaParts.join(' &middot; ')}</div>` : ''}
                    </div>
                </div>
            `;
        }).join('');
    }

    function closeHistoryModal() {
        if (!historyModal) return;
        historyModal.style.display = 'none';
    }

    function closeInventoryActionMenus() {
        document.querySelectorAll('.inventory-actions-dropdown.is-open').forEach(function(dropdown) {
            dropdown.classList.remove('is-open');
        });
    }

    function toggleInventoryActionMenu(event) {
        event.stopPropagation();
        const button = event.currentTarget;
        const dropdown = button.closest('.inventory-actions-dropdown');
        if (!dropdown) return;
        const isOpen = dropdown.classList.contains('is-open');
        closeInventoryActionMenus();
        dropdown.classList.toggle('is-open', !isOpen);
    }

    document.addEventListener('click', function(event) {
        if (!event.target.closest('.inventory-actions-dropdown')) {
            closeInventoryActionMenus();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeInventoryActionMenus();
        }
    });

    if (categoryDisplay && categorySelect) {
        categoryDisplay.addEventListener('click', function(event) {
            event.preventDefault();
            const shouldOpen = !categoryWrap.classList.contains('is-open');
            setMedicineTypeOpenState(false);
            setCategoryOpenState(shouldOpen);
        });

        categoryOptions.forEach(function(option) {
            option.addEventListener('click', function(event) {
                event.preventDefault();
                categorySelect.value = option.dataset.categoryValue || 'Medicine';
                categorySelect.dispatchEvent(new Event('change', { bubbles: true }));
                syncCategoryDisplay();
                setCategoryOpenState(false);
            });
        });

        categorySelect.addEventListener('change', syncCategoryDisplay);

        syncCategoryDisplay();
    }

    if (medicineTypeDisplay && medicineTypeMenu && medicineSelect) {
        medicineTypeDisplay.addEventListener('click', function(event) {
            event.preventDefault();
            const shouldOpen = !medicineTypeWrap.classList.contains('is-open');
            setCategoryOpenState(false);
            setMedicineTypeOpenState(shouldOpen);
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

        window.addEventListener('resize', positionMedicineTypeMenu);
        window.addEventListener('scroll', positionMedicineTypeMenu, true);

        document.addEventListener('click', function(event) {
            if (categoryWrap && !categoryWrap.contains(event.target)) {
                setCategoryOpenState(false);
            }

            if (
                medicineTypeWrap &&
                medicineTypeMenu &&
                !medicineTypeWrap.contains(event.target) &&
                !medicineTypeMenu.contains(event.target)
            ) {
                setMedicineTypeOpenState(false);
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                setCategoryOpenState(false);
                setMedicineTypeOpenState(false);
            }
        });

        syncMedicineTypeDisplay();
    }

    if (itemForm && medicineSelect && medicineTypeDisplay) {
        itemForm.addEventListener('submit', function(event) {
            const category = categorySelect.value;

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
        function applyInventoryFilters() {
            const searchTerm = inventorySearchInput.value.trim().toLowerCase();

            inventoryRows.forEach(function (row) {
                const rowText = row.innerText.toLowerCase();
                const category     = row.dataset.category || '';
                const stock        = Number(row.dataset.stock || 0);
                const effectiveQty = Number(row.dataset.effectiveQty ?? row.dataset.stock ?? 0);
                const minimumStock = Number(row.dataset.minimumStock || 10);
                const matchesSearch = rowText.includes(searchTerm);
                const matchesFilter = activeInventoryFilter === 'all'
                    || activeInventoryFilter === category
                    || (activeInventoryFilter === 'low' && stock > 0 && effectiveQty <= minimumStock)
                    || (activeInventoryFilter === 'out' && stock <= 0);

                row.style.display = matchesSearch && matchesFilter ? '' : 'none';
            });
        }

        inventorySearchInput.addEventListener('input', applyInventoryFilters);

        function setInventoryFilter(filter) {
            activeInventoryFilter = filter || 'all';
            inventoryFilterItems.forEach(function(item) {
                item.classList.toggle('is-active', item.dataset.inventoryFilter === activeInventoryFilter);
            });

            const allLabel = document.getElementById('inventoryFilterAllLabel');
            if (allLabel) {
                if (activeInventoryFilter === 'all') {
                    allLabel.textContent = 'All';
                } else {
                    const activeItem = inventoryFilterItems.find(function(item) {
                        return item.dataset.inventoryFilter === activeInventoryFilter;
                    });
                    allLabel.textContent = activeItem ? activeItem.textContent.trim() : 'All';
                }
            }

            applyInventoryFilters();
        }

        const inventoryFilterBar    = document.getElementById('inventoryFilterBar');
        const inventoryFilterAllBtn = document.getElementById('inventoryFilterAllBtn');

        function collapseFilterBar() {
            if (inventoryFilterBar) inventoryFilterBar.classList.remove('is-expanded');
        }

        // All pill — toggles the option pills open/closed
        if (inventoryFilterAllBtn) {
            inventoryFilterAllBtn.addEventListener('click', function() {
                if (!inventoryFilterBar) return;
                const expanding = !inventoryFilterBar.classList.contains('is-expanded');
                inventoryFilterBar.classList.toggle('is-expanded', expanding);
                setInventoryFilter('all');
            });
        }

        // Option pills — apply filter then collapse
        inventoryFilterItems.forEach(function(item) {
            if (item === inventoryFilterAllBtn) return;
            item.addEventListener('click', function() {
                setInventoryFilter(item.dataset.inventoryFilter || 'all');
                collapseFilterBar();
            });
        });

        // Click outside — collapse
        document.addEventListener('click', function(event) {
            if (inventoryFilterBar && !inventoryFilterBar.contains(event.target)) {
                collapseFilterBar();
            }
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
    const minStockUnitLabel = document.getElementById('iMinStockUnitLabel');

    function syncMinStockUnitLabel() {
        if (!minStockUnitLabel || !unitInput) return;
        const stockUnit      = unitInput.value.trim() || 'pcs';
        const dispensingInput = document.getElementById('iDispensingUnit');
        const unitsPerInput   = document.getElementById('iUnitsPerStockUnit');
        const dispensingUnit  = (dispensingInput ? dispensingInput.value.trim() : '');
        const unitsPerStock   = parseInt(unitsPerInput ? unitsPerInput.value : '0', 10) || 0;
        const hasConversion   = dispensingUnit !== '' && unitsPerStock > 1;

        // Show dispensing unit in the label when conversion is active
        minStockUnitLabel.textContent = hasConversion ? dispensingUnit : stockUnit;

        const note = document.getElementById('iMinStockNote');
        if (!note) return;
        note.textContent = hasConversion
            ? `Enter min in ${dispensingUnit}. 1 ${stockUnit} = ${unitsPerStock} ${dispensingUnit}.`
            : `Value is in the stock unit (${stockUnit}).`;
    }

    if (unitInput) {
        unitInput.addEventListener('input',  () => { toggleDispensingFields(); syncMinStockUnitLabel(); });
        unitInput.addEventListener('change', () => { toggleDispensingFields(); syncMinStockUnitLabel(); });
    }

    const dispensingUnitWatcher   = document.getElementById('iDispensingUnit');
    const unitsPerStockUnitWatcher = document.getElementById('iUnitsPerStockUnit');
    if (dispensingUnitWatcher)    dispensingUnitWatcher.addEventListener('input',  syncMinStockUnitLabel);
    if (unitsPerStockUnitWatcher) unitsPerStockUnitWatcher.addEventListener('input', syncMinStockUnitLabel);
</script>
@endpush
