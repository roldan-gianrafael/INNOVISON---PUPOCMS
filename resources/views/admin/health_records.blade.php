@extends('layouts.admin')

@section('title', 'Health Records')

@push('styles')
<style>
    /* Table & Card Styling */
    .card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
        height: 100%; /* Para pantay ang taas nila */
    }
    .awaiting-links-btn {
        background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%) !important;
        border: 1px solid #e0f2fe !important;
        border-left: 5px solid #0369a1 !important;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
        height: 100%;
        width: 100%;
    }
    .awaiting-links-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }
    .awaiting-links-btn:hover::before {
        left: 100%;
    }
    .awaiting-links-btn:hover {
        box-shadow: 0 8px 20px rgba(3, 105, 161, 0.15);
        transform: translateY(-2px);
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%) !important;
        border-color: #0369a1 !important;
    }
    .awaiting-links-btn:active {
        transform: translateY(0);
        box-shadow: 0 4px 12px rgba(3, 105, 161, 0.1);
    }

    /* Awaiting Links Modal Styling */
    .awaiting-links-modal-shell {
        width: min(900px, 95%);
        max-height: 85vh;
        overflow: hidden;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 26px 60px rgba(15, 23, 42, 0.24);
        border: 1px solid rgba(255,255,255,0.5);
        display: flex;
        flex-direction: column;
    }
    .awaiting-links-modal-head {
        position: relative;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 24px;
        background: linear-gradient(135deg, #7f1d1d, #991b1b 55%, #b91c1c);
        color: #ffffff;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .awaiting-links-modal-head-main {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        min-width: 0;
    }
    .awaiting-links-modal-badge {
        width: 48px;
        height: 48px;
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        font-weight: 700;
        font-size: 16px;
        color: #ffffff;
        border: 1px solid rgba(255,255,255,0.3);
    }
    .awaiting-links-modal-copy h3 {
        margin: 0 0 4px 0;
        font-size: 18px;
        font-weight: 700;
        color: #ffffff !important;
    }
    .awaiting-links-modal-copy p {
        margin: 0;
        font-size: 13px;
        color: #ffffff !important;
    }
    .awaiting-links-modal-close {
        flex: 0 0 auto;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 50%;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .awaiting-links-modal-close::before {
        content: '';
        position: absolute;
        top: 50%;
        left: -100%;
        width: 100%;
        height: 100%;
        background: #facc15;
        transform: translateY(-50%);
        transition: left 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: -1;
    }
    .awaiting-links-modal-close:hover {
        color: #70131B;
    }
    .awaiting-links-modal-close:hover::before {
        left: 100%;
    }
    .awaiting-links-modal-close svg {
        width: 20px;
        height: 20px;
        stroke-width: 2;
        stroke: currentColor;
        fill: none;
        position: relative;
        z-index: 1;
    }
    .awaiting-links-modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 24px;
    }
    .awaiting-links-modal-body table {
        width: 100%;
        border-collapse: collapse;
    }
    .awaiting-links-modal-body thead tr {
        border-bottom: 2px solid #e2e8f0;
    }
    .awaiting-links-modal-body th {
        text-align: left;
        padding: 14px 16px;
        font-size: 12px;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .awaiting-links-modal-body tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.2s ease;
    }
    .awaiting-links-modal-body tbody tr:hover {
        background-color: #f8fafc;
    }
    .awaiting-links-modal-body td {
        padding: 16px;
        color: #334155;
        font-size: 14px;
    }
    .health-summary-card {
        position: relative;
        overflow: hidden;
    }
    .health-summary-card::before {
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
    .card *:not(.status):not(.btn-action):not(.btn-sign) {
        color: #111827;
    }
    
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    
    th {
        text-align: left;
        font-size: 12px;
        font-weight: 800;
        color: #111827;
        text-transform: uppercase;
        padding: 12px 16px;
        border-bottom: 2px solid #f1f5f9;
    }
    
    td {
        padding: 16px;
        border-bottom: 1px solid #f8fafc;
        font-size: 14px;
        color: #111827;
        vertical-align: middle;
    }

    /* Status Badges */
    .status { padding: 5px 12px; border-radius: 99px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .status.pending { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
    .status.issued { background: #dcfce7; color: #15803d; }
    .status.review { background: #fee2e2; color: #b91c1c; }
    .status.submitted { background: #e0f2fe; color: #0369a1; }

    html[data-theme="dark"] .status.pending {
        background: #fff7ed;
        color: #c2410c !important;
        border-color: #fed7aa;
    }

    html[data-theme="dark"] .status.issued {
        background: #dcfce7;
        color: #15803d !important;
        border-color: #86efac;
    }

    html[data-theme="dark"] .status.review {
        background: #fee2e2;
        color: #b91c1c !important;
        border-color: #fecaca;
    }

    html[data-theme="dark"] .status.submitted {
        background: #e0f2fe;
        color: #0369a1 !important;
        border-color: #bae6fd;
    }

    /* Buttons */
    .btn-action {
        min-width: 92px;
        min-height: 38px;
        padding: 8px 16px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.01em;
        cursor: pointer;
        border: 1px solid transparent;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease, background 0.18s ease, color 0.18s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
    }

    .btn-action svg {
        width: 14px;
        height: 14px;
        margin-right: 6px;
        flex: 0 0 auto;
        stroke-width: 2;
    }
    .btn-action:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .btn-view {
        background: linear-gradient(135deg, #ffffff, #fff3f5);
        color: #70131B;
        border-color: #f0d7dc;
    }

    .btn-view:hover {
        background: linear-gradient(135deg, #fff7f8, #ffe7ed);
        border-color: #d9a9b4;
        box-shadow: 0 14px 24px rgba(112, 19, 27, 0.12);
    }

    .btn-sign {
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #ffffff;
        border-color: #8f2230;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.10),
            0 12px 24px rgba(112, 19, 27, 0.18);
    }

    .btn-sign:hover {
        color: #ffffff;
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.16),
            0 14px 26px rgba(112, 19, 27, 0.20);
    }

    .btn-signed,
    .btn-readonly {
        background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
        color: #475569;
        border-color: #cbd5e1;
        cursor: not-allowed;
        box-shadow: none;
    }

    .btn-signed::before {
        content: "✓";
        margin-right: 6px;
        font-weight: 900;
    }

    .health-issued-badge {
        min-width: 118px;
        min-height: 38px;
        padding: 8px 14px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #166534;
        border: 1px solid #86efac;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.01em;
        box-shadow:
            0 0 0 3px rgba(34, 197, 94, 0.10),
            0 10px 20px rgba(22, 101, 52, 0.10);
    }

    .health-issued-badge svg {
        width: 16px;
        height: 16px;
        flex: 0 0 auto;
        stroke-width: 2;
        margin-right: 0;
    }

    /* Custom Flex Grid para sa Summary Cards */
    .summary-container {
        display: flex;
        gap: 20px;
        width: 100%;
        margin-bottom: 25px;
        align-items: stretch;
    }
    .summary-item {
        flex: 1; /* Hahatiin ang space sa dalawa (50/50) */
    }
    .health-records-title {
        margin: 0;
        color: #111827;
        display: inline-flex;
        align-items: center;
        padding: 10px 18px;
        border-radius: 0 0 14px 14px;
        border: 0;
        border-bottom: 2px solid rgba(234, 215, 160, 0.9);
        background: transparent;
        box-shadow: none;
    }

    .health-records-title svg {
        width: 18px;
        height: 18px;
        margin-right: 10px;
        flex: 0 0 auto;
    }

    .health-records-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        width: 100%;
        margin-bottom: 20px;
        padding: 16px 18px;
        border-radius: 0 0 20px 20px;
        border: 0;
        border-bottom: 2px solid rgba(112, 19, 27, 0.72);
        background: linear-gradient(135deg, rgba(255, 253, 246, 0.76) 0%, rgba(255, 249, 231, 0.58) 42%, rgba(255, 255, 255, 0.82) 100%);
        box-shadow: 0 14px 26px rgba(112, 19, 27, 0.05);
    }

    .health-records-toolbar-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        flex-wrap: wrap;
        margin-left: auto;
    }

    .health-medical-launch-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        min-height: 50px;
        padding: 0 18px;
        border-radius: 999px;
        border: 1px solid #8f2230;
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #ffffff;
        font-size: 13px;
        font-weight: 900;
        letter-spacing: 0.01em;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20);
        transition: color .08s linear, transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        white-space: nowrap;
    }

    .health-medical-launch-btn::after {
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
        pointer-events: none;
    }

    .health-medical-launch-btn:hover,
    .health-medical-launch-btn:focus {
        transform: translateY(-1px);
        background: #facc15;
        color: #111827;
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16);
        outline: none;
    }

    .health-medical-launch-btn:hover::after,
    .health-medical-launch-btn:focus::after {
        transform: translateX(135%);
    }

    .health-medical-launch-icon {
        width: 28px;
        height: 28px;
        border-radius: 999px;
        background: rgba(255,255,255,0.16);
        border: 1px solid rgba(255,255,255,0.20);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 28px;
        position: relative;
        z-index: 1;
    }

    .health-medical-launch-btn svg {
        width: 16px;
        height: 16px;
        stroke-width: 2;
        flex: 0 0 auto;
        position: relative;
        z-index: 1;
    }

    .health-medical-launch-text {
        position: relative;
        z-index: 1;
    }
    .health-records-toolbar-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        justify-content: flex-end;
    }

    .health-records-search-shell {
        display: inline-flex;
        align-items: center;
        gap: 0;
        justify-content: flex-end;
    }
    .health-records-search-wrap {
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
    .health-records-search-shell.is-open .health-records-search-wrap {
        width: 320px;
        flex: 0 0 320px;
        opacity: 1;
        pointer-events: auto;
        transform: translateX(0) scaleX(1);
    }

    .health-filter-shell {
        display: flex;
        align-items: center;
    }

    .health-filter-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 50px;
        min-width: 140px !important;
        padding: 0 16px !important;
        gap: 8px !important;
        width: auto !important;
        border-radius: 14px !important;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #70131B;
        cursor: pointer;
        font-weight: 700;
        font-size: 13px;
        transition: all .18s ease;
        white-space: nowrap;
    }

    .health-filter-toggle svg {
        width: 18px !important;
        height: 18px !important;
        flex: 0 0 auto;
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
    }

    .health-filter-toggle:hover,
    .health-filter-toggle.is-open {
        background: #fef3c7;
        border-color: #facc15;
        color: #111827 !important;
        box-shadow: 0 0 0 3px rgba(250, 204, 21, 0.18);
    }

    .health-records-search-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 50px;
        min-width: 50px;
        padding: 0 14px;
        border-radius: 14px;
        border: none;
        background: linear-gradient(135deg, #0369a1 0%, #0284c7 100%);
        color: #ffffff;
        cursor: pointer;
        transition: all .18s ease;
        z-index: 1;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(3, 105, 161, 0.2);
    }

    .health-records-search-toggle:hover,
    .health-records-search-toggle:focus {
        background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%);
        color: #ffffff;
        outline: none;
        box-shadow: 0 8px 20px rgba(3, 105, 161, 0.3);
        transform: translateY(-2px);
    }

    .health-records-search-toggle:active {
        transform: translateY(0);
        box-shadow: 0 4px 12px rgba(3, 105, 161, 0.2);
    }

    .health-records-search-toggle svg {
        width: 20px;
        height: 20px;
        stroke-width: 2.5;
        stroke: currentColor;
        fill: none;
    }

    .health-filter-form {
        display: flex;
        align-items: flex-end;
        justify-content: flex-start;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 16px;
    }

    .health-filter-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .health-filter-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-left: auto;
        flex-wrap: nowrap;
    }

    .health-filter-field label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
    }

    .health-records-search,
    .health-filter-select {
        min-height: 48px;
        height: 48px;
        padding: 12px 18px;
        border-radius: 0 0 14px 14px;
        border: 0 !important;
        border-bottom: 3px solid #8f2230 !important;
        min-width: 180px;
        color: #111827;
        background: transparent !important;
        box-shadow: none !important;
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        appearance: none;
        -webkit-appearance: none;
    }

    .health-records-search {
        width: 280px;
    }
    .health-records-search::placeholder {
        color: #7f1d2d;
        font-weight: 700;
    }

    .health-records-search:focus,
    .health-filter-select:focus {
        outline: none;
        border-bottom-color: #70131B;
        box-shadow: none !important;
        transform: translateY(-1px);
    }
    .health-records-search-toggle {
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
    .health-records-search-toggle svg {
        width: 28px !important;
        height: 28px !important;
        stroke-width: 2 !important;
        display: block;
    }
    .health-records-search-toggle:hover,
    .health-records-search-toggle:focus {
        background: #facc15 !important;
        color: #111827 !important;
        border-color: #facc15 !important;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16) !important;
        outline: none !important;
    }

    .health-records-search-toggle:hover svg,
    .health-records-search-toggle:focus svg {
        color: #111827 !important;
        stroke: currentColor !important;
    }

    .health-filter-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        min-height: 44px;
        padding: 0 18px;
        border-radius: 999px;
        border: 1px solid #8f2230;
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #ffffff;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20);
        transition: color .08s linear, transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        z-index: 0;
    }

    .health-filter-btn::after {
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

    .health-filter-btn:hover {
        transform: translateY(-1px);
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16);
    }

    .health-filter-btn:hover::after {
        transform: translateX(135%);
    }

    .health-filter-btn-reset {
        background: linear-gradient(135deg, #64748b, #475569);
        border-color: #475569;
        box-shadow:
            0 0 0 3px rgba(100, 116, 139, 0.12),
            0 10px 22px rgba(71, 85, 105, 0.20);
    }

    .health-filter-modal {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(15, 23, 42, 0.42);
        backdrop-filter: blur(6px);
        z-index: 1200;
    }

    .health-filter-modal.is-open {
        display: flex;
    }

    .health-filter-modal-card {
        width: min(760px, 100%);
        border-radius: 22px;
        background: #ffffff;
        border: 1px solid rgba(127, 29, 45, 0.12);
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
        padding: 22px;
    }

    .health-filter-modal-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 12px;
    }

    .health-filter-modal-title {
        margin: 0;
        font-size: 20px;
        font-weight: 900;
        color: #70131B;
    }

    .health-filter-modal-copy {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }

    .health-filter-modal-close {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border: 1px solid rgba(127, 29, 45, 0.12);
        border-radius: 999px;
        background: #ffffff;
        color: #111827;
        cursor: pointer;
    }

    .health-filter-modal-close svg {
        width: 18px;
        height: 18px;
    }

    .health-summary-label {
        font-size: 17px;
        letter-spacing: 0.5px;
        display: inline-flex;
        flex-direction: column;
        line-height: 1.15;
    }

    .health-summary-value {
        color: #70131B;
        font-size: 17px;
    }

    .health-summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .health-table-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 12px;
    }

    .health-table-title {
        margin: 0;
        font-size: 18px;
        font-weight: 900;
        color: #70131B;
        letter-spacing: 0.01em;
    }

    .health-highlight-row {
        position: relative;
        background: linear-gradient(180deg, rgba(243, 232, 255, 0.98), rgba(237, 233, 254, 0.98));
        box-shadow: inset 4px 0 0 #7c3aed;
        transition: background 0.3s ease, box-shadow 0.3s ease;
    }

    .health-row-clickable {
        cursor: pointer;
    }

    .health-row-clickable:hover td {
        background: rgba(220, 252, 231, 0.58);
    }

    .health-row-clickable td {
        transition: background 0.16s ease;
    }

    .health-highlight-row td {
        background: transparent;
    }

    @keyframes healthHighlightPulse {
        0%, 100% {
            box-shadow: inset 4px 0 0 #7c3aed, 0 0 0 rgba(124, 58, 237, 0);
        }
        50% {
            box-shadow: inset 4px 0 0 #7c3aed, 0 0 0 6px rgba(124, 58, 237, 0.12);
        }
    }

    html[data-theme="dark"] .health-records-title,
    html[data-theme="dark"] .health-table-title,
    html[data-theme="dark"] .text-muted.health-summary-label,
    html[data-theme="dark"] .summary-item .health-summary-label span,
    html[data-theme="dark"] .health-filter-field label,
    html[data-theme="dark"] .health-records-search,
    html[data-theme="dark"] .health-records-search::placeholder,
    html[data-theme="dark"] .health-filter-select,
    html[data-theme="dark"] .health-summary-label,
    html[data-theme="dark"] .health-summary-value,
    html[data-theme="dark"] .summary-item h3,
    html[data-theme="dark"] .summary-item .text-danger {
        color: #ffffff !important;
    }

    html[data-theme="dark"] .health-records-search-toggle {
        background: rgba(17, 24, 39, 0.96);
        border-color: rgba(250, 204, 21, 0.16);
        color: #facc15;
    }

    html[data-theme="dark"] .health-records-search-toggle:hover,
    html[data-theme="dark"] .health-records-search-toggle:focus {
        background: rgba(250, 204, 21, 0.18);
        border-color: #facc15;
        color: #111827;
    }

    html[data-theme="dark"] .health-filter-toggle {
        background: rgba(17, 24, 39, 0.96);
        border-color: rgba(250, 204, 21, 0.16);
        color: #facc15;
    }

    html[data-theme="dark"] .health-filter-toggle:hover,
    html[data-theme="dark"] .health-filter-toggle.is-open {
        background: rgba(250, 204, 21, 0.18);
        border-color: #facc15;
        color: #111827 !important;
    }
    html[data-theme="dark"] .card .text-muted,
    html[data-theme="dark"] .card th,
    html[data-theme="dark"] .card td,
    html[data-theme="dark"] .card td *,
    html[data-theme="dark"] .student-name {
        color: #ffffff !important;
    }

    html[data-theme="dark"] .health-records-search::placeholder {
        color: #fecdd3 !important;
    }

    html[data-theme="dark"] .health-records-title {
        border-color: rgba(250, 204, 21, 0.30);
        background: transparent;
        box-shadow: none;
    }

    html[data-theme="dark"] .health-records-search,
    html[data-theme="dark"] .health-filter-select {
        background: rgba(18, 8, 12, 0.86);
        border-color: rgba(250, 204, 21, 0.28);
        box-shadow:
            0 0 0 2px rgba(250, 204, 21, 0.06),
            0 10px 20px rgba(0, 0, 0, 0.20);
    }

    html[data-theme="dark"] .health-records-search-toggle {
        background: linear-gradient(135deg, #70131B, #8f2230) !important;
        border-color: rgba(250, 204, 21, 0.28) !important;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.16),
            0 12px 22px rgba(0, 0, 0, 0.24) !important;
    }

    html[data-theme="dark"] .health-records-toolbar {
        border-color: rgba(250, 204, 21, 0.24);
        background: linear-gradient(135deg, rgba(112, 19, 27, 0.68) 0%, rgba(86, 16, 26, 0.64) 48%, rgba(44, 14, 18, 0.72) 100%);
        box-shadow:
            0 0 0 2px rgba(250, 204, 21, 0.07),
            0 16px 28px rgba(0, 0, 0, 0.22);
    }

    html[data-theme="dark"] .health-medical-launch-btn {
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.16),
            0 12px 22px rgba(0, 0, 0, 0.24);
    }

    html[data-theme="dark"] .health-medical-launch-btn:hover,
    html[data-theme="dark"] .health-medical-launch-btn:focus {
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(0, 0, 0, 0.22);
    }

    html[data-theme="dark"] .btn-view {
        background: linear-gradient(135deg, rgba(127, 29, 45, 0.22), rgba(148, 28, 57, 0.18));
        border-color: rgba(250, 204, 21, 0.18);
        color: #ffffff;
    }

    html[data-theme="dark"] .btn-view:hover {
        border-color: rgba(250, 204, 21, 0.4);
        box-shadow: 0 14px 24px rgba(0, 0, 0, 0.22);
    }

    html[data-theme="dark"] .btn-sign {
        color: #ffffff;
    }

    html[data-theme="dark"] .btn-signed,
    html[data-theme="dark"] .btn-readonly {
        background: linear-gradient(135deg, rgba(71, 85, 105, 0.78), rgba(51, 65, 85, 0.92));
        border-color: rgba(148, 163, 184, 0.28);
        color: #e2e8f0;
    }

    html[data-theme="dark"] .health-issued-badge {
        background: linear-gradient(135deg, rgba(20, 83, 45, 0.96), rgba(21, 128, 61, 0.84));
        border-color: rgba(74, 222, 128, 0.30);
        color: #ecfdf5;
        box-shadow:
            0 0 0 3px rgba(34, 197, 94, 0.10),
            0 12px 22px rgba(0, 0, 0, 0.24);
    }

    html[data-theme="dark"] .health-filter-modal-card {
        background: rgba(15, 23, 42, 0.98);
        border-color: rgba(148, 163, 184, 0.14);
        box-shadow: 0 18px 32px rgba(0, 0, 0, 0.24);
    }

    html[data-theme="dark"] .health-filter-modal-title,
    html[data-theme="dark"] .health-filter-modal-close {
        color: #ffffff;
    }

    html[data-theme="dark"] .health-filter-modal-copy {
        color: #cbd5e1;
    }

    html[data-theme="dark"] .health-filter-modal-close {
        background: rgba(15, 23, 42, 0.92);
        border-color: rgba(148, 163, 184, 0.24);
    }

    html[data-theme="dark"] .health-summary-card::before {
        background: #facc15;
    }

    html[data-theme="dark"] .summary-item .card {
        background: rgba(15, 23, 42, 0.96);
        border-color: rgba(148, 163, 184, 0.14);
    }

    html[data-theme="dark"] .health-highlight-row {
        background: linear-gradient(180deg, rgba(76, 29, 149, 0.34), rgba(91, 33, 182, 0.28));
        box-shadow: inset 4px 0 0 #a855f7;
    }

    html[data-theme="dark"] .health-row-clickable:hover td {
        background: rgba(20, 83, 45, 0.34);
    }
    .verify-approval-modal {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(6px);
        z-index: 1300;
    }

    .verify-approval-modal.is-open {
        display: flex;
    }

    .verify-approval-modal-card {
        width: min(760px, 100%);
        border-radius: 18px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22);
        overflow: hidden;
    }

    .verify-approval-modal-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 18px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .verify-approval-modal-title {
        margin: 0;
        font-size: 17px;
        font-weight: 900;
        color: #70131B;
    }

    .verify-approval-modal-copy {
        margin: 4px 0 0;
        font-size: 12px;
        color: #64748b;
    }

    .verify-approval-modal-close {
        width: 36px;
        height: 36px;
        border-radius: 999px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #111827;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .verify-approval-modal-close svg {
        width: 16px;
        height: 16px;
    }

    .verify-approval-body {
        padding: 16px 18px 18px;
        display: grid;
        gap: 14px;
    }

    .verify-approval-student {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .verify-approval-meta {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 10px;
        padding: 10px 12px;
    }

    .verify-approval-meta-k {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .verify-approval-meta-v {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        word-break: break-word;
    }

    .verify-approval-doc-wrap {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        background: #ffffff;
    }

    .verify-approval-doc-title {
        margin: 0 0 8px;
        font-size: 13px;
        font-weight: 800;
        color: #1e293b;
    }

    .verify-approval-doc-frame {
        height: 340px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        background: #f8fafc;
    }

    .verify-approval-doc-frame iframe {
        width: 100%;
        height: 100%;
        border: 0;
        background: #ffffff;
    }

    .verify-approval-doc-missing {
        border: 1px dashed #cbd5e1;
        background: #f8fafc;
        color: #64748b;
        border-radius: 10px;
        padding: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .verify-approval-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .verify-approval-btn {
        border-radius: 999px;
        padding: 9px 16px;
        font-size: 12px;
        font-weight: 800;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .verify-approval-btn-cancel {
        background: #e2e8f0;
        color: #334155;
        border-color: #cbd5e1;
    }

    .verify-approval-btn-approve {
        background: #70131B;
        color: #ffffff;
        border-color: #8f2230;
    }

    .verify-approval-btn-approve:disabled {
        background: #cbd5e1;
        color: #64748b;
        border-color: #cbd5e1;
        cursor: not-allowed;
    }

    html[data-theme="dark"] .verify-approval-modal-card {
        background: #0f172a;
        border-color: #334155;
    }

    html[data-theme="dark"] .verify-approval-modal-head {
        background: #111827;
        border-color: #334155;
    }

    html[data-theme="dark"] .verify-approval-body,
    html[data-theme="dark"] .verify-approval-body * {
        color: #e5e7eb !important;
    }

    html[data-theme="dark"] .verify-approval-modal-title,
    html[data-theme="dark"] .verify-approval-meta-v,
    html[data-theme="dark"] .verify-approval-doc-title {
        color: #f8fafc !important;
    }

    html[data-theme="dark"] .verify-approval-modal-copy,
    html[data-theme="dark"] .verify-approval-meta-k,
    html[data-theme="dark"] .verify-approval-doc-missing {
        color: #cbd5e1 !important;
    }

    html[data-theme="dark"] .verify-approval-modal-close {
        background: #1e293b;
        border-color: #475569;
        color: #f8fafc;
    }

    html[data-theme="dark"] .verify-approval-meta {
        background: #111827;
        border-color: #334155;
    }

    html[data-theme="dark"] .verify-approval-doc-wrap {
        background: #0f172a;
        border-color: #334155;
    }

    html[data-theme="dark"] .verify-approval-doc-frame {
        border-color: #334155;
        background: #111827;
    }

    html[data-theme="dark"] .verify-approval-doc-missing {
        border-color: #475569;
        background: #111827;
    }

    html[data-theme="dark"] .verify-approval-btn-cancel {
        background: #1e293b;
        border-color: #475569;
        color: #f8fafc !important;
    }

    html[data-theme="dark"] .verify-approval-btn-approve {
        background: #70131B;
        border-color: #8f2230;
        color: #ffffff !important;
    }

    html[data-theme="dark"] .verify-approval-btn-approve:disabled {
        background: #334155;
        border-color: #475569;
        color: #cbd5e1 !important;
    }

    @media (max-width: 980px) {
        .health-records-toolbar {
            flex-direction: column;
            align-items: stretch;
            border-radius: 24px;
        }

        .health-records-toolbar-actions,
        .health-filter-shell {
            justify-content: flex-start;
            margin-left: 0;
            align-items: stretch;
        }

        .health-records-toolbar-actions {
            width: 100%;
        }

        .health-medical-launch-btn {
            width: 100%;
            justify-content: center;
        }

        .health-records-search-shell {
            width: 100%;
        }

        .health-records-search-wrap,
        .health-records-search-shell.is-open .health-records-search-wrap {
            width: 100%;
            flex: 1 1 100%;
        }

        .health-records-search-shell:not(.is-open) .health-records-search-wrap {
            width: 0;
            flex-basis: 0;
        }

        .health-records-search {
            width: 100%;
        }

        .verify-approval-student {
            grid-template-columns: 1fr;
        }

        .summary-container {
            flex-direction: column;
        }

    }

    .hr-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 1300;
        align-items: center;
        justify-content: center;
        padding: clamp(12px, 2vw, 28px);
        background: rgba(15, 23, 42, 0.52);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    .hr-modal-backdrop.show { display: flex; }
    .hr-modal-shell {
        width: min(520px, 100%);
        max-height: calc(100dvh - clamp(24px, 4vw, 56px));
        border-radius: 24px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background: rgba(255,255,255,0.96);
        border-left: 1px solid rgba(112,19,27,0.12);
        border-right: 1px solid rgba(112,19,27,0.12);
        border-top: 4px solid #facc15;
        border-bottom: 4px solid #70131B;
        box-shadow: 0 26px 60px rgba(15,23,42,0.22);
    }
    .hr-modal-shell.hr-ma-shell { width: min(880px, 100%); }
    .hr-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: clamp(12px,1.4vw,18px) clamp(14px,1.6vw,22px);
        background: linear-gradient(135deg, #70131B, #8f2230);
        border-bottom: 1px solid rgba(255,255,255,0.12);
        flex: 0 0 auto;
    }
    .hr-modal-head-main { display:flex; align-items:center; gap:14px; min-width:0; flex:1 1 auto; }
    .hr-modal-head-badge {
        width:44px; height:44px; flex:0 0 44px; border-radius:14px;
        display:inline-flex; align-items:center; justify-content:center;
        background:rgba(255,255,255,0.16); border:1px solid rgba(255,255,255,0.24);
        color:#ffffff; font-size:12px; font-weight:900; letter-spacing:.06em;
    }
    .hr-modal-head h3 { margin:0; color:#ffffff !important; font-size:clamp(15px,1.4vw,18px); font-weight:900; }
    .hr-modal-head p  { margin:3px 0 0; color:rgba(255,255,255,0.82) !important; font-size:12px; line-height:1.5; }
    .hr-modal-close {
        width:38px; height:38px; flex:0 0 38px; border-radius:999px;
        border:1px solid rgba(255,255,255,0.22); background:rgba(255,255,255,0.12);
        color:#ffffff; display:inline-flex; align-items:center; justify-content:center;
        cursor:pointer; transition:background .18s ease, transform .18s ease;
    }
    .hr-modal-close:hover { background:rgba(255,255,255,0.26); transform:translateY(-1px); }
    .hr-modal-close svg { width:16px; height:16px; stroke-width:2.2; }
    .hr-modal-body {
        flex:1 1 auto; overflow-y:auto; padding:24px;
        min-height:0; scrollbar-width:none; -ms-overflow-style:none;
    }
    .hr-modal-body::-webkit-scrollbar { display:none; }
    /* Default pane */
    .hr-ref-default {
        display:flex; flex-direction:column;
        align-items:center; text-align:center; gap:16px; padding:8px 0;
    }
    .hr-ref-default h4 { margin:0; font-size:20px; font-weight:900; color:#111827; }
    .hr-ref-default p  { margin:0; font-size:13px; color:#64748b; line-height:1.55; max-width:360px; }
    /* Entry pane */
    .hr-ref-entry {
        display: none;
        position: relative;
        gap: 12px;
    }
    .hr-ref-entry.is-visible { display: grid; }
    .hr-ref-tip {
        position: relative;
        max-width: 100%;
        padding:10px 12px;
        border-radius:14px; background:#fff7ed;
        border:1px solid #fed7aa; color:#9a3412;
        font-size:12px; line-height:1.5;
        box-shadow:0 8px 18px rgba(180,83,9,0.10);
    }
    .hr-ref-tip strong { display:block; margin-bottom:3px; font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.06em; }
    .hr-ref-tip::before,
    .hr-ref-tip::after {
        display: none;
    }
    .hr-ref-label { font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:.06em; color:#475569; margin-bottom:6px; display:block; }
    .hr-ref-input {
        width:100%; min-height:52px; padding:14px 16px;
        border:1px solid rgba(112,19,27,0.18); border-radius:14px;
        background:linear-gradient(180deg,#ffffff,#fff8f6); color:#111827;
        font-size:14px; font-weight:700; outline:none;
        box-shadow:0 8px 18px rgba(15,23,42,0.06), inset 0 1px 0 rgba(255,255,255,0.9);
        transition:border-color .18s ease, box-shadow .18s ease;
        margin-bottom:10px;
    }
    .hr-ref-input:focus { border-color:#70131B; box-shadow:0 0 0 3px rgba(112,19,27,0.08), 0 8px 18px rgba(15,23,42,0.08); }
    .hr-ref-status { margin:8px 0; padding:10px 12px; border-radius:10px; font-size:12px; font-weight:700; display:none; }
    .hr-ref-status.info    { display:block; background:#eff6ff; border:1px solid #bfdbfe; color:#1d4ed8; }
    .hr-ref-status.success { display:block; background:#ecfdf5; border:1px solid #a7f3d0; color:#047857; }
    .hr-ref-status.error   { display:block; background:#fff1f2; border:1px solid #fecdd3; color:#be123c; }
    .hr-ref-actions { display:flex; gap:10px; margin-top:12px; }
    .hr-btn {
        flex:1; min-height:46px; border-radius:999px; padding:0 18px;
        font-size:13px; font-weight:900; cursor:pointer;
        border:1px solid transparent; display:inline-flex;
        align-items:center; justify-content:center; gap:8px;
        transition:transform .18s ease, background .18s ease, color .18s ease, box-shadow .18s ease;
    }
    .hr-btn:hover { transform:translateY(-1px); }
    .hr-btn-cancel { background:#f1f5f9; color:#334155; border-color:#cbd5e1; }
    .hr-btn-cancel:hover { background:#e2e8f0; }
    .hr-btn-primary {
        background:linear-gradient(135deg,#70131B,#8f2230);
        color:#ffffff; border-color:#8f2230;
        box-shadow:0 10px 22px rgba(112,19,27,0.22);
    }
    .hr-btn-primary:hover { background:#facc15; color:#111827; border-color:#facc15; }
    .hr-btn-toggle {
        background:#ffffff; color:#70131B; border-color:rgba(112,19,27,0.18);
        box-shadow:0 6px 14px rgba(15,23,42,0.06);
    }
    .hr-btn-toggle:hover { border-color:rgba(112,19,27,0.32); }
    /* MA form inside modal */
    .hr-ma-section {
        margin-bottom:14px; padding:16px 18px; border-radius:16px;
        border:1px solid rgba(112,19,27,0.12);
        background:linear-gradient(180deg,#ffffff,#f8fafc);
        box-shadow:inset 0 1px 0 rgba(255,255,255,0.9), 0 8px 20px rgba(15,23,42,0.05);
    }
    .hr-ma-section-title {
        margin:0 0 14px; font-size:12px; font-weight:900;
        text-transform:uppercase; letter-spacing:.08em; color:#70131B;
        display:flex; align-items:center; gap:8px;
    }
    .hr-ma-section-num {
        width:26px; height:26px; border-radius:999px;
        background:#fff1f2; border:1px solid #fecdd3;
        display:inline-flex; align-items:center; justify-content:center;
        font-size:11px; font-weight:900; color:#70131B; flex:0 0 auto;
    }
    .hr-ma-grid   { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; }
    .hr-ma-grid-3 { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; }
    .hr-ma-grid-4 { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; }
    .hr-ma-grid-1 { display:grid; grid-template-columns:1fr; gap:12px; }
    .hr-ma-field  { display:flex; flex-direction:column; gap:5px; }
    .hr-ma-label  { font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.06em; color:#64748b; }
    .hr-ma-control {
        width:100%; min-height:44px; padding:10px 14px;
        border:1px solid rgba(112,19,27,0.15); border-radius:12px;
        background:linear-gradient(180deg,#ffffff,#fff8f6); color:#111827;
        font-size:13px; font-weight:700; outline:none;
        box-shadow:inset 0 1px 0 rgba(255,255,255,0.92), 0 2px 4px rgba(112,19,27,0.04), 0 8px 16px rgba(112,19,27,0.07);
        transition:border-color .18s ease, box-shadow .2s ease, transform .18s ease;
    }
    .hr-ma-control:focus { border-color:#70131B; transform:translateY(-1px); box-shadow:inset 0 1px 0 rgba(255,255,255,0.92), 0 0 0 3px rgba(112,19,27,0.08), 0 10px 24px rgba(112,19,27,0.12); }
    .hr-ma-control[readonly] { background:#f8fafc; color:#64748b; border-color:#e2e8f0; }
    textarea.hr-ma-control { min-height:96px; resize:vertical; line-height:1.55; }
    .hr-ma-radio-group { display:flex; gap:8px; flex-wrap:wrap; }
    .hr-ma-radio {
        display:inline-flex; align-items:center; gap:6px; min-height:40px;
        padding:0 14px; border-radius:999px; background:#f8fafc;
        border:1px solid #e2e8f0; color:#334155; font-size:12px; font-weight:900; cursor:pointer;
    }
    .hr-ma-radio input { accent-color:#70131B; }
    .hr-ma-required { display:inline-flex; align-items:center; padding:2px 7px; border-radius:999px; background:#fff1f2; border:1px solid #fecdd3; color:#be123c; font-size:9px; font-weight:900; text-transform:uppercase; letter-spacing:.06em; margin-left:5px; }
    .hr-ma-actions { display:flex; justify-content:flex-end; gap:10px; padding:14px 0 4px; border-top:1px solid rgba(112,19,27,0.10); margin-top:6px; }
    @media (max-width:640px) {
        .hr-ma-grid, .hr-ma-grid-3, .hr-ma-grid-4 { grid-template-columns:1fr; }
    }
    html[data-theme="dark"] .hr-modal-shell { background:rgba(15,23,42,0.98); border-top-color:#facc15; border-bottom-color:#facc15; }
    html[data-theme="dark"] .hr-modal-head { background:#4d0d17; }
    html[data-theme="dark"] .hr-ma-section { background:linear-gradient(180deg,rgba(17,24,39,0.96),rgba(15,23,42,0.94)); border-color:rgba(250,204,21,0.14); }
    html[data-theme="dark"] .hr-ma-control { background:rgba(17,24,39,0.88); color:#f8fafc; border-color:rgba(148,163,184,0.22); }
    html[data-theme="dark"] .hr-ma-label { color:#94a3b8; }
    html[data-theme="dark"] .hr-ref-input { background:rgba(30,41,59,0.9); color:#f1f5f9; border-color:rgba(148,163,184,0.24); }
    html[data-theme="dark"] .hr-ref-default h4, html[data-theme="dark"] .hr-ref-default p { color:#f1f5f9; }
    html[data-theme="dark"] .hr-ma-radio { background:rgba(17,24,39,0.86); color:#f8fafc; border-color:rgba(148,163,184,0.18); }

</style>
@endpush

@section('content')
    @php
        $role = \App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '');
        $basePrefix = $role === \App\Models\User::ROLE_ADMIN ? '/assistant' : '/admin';
        $canSignHealth = $role === \App\Models\User::ROLE_SUPERADMIN;
        $highlightHealthId = trim((string) request()->query('highlight_health', ''));
    @endphp

    {{-- Header with Search / Filters --}}
    <div class="health-records-toolbar">
        <h2 class="health-records-title"><x-outline-icon name="document-text" />Health Records</h2>
        <div class="health-records-toolbar-actions">
            <div class="health-records-search-shell" id="healthRecordsSearchShell">
                <div class="health-records-search-wrap">
                    <input
                        type="text"
                        id="recordSearch"
                        name="q"
                        value="{{ $search ?? '' }}"
                        class="health-records-search"
                        placeholder="Search by student name or ID..."
                    >
                </div>
                <button type="button" class="health-records-search-toggle" id="healthRecordsSearchToggle" aria-label="Open search" aria-expanded="false" aria-controls="recordSearch" onclick="document.getElementById('healthRecordsSearchShell').classList.toggle('is-open'); document.getElementById('recordSearch').focus();">
                    <x-outline-icon name="magnifying-glass" />
                </button>
            </div>
        </div>
    </div>

    {{-- Get approved applicants data --}}
    @php
        $approvedApplicants = [];
        try {
            $approvalLogs = \App\Models\ActivityLog::where('event_type', 'applicant_approval')->get();
            $referenceNumbers = $approvalLogs->pluck('subject_id')->unique();

            foreach($referenceNumbers as $ref) {
                $approvalLog = $approvalLogs->where('subject_id', $ref)->last();
                $pendingAssessment = \App\Models\PendingMedicalAssessment::where('reference_number', $ref)->first();

                $approvedApplicants[] = [
                    'reference_number' => $ref,
                    'name' => $approvalLog->description ?? 'Unknown',
                    'approve_date' => $approvalLog->created_at,
                    'has_uploaded' => $pendingAssessment !== null,
                ];
            }
        } catch (\Exception $e) {
            // Tables may not exist
        }
    @endphp

    {{-- Summary Cards - Hardcoded Side by Side --}}
    <div class="summary-container">
        <div class="summary-item">
            <div class="card p-3" style="padding: 15px 24px !important; border-left: 5px solid #70131B;">
                <div class="health-summary-row">
                    <small class="text-muted fw-bold text-uppercase health-summary-label"><span>Total</span><span>Submissions</span></small>
                    <h3 class="fw-bold mb-0 health-summary-value">{{ $records->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="summary-item">
            <div class="card p-3" style="padding: 15px 24px !important; border-left: 5px solid #dc3545;">
                <div class="health-summary-row">
                    <small class="text-muted fw-bold text-uppercase health-summary-label"><span>With Medical</span><span>Conditions</span></small>
                    <h3 class="fw-bold mb-0 text-danger">{{ $records->where('has_disability', 'Yes')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="summary-item">
            <button type="button" class="card p-3 awaiting-links-btn" id="awaitingLinksBtn" style="padding: 15px 24px !important; border-left: 5px solid #0369a1;" onclick="document.getElementById('awaitingLinksModal').style.display='flex';">
                <div class="health-summary-row">
                    <small class="text-muted fw-bold text-uppercase health-summary-label"><span>Awaiting</span><span>Links</span></small>
                    <h3 class="fw-bold mb-0" style="color: #0369a1;">{{ count($approvedApplicants) }}</h3>
                </div>
            </button>
        </div>
    </div>

    {{-- Main Table Card --}}
<div class="card health-summary-card">
    <div class="health-table-head">
        <div class="health-table-title">Health Profile Summary</div>
    </div>
    <table id="healthTable">
        <thead>
            <tr>
                <th>ID Number</th>
                <th>Full Name</th>
                <th>Course / Yr / Sec</th>
                <th>Medical Condition</th>
                <th>Clearance Status</th>
                <th>Submitted At</th>
                <th style="text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
                <tr
                    data-health-row
                    data-health-id="{{ $record->id }}"
                    data-view-url="{{ $record->clearance_status == 'Issued' ? route('admin.show_health', $record->id) : '' }}"
                    title="{{ $record->clearance_status == 'Issued' ? 'Click to view' : '' }}"
                    class="{{ implode(' ', array_filter([
                        $highlightHealthId !== '' && $highlightHealthId === (string) $record->id ? 'health-highlight-row' : '',
                        $record->clearance_status == 'Issued' ? 'health-row-clickable' : '',
                    ])) }}"
                >
                    <td class="fw-bold">{{ $record->user->student_number ?: $record->user->student_id }}</td>
                    <td>
                        <div class="student-name" style="font-weight: 700;">{{ $record->user->name }}</div>
                    </td>
                    <td>{{ $record->course_college ?: $record->user->course }} {{ $record->user->year }}-{{ $record->user->section }}</td>
                    
                    {{-- Column 1: Medical Condition Status --}}
                    <td>
                        @if($record->has_disability == 'Yes')
                            <span class="status review">With Condition</span>
                        @else
                            <span class="status submitted">No Condition</span>
                        @endif
                    </td>

                    {{-- Column 2: Clearance Issuance Status --}}
                    <td>
                        @if($record->clearance_status == 'Issued')
                            <span class="status issued"><i class="fas fa-check-circle me-1"></i> Issued</span>
                        @elseif($record->clearance_status == 'Rejected')
                            <span class="status review">Rejected</span>
                        @elseif(in_array($record->clearance_status, ['Pending', 'For Verification'], true))
                            <span class="status pending">For Verification</span>
                        @else
                            <span class="status submitted">Not Processed</span>
                        @endif
                    </td>

                    <td style="color: #94a3b8; font-size: 12px;">
                        {{ $record->created_at->format('M d, Y') }}
                    </td>

                    <td style="text-align: center;">
                        @if($record->clearance_status == 'Issued')
                            <div class="d-flex justify-content-center">
                                <span class="health-issued-badge" aria-hidden="true">
                                    <x-outline-icon name="check" />
                                    Issued
                                </span>
                            </div>
                        @else
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.show_health', $record->id) }}" class="btn-action btn-view">
                                    <x-outline-icon name="document-text" />
                                    View
                                </a>
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">No health records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Awaiting Links Modal --}}
<div id="awaitingLinksModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;" onclick="if(event.target.id==='awaitingLinksModal') document.getElementById('awaitingLinksModal').style.display='none';">
    <div class="awaiting-links-modal-shell">
        <div class="awaiting-links-modal-head">
            <div class="awaiting-links-modal-head-main">
                <div class="awaiting-links-modal-badge">AL</div>
                <div class="awaiting-links-modal-copy">
                    <h3>Awaiting Links</h3>
                    <p>Medical assessments uploaded via reference lookup, waiting to be linked to student accounts.</p>
                </div>
            </div>
            <button type="button" class="awaiting-links-modal-close" id="closeAwaitingLinksModal" aria-label="Close modal" onclick="document.getElementById('awaitingLinksModal').style.display='none';">
                <x-outline-icon name="x-mark" />
            </button>
        </div>
        <div class="awaiting-links-modal-body">
            @if(count($approvedApplicants) > 0)
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #f1f5f9;">
                            <th style="text-align: left; padding: 12px 16px; font-size: 12px; font-weight: 800; color: #111827; text-transform: uppercase;">Reference Number</th>
                            <th style="text-align: left; padding: 12px 16px; font-size: 12px; font-weight: 800; color: #111827; text-transform: uppercase;">Name</th>
                            <th style="text-align: left; padding: 12px 16px; font-size: 12px; font-weight: 800; color: #111827; text-transform: uppercase;">Email</th>
                            <th style="text-align: left; padding: 12px 16px; font-size: 12px; font-weight: 800; color: #111827; text-transform: uppercase;">Approve Date</th>
                            <th style="text-align: center; padding: 12px 16px; font-size: 12px; font-weight: 800; color: #111827; text-transform: uppercase;">Uploaded Requirements</th>
                            <th style="text-align: center; padding: 12px 16px; font-size: 12px; font-weight: 800; color: #111827; text-transform: uppercase;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($approvedApplicants as $applicant)
                            <tr style="border-bottom: 1px solid #f8fafc;">
                                <td style="padding: 16px; color: #111827; font-weight: 700;">{{ $applicant['reference_number'] }}</td>
                                <td style="padding: 16px; color: #111827;">{{ $applicant['name'] }}</td>
                                <td style="padding: 16px; color: #111827;">-</td>
                                <td style="padding: 16px; color: #111827;">{{ $applicant['approve_date']->format('M d, Y h:i A') }}</td>
                                <td style="text-align: center; padding: 16px;">
                                    @if($applicant['has_uploaded'])
                                        <span style="background: #dcfce7; color: #15803d; padding: 5px 12px; border-radius: 99px; font-size: 11px; font-weight: 700; text-transform: uppercase;">✓ Uploaded</span>
                                    @else
                                        <span style="background: #fee2e2; color: #b91c1c; padding: 5px 12px; border-radius: 99px; font-size: 11px; font-weight: 700; text-transform: uppercase;">✗ Not Uploaded</span>
                                    @endif
                                </td>
                                <td style="text-align: center; padding: 16px;">
                                    <a href="#" class="link-button" style="color: #0369a1; text-decoration: none; font-weight: 600; font-size: 13px;">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="text-align: center; padding: 40px; color: #94a3b8;">
                    <p>No approved applicants yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Pending Medical Assessments Section --}}
@php
    $pendingAssessments = [];
    try {
        $pendingAssessments = \App\Models\PendingMedicalAssessment::all();
    } catch (\Exception $e) {
        // Table may not exist
    }
@endphp

@if(count($pendingAssessments) > 0)
<div class="card health-summary-card" style="margin-top: 30px;">
    <div class="health-table-head">
        <div class="health-table-title">Pending Medical Assessments</div>
        <p style="margin: 0; font-size: 13px; color: #64748b; margin-top: 4px;">Medical assessments uploaded via reference lookup, waiting to be linked to student accounts.</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Reference Number</th>
                <th>Email</th>
                <th>File Name</th>
                <th>Uploaded Date</th>
                <th>Status</th>
                <th>Student Name</th>
                <th style="text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendingAssessments as $assessment)
                <tr>
                    <td class="fw-bold">{{ $assessment->reference_number }}</td>
                    <td>{{ $assessment->email }}</td>
                    <td style="word-break: break-word; max-width: 250px;">{{ $assessment->file_name }}</td>
                    <td>{{ $assessment->created_at->format('M d, Y h:i A') }}</td>
                    <td>
                        @if($assessment->user_id)
                            <span class="status issued">Linked</span>
                        @else
                            <span class="status pending">Pending</span>
                        @endif
                    </td>
                    <td>{{ $assessment->user?->name ?? 'Not yet linked' }}</td>
                    <td style="text-align: center;">
                        <a href="{{ asset('storage/' . $assessment->file_path) }}" target="_blank" class="btn-action btn-view" style="display: inline-block;">
                            <x-outline-icon name="document-text" />
                            View
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="verify-approval-modal" id="verifyApprovalModal" aria-hidden="true">
    <div class="verify-approval-modal-card">
        <div class="verify-approval-modal-head">
            <div>
                <h3 class="verify-approval-modal-title">Verify Health Form Upload</h3>
                <p class="verify-approval-modal-copy">Review the uploaded Health Form PDF, then approve to sync to student side and Admission webhook.</p>
            </div>
            <button type="button" class="verify-approval-modal-close" id="verifyApprovalCloseBtn" aria-label="Close verification popup">
                <x-outline-icon name="x-mark" />
            </button>
        </div>
        <div class="verify-approval-body">
            <div class="verify-approval-student">
                <div class="verify-approval-meta">
                    <div class="verify-approval-meta-k">Student Name</div>
                    <div class="verify-approval-meta-v" id="verifyApprovalStudentName">-</div>
                </div>
                <div class="verify-approval-meta">
                    <div class="verify-approval-meta-k">Student Number</div>
                    <div class="verify-approval-meta-v" id="verifyApprovalStudentNumber">-</div>
                </div>
                <div class="verify-approval-meta">
                    <div class="verify-approval-meta-k">Course</div>
                    <div class="verify-approval-meta-v" id="verifyApprovalStudentCourse">-</div>
                </div>
            </div>

            <div class="verify-approval-doc-wrap">
                <p class="verify-approval-doc-title">Uploaded Health Form (PDF)</p>
                <div id="verifyApprovalDocMissing" class="verify-approval-doc-missing" style="display:none;">
                    No uploaded Health Form PDF found.
                </div>
                <div id="verifyApprovalDocFrame" class="verify-approval-doc-frame" style="display:none;">
                    <iframe id="verifyApprovalDocViewer" src=""></iframe>
                </div>
            </div>

            <form id="verifyApprovalForm" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" name="clearance_status" value="Issued">
                <input type="hidden" name="pending_reason" value="">
                <div class="verify-approval-actions">
                    <button type="button" class="verify-approval-btn verify-approval-btn-cancel" id="verifyApprovalCancelBtn">Cancel</button>
                    <button type="submit" class="verify-approval-btn verify-approval-btn-approve" id="verifyApprovalApproveBtn">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="health-filter-modal" id="healthFilterModal" aria-hidden="true">
    <div class="health-filter-modal-card">
        <div class="health-filter-modal-head">
            <div>
                <h3 class="health-filter-modal-title">Filter Health Forms</h3>
                <p class="health-filter-modal-copy">Narrow the student health form list by course, month, or year level.</p>
            </div>
            <button type="button" class="health-filter-modal-close" id="healthFilterCloseBtn" aria-label="Close filter popup">
                <x-outline-icon name="x-mark" />
            </button>
        </div>

        <form method="GET" class="health-filter-form" id="healthFilterForm">
            <div class="health-filter-field">
                <label for="courseFilter">Course</label>
                <select id="courseFilter" name="course" class="health-filter-select">
                    <option value="">All Courses</option>
                    @foreach(($courseOptions ?? collect()) as $courseOption)
                        <option value="{{ $courseOption }}" {{ ($courseFilter ?? '') === $courseOption ? 'selected' : '' }}>{{ $courseOption }}</option>
                    @endforeach
                </select>
            </div>
            <div class="health-filter-field">
                <label for="monthFilter">Time</label>
                <input
                    type="month"
                    id="monthFilter"
                    name="month"
                    value="{{ $monthFilter ?? '' }}"
                    class="health-filter-select"
                >
            </div>
            <div class="health-filter-field">
                <label for="yearFilter">Year Level</label>
                <select id="yearFilter" name="year" class="health-filter-select">
                    <option value="">All Year Levels</option>
                    @foreach(($yearOptions ?? collect()) as $yearOption)
                        <option value="{{ $yearOption }}" {{ (string) ($yearFilter ?? '') === (string) $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                    @endforeach
                </select>
            </div>
            <div class="health-filter-actions">
                <button type="submit" class="health-filter-btn">Apply</button>
                <a href="{{ route('admin.health_records') }}" class="health-filter-btn health-filter-btn-reset">Reset</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Simple search toggle function
    function toggleHealthSearch() {
        const shell = document.getElementById('healthRecordsSearchShell');
        const toggle = document.getElementById('healthRecordsSearchToggle');
        const input = document.getElementById('recordSearch');

        if (shell && toggle) {
            const isOpen = shell.classList.contains('is-open');
            shell.classList.toggle('is-open', !isOpen);
            toggle.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');

            if (!isOpen && input) {
                setTimeout(() => input.focus(), 100);
            }
        }
    }

    const healthFilterToggle = document.getElementById('healthFilterToggle');
    const healthFilterModal = document.getElementById('healthFilterModal');
    const healthFilterCloseBtn = document.getElementById('healthFilterCloseBtn');
    const healthFilterForm = document.getElementById('healthFilterForm');
    const highlightedHealthId = @json($highlightHealthId);
    const healthRecordsSearchInput = document.getElementById('recordSearch');
    const healthRecordsSearchShell = document.getElementById('healthRecordsSearchShell');
    const healthRecordsSearchToggle = document.getElementById('healthRecordsSearchToggle');
    const healthRows = Array.from(document.querySelectorAll('#healthTable tbody tr[data-health-row]'));
    const verifyApprovalModal = document.getElementById('verifyApprovalModal');
    const verifyApprovalCloseBtn = document.getElementById('verifyApprovalCloseBtn');
    const verifyApprovalCancelBtn = document.getElementById('verifyApprovalCancelBtn');
    const verifyApprovalStudentName = document.getElementById('verifyApprovalStudentName');
    const verifyApprovalStudentNumber = document.getElementById('verifyApprovalStudentNumber');
    const verifyApprovalStudentCourse = document.getElementById('verifyApprovalStudentCourse');
    const verifyApprovalDocViewer = document.getElementById('verifyApprovalDocViewer');
    const verifyApprovalDocFrame = document.getElementById('verifyApprovalDocFrame');
    const verifyApprovalDocMissing = document.getElementById('verifyApprovalDocMissing');
    const verifyApprovalForm = document.getElementById('verifyApprovalForm');
    const verifyApprovalApproveBtn = document.getElementById('verifyApprovalApproveBtn');

    function setHealthFilterOpenState(isOpen) {
        if (!healthFilterToggle || !healthFilterModal) {
            return;
        }

        healthFilterToggle.classList.toggle('is-open', isOpen);
        healthFilterToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        healthFilterModal.classList.toggle('is-open', isOpen);
        healthFilterModal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
    }

    if (healthFilterToggle && healthFilterModal) {
        healthFilterToggle.addEventListener('click', function () {
            setHealthFilterOpenState(true);
        });
    }

    if (healthFilterCloseBtn) {
        healthFilterCloseBtn.addEventListener('click', function () {
            setHealthFilterOpenState(false);
        });
    }

    if (healthFilterModal) {
        healthFilterModal.addEventListener('click', function (event) {
            if (event.target === healthFilterModal) {
                setHealthFilterOpenState(false);
            }
        });
    }

    if (healthFilterForm) {
        healthFilterForm.addEventListener('submit', function () {
            setHealthFilterOpenState(false);
        });
    }

    if (highlightedHealthId) {
        window.addEventListener('DOMContentLoaded', function () {
            const highlightedRow = document.querySelector('[data-health-row][data-health-id="' + highlightedHealthId + '"]');
            if (highlightedRow) {
                highlightedRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                window.setTimeout(function () {
                    highlightedRow.classList.remove('health-highlight-row');

                    const url = new URL(window.location.href);
                    if (url.searchParams.has('highlight_health')) {
                        url.searchParams.delete('highlight_health');
                        window.history.replaceState({}, document.title, url.toString());
                    }
                }, 5000);
            }
        });
    }

    if (healthRecordsSearchInput) {
        healthRecordsSearchInput.addEventListener('input', function () {
            const searchTerm = this.value.trim().toLowerCase();

            healthRows.forEach(function (row) {
                const rowText = row.innerText.toLowerCase();
                row.style.display = rowText.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const healthRecordsSearchShell = document.getElementById('healthRecordsSearchShell');
        const healthRecordsSearchInput = document.getElementById('recordSearch');
        const healthRecordsSearchToggle = document.getElementById('healthRecordsSearchToggle');

        if (healthRecordsSearchShell && healthRecordsSearchInput && healthRecordsSearchToggle) {
            const setHealthRecordsSearchOpenState = function (isOpen) {
                healthRecordsSearchShell.classList.toggle('is-open', isOpen);
                healthRecordsSearchToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            };

            setHealthRecordsSearchOpenState(healthRecordsSearchInput.value.trim() !== '');

            healthRecordsSearchToggle.addEventListener('click', function () {
                const shouldOpen = !healthRecordsSearchShell.classList.contains('is-open');
                setHealthRecordsSearchOpenState(shouldOpen);

                if (shouldOpen) {
                    window.requestAnimationFrame(function () {
                        healthRecordsSearchInput.focus();
                    });
                }
            });
        }
    });

    window.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-health-row][data-view-url]').forEach(function (row) {
            const viewUrl = row.getAttribute('data-view-url') || '';
            if (viewUrl.trim() === '') {
                return;
            }

            row.addEventListener('click', function (event) {
                if (event.target.closest('a, button, input, select, textarea, label')) {
                    return;
                }

                window.location.href = viewUrl;
            });
        });
    });

    const setVerifyApprovalModalOpenState = function (isOpen) {
        if (!verifyApprovalModal) {
            return;
        }

        verifyApprovalModal.classList.toggle('is-open', isOpen);
        verifyApprovalModal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
    };

    if (verifyApprovalCloseBtn) {
        verifyApprovalCloseBtn.addEventListener('click', function () {
            setVerifyApprovalModalOpenState(false);
        });
    }

    if (verifyApprovalCancelBtn) {
        verifyApprovalCancelBtn.addEventListener('click', function () {
            setVerifyApprovalModalOpenState(false);
        });
    }

    if (verifyApprovalModal) {
        verifyApprovalModal.addEventListener('click', function (event) {
            if (event.target === verifyApprovalModal) {
                setVerifyApprovalModalOpenState(false);
            }
        });
    }

    // Awaiting Links Modal - wrapped in DOMContentLoaded to ensure elements exist
    document.addEventListener('DOMContentLoaded', function() {
        const awaitingLinksBtn = document.getElementById('awaitingLinksBtn');
        const awaitingLinksModal = document.getElementById('awaitingLinksModal');
        const closeAwaitingLinksModal = document.getElementById('closeAwaitingLinksModal');

        console.log('Awaiting Links Debug:', {
            btnFound: !!awaitingLinksBtn,
            modalFound: !!awaitingLinksModal,
            closeFound: !!closeAwaitingLinksModal
        });

        if (awaitingLinksBtn) {
            awaitingLinksBtn.addEventListener('click', function () {
                console.log('Awaiting Links button clicked');
                if (awaitingLinksModal) {
                    awaitingLinksModal.style.display = 'flex';
                    console.log('Modal opened');
                }
            });
        } else {
            console.warn('Awaiting Links button not found');
        }

        if (closeAwaitingLinksModal) {
            closeAwaitingLinksModal.addEventListener('click', function () {
                if (awaitingLinksModal) {
                    awaitingLinksModal.style.display = 'none';
                }
            });
        }

        if (awaitingLinksModal) {
            awaitingLinksModal.addEventListener('click', function (event) {
                if (event.target === awaitingLinksModal) {
                    awaitingLinksModal.style.display = 'none';
                }
            });
        }
    });

    document.querySelectorAll('.js-open-verify-modal').forEach(function (button) {
        button.addEventListener('click', function () {
            const studentName = button.getAttribute('data-student-name') || '-';
            const studentNumber = button.getAttribute('data-student-number') || '-';
            const studentCourse = button.getAttribute('data-student-course') || '-';
            const healthFormUrl = button.getAttribute('data-health-form-url') || '';
            const approveUrl = button.getAttribute('data-approve-url') || '';

            if (verifyApprovalStudentName) {
                verifyApprovalStudentName.textContent = studentName;
            }
            if (verifyApprovalStudentNumber) {
                verifyApprovalStudentNumber.textContent = studentNumber;
            }
            if (verifyApprovalStudentCourse) {
                verifyApprovalStudentCourse.textContent = studentCourse;
            }
            if (verifyApprovalForm) {
                verifyApprovalForm.setAttribute('action', approveUrl);
            }

            if (healthFormUrl.trim() !== '') {
                if (verifyApprovalDocViewer) {
                    verifyApprovalDocViewer.setAttribute('src', healthFormUrl);
                }
                if (verifyApprovalDocFrame) {
                    verifyApprovalDocFrame.style.display = '';
                }
                if (verifyApprovalDocMissing) {
                    verifyApprovalDocMissing.style.display = 'none';
                }
                if (verifyApprovalApproveBtn) {
                    verifyApprovalApproveBtn.disabled = false;
                }
            } else {
                if (verifyApprovalDocViewer) {
                    verifyApprovalDocViewer.setAttribute('src', '');
                }
                if (verifyApprovalDocFrame) {
                    verifyApprovalDocFrame.style.display = 'none';
                }
                if (verifyApprovalDocMissing) {
                    verifyApprovalDocMissing.style.display = '';
                }
                if (verifyApprovalApproveBtn) {
                    verifyApprovalApproveBtn.disabled = true;
                }
            }

            setVerifyApprovalModalOpenState(true);
        });
    });

    (function initHealthSummaryLiveSync() {
        const liveFeedNode = document.getElementById('adminLiveAlertFeedUrl');
        if (!liveFeedNode) {
            return;
        }

        let feedUrl = '';
        try {
            feedUrl = JSON.parse(liveFeedNode.textContent || '""') || '';
        } catch (error) {
            feedUrl = '';
        }

        if (!feedUrl) {
            return;
        }

        let knownNotificationIds = new Set();
        let healthLivePollTimer = null;

        const isHealthNotification = function (notification) {
            const id = (notification && notification.id ? String(notification.id) : '').trim();
            return id.startsWith('health-form:');
        };

        const hydrateKnownIds = function (payload) {
            const notifications = Array.isArray(payload && payload.notifications) ? payload.notifications : [];
            knownNotificationIds = new Set(
                notifications
                    .filter(isHealthNotification)
                    .map(function (notification) {
                        return String(notification.id);
                    })
            );
        };

        const pullFeed = function () {
            if (document.hidden) {
                return;
            }

            fetch(feedUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Failed to fetch live health updates.');
                    }
                    return response.json();
                })
                .then(function (payload) {
                    const notifications = Array.isArray(payload && payload.notifications) ? payload.notifications : [];
                    const healthNotifications = notifications.filter(isHealthNotification);
                    const hasNewHealthSubmission = healthNotifications.some(function (notification) {
                        return !knownNotificationIds.has(String(notification.id));
                    });

                    if (hasNewHealthSubmission) {
                        window.location.reload();
                        return;
                    }

                    hydrateKnownIds(payload);
                })
                .catch(function () {

</script>
@endpush
