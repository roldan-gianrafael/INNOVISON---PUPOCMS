@extends('layouts.admin')

@section('title', 'Clinical Consultation')

@push('styles')
<style>
    .consultation-workspace {
        display: flex;
        flex-wrap: nowrap;
        gap: 20px;
        align-items: start;
        width: 100%;
        overflow-x: auto;
        padding-bottom: 8px;
    }
    .consultation-documents {
        display: none;
        flex: 0 0 28%;
        width: 28%;
        min-width: 260px;
        max-width: 360px;
        position: sticky;
        top: 20px;
        max-height: calc(100vh - 40px);
        overflow-y: auto;
        padding: 18px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .06);
        scrollbar-width: thin;
    }
    .consultation-workspace.documents-open .consultation-documents {
        display: block;
        animation: consultationDocumentsIn .24s ease;
    }
    @keyframes consultationDocumentsIn {
        from {
            opacity: 0;
            transform: translateX(-14px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    .documents-heading,
    .inventory-drawer-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
    }
    .documents-heading svg,
    .inventory-drawer-heading svg {
        width: 22px;
        height: 22px;
        color: #800000;
        flex: 0 0 auto;
    }
    .documents-heading h2,
    .inventory-drawer-heading h2 {
        margin: 0;
        color: #111827;
        font-size: 17px;
    }
    .documents-count {
        margin-left: auto;
        padding: 3px 8px;
        border-radius: 999px;
        background: #facc15;
        color: #111827;
        font-size: 11px;
        font-weight: 800;
    }
    .documents-panel-close {
        display: grid;
        place-items: center;
        width: 30px;
        height: 30px;
        padding: 0;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        background: #fff;
        color: #111827;
        font-size: 21px;
        line-height: 1;
        cursor: pointer;
    }
    .documents-panel-close:hover {
        border-color: #800000;
        background: #800000;
        color: #fff;
    }
    .document-list {
        display: grid;
        gap: 12px;
    }
    .document-card {
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
    }
    .document-preview {
        display: grid;
        place-items: center;
        width: 100%;
        height: 116px;
        background: #e5e7eb;
        color: #800000;
    }
    .document-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .document-preview svg {
        width: 38px;
        height: 38px;
    }
    .document-card-body {
        padding: 11px;
    }
    .document-card-title {
        display: block;
        margin-bottom: 9px;
        color: #111827;
        font-size: 12px;
        font-weight: 800;
    }
    .document-open {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #800000;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
    }
    .document-open:hover {
        color: #a16207;
    }
    .document-open svg {
        width: 15px;
        height: 15px;
    }
    .documents-empty {
        padding: 24px 12px;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
        text-align: center;
    }
    .consultation-main {
        flex: 1 1 auto;
        width: 72%;
        min-width: 640px;
    }
    .consult-card {
        margin-bottom: 20px;
        padding: 22px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .05);
    }
    .consult-card h3 {
        margin: 0 0 18px;
        color: #111827;
        font-size: 18px;
    }
    .patient-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        border-left: 4px solid #800000;
        background: #f8fafc;
    }
    .patient-header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .documents-panel-trigger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 38px;
        padding: 8px 12px;
        border: 1px solid #800000;
        border-radius: 7px;
        background: #fff;
        color: #800000;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        white-space: nowrap;
        transition: background-color .18s ease, color .18s ease;
    }
    .documents-panel-trigger:hover,
    .documents-panel-trigger[aria-expanded="true"] {
        background: #800000;
        color: #fff;
    }
    .documents-panel-trigger svg {
        width: 18px;
        height: 18px;
    }
    .patient-name {
        margin: 0 0 8px;
        color: #111827;
        font-size: 20px;
    }
    .patient-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
    }
    .patient-badge,
    .badge-source {
        display: inline-flex;
        align-items: center;
        padding: 4px 9px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }
    .patient-badge {
        background: #e2e8f0;
        color: #334155;
    }
    .source-online {
        border: 1px solid #bfdbfe;
        background: #dbeafe;
        color: #1e40af;
    }
    .source-walkin {
        border: 1px solid #fde68a;
        background: #fef3c7;
        color: #92400e;
    }
    .consultation-date {
        flex: 0 0 auto;
        color: #334155;
        font-size: 13px;
        font-weight: 700;
        text-align: right;
    }
    .consultation-date span {
        display: block;
        margin-bottom: 3px;
        color: #64748b;
        font-size: 11px;
        font-weight: 600;
    }
    .form-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-group:last-child {
        margin-bottom: 0;
    }
    .form-group label {
        display: block;
        margin-bottom: 6px;
        color: #111827;
        font-size: 13px;
        font-weight: 800;
    }
    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #94a3b8;
        border-radius: 6px;
        background: #fff;
        color: #111827;
        font-size: 14px;
    }
    .form-control:focus {
        border-color: #800000;
        outline: 3px solid rgba(128, 0, 0, .12);
    }
    .form-control::placeholder {
        color: #64748b;
    }
    .form-help {
        margin-top: 6px;
        color: #64748b;
        font-size: 11px;
    }
    .mar-required {
        border-color: #fca5a5;
        background: #fff7f7;
    }
    .choice-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }
    .choice-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .choice-card {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        border: 1px solid #cbd5e1;
        border-radius: 7px;
        background: #fff;
        color: #111827;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        transition: background-color .18s ease, border-color .18s ease, color .18s ease;
    }
    .choice-input:checked + .choice-card {
        border-color: #800000;
        background: #800000;
        color: #fff;
    }
    .medicine-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }
    .medicine-header h3 {
        margin: 0;
    }
    .medicine-selection-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 10px;
        align-items: center;
    }
    .inventory-tally-trigger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 38px;
        padding: 8px 12px;
        border: 1px solid #800000;
        border-radius: 7px;
        background: #fff;
        color: #800000;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        transition: background-color .18s ease, color .18s ease, transform .18s ease;
    }
    .inventory-tally-trigger:hover {
        background: #800000;
        color: #fff;
        transform: translateY(-1px);
    }
    .inventory-tally-trigger svg {
        width: 18px;
        height: 18px;
    }
    .selected-stock {
        display: none;
        align-items: center;
        gap: 7px;
        margin-top: 9px;
        padding: 8px 10px;
        border-radius: 6px;
        background: #ecfdf5;
        color: #166534;
        font-size: 12px;
        font-weight: 800;
    }
    .selected-stock.visible {
        display: flex;
    }
    .selected-stock.low {
        background: #fff7ed;
        color: #c2410c;
    }
    .form-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 12px;
    }
    .btn-save {
        flex: 1;
        min-height: 46px;
        padding: 12px 22px;
        border: 0;
        border-radius: 8px;
        background: #800000;
        color: #fff;
        font-weight: 800;
        cursor: pointer;
        transition: background-color .2s ease, color .2s ease;
    }
    .btn-save:hover {
        background: #facc15;
        color: #111827;
    }
    .btn-cancel {
        padding: 12px;
        color: #111827;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
    }
    .inventory-tally-list {
        display: grid;
        gap: 9px;
    }
    .inventory-tally-item {
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
        cursor: pointer;
        transition: border-color .18s ease, background-color .18s ease, box-shadow .18s ease;
    }
    .inventory-tally-item:hover,
    .inventory-tally-item.selected {
        border-color: #800000;
        background: #fff7ed;
        box-shadow: 0 5px 14px rgba(128, 0, 0, .08);
    }
    .inventory-tally-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
    }
    .inventory-tally-name {
        color: #111827;
        font-size: 13px;
        font-weight: 800;
    }
    .inventory-tally-meta {
        margin-top: 5px;
        color: #64748b;
        font-size: 11px;
    }
    .inventory-tally-actions {
        display: none;
        justify-content: flex-end;
        margin-top: 10px;
        padding-top: 9px;
        border-top: 1px solid #e2e8f0;
    }
    .inventory-tally-item.selected .inventory-tally-actions {
        display: flex;
    }
    .inventory-issue-button {
        min-height: 30px;
        padding: 6px 13px;
        border: 1px solid #800000;
        border-radius: 6px;
        background: #800000;
        color: #fff;
        font: inherit;
        font-size: 11px;
        font-weight: 900;
        cursor: pointer;
        transition: background-color .18s ease, border-color .18s ease, color .18s ease;
    }
    .inventory-issue-button:hover {
        border-color: #facc15;
        background: #facc15;
        color: #111827;
    }
    .stock-badge {
        flex: 0 0 auto;
        padding: 4px 7px;
        border-radius: 999px;
        background: #dcfce7;
        color: #166534;
        font-size: 10px;
        font-weight: 900;
    }
    .stock-badge.low {
        background: #ffedd5;
        color: #c2410c;
    }
    html[data-theme="dark"] .consultation-documents,
    html[data-theme="dark"] .consult-card {
        border-color: #374151;
        background: #111827;
    }
    html[data-theme="dark"] .patient-header,
    html[data-theme="dark"] .document-card,
    html[data-theme="dark"] .inventory-tally-item {
        border-color: #374151;
        background: #1f2937;
    }
    html[data-theme="dark"] .documents-heading h2,
    html[data-theme="dark"] .inventory-drawer-heading h2,
    html[data-theme="dark"] .consult-card h3,
    html[data-theme="dark"] .patient-name,
    html[data-theme="dark"] .form-group label,
    html[data-theme="dark"] .document-card-title,
    html[data-theme="dark"] .inventory-tally-name,
    html[data-theme="dark"] .btn-cancel {
        color: #f8fafc;
    }
    html[data-theme="dark"] .form-control,
    html[data-theme="dark"] .choice-card,
    html[data-theme="dark"] .inventory-tally-trigger,
    html[data-theme="dark"] .documents-panel-trigger,
    html[data-theme="dark"] .documents-panel-close {
        border-color: #4b5563;
        background: #0f172a;
        color: #f8fafc;
    }
    html[data-theme="dark"] .mar-required {
        background: #2b1720;
    }
    @media (max-width: 1180px) {
        .form-grid-2 {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 820px) {
        .consultation-documents {
            flex-basis: 260px;
            width: 260px;
        }
        .document-list {
            grid-template-columns: 1fr;
        }
        .patient-header,
        .patient-header-actions {
            align-items: flex-start;
            flex-direction: column;
        }
        .consultation-date {
            text-align: left;
        }
    }
    @media (max-width: 520px) {
        .form-grid-2 {
            grid-template-columns: 1fr;
        }
        .consult-card,
        .consultation-documents {
            padding: 16px;
        }
        .btn-save {
            width: 100%;
        }
        .medicine-selection-row {
            grid-template-columns: minmax(360px, 1fr) auto;
        }
        .form-actions {
            align-items: stretch;
            flex-direction: column;
        }
        .btn-cancel {
            text-align: center;
        }
    }

    /* Right-side consultation utility ecosystem */
    .consultation-workspace {
        display: block;
        overflow: visible;
        padding-right: 144px;
    }
    .consultation-main {
        width: 100%;
        min-width: 0;
    }
    .medicine-selection-row {
        grid-template-columns: minmax(0, 1fr);
    }
    .consultation-utility-rail {
        position: fixed;
        z-index: 1049;
        top: 50%;
        right: 18px;
        display: grid;
        gap: 9px;
        width: 126px;
        transform: translateY(-50%);
        transition: right .3s ease, opacity .18s ease, transform .18s ease, visibility .18s ease;
    }
    .consultation-utility-rail.panel-open {
        right: 338px;
    }
    .consultation-utility-rail.quick-actions-active {
        visibility: hidden;
        opacity: 0;
        pointer-events: none;
        transform: translateY(-50%) translateX(28px);
    }
    .utility-rail-button {
        display: flex;
        align-items: center;
        gap: 9px;
        width: 100%;
        min-height: 48px;
        padding: 8px 10px;
        border: 1px solid #cbd5e1;
        border-radius: 7px;
        background: #fff;
        color: #334155;
        box-shadow: 0 5px 16px rgba(15, 23, 42, .12);
        font-size: 11px;
        font-weight: 800;
        line-height: 1.2;
        text-align: left;
        cursor: pointer;
        transition: background-color .18s ease, border-color .18s ease, color .18s ease;
    }
    .utility-rail-button:hover,
    .utility-rail-button.active {
        border-color: #800000;
        background: #800000;
        color: #fff;
    }
    .utility-rail-button svg {
        width: 21px;
        height: 21px;
        flex: 0 0 auto;
    }
    .utility-rail-count {
        display: inline-grid;
        place-items: center;
        min-width: 19px;
        height: 19px;
        margin-left: auto;
        padding: 0 5px;
        border-radius: 999px;
        background: #facc15;
        color: #111827;
        font-size: 9px;
    }
    #right-utility-panel {
        position: fixed;
        z-index: 1050;
        top: 0;
        right: -350px;
        width: 320px;
        height: 100vh;
        padding: 20px;
        overflow-y: auto;
        border-left: 1px solid #e2e8f0;
        background: #fff;
        color: #111827;
        box-shadow: -2px 0 8px rgba(0, 0, 0, .18);
        transition: right .3s ease;
    }
    #right-utility-panel.open {
        right: 0;
    }
    .utility-panel-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: -20px -20px 18px;
        padding: 18px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, .16);
        background: linear-gradient(135deg, #70131b 0%, #8f2230 100%);
    }
    .utility-panel-header > svg {
        width: 23px;
        height: 23px;
        color: #ffffff;
    }
    .utility-panel-title {
        margin: 0;
        color: #ffffff !important;
        font-size: 18px;
        font-weight: 900;
    }
    .utility-panel-header,
    .utility-panel-header * {
        color: #ffffff;
    }
    .utility-panel-header > svg {
        color: #ffffff !important;
        stroke: currentColor;
    }
    #close-utility-panel {
        position: relative;
        overflow: hidden;
        display: grid;
        place-items: center;
        width: 32px;
        height: 32px;
        flex: 0 0 32px;
        margin-left: auto;
        padding: 0;
        border: 1px solid #facc15;
        border-radius: 999px;
        background: linear-gradient(90deg, #facc15 0 50%, rgba(255, 255, 255, .12) 50% 100%);
        background-size: 205% 100%;
        background-position: 100% 0;
        color: #ffffff;
        font-size: 20px;
        line-height: 1;
        cursor: pointer;
        transition: background-position .32s ease, color .18s ease, transform .18s ease, box-shadow .18s ease;
    }
    #close-utility-panel:hover,
    #close-utility-panel:focus {
        border-color: #facc15;
        background-position: 0 0;
        color: #70131b !important;
        transform: rotate(90deg);
        box-shadow: 0 8px 18px rgba(15, 23, 42, .2);
        outline: none;
    }
    .utility-panel-pane {
        display: none;
    }
    .utility-panel-pane.active {
        display: block;
    }
    .utility-pane-note {
        margin: -6px 0 15px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }
    .treatment-history-list {
        display: grid;
        gap: 11px;
    }
    .treatment-history-card {
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
    }
    .treatment-history-head {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 8px;
    }
    .treatment-history-date {
        color: #800000;
        font-size: 12px;
        font-weight: 900;
    }
    .treatment-history-service {
        color: #64748b;
        font-size: 10px;
        font-weight: 800;
        text-align: right;
    }
    .treatment-history-card strong {
        display: block;
        margin-bottom: 3px;
        color: #111827;
        font-size: 12px;
    }
    .treatment-history-card p {
        margin: 0 0 8px;
        color: #475569;
        font-size: 11px;
        line-height: 1.5;
    }
    .treatment-history-meta {
        display: grid;
        gap: 3px;
        padding-top: 8px;
        border-top: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 10px;
    }
    html[data-theme="dark"] #right-utility-panel,
    html[data-theme="dark"] .utility-rail-button,
    html[data-theme="dark"] #close-utility-panel {
        border-color: #374151;
        background: #111827;
        color: #f8fafc;
    }
    html[data-theme="dark"] .utility-panel-title,
    html[data-theme="dark"] .treatment-history-card strong {
        color: #f8fafc;
    }
    html[data-theme="dark"] .utility-panel-header {
        border-bottom-color: rgba(255, 255, 255, .16);
        background: linear-gradient(135deg, #70131b 0%, #8f2230 100%);
    }
    html[data-theme="dark"] .utility-panel-header > svg,
    html[data-theme="dark"] .utility-panel-title {
        color: #ffffff;
    }
    html[data-theme="dark"] #close-utility-panel {
        border-color: #facc15;
        background: linear-gradient(90deg, #facc15 0 50%, rgba(255, 255, 255, .12) 50% 100%);
        background-size: 205% 100%;
        background-position: 100% 0;
        color: #ffffff;
    }
    html[data-theme="dark"] #close-utility-panel:hover,
    html[data-theme="dark"] #close-utility-panel:focus {
        border-color: #facc15;
        background-position: 0 0;
        color: #70131b;
    }
    html[data-theme="dark"] .treatment-history-card {
        border-color: #374151;
        background: #1f2937;
    }
    @media (max-width: 760px) {
        .consultation-workspace {
            padding-right: 54px;
        }
        .consultation-utility-rail {
            right: 7px;
            width: 44px;
        }
        .consultation-utility-rail.panel-open {
            right: 327px;
        }
        .utility-rail-button {
            justify-content: center;
            min-height: 44px;
            padding: 8px;
        }
        .utility-rail-button span:not(.utility-rail-count) {
            display: none;
        }
        .utility-rail-count {
            position: absolute;
            margin: -27px 0 0 27px;
        }
    }

    /* Student Health Profile visual language for the consultation form */
    .consultation-main {
        --clinic-form-maroon: #7f1d2d;
        --clinic-form-maroon-dark: #5f0012;
        --clinic-form-yellow: #facc15;
        --clinic-form-field: #f8fafc;
        --clinic-form-border: #d1d5db;
        font-family: "Segoe UI", Arial, sans-serif;
        color: #111827;
    }
    .consultation-main .consult-card {
        padding: 22px 24px;
        border: 1px solid rgba(127, 29, 45, .16);
        border-radius: 16px;
        background: rgba(255, 255, 255, .98);
        box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
    }
    .consultation-main .patient-header {
        border-left: 0;
        border-top: 5px solid var(--clinic-form-maroon);
        background:
            radial-gradient(circle at top left, rgba(250, 204, 21, .16), transparent 34%),
            linear-gradient(180deg, #fff 0%, #fffaf2 100%);
        box-shadow: 0 14px 30px rgba(127, 29, 45, .08);
    }
    .consultation-main .patient-name {
        color: #70131b;
        font-size: 1.25rem;
        font-weight: 800;
    }
    .consultation-main .patient-badge {
        border: 1px solid rgba(127, 29, 45, .12);
        border-radius: 10px;
        background: #fff;
        color: #4b5563;
        font-size: .68rem;
        font-weight: 800;
    }
    .consultation-main .consultation-date {
        color: #111827;
        font-size: .9rem;
        font-weight: 800;
    }
    .consultation-main .consultation-date span {
        color: #6b7280;
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    .consultation-main .consult-card > h3,
    .consultation-main .medicine-header h3 {
        margin: 0 0 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid rgba(127, 29, 45, .12);
        color: var(--clinic-form-maroon);
        font-size: 1.05rem;
        font-weight: 800;
    }
    .consultation-main .medicine-header {
        display: block;
        margin-bottom: 16px;
    }
    .consultation-main .form-grid-2 {
        gap: 12px;
    }
    .consultation-main .form-group {
        position: relative;
        margin-bottom: 12px;
        padding: 10px 12px;
        border: 1px solid rgba(127, 29, 45, .12);
        border-radius: 12px;
        background: #fff;
    }
    .consultation-main .form-group label {
        display: block;
        margin: 0 0 5px;
        color: #6b7280;
        font-size: .74rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }
    .consultation-main .form-control {
        min-height: 46px;
        padding: 10px 12px;
        border: 1.5px solid rgba(127, 29, 45, .42);
        border-radius: 12px;
        background: linear-gradient(180deg, #fffafb 0%, #fff6f7 100%);
        box-shadow:
            0 8px 18px rgba(127, 29, 45, .06),
            inset 0 1px 0 rgba(255, 255, 255, .82);
        color: #111827;
        font-family: "Segoe UI", Arial, sans-serif;
        font-size: .9rem;
        font-weight: 700;
        transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
    }
    .consultation-main textarea.form-control {
        min-height: 118px;
        line-height: 1.55;
        resize: vertical;
    }
    .consultation-main .form-control:focus {
        border-color: var(--clinic-form-maroon);
        outline: none;
        background: linear-gradient(180deg, #fffafb 0%, #fff6f7 100%);
        box-shadow:
            0 0 0 .18rem rgba(127, 29, 45, .12),
            0 10px 22px rgba(127, 29, 45, .10);
    }
    .consultation-main .form-control[readonly] {
        border-color: rgba(209, 213, 219, .9);
        background: #f4f1f1;
        color: #4b5563;
        box-shadow: none;
    }
    .consultation-main .form-control::placeholder {
        color: #9ca3af;
        font-weight: 600;
    }
    .consultation-main select.form-control {
        cursor: pointer;
    }
    .clinic-select-shell {
        position: relative;
        width: 100%;
    }
    .clinic-select-native {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        opacity: 0;
        pointer-events: none;
    }
    .clinic-select-display {
        position: relative;
        width: 100%;
        min-height: 52px;
        padding: 13px 52px 13px 16px;
        border: 1px solid rgba(127, 29, 29, .28);
        border-radius: 16px;
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, .1), transparent 36%),
            linear-gradient(180deg, #fff 0%, #fff8f6 100%);
        box-shadow: 0 10px 20px rgba(15, 23, 42, .07), inset 0 1px 0 rgba(255, 255, 255, .86);
        color: #111827;
        font: inherit;
        font-size: .86rem;
        font-weight: 750;
        text-align: left;
        cursor: pointer;
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }
    .clinic-select-display::before {
        content: "";
        position: absolute;
        top: 50%;
        right: 42px;
        width: 1px;
        height: 24px;
        background: rgba(148, 163, 184, .28);
        transform: translateY(-50%);
    }
    .clinic-select-display::after {
        content: "";
        position: absolute;
        top: 50%;
        right: 19px;
        width: 9px;
        height: 9px;
        border-right: 2px solid #800000;
        border-bottom: 2px solid #800000;
        transform: translateY(-70%) rotate(45deg);
        transition: transform .18s ease;
    }
    .clinic-select-display:hover,
    .clinic-select-display.is-open {
        border-color: #800000;
        box-shadow: 0 0 0 4px rgba(128, 0, 0, .06), 0 13px 24px rgba(128, 0, 0, .1);
        transform: translateY(-1px);
    }
    .clinic-select-display.is-open::after {
        transform: translateY(-20%) rotate(225deg);
    }
    .clinic-select-display:disabled {
        cursor: not-allowed;
        opacity: .72;
        transform: none;
    }
    .clinic-select-menu {
        position: absolute;
        z-index: 90;
        top: calc(100% + 9px);
        left: 0;
        right: 0;
        display: none;
        gap: 8px;
        max-height: 270px;
        padding: 12px;
        overflow-y: auto;
        border: 1px solid rgba(128, 0, 0, .14);
        border-radius: 16px;
        background: rgba(255, 255, 255, .98);
        box-shadow: 0 18px 34px rgba(15, 23, 42, .15);
        backdrop-filter: blur(8px);
    }
    .clinic-select-shell.is-open .clinic-select-menu {
        display: grid;
    }
    .clinic-select-option {
        width: 100%;
        padding: 11px 13px;
        border: 1px solid rgba(148, 163, 184, .24);
        border-radius: 999px;
        background: linear-gradient(180deg, #fff 0%, #f8fafc 100%);
        color: #1e293b;
        font: inherit;
        font-size: .8rem;
        font-weight: 800;
        text-align: left;
        cursor: pointer;
        transition: background-color .18s ease, border-color .18s ease, color .18s ease, transform .18s ease;
    }
    .clinic-select-option:hover,
    .clinic-select-option.is-selected {
        border-color: #800000;
        background: linear-gradient(135deg, #800000, #70131b);
        color: #facc15;
        transform: translateY(-1px);
    }
    .clinic-select-option:disabled {
        display: none;
    }
    html[data-theme="dark"] .clinic-select-display,
    html[data-theme="dark"] .clinic-select-menu,
    html[data-theme="dark"] .clinic-select-option {
        border-color: #4b5563;
        background: #0f172a;
        color: #f8fafc;
    }
    html[data-theme="dark"] .clinic-select-option:hover,
    html[data-theme="dark"] .clinic-select-option.is-selected {
        border-color: #facc15;
        background: #800000;
        color: #facc15;
    }
    .consultation-main .mar-required {
        border-color: rgba(127, 29, 45, .52);
        background: linear-gradient(180deg, #fffafb 0%, #fff6f7 100%);
    }
    .consultation-main .form-help {
        margin-top: 7px;
        color: #7f1d2d;
        font-size: .75rem;
        font-weight: 650;
        line-height: 1.45;
    }
    .consultation-main .choice-grid {
        gap: 10px;
    }
    .consultation-main .choice-card {
        min-height: 44px;
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
        color: #334155;
        font-size: .84rem;
        font-weight: 700;
    }
    .consultation-main .choice-input:checked + .choice-card {
        border-color: transparent;
        background: linear-gradient(135deg, var(--clinic-form-maroon) 0%, var(--clinic-form-maroon-dark) 100%);
        color: #fff;
        box-shadow: 0 8px 16px rgba(127, 29, 45, .2);
    }
    .consultation-main .selected-stock {
        border: 1px solid rgba(22, 101, 52, .14);
        border-radius: 10px;
        font-size: .76rem;
    }
    .consultation-main .btn-save {
        position: relative;
        z-index: 0;
        overflow: hidden;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--clinic-form-maroon) 0%, var(--clinic-form-maroon-dark) 100%);
        color: #ffffff;
        box-shadow: 0 10px 22px rgba(127, 29, 45, .28);
        font-family: "Segoe UI", Arial, sans-serif;
        font-size: .9rem;
        font-weight: 700;
        isolation: isolate;
    }
    .consultation-main .btn-save::before {
        content: "";
        position: absolute;
        z-index: 0;
        inset: 0;
        background: #facc15;
        transform: translateX(-105%);
        transition: transform .34s ease;
    }
    .consultation-main .btn-save span {
        position: relative;
        z-index: 1;
    }
    .consultation-main .btn-save:hover,
    .consultation-main .btn-save:focus {
        background: var(--clinic-form-maroon);
        color: var(--clinic-form-maroon);
        box-shadow: 0 13px 26px rgba(127, 29, 45, .24);
        outline: none;
    }
    .consultation-main .btn-save:hover::before,
    .consultation-main .btn-save:focus::before {
        transform: translateX(0);
    }
    .consultation-main .btn-save.is-finalizing {
        color: var(--clinic-form-maroon);
        pointer-events: none;
    }
    .consultation-main .btn-save.is-finalizing::before {
        transform: translateX(0);
    }
    .consultation-success-overlay {
        position: fixed;
        z-index: 9999;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, .58);
        backdrop-filter: blur(5px);
    }
    .consultation-success-overlay.is-open {
        display: flex;
    }
    .consultation-success-card {
        width: min(360px, calc(100vw - 30px));
        padding: 28px 22px;
        border: 1px solid rgba(250, 204, 21, .48);
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 24px 54px rgba(15, 23, 42, .3);
        text-align: center;
        animation: consultationSuccessPop .38s cubic-bezier(.2, .9, .25, 1.2);
    }
    .consultation-success-check {
        width: 76px;
        height: 76px;
        display: grid;
        place-items: center;
        margin: 0 auto 14px;
        border-radius: 999px;
        background: #70131b;
        color: #facc15;
        box-shadow: 0 12px 26px rgba(112, 19, 27, .24);
    }
    .consultation-success-check svg {
        width: 39px;
        height: 39px;
        stroke-width: 2.8;
    }
    .consultation-success-card strong {
        display: block;
        color: #70131b;
        font-size: 20px;
        font-weight: 900;
    }
    @keyframes consultationSuccessPop {
        from { opacity: 0; transform: scale(.72) translateY(18px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .consultation-main .btn-cancel {
        color: #4b5563;
        font-family: "Segoe UI", Arial, sans-serif;
        font-size: .86rem;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
@php
    $role = \App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '');
    $walkinStoreRoute = $role === \App\Models\User::ROLE_ADMIN ? 'assistant.walkin.store' : 'walkin.store';
    $walkinIndexRoute = $role === \App\Models\User::ROLE_ADMIN ? 'assistant.walkin.index' : 'walkin.index';
    $studentDisplayRole = \App\Models\Appointment::normalizeUserType($student->user_role ?? $student->user_type ?? 'Student');
    $isAssistedIntake = ($user_source ?? '') === 'assisted';
    $studentDocuments = $studentDocuments ?? [];
@endphp

<div class="consultation-workspace">
    <div class="consultation-main">
        <header class="patient-header consult-card">
            <div>
                <h2 class="patient-name">{{ $student->first_name }} {{ $student->last_name }}</h2>
                <div class="patient-badges">
                    <span class="patient-badge">{{ $studentDisplayRole }}</span>
                    <span class="patient-badge">{{ $student->student_number ?: $student->student_id ?: 'N/A' }}</span>
                    @if(($user_source ?? '') === 'online' && $latestAppointment)
                        <span class="badge-source source-online">Online Appointment</span>
                    @elseif($isAssistedIntake)
                        <span class="badge-source source-walkin">Assisted Intake</span>
                    @else
                        <span class="badge-source source-walkin">Walk-in Patient</span>
                    @endif
                </div>
                @if(($user_source ?? '') === 'online' && $latestAppointment)
                    <div class="form-help">
                        Scheduled {{ \Carbon\Carbon::parse($latestAppointment->date)->format('M d, Y') }}
                        at {{ \Carbon\Carbon::parse($latestAppointment->time)->format('g:i A') }}
                    </div>
                @endif
            </div>
            <div class="patient-header-actions">
                <div class="consultation-date">
                    <span>Today's Consultation</span>
                    {{ now()->format('F d, Y') }}
                </div>
            </div>
        </header>

        <form action="{{ route($walkinStoreRoute) }}" method="POST" id="consultationForm">
            @csrf
            <input type="hidden" name="student_number" value="{{ $student->student_number ?: $student->student_id }}">
            <input type="hidden" name="user_role" value="{{ $studentDisplayRole }}">
            <input type="hidden" name="user_type" value="{{ $user_source ?? 'walkin' }}">
            <input type="hidden" name="consultation_started_at" value="{{ old('consultation_started_at', now()->format('H:i:s')) }}">

            <section class="consult-card">
                <h3>Physical Assessment</h3>
                <div class="form-group">
                    <label for="consultDob">Date of Birth</label>
                    <input type="date" id="consultDob" name="dob" class="form-control" value="{{ old('dob', $consultationDob ?? '') }}">
                    <div class="form-help">Prefilled from saved student information when available.</div>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="consultHeight">Height (cm)</label>
                        <input type="number" id="consultHeight" step="0.01" name="height" class="form-control" placeholder="165" value="{{ old('height', $consultationHeight ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label for="consultWeight">Weight (kg)</label>
                        <input type="number" id="consultWeight" step="0.01" name="weight" class="form-control" placeholder="60" value="{{ old('weight', $consultationWeight ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label for="consultTemp">Temperature (C)</label>
                        <input type="number" id="consultTemp" step="0.1" name="temp" class="form-control" placeholder="36.5" value="{{ old('temp') }}">
                    </div>
                    <div class="form-group">
                        <label for="consultBp">Blood Pressure</label>
                        <input type="text" id="consultBp" name="bp" class="form-control" placeholder="120/80" value="{{ old('bp') }}">
                    </div>
                    <div class="form-group">
                        <label for="consultPulse">Pulse Rate (bpm)</label>
                        <input type="number" id="consultPulse" name="pulse_rate" class="form-control" placeholder="72" value="{{ old('pulse_rate') }}">
                    </div>
                    <div class="form-group">
                        <label for="consultRespiratory">Respiratory Rate (cpm)</label>
                        <input type="number" id="consultRespiratory" name="respiratory_rate" class="form-control" placeholder="18" value="{{ old('respiratory_rate') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>Covid Positive?</label>
                    <div class="choice-grid">
                        <label>
                            <input type="radio" name="covid_status" class="choice-input" value="Yes" {{ old('covid_status') === 'Yes' ? 'checked' : '' }}>
                            <span class="choice-card">Yes</span>
                        </label>
                        <label>
                            <input type="radio" name="covid_status" class="choice-input" value="No" {{ old('covid_status', 'No') === 'No' ? 'checked' : '' }}>
                            <span class="choice-card">No</span>
                        </label>
                    </div>
                </div>
            </section>

            <section class="consult-card">
                <h3>Visit Details</h3>
                <div class="form-group">
                    <label for="consultReason">{{ ($user_source ?? '') === 'online' ? 'Appointment Remarks' : 'Reason for Visiting Clinic' }}</label>
                    <input type="text" id="consultReason" name="reason_for_visit" class="form-control" {{ ($user_source ?? '') === 'online' ? 'readonly' : '' }} value="{{ old('reason_for_visit', optional($latestAppointment)->remarks) }}">
                </div>
                <div class="form-group">
                    <label for="consultService">Purpose of Visit / Service</label>
                    <select id="consultService" class="form-control" data-clinic-select @if(($user_source ?? '') === 'online') disabled @else name="service" @endif required>
                        <option value="" disabled {{ !old('service', optional($latestAppointment)->service) ? 'selected' : '' }}>-- Select Service --</option>
                        <option value="General Consultation" {{ old('service', optional($latestAppointment)->service) === 'General Consultation' ? 'selected' : '' }}>General Consultation</option>
                        <option value="BP Monitoring" {{ old('service', optional($latestAppointment)->service) === 'BP Monitoring' ? 'selected' : '' }}>BP Monitoring</option>
                    </select>
                    @if(($user_source ?? '') === 'online')
                        <input type="hidden" name="service" value="{{ old('service', optional($latestAppointment)->service) }}">
                    @endif
                </div>
                <div class="form-group">
                    <label for="consultCondition">Medical Condition (MAR Classification)</label>
                    <select name="condition_id" id="consultCondition" class="form-control mar-required" data-clinic-select required>
                        <option value="" disabled {{ old('condition_id') ? '' : 'selected' }}>-- Select Diagnosis --</option>
                        @foreach($conditions as $condition)
                            <option value="{{ $condition->id }}" {{ (string) old('condition_id') === (string) $condition->id ? 'selected' : '' }}>
                                Category {{ optional($condition->category)->code }}: {{ $condition->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-help">Required for MAR Report.</div>
                </div>
                <div class="form-group">
                    <label for="consultCertificate">Medical Certificate / Clearance</label>
                    <select name="certificate_type" id="consultCertificate" class="form-control" data-clinic-select>
                        <option value="none" {{ old('certificate_type', 'none') === 'none' ? 'selected' : '' }}>None</option>
                        <option value="excused_letter" {{ old('certificate_type') === 'excused_letter' ? 'selected' : '' }}>Excused Letter</option>
                        <option value="coc_ijt" {{ old('certificate_type') === 'coc_ijt' ? 'selected' : '' }}>COC for IJT</option>
                        <option value="coc_ladderized" {{ old('certificate_type') === 'coc_ladderized' ? 'selected' : '' }}>COC for Ladderized</option>
                    </select>
                </div>
            </section>

            <section class="consult-card">
                <div class="medicine-header">
                    <h3>Medicine Dispensing</h3>
                </div>
                <div class="form-group">
                    <label for="consultMedicineSelect">Select Medicine (Inventory)</label>
                    <div class="medicine-selection-row">
                        <select name="item_id" id="consultMedicineSelect" class="form-control" data-clinic-select>
                            <option value="">-- No Medicine Issued --</option>
                            @foreach($items as $item)
                                @php
                                    $availableDispensingQuantity = $item->hasDispensingConversion()
                                        ? $item->availableDispensingQuantity()
                                        : (float) $item->quantity;
                                    $issueUnit = $item->hasDispensingConversion()
                                        ? ($item->dispensing_unit ?: $item->unit)
                                        : ($item->unit ?: 'pcs');
                                    $stockDisplay = rtrim(rtrim(number_format((float) $item->quantity, 2, '.', ''), '0'), '.');
                                    $availableDisplay = rtrim(rtrim(number_format($availableDispensingQuantity, 2, '.', ''), '0'), '.');
                                    $isLowStock = (float) $item->quantity <= (float) ($item->minimum_stock ?? 0);
                                @endphp
                                <option
                                    value="{{ $item->id }}"
                                    data-stock-unit="{{ $item->unit ?: 'pcs' }}"
                                    data-dispensing-unit="{{ $issueUnit }}"
                                    data-has-conversion="{{ $item->hasDispensingConversion() ? '1' : '0' }}"
                                    data-units-per-stock="{{ $item->units_per_stock_unit ?: 1 }}"
                                    data-available-dispensing="{{ $availableDispensingQuantity }}"
                                    data-low-stock="{{ $isLowStock ? '1' : '0' }}"
                                    {{ (string) old('item_id') === (string) $item->id ? 'selected' : '' }}
                                >
                                    {{ $item->name }} (Available: {{ $availableDisplay }} {{ $issueUnit }}@if($item->hasDispensingConversion()) | {{ $stockDisplay }} {{ $item->unit }}@endif)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="selected-stock" id="selectedMedicineStock" aria-live="polite"></div>
                </div>
                <div class="form-group">
                    <label id="consultIssuedQuantityLabel" for="consultIssuedQuantityInput">Quantity to Issue</label>
                    <input type="number" name="issued_quantity" id="consultIssuedQuantityInput" class="form-control" min="0" step="0.01" placeholder="Enter amount" value="{{ old('issued_quantity') }}">
                    <div class="form-help" id="consultIssuedQuantityHelp">Select a medicine to see the dispensing unit and available stock.</div>
                </div>
            </section>

            <section class="consult-card">
                <h3>Clinical Findings</h3>
                <div class="form-group">
                    <label for="consultRemarks">Remarks / Assessment</label>
                    <textarea name="remarks" id="consultRemarks" class="form-control" rows="5" required placeholder="Describe symptoms or concerns...">{{ old('remarks') }}</textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-save" id="finalizeConsultationButton">
                        <span>Save &amp; Finalize Consultation</span>
                    </button>
                    <a href="{{ route($walkinIndexRoute) }}" class="btn-cancel">Cancel</a>
                </div>
            </section>
        </form>
    </div>
</div>

<div class="consultation-success-overlay" id="consultationSuccessOverlay" aria-live="assertive" aria-hidden="true">
    <div class="consultation-success-card">
        <div class="consultation-success-check" aria-hidden="true">
            <x-outline-icon name="check" />
        </div>
        <strong>Done Consultation</strong>
    </div>
</div>

<nav class="consultation-utility-rail" id="consultationUtilityRail" aria-label="Consultation tools">
    <button type="button" class="utility-rail-button" data-utility-target="documents" title="Uploaded Documents">
        <x-outline-icon name="document-text" />
        <span>Documents</span>
        <span class="utility-rail-count">{{ count($studentDocuments) }}</span>
    </button>
    <button type="button" class="utility-rail-button" data-utility-target="inventory" title="Live Stock Tally">
        <x-outline-icon name="cube" />
        <span>Stock Tally</span>
        <span class="utility-rail-count">{{ $items->count() }}</span>
    </button>
    <button type="button" class="utility-rail-button" data-utility-target="treatments" title="Treatment Record">
        <x-outline-icon name="clipboard-document-list" />
        <span>Treatment Record</span>
        <span class="utility-rail-count">{{ $studentTreatments->count() }}</span>
    </button>
</nav>

<aside id="right-utility-panel" aria-hidden="true" aria-label="Consultation utility panel">
    <header class="utility-panel-header">
        <x-outline-icon name="document-text" id="utilityPanelIcon" />
        <h2 class="utility-panel-title" id="utilityPanelTitle">Uploaded Documents</h2>
        <button type="button" id="close-utility-panel" aria-label="Close utility panel">&times;</button>
    </header>

    <section class="utility-panel-pane" data-utility-pane="documents">
        <p class="utility-pane-note">Submitted clinic files and the generated Health Information Form.</p>
        @if(count($studentDocuments))
            <div class="document-list">
                @foreach($studentDocuments as $document)
                    <article class="document-card">
                        <a class="document-preview" href="{{ $document['url'] }}" target="_blank" rel="noopener">
                            @if($document['type'] === 'image')
                                <img src="{{ $document['url'] }}" alt="{{ $document['label'] }} preview">
                            @else
                                <x-outline-icon name="document-text" />
                            @endif
                        </a>
                        <div class="document-card-body">
                            <span class="document-card-title">{{ $document['label'] }}</span>
                            <a class="document-open" href="{{ $document['url'] }}" target="_blank" rel="noopener">
                                <x-outline-icon name="eye" />
                                Open document
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="documents-empty">No uploaded clinic documents are available for this student.</div>
        @endif
    </section>

    <section class="utility-panel-pane" data-utility-pane="inventory">
        <p class="utility-pane-note">{{ $items->count() }} available medicine {{ $items->count() === 1 ? 'item' : 'items' }} from clinic inventory.</p>
        <div class="inventory-tally-list">
            @forelse($items as $item)
                @php
                    $drawerAvailable = $item->hasDispensingConversion() ? $item->availableDispensingQuantity() : (float) $item->quantity;
                    $drawerUnit = $item->hasDispensingConversion() ? ($item->dispensing_unit ?: $item->unit) : ($item->unit ?: 'pcs');
                    $drawerAvailableDisplay = rtrim(rtrim(number_format($drawerAvailable, 2, '.', ''), '0'), '.');
                    $drawerStockDisplay = rtrim(rtrim(number_format((float) $item->quantity, 2, '.', ''), '0'), '.');
                    $drawerLowStock = (float) $item->quantity <= (float) ($item->minimum_stock ?? 0);
                @endphp
                <article class="inventory-tally-item" data-inventory-item="{{ $item->id }}">
                    <div class="inventory-tally-row">
                        <span class="inventory-tally-name">{{ $item->name }}</span>
                        <span class="stock-badge {{ $drawerLowStock ? 'low' : '' }}">{{ $drawerLowStock ? 'Low' : 'In Stock' }}</span>
                    </div>
                    <div class="inventory-tally-meta">
                        {{ $drawerAvailableDisplay }} {{ $drawerUnit }} available
                        @if($item->hasDispensingConversion())
                            | {{ $drawerStockDisplay }} {{ $item->unit }} in storage
                        @endif
                    </div>
                    <div class="inventory-tally-actions">
                        <button type="button" class="inventory-issue-button" data-issue-medicine="{{ $item->id }}">Issue</button>
                    </div>
                </article>
            @empty
                <div class="documents-empty">No medicines are currently available.</div>
            @endforelse
        </div>
    </section>

    <section class="utility-panel-pane" data-utility-pane="treatments">
        <p class="utility-pane-note">The 20 most recent finalized consultations for this patient.</p>
        <div class="treatment-history-list">
            @forelse($studentTreatments as $treatment)
                @php
                    $treatmentDiagnosis = trim((string) optional($treatment->medicalCondition)->name);
                    $treatmentMedicine = trim((string) (optional($treatment->medicineItem)->name ?: $treatment->medicine));
                    $treatmentStaff = trim((string) ($treatment->attending_staff_name ?: optional($treatment->attendingStaff)->name));
                    $treatmentQuantity = (float) $treatment->medicine_quantity;
                @endphp
                <article class="treatment-history-card">
                    <div class="treatment-history-head">
                        <span class="treatment-history-date">{{ optional($treatment->consultation_date)->format('M d, Y') ?: '-' }}</span>
                        <span class="treatment-history-service">{{ $treatment->service ?: 'Consultation' }}</span>
                    </div>
                    <strong>{{ $treatmentDiagnosis ?: 'No diagnosis recorded' }}</strong>
                    <p>{{ $treatment->reason_for_visit ?: $treatment->comments ?: 'No complaint recorded.' }}</p>
                    <div class="treatment-history-meta">
                        <span>Medicine: {{ $treatmentMedicine !== '' && strtolower($treatmentMedicine) !== 'none' ? $treatmentMedicine : 'None issued' }}@if($treatmentQuantity > 0) ({{ rtrim(rtrim(number_format($treatmentQuantity, 2, '.', ''), '0'), '.') }})@endif</span>
                        <span>Attending: {{ $treatmentStaff ?: 'Clinic Staff' }}</span>
                    </div>
                </article>
            @empty
                <div class="documents-empty">No previous treatment records are available for this student.</div>
            @endforelse
        </div>
    </section>
</aside>

<script>
    (function () {
        const medicineSelect = document.getElementById('consultMedicineSelect');
        const quantityLabel = document.getElementById('consultIssuedQuantityLabel');
        const quantityHelp = document.getElementById('consultIssuedQuantityHelp');
        const quantityInput = document.getElementById('consultIssuedQuantityInput');
        const selectedStock = document.getElementById('selectedMedicineStock');
        const utilityPanel = document.getElementById('right-utility-panel');
        const utilityRail = document.getElementById('consultationUtilityRail');
        const utilityButtons = Array.from(document.querySelectorAll('[data-utility-target]'));
        const utilityPanes = Array.from(document.querySelectorAll('[data-utility-pane]'));
        const utilityTitle = document.getElementById('utilityPanelTitle');
        const closeUtilityPanel = document.getElementById('close-utility-panel');
        const headerQuickActions = document.getElementById('headerQuickActions');
        const inventoryCards = Array.from(document.querySelectorAll('[data-inventory-item]'));
        const issueButtons = Array.from(document.querySelectorAll('[data-issue-medicine]'));
        const consultationForm = document.getElementById('consultationForm');
        const finalizeButton = document.getElementById('finalizeConsultationButton');
        const consultationSuccessOverlay = document.getElementById('consultationSuccessOverlay');
        let isSubmittingConsultation = false;
        let activeUtility = '';

        const formatQty = function (value) {
            const numeric = Number(value || 0);
            if (Number.isNaN(numeric)) {
                return '0';
            }
            return Number.isInteger(numeric) ? String(numeric) : numeric.toFixed(2).replace(/\.?0+$/, '');
        };

        const utilityLabels = {
            documents: 'Uploaded Documents',
            inventory: 'Live Medicine Stock',
            treatments: 'Treatment Record'
        };

        const closeUtility = function () {
            utilityPanel.classList.remove('open');
            utilityRail.classList.remove('panel-open');
            utilityPanel.setAttribute('aria-hidden', 'true');
            utilityButtons.forEach(function (button) {
                button.classList.remove('active');
                button.setAttribute('aria-expanded', 'false');
            });
            activeUtility = '';
        };

        const openUtility = function (target) {
            if (activeUtility === target && utilityPanel.classList.contains('open')) {
                closeUtility();
                return;
            }

            activeUtility = target;
            utilityTitle.textContent = utilityLabels[target] || 'Consultation Tools';
            utilityPanes.forEach(function (pane) {
                pane.classList.toggle('active', pane.dataset.utilityPane === target);
            });
            utilityButtons.forEach(function (button) {
                const isActive = button.dataset.utilityTarget === target;
                button.classList.toggle('active', isActive);
                button.setAttribute('aria-expanded', isActive ? 'true' : 'false');
            });
            utilityPanel.classList.add('open');
            utilityRail.classList.add('panel-open');
            utilityPanel.setAttribute('aria-hidden', 'false');
        };

        const syncQuickActionsState = function () {
            const quickActionsOpen = Boolean(headerQuickActions && headerQuickActions.classList.contains('is-open'));
            utilityRail.classList.toggle('quick-actions-active', quickActionsOpen);
            if (quickActionsOpen && utilityPanel.classList.contains('open')) {
                closeUtility();
                utilityRail.classList.add('quick-actions-active');
            }
        };

        const updateMedicineStock = function () {
            const selected = medicineSelect.options[medicineSelect.selectedIndex];
            document.querySelectorAll('[data-inventory-item]').forEach(function (item) {
                item.classList.toggle('selected', Boolean(selected && selected.value && item.dataset.inventoryItem === selected.value));
            });

            quantityInput.setCustomValidity('');
            if (!selected || !selected.value) {
                quantityLabel.textContent = 'Quantity to Issue';
                quantityHelp.textContent = 'Select a medicine to see the dispensing unit and available stock.';
                quantityInput.placeholder = 'Enter amount';
                quantityInput.removeAttribute('max');
                selectedStock.className = 'selected-stock';
                selectedStock.textContent = '';
                return;
            }

            const dispensingUnit = selected.dataset.dispensingUnit || selected.dataset.stockUnit || 'unit';
            const stockUnit = selected.dataset.stockUnit || 'pcs';
            const availableValue = Number(selected.dataset.availableDispensing || 0);
            const available = formatQty(availableValue);
            const hasConversion = selected.dataset.hasConversion === '1';
            const unitsPerStock = formatQty(selected.dataset.unitsPerStock || 1);
            const isLowStock = selected.dataset.lowStock === '1';

            quantityLabel.textContent = 'Quantity to Issue (' + dispensingUnit + ')';
            quantityInput.placeholder = 'Enter ' + dispensingUnit + ' quantity';
            quantityInput.max = String(availableValue);
            quantityHelp.textContent = hasConversion
                ? 'Available: ' + available + ' ' + dispensingUnit + ' (' + unitsPerStock + ' ' + dispensingUnit + ' per ' + stockUnit + ').'
                : 'Available: ' + available + ' ' + stockUnit + '.';
            selectedStock.className = 'selected-stock visible' + (isLowStock ? ' low' : '');
            selectedStock.textContent = (isLowStock ? 'Low stock: ' : 'Available: ') + available + ' ' + dispensingUnit;
        };

        const closeClinicSelects = function (exceptShell) {
            document.querySelectorAll('.clinic-select-shell.is-open').forEach(function (shell) {
                if (shell === exceptShell) return;
                shell.classList.remove('is-open');
                const display = shell.querySelector('.clinic-select-display');
                if (display) {
                    display.classList.remove('is-open');
                    display.setAttribute('aria-expanded', 'false');
                }
            });
        };

        document.querySelectorAll('select[data-clinic-select]').forEach(function (select) {
            const shell = document.createElement('div');
            shell.className = 'clinic-select-shell';
            select.parentNode.insertBefore(shell, select);
            shell.appendChild(select);
            select.classList.add('clinic-select-native');

            const display = document.createElement('button');
            display.type = 'button';
            display.className = 'clinic-select-display';
            display.setAttribute('aria-haspopup', 'listbox');
            display.setAttribute('aria-expanded', 'false');
            display.disabled = select.disabled;

            const menu = document.createElement('div');
            menu.className = 'clinic-select-menu';
            menu.setAttribute('role', 'listbox');

            const syncDisplay = function () {
                const selected = select.options[select.selectedIndex];
                display.textContent = selected ? selected.textContent.trim() : 'Select an option';
                menu.querySelectorAll('.clinic-select-option').forEach(function (optionButton) {
                    optionButton.classList.toggle('is-selected', optionButton.dataset.value === select.value);
                });
            };

            Array.from(select.options).forEach(function (option) {
                const optionButton = document.createElement('button');
                optionButton.type = 'button';
                optionButton.className = 'clinic-select-option';
                optionButton.dataset.value = option.value;
                optionButton.textContent = option.textContent.trim();
                optionButton.disabled = option.disabled;
                optionButton.addEventListener('click', function () {
                    select.value = option.value;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    syncDisplay();
                    closeClinicSelects();
                });
                menu.appendChild(optionButton);
            });

            display.addEventListener('click', function () {
                if (display.disabled) return;
                const opening = !shell.classList.contains('is-open');
                closeClinicSelects(shell);
                shell.classList.toggle('is-open', opening);
                display.classList.toggle('is-open', opening);
                display.setAttribute('aria-expanded', opening ? 'true' : 'false');
            });

            select.addEventListener('change', syncDisplay);
            shell.append(display, menu);
            syncDisplay();
        });

        inventoryCards.forEach(function (card) {
            card.addEventListener('click', function (event) {
                if (event.target.closest('[data-issue-medicine]')) return;
                const shouldSelect = !card.classList.contains('selected');
                inventoryCards.forEach(function (item) {
                    item.classList.remove('selected');
                });
                card.classList.toggle('selected', shouldSelect);
            });
        });

        issueButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const itemId = button.dataset.issueMedicine || '';
                const option = Array.from(medicineSelect.options).find(function (medicineOption) {
                    return medicineOption.value === itemId;
                });
                if (!option) return;

                medicineSelect.value = itemId;
                medicineSelect.dispatchEvent(new Event('change', { bubbles: true }));
                closeUtility();
                medicineSelect.closest('.consult-card')?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                window.setTimeout(function () {
                    quantityInput.focus();
                }, 350);
            });
        });

        quantityInput.addEventListener('input', function () {
            const selected = medicineSelect.options[medicineSelect.selectedIndex];
            const available = selected && selected.value ? Number(selected.dataset.availableDispensing || 0) : 0;
            const requested = Number(quantityInput.value || 0);
            quantityInput.setCustomValidity(requested > available ? 'Quantity cannot exceed the available medicine stock.' : '');
        });
        medicineSelect.addEventListener('change', updateMedicineStock);
        utilityButtons.forEach(function (button) {
            button.setAttribute('aria-expanded', 'false');
            button.addEventListener('click', function () {
                openUtility(button.dataset.utilityTarget);
            });
        });
        closeUtilityPanel.addEventListener('click', closeUtility);
        if (headerQuickActions) {
            new MutationObserver(syncQuickActionsState).observe(headerQuickActions, {
                attributes: true,
                attributeFilter: ['class']
            });
            syncQuickActionsState();
        }
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && utilityPanel.classList.contains('open')) {
                closeUtility();
            }
            if (event.key === 'Escape') {
                closeClinicSelects();
            }
        });
        document.addEventListener('click', function (event) {
            if (!event.target.closest('.clinic-select-shell')) {
                closeClinicSelects();
            }
        });
        consultationForm?.addEventListener('submit', function (event) {
            if (!consultationForm.checkValidity() || !finalizeButton || isSubmittingConsultation) return;
            event.preventDefault();
            isSubmittingConsultation = true;
            finalizeButton.classList.add('is-finalizing');
            finalizeButton.setAttribute('aria-disabled', 'true');
            const label = finalizeButton.querySelector('span');
            if (label) label.textContent = 'Finalizing Consultation...';
            consultationSuccessOverlay?.classList.add('is-open');
            consultationSuccessOverlay?.setAttribute('aria-hidden', 'false');
            window.setTimeout(function () {
                consultationForm.submit();
            }, 850);
        });

        updateMedicineStock();
    })();
</script>
@endsection
