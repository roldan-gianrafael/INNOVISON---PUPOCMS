@extends('layouts.admin')

@section('title', 'Settings')

@push('styles')
<style>
    :root {
<<<<<<< ours
        --maroon: #7f0000;
        --maroon-deep: #4f0000;
        --text: #111827;
        --muted: #64748b;
        --border: rgba(127, 0, 0, 0.12);
        --surface: rgba(255, 255, 255, 0.92);
    }

    .settings-page {
        position: relative;
    }
    .settings-page::before {
        content: '';
        position: fixed;
        inset: 0;
        background:
            radial-gradient(circle at top left, rgba(127,0,0,0.08), transparent 25%),
            linear-gradient(180deg, rgba(248,250,252,0.95), rgba(255,255,255,0.92));
        pointer-events: none;
        z-index: 0;
    }
    .settings-page > * { position: relative; z-index: 1; }

    .hero {
        border-radius: 28px;
        padding: 28px;
        margin-bottom: 22px;
        color: #fff;
        background: linear-gradient(135deg, rgba(127,0,0,0.98), rgba(79,0,0,0.94));
        box-shadow: 0 24px 60px rgba(127,0,0,0.18);
    }
    .hero-top {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: flex-start;
        flex-wrap: wrap;
    }
    .hero h1 {
        margin: 0;
        font-size: clamp(28px, 3vw, 36px);
        line-height: 1.05;
        font-weight: 900;
        letter-spacing: -0.03em;
    }
    .hero p {
        margin: 12px 0 0;
        max-width: 760px;
        color: rgba(255,255,255,0.82);
        line-height: 1.7;
        font-size: 14px;
    }
    .hero-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .hero-btn {
        border: 1px solid rgba(255,255,255,0.18);
        background: rgba(255,255,255,0.12);
        color: #fff;
        padding: 11px 16px;
        border-radius: 14px;
        font-weight: 800;
        cursor: pointer;
    }
    .hero-btn.primary {
        background: #fff;
        color: var(--maroon);
    }
    .badges { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 18px; }
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 12px;
        border-radius: 999px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.15);
        font-size: 12px;
        font-weight: 800;
    }
    .badge span { width: 8px; height: 8px; border-radius: 50%; background: #f6c36a; }

    .grid {
        display: grid;
        grid-template-columns: 1.05fr 1.25fr;
        gap: 22px;
    }
    .panel {
        position: relative;
        background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(255,255,255,0.92));
        border: 1px solid var(--border);
        border-radius: 26px;
        box-shadow: 0 24px 70px rgba(15,23,42,0.10);
        overflow: hidden;
    }
    .panel::before {
=======
        --settings-maroon: #7f0000;
        --settings-maroon-deep: #4f0000;
        --settings-maroon-soft: rgba(127, 0, 0, 0.08);
        --settings-surface: rgba(255, 255, 255, 0.88);
        --settings-surface-strong: #ffffff;
        --settings-border: rgba(127, 0, 0, 0.12);
        --settings-text: #111827;
        --settings-muted: #64748b;
        --settings-shadow: 0 24px 70px rgba(15, 23, 42, 0.10);
    }

    /* Cards */
    .card {
        position: relative;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 255, 255, 0.90)),
            linear-gradient(135deg, rgba(127, 0, 0, 0.04), transparent 45%);
        border-radius: 28px;
        padding: 30px;
        box-shadow: var(--settings-shadow);
        border: 1px solid var(--settings-border);
        margin-bottom: 24px;
        overflow: hidden;
        backdrop-filter: blur(8px);
    }
    .card::before {
>>>>>>> theirs
        content: '';
        position: absolute;
        inset: 0 auto auto 0;
        width: 100%;
<<<<<<< ours
        height: 4px;
        background: linear-gradient(90deg, var(--maroon), #ad2234 55%, #d4a373);
    }
    .panel-head {
        padding: 22px 24px 16px;
        border-bottom: 1px solid rgba(127,0,0,0.08);
    }
    .panel-head h3 {
        margin: 0;
        font-size: 18px;
        color: var(--maroon);
        font-weight: 900;
    }
    .panel-head p {
        margin: 6px 0 0;
        color: var(--muted);
        line-height: 1.6;
        font-size: 13px;
    }
    .panel-body { padding: 24px; }

    .profile-list { display: grid; gap: 10px; }
    .profile-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 16px;
        background: rgba(255,255,255,0.9);
        border: 1px solid rgba(148,163,184,0.16);
    }
    .profile-row .key {
        color: var(--muted);
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .profile-row .val {
        color: var(--text);
        font-size: 13px;
        font-weight: 700;
        text-align: right;
    }
    .profile-top {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        margin-bottom: 14px;
    }
    .profile-name { margin: 0; font-size: 16px; font-weight: 900; color: var(--text); }
    .profile-role {
        padding: 7px 10px;
        border-radius: 999px;
        color: #fff;
        background: linear-gradient(135deg, var(--maroon), var(--maroon-deep));
        font-size: 11px;
        font-weight: 900;
    }

    .field-grid { display: grid; gap: 16px; }
    .field-grid.two { grid-template-columns: repeat(2, minmax(0,1fr)); }
    .field-grid.three { grid-template-columns: repeat(3, minmax(0,1fr)); }
    .field {
        position: relative;
        padding: 14px 14px 12px;
        border-radius: 22px;
        background: linear-gradient(180deg, rgba(255,255,255,0.88), rgba(248,250,252,0.72));
        border: 1px solid rgba(127,0,0,0.08);
        box-shadow: 0 12px 28px rgba(15,23,42,0.06);
        backdrop-filter: blur(8px);
    }
    .field label {
=======
        height: 5px;
        background: linear-gradient(90deg, var(--settings-maroon), #a61a2b 50%, #d4a373);
    }
    .card,
    .card *:not(.pill-status):not(.btn-save) {
        color: var(--settings-text);
    }
    .card h3 {
        margin-top: 0;
        color: var(--settings-maroon);
        margin-bottom: 20px;
        font-size: 19px;
        font-weight: 800;
        letter-spacing: 0.01em;
    }
    .card-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 20px;
        padding-bottom: 18px;
        border-bottom: 1px solid rgba(127, 0, 0, 0.10);
    }
    .card-header-row h3 {
        margin: 0;
        line-height: 1.2;
    }
    .section-subtitle {
        margin-top: 6px;
        color: var(--settings-muted);
        font-size: 13px;
        line-height: 1.5;
    }
    .section-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }
    .profile-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px 20px; }
    .profile-grid-wide { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px 20px; }
    .profile-note {
        padding: 15px 18px;
        border-radius: 18px;
        background: linear-gradient(180deg, #fff, #f8fafc);
        color: var(--settings-text);
        font-size: 13px;
        line-height: 1.6;
        margin-bottom: 18px;
        border: 1px solid rgba(127, 0, 0, 0.10);
    }
    .pill-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        text-transform: capitalize;
        letter-spacing: 0.02em;
    }
    .pill-status.active { background: #dcfce7; color: #166534; }
    .pill-status.inactive { background: #fee2e2; color: #991b1b; }
    .pill-status.pending { background: #e2e8f0; color: #334155; }
    .readonly-helper { font-size: 12px; color: var(--settings-muted); margin-top: 6px; }

    /* Forms */
    .form-group {
        position: relative;
        margin-bottom: 18px;
        padding: 16px 14px 14px;
        border-radius: 22px;
        background: linear-gradient(180deg, rgba(255,255,255,0.72), rgba(248,250,252,0.58));
        border: 1px solid rgba(127, 0, 0, 0.08);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        backdrop-filter: blur(6px);
        overflow: visible;
    }
    .form-group label {
>>>>>>> theirs
        position: absolute;
        left: 14px;
        top: 0;
        transform: translateY(-10px);
<<<<<<< ours
        padding: 0 12px;
        min-height: 26px;
        border-radius: 999px;
        border: 1px solid rgba(127,0,0,0.10);
        background: linear-gradient(180deg, rgba(255,255,255,0.99), rgba(249,250,252,0.98));
        color: #5f6677;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        pointer-events: none;
        box-shadow: 0 8px 18px rgba(15,23,42,0.08);
    }
    .field:focus-within label { color: var(--maroon); border-color: rgba(127,0,0,0.20); }
    .field input,
    .field select {
        width: 100%;
        min-height: 60px;
        padding: 22px 16px 14px;
        border-radius: 18px;
        border: 1px solid rgba(127,0,0,0.10);
        background: linear-gradient(180deg, rgba(255,255,255,0.99), rgba(250,251,252,0.96));
        color: var(--text);
        box-shadow: 0 14px 32px rgba(15,23,42,0.08), inset 0 1px 0 rgba(255,255,255,0.96);
        transition: 0.2s ease;
        appearance: none;
    }
    .field input:hover,
    .field select:hover { transform: translateY(-1px); box-shadow: 0 16px 34px rgba(15,23,42,0.10), inset 0 1px 0 rgba(255,255,255,0.98); }
    .field input:focus,
    .field select:focus {
        outline: none;
        border-color: var(--maroon);
        background: #fff;
        box-shadow: 0 0 0 6px rgba(127,0,0,0.10), 0 20px 40px rgba(15,23,42,0.12), 0 0 28px rgba(127,0,0,0.10);
        transform: translateY(-2px);
    }
    .field input::placeholder { color: #94a3b8; }
    .field input:disabled {
        cursor: not-allowed;
        background: linear-gradient(180deg, #f8fafc, #eef2f7);
        color: #64748b;
    }
    .field select {
        padding-right: 44px;
=======
        display: inline-flex;
        align-items: center;
        min-height: 26px;
        padding: 0 12px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.09em;
        text-transform: uppercase;
        color: #5f6677;
        background: linear-gradient(180deg, rgba(255,255,255,0.99), rgba(249,250,252,0.98));
        border: 1px solid rgba(127, 0, 0, 0.10);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        pointer-events: none;
        z-index: 2;
        transition: transform 0.22s ease, color 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease, background 0.22s ease;
    }
    .form-group:focus-within label {
        transform: translateY(-14px) scale(1.03);
        color: var(--settings-maroon);
        border-color: rgba(127, 0, 0, 0.22);
        box-shadow: 0 12px 24px rgba(127, 0, 0, 0.14);
        background: linear-gradient(180deg, rgba(255,255,255,0.99), rgba(255,250,250,0.98));
    }
    .form-control {
        width: 100%;
        min-height: 62px;
        padding: 22px 16px 14px;
        border: 1px solid rgba(127, 0, 0, 0.10);
        border-radius: 18px;
        font-size: 14px;
        color: var(--settings-text);
        background: linear-gradient(180deg, rgba(255,255,255,0.99), rgba(249,250,252,0.96));
        transition: border-color 0.22s ease, box-shadow 0.22s ease, transform 0.22s ease, background 0.22s ease, color 0.22s ease, filter 0.22s ease;
        box-shadow:
            0 16px 32px rgba(15, 23, 42, 0.10),
            0 4px 10px rgba(15, 23, 42, 0.05),
            inset 0 1px 0 rgba(255,255,255,0.96);
        letter-spacing: 0.01em;
    }
    .form-control:hover {
        border-color: rgba(127, 0, 0, 0.28);
        box-shadow:
            0 18px 36px rgba(15, 23, 42, 0.12),
            0 6px 12px rgba(15, 23, 42, 0.06),
            inset 0 1px 0 rgba(255,255,255,0.97);
        transform: translateY(-2px);
    }
    .form-control:focus {
        border-color: var(--settings-maroon);
        background: #fff;
        box-shadow:
            0 0 0 6px rgba(127, 0, 0, 0.12),
            0 20px 40px rgba(15, 23, 42, 0.12),
            0 6px 12px rgba(15, 23, 42, 0.06),
            0 0 28px rgba(127, 0, 0, 0.12);
        outline: none;
        transform: translateY(-3px);
    }
    .form-control::placeholder {
        color: #94a3b8;
        opacity: 1;
    }
    .form-control:disabled {
        background: linear-gradient(180deg, #f8fafc, #eef2f7);
        color: #64748b;
        cursor: not-allowed;
        box-shadow:
            0 12px 22px rgba(15, 23, 42, 0.05),
            inset 0 1px 0 rgba(255,255,255,0.92);
    }
    .form-group .readonly-helper {
        margin-top: 8px;
        padding-left: 8px;
    }
    select.form-control {
        appearance: none;
>>>>>>> theirs
        background-image:
            linear-gradient(45deg, transparent 50%, #7f0000 50%),
            linear-gradient(135deg, #7f0000 50%, transparent 50%);
        background-position:
            calc(100% - 20px) calc(50% - 3px),
            calc(100% - 14px) calc(50% - 3px);
        background-size: 6px 6px, 6px 6px;
        background-repeat: no-repeat;
<<<<<<< ours
    }
    .field-help { margin: 8px 0 0 6px; font-size: 12px; color: var(--muted); }

    .switch-list { display: grid; gap: 14px; }
    .switch-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        border-radius: 20px;
        background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,250,252,0.92));
        border: 1px solid rgba(127,0,0,0.10);
        box-shadow: 0 12px 28px rgba(15,23,42,0.05);
    }
    .switch-item input { width: 18px; height: 18px; accent-color: var(--maroon); }
    .switch-item label {
        position: static;
        transform: none;
        background: transparent;
        border: none;
        box-shadow: none;
        pointer-events: auto;
        padding: 0;
        min-height: auto;
        text-transform: none;
        letter-spacing: 0;
        font-size: 14px;
        color: var(--text);
    }

    .actions-row { display: flex; justify-content: flex-end; margin-top: 18px; }
    .btn-save {
        background: linear-gradient(135deg, var(--maroon), #9a1010 48%, var(--maroon-deep));
        color: #fff;
        padding: 13px 24px;
        border: none;
        border-radius: 16px;
        font-weight: 900;
        box-shadow: 0 16px 30px rgba(127,0,0,0.22);
        cursor: pointer;
    }

=======
        padding-right: 42px;
    }

    /* Switches */
    .switch-row {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 12px;
        padding: 15px 18px;
        border-radius: 18px;
        transition: 0.2s ease;
        background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,250,252,0.9));
        border: 1px solid rgba(127, 0, 0, 0.10);
        box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
    }
    .switch-row:hover {
        background: rgba(255, 255, 255, 0.96);
        border-color: rgba(127, 0, 0, 0.18);
        transform: translateY(-1px) scale(1.005);
    }
    .switch-row input { width: 18px; height: 18px; accent-color: var(--settings-maroon); cursor: pointer; }
    .switch-label { font-size: 14px; font-weight: 700; color: var(--settings-text); cursor: pointer; flex: 1; }
    .switch-row input:focus-visible {
        outline: none;
        box-shadow: 0 0 0 4px rgba(127, 0, 0, 0.12);
        border-radius: 4px;
    }

    /* Buttons */
    .btn-save {
        background: linear-gradient(135deg, var(--settings-maroon), #9a1010 48%, var(--settings-maroon-deep));
        color: white;
        padding: 12px 24px;
        border-radius: 16px;
        border: none;
        font-weight: 800;
        letter-spacing: 0.01em;
        cursor: pointer;
        box-shadow: 0 14px 26px rgba(127, 0, 0, 0.22), inset 0 1px 0 rgba(255,255,255,0.18);
        transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease, background 0.2s ease;
    }
    .btn-save:hover {
        filter: brightness(1.03);
        transform: translateY(-1px);
        box-shadow: 0 18px 34px rgba(127, 0, 0, 0.28), inset 0 1px 0 rgba(255,255,255,0.22);
    }
    .btn-save:active {
        transform: translateY(0);
    }
    
    .btn-edit {
        background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,250,252,0.92));
        color: var(--settings-maroon);
        padding: 10px 16px;
        border-radius: 14px;
        border: 1px solid rgba(127, 0, 0, 0.14);
        font-weight: 800;
        cursor: pointer;
        font-size: 13px;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }
    .btn-edit:hover {
        background: #fff;
        transform: translateY(-1px);
        box-shadow: 0 12px 22px rgba(15, 23, 42, 0.10);
    }

    /* Modal */
>>>>>>> theirs
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        padding: 24px 16px;
<<<<<<< ours
        background: rgba(15,23,42,0.62);
=======
        background: rgba(15, 23, 42, 0.62);
>>>>>>> theirs
        backdrop-filter: blur(10px);
        z-index: 1000;
        justify-content: center;
        align-items: flex-start;
        overflow-y: auto;
    }
    .modal-box {
<<<<<<< ours
        width: min(980px, 100%);
        max-width: 96vw;
        border-radius: 28px;
        overflow: hidden;
        background: linear-gradient(180deg, rgba(255,255,255,0.99), rgba(249,250,251,0.96));
        border: 1px solid rgba(255,255,255,0.76);
        box-shadow: 0 34px 90px rgba(15,23,42,0.32);
    }
    .modal-head {
        padding: 24px 26px 18px;
        border-bottom: 1px solid rgba(127,0,0,0.10);
    }
    .modal-head h3 { margin: 0; color: var(--maroon); font-size: 20px; font-weight: 900; }
    .modal-head p { margin: 6px 0 0; color: var(--muted); font-size: 13px; line-height: 1.6; }
=======
        background: radial-gradient(circle at top right, rgba(127, 0, 0, 0.06), transparent 24%), linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(249, 250, 251, 0.96));
        padding: 0;
        border-radius: 28px;
        width: min(960px, 100%);
        max-width: 96vw;
        max-height: calc(100vh - 48px);
        overflow-y: auto;
        box-shadow: 0 32px 90px rgba(15, 23, 42, 0.32);
        border: 1px solid rgba(255, 255, 255, 0.78);
    }
    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 24px 26px 18px;
        margin-bottom: 0;
        border-bottom: 1px solid rgba(127, 0, 0, 0.10);
        background: linear-gradient(180deg, rgba(255,255,255,0.99), rgba(250,250,252,0.96));
        position: sticky;
        top: 0;
        z-index: 1;
    }
    .modal-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: var(--settings-maroon);
    }
>>>>>>> theirs
    .modal-body { padding: 26px; }
    .modal-actions {
        position: sticky;
        bottom: 0;
        display: flex;
<<<<<<< ours
        justify-content: flex-end;
        gap: 10px;
        padding: 18px 26px 26px;
        background: linear-gradient(to top, rgba(255,255,255,0.98) 74%, rgba(255,255,255,0.88) 90%, rgba(255,255,255,0));
        border-top: 1px solid rgba(127,0,0,0.10);
    }
    .btn-cancel {
        padding: 12px 18px;
        border-radius: 14px;
        border: 1px solid rgba(148,163,184,0.22);
        background: rgba(255,255,255,0.92);
        color: #334155;
        font-weight: 800;
        cursor: pointer;
    }

=======
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
        margin-left: 0;
        margin-right: 0;
        margin-bottom: 0;
        padding: 18px 26px 26px;
        background: linear-gradient(to top, rgba(255,255,255,0.98) 74%, rgba(255,255,255,0.88) 90%, rgba(255,255,255,0));
        border-top: 1px solid rgba(127, 0, 0, 0.10);
        border-radius: 0 0 28px 28px;
    }
    
    /* Alerts */
>>>>>>> theirs
    .alert {
        padding: 14px 16px;
        border-radius: 16px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 700;
        border: 1px solid transparent;
    }
    .alert-success { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }
    .alert-error { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }

<<<<<<< ours
    @media (max-width: 1080px) {
        .grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .hero { padding: 24px 20px; }
        .panel-body, .modal-body, .modal-head { padding-left: 18px; padding-right: 18px; }
        .field-grid.two, .field-grid.three { grid-template-columns: 1fr; }
        .modal-overlay { padding: 12px; }
        .modal-actions { padding: 14px 18px 18px; flex-wrap: wrap; }
        .modal-actions button { width: 100%; }
=======
    @media (max-width: 768px) {
        .profile-grid,
        .profile-grid-wide {
            grid-template-columns: 1fr;
        }

        .modal-overlay {
            padding: 12px;
        }

        .modal-box {
            padding: 18px;
            max-height: calc(100vh - 24px);
        }

        .modal-actions {
            bottom: -18px;
            margin-left: -18px;
            margin-right: -18px;
            margin-bottom: -18px;
            padding: 14px 18px 18px;
            flex-wrap: wrap;
        }

        .modal-actions button {
            width: 100%;
        }
>>>>>>> theirs
    }
</style>
@endpush

@section('content')
<<<<<<< ours
<div class="settings-page">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
=======

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
>>>>>>> theirs
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
<<<<<<< ours
            <ul style="margin:0; padding-left:18px;">
=======
            <ul>
>>>>>>> theirs
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

<<<<<<< ours
    <section class="hero">
        <div class="hero-top">
            <div>
                <h1>Settings</h1>
                <p>Manage the clinic identity, operating hours, and system preferences from one modern control panel.</p>
                <div class="badges">
                    <div class="badge"><span></span> CMS Admin Profile</div>
                    <div class="badge"><span></span> Clinic Operations</div>
                    <div class="badge"><span></span> System Preferences</div>
                </div>
            </div>
            <div class="hero-actions">
                <button class="hero-btn primary" onclick="openProfileModal()">Edit Profile</button>
=======
    <section class="card">
        <div class="card-header-row">
            <div>
                <h3>CMS Admin Profile</h3>
                <div class="section-subtitle">The clinic hub profile shown across the admin side.</div>
            </div>
            <div class="section-actions">
                <button class="btn-edit" onclick="openProfileModal()">Edit Profile</button>
            </div>
        </div>

        <div class="profile-grid-wide">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['first_name'] ?? 'N/A' }}" disabled>
            </div>
            <div class="form-group">
                <label>Middle Name</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['middle_name'] ?? 'N/A' }}" disabled>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['last_name'] ?? 'N/A' }}" disabled>
            </div>
            <div class="form-group">
                <label>Suffix Name</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['suffix_name'] ?? 'N/A' }}" disabled>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" value="{{ $cmsProfile['email'] ?? ($admin->email ?? '') }}" disabled>
            </div>
            <div class="form-group">
                <label>Birthday</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['birthday'] ?? 'N/A' }}" disabled>
            </div>
            <div class="form-group">
                <label>Age</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['age'] ?? 'N/A' }}" disabled>
            </div>
            <div class="form-group">
                <label>Gender</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['gender'] ?? 'N/A' }}" disabled>
            </div>
            <div class="form-group">
                <label>Civil Status</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['civil_status'] ?? 'N/A' }}" disabled>
            </div>
            <div class="form-group">
                <label>Emergency Contact Person</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['emergency_contact_person'] ?? 'N/A' }}" disabled>
            </div>
            <div class="form-group">
                <label>Emergency Contact No.</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['emergency_contact_no'] ?? ($cmsProfile['contact_number'] ?? 'N/A') }}" disabled>
            </div>
            <div class="form-group">
                <label>Office</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['office'] ?? 'Admission Office' }}" disabled>
            </div>
            <div class="form-group">
                <label>Status</label>
                <input type="text" class="form-control" value="{{ ucfirst($cmsProfile['status'] ?? 'active') }}" disabled>
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Address</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['address'] ?? 'N/A' }}" disabled>
>>>>>>> theirs
            </div>
        </div>
    </section>

<<<<<<< ours
    <div class="grid">
        <section class="panel">
            <div class="panel-head">
                <h3>CMS Admin Profile</h3>
                <p>Read-only hub profile for the current clinic administrator.</p>
            </div>
            <div class="panel-body">
                <div class="profile-top">
                    <div>
                        <p class="profile-name">{{ $cmsProfile['first_name'] ?? 'N/A' }} {{ $cmsProfile['last_name'] ?? '' }}</p>
                        <div class="section-subtitle" style="margin-top:4px; color:var(--muted);">{{ $cmsProfile['email'] ?? ($admin->email ?? 'N/A') }}</div>
                    </div>
                    <div class="profile-role">{{ ucfirst($cmsProfile['status'] ?? 'active') }}</div>
                </div>
                <div class="profile-list">
                    <div class="profile-row"><div class="key">Admin ID</div><div class="val">{{ !empty($cmsProfile['admin_id']) ? str_pad((string) $cmsProfile['admin_id'], 3, '0', STR_PAD_LEFT) : 'N/A' }}</div></div>
                    <div class="profile-row"><div class="key">Middle Name</div><div class="val">{{ $cmsProfile['middle_name'] ?? 'N/A' }}</div></div>
                    <div class="profile-row"><div class="key">Suffix</div><div class="val">{{ $cmsProfile['suffix_name'] ?? 'N/A' }}</div></div>
                    <div class="profile-row"><div class="key">Birthday</div><div class="val">{{ $cmsProfile['birthday'] ?? 'N/A' }}</div></div>
                    <div class="profile-row"><div class="key">Age</div><div class="val">{{ $cmsProfile['age'] ?? 'N/A' }}</div></div>
                    <div class="profile-row"><div class="key">Gender</div><div class="val">{{ $cmsProfile['gender'] ?? 'N/A' }}</div></div>
                    <div class="profile-row"><div class="key">Civil Status</div><div class="val">{{ $cmsProfile['civil_status'] ?? 'N/A' }}</div></div>
                    <div class="profile-row"><div class="key">Office</div><div class="val">{{ $cmsProfile['office'] ?? 'Admission Office' }}</div></div>
                    <div class="profile-row"><div class="key">Emergency Contact</div><div class="val">{{ $cmsProfile['emergency_contact_person'] ?? 'N/A' }}</div></div>
                    <div class="profile-row"><div class="key">Contact No.</div><div class="val">{{ $cmsProfile['emergency_contact_no'] ?? ($cmsProfile['contact_number'] ?? 'N/A') }}</div></div>
                    <div class="profile-row"><div class="key">Address</div><div class="val">{{ $cmsProfile['address'] ?? 'N/A' }}</div></div>
=======
    <form action="{{ url('/admin/settings/update') }}" method="POST">
        @csrf @method('PUT')
        
        <section class="card">
            <div class="card-header-row">
                <div>
                    <h3>Clinic Information</h3>
                    <div class="section-subtitle">Update the public clinic name and location used across the system.</div>
                </div>
            </div>

            <div class="form-group">
                <label>Clinic Name</label>
                <input type="text" name="clinic_name" class="form-control" value="{{ $settings->clinic_name }}">
            </div>

            <div class="form-group">
                <label>Location</label>
                <input type="text" name="clinic_location" class="form-control" value="{{ $settings->clinic_location }}">
            </div>
        </section>

        <section class="card">
            <div class="card-header-row">
                <div>
                    <h3>Clinic Hours</h3>
                    <div class="section-subtitle">Set the regular open and close schedule for the clinic.</div>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Opening Time</label>
                    <input type="time" name="open_time" class="form-control" value="{{ $settings->open_time }}">
                </div>
                <div class="form-group">
                    <label>Closing Time</label>
                    <input type="time" name="close_time" class="form-control" value="{{ $settings->close_time }}">
>>>>>>> theirs
                </div>
            </div>
        </section>

<<<<<<< ours
        <div style="display:grid; gap:22px;">
            <form action="{{ url('/admin/settings/update') }}" method="POST">
                @csrf @method('PUT')
                <section class="panel">
                    <div class="panel-head">
                        <h3>Clinic Information</h3>
                        <p>Update the clinic name and location shown throughout the system.</p>
                    </div>
                    <div class="panel-body">
                        <div class="field-grid">
                            <div class="field">
                                <label>Clinic Name</label>
                                <input type="text" name="clinic_name" value="{{ $settings->clinic_name }}" placeholder="Clinic name">
                            </div>
                            <div class="field">
                                <label>Location</label>
                                <input type="text" name="clinic_location" value="{{ $settings->clinic_location }}" placeholder="Clinic location">
                            </div>
                        </div>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-head">
                        <h3>Clinic Hours</h3>
                        <p>Set the daily opening and closing time for the clinic.</p>
                    </div>
                    <div class="panel-body">
                        <div class="field-grid two">
                            <div class="field">
                                <label>Opening Time</label>
                                <input type="time" name="open_time" value="{{ $settings->open_time }}">
                            </div>
                            <div class="field">
                                <label>Closing Time</label>
                                <input type="time" name="close_time" value="{{ $settings->close_time }}">
                            </div>
                        </div>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-head">
                        <h3>System Preferences</h3>
                        <p>Control reminder and auto-approval behavior for the clinic workflow.</p>
                    </div>
                    <div class="panel-body">
                        <div class="switch-list">
                            <div class="switch-item">
                                <input type="checkbox" name="email_notifications" id="emailNotif" {{ $settings->email_notifications ? 'checked' : '' }}>
                                <label for="emailNotif">Enable Email Notifications</label>
                            </div>
                            <div class="switch-item">
                                <input type="checkbox" name="auto_approve" id="autoApprove" {{ $settings->auto_approve ? 'checked' : '' }}>
                                <label for="autoApprove">Auto-approve Student Requests</label>
                            </div>
                        </div>
                        <div class="actions-row">
                            <button type="submit" class="btn-save">Save System Settings</button>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </div>

    <div id="profileModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-head">
                <h3>Edit Profile</h3>
                <p>Keep your admin identity and clinic contact details aligned with the hub record.</p>
            </div>

            <form action="{{ url('/admin/profile/update') }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="field-grid three">
                        <div class="field">
                            <label>Admin ID</label>
                            <input type="text" value="{{ !empty($cmsProfile['admin_id']) ? str_pad((string) $cmsProfile['admin_id'], 3, '0', STR_PAD_LEFT) : 'N/A' }}" disabled>
                        </div>
                        <div class="field">
                            <label>First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $cmsProfile['first_name'] ?? '') }}" required>
                        </div>
                        <div class="field">
                            <label>Middle Name</label>
                            <input type="text" name="middle_name" value="{{ old('middle_name', $cmsProfile['middle_name'] ?? '') }}" placeholder="Middle name">
                        </div>
                        <div class="field">
                            <label>Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $cmsProfile['last_name'] ?? '') }}" required>
                        </div>
                        <div class="field">
                            <label>Suffix Name</label>
                            <input type="text" name="suffix_name" value="{{ old('suffix_name', $cmsProfile['suffix_name'] ?? '') }}" placeholder="Jr., Sr., III">
                        </div>
                        <div class="field">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ old('email', $cmsProfile['email'] ?? ($admin->email ?? '')) }}" required>
                        </div>
                    </div>

                    <div class="field-grid two" style="margin-top:16px;">
                        <div class="field">
                            <label>Birthday</label>
                            <input type="date" id="cmsBirthdayInput" name="birthday" value="{{ old('birthday', $cmsProfile['birthday'] ?? '') }}">
                        </div>
                        <div class="field">
                            <label>Age</label>
                            <input type="text" id="cmsAgeInput" value="{{ old('age', $cmsProfile['age'] ?? '') }}" readonly>
                            <p class="field-help">Auto-calculated from birthday.</p>
                        </div>
                    </div>

                    <div class="field-grid two">
                        <div class="field">
                            <label>Address</label>
                            <input type="text" name="address" value="{{ old('address', $cmsProfile['address'] ?? '') }}" placeholder="Complete address">
                        </div>
                        <div class="field">
                            <label>Contact Number</label>
                            <input type="text" name="contact_number" value="{{ old('contact_number', $cmsProfile['contact_number'] ?? '') }}" placeholder="Contact number">
                        </div>
                    </div>

                    <div class="field-grid two">
                        <div class="field">
                            <label>Gender</label>
                            <input type="text" name="gender" value="{{ old('gender', $cmsProfile['gender'] ?? '') }}" placeholder="Gender">
                        </div>
                        <div class="field">
                            <label>Civil Status</label>
                            <input type="text" name="civil_status" value="{{ old('civil_status', $cmsProfile['civil_status'] ?? '') }}" placeholder="Civil status">
                        </div>
                    </div>

                    <div class="field-grid two">
                        <div class="field">
                            <label>Emergency Contact Person</label>
                            <input type="text" name="emergency_contact_person" value="{{ old('emergency_contact_person', $cmsProfile['emergency_contact_person'] ?? '') }}" placeholder="Emergency contact person">
                        </div>
                        <div class="field">
                            <label>Emergency Contact No.</label>
                            <input type="text" name="emergency_contact_no" value="{{ old('emergency_contact_no', $cmsProfile['emergency_contact_no'] ?? ($cmsProfile['contact_number'] ?? '')) }}" placeholder="Emergency contact number">
                        </div>
                    </div>

                    <div class="field-grid two">
                        <div class="field">
                            <label>Office</label>
                            <input type="text" name="office" value="{{ old('office', $cmsProfile['office'] ?? 'Admission Office') }}" placeholder="Office">
                        </div>
                        <div class="field">
                            <label>Status</label>
                            <select name="status">
                                <option value="active" {{ old('status', $cmsProfile['status'] ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $cmsProfile['status'] ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin: 6px 0 4px; padding-top: 10px; border-top: 1px solid rgba(127,0,0,0.10);">
                        <p class="section-subtitle" style="margin:0; font-size:12px; font-weight:900; letter-spacing:0.06em; text-transform:uppercase;">Change Password (Optional)</p>
                    </div>

                    <div class="field-grid two">
                        <div class="field">
                            <label>New Password</label>
                            <input type="password" name="password" placeholder="Leave blank to keep current">
                        </div>
                        <div class="field">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" placeholder="Retype password">
                        </div>
=======
        <section class="card">
            <div class="card-header-row">
                <div>
                    <h3>System Preferences</h3>
                    <div class="section-subtitle">Control the behavior of reminders and automatic approvals.</div>
                </div>
            </div>

            <div class="switch-row">
                <input type="checkbox" name="email_notifications" id="emailNotif" {{ $settings->email_notifications ? 'checked' : '' }}>
                <label for="emailNotif" class="switch-label">Enable Email Notifications</label>
            </div>

            <div class="switch-row">
                <input type="checkbox" name="auto_approve" id="autoApprove" {{ $settings->auto_approve ? 'checked' : '' }}>
                <label for="autoApprove" class="switch-label">Auto-approve Student Requests</label>
            </div>
        </section>

        <div style="text-align: right;">
            <button type="submit" class="btn-save">Save System Settings</button>
        </div>
    </form>

    <div id="profileModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <div>
                    <h3>Edit Profile</h3>
                    <div class="section-subtitle">Keep your admin identity and clinic contact details current.</div>
                </div>
            </div>
            
            <form action="{{ url('/admin/profile/update') }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">

                    <div class="profile-grid-wide">
                        <div class="form-group">
                            <label>Admin ID</label>
                            <input type="text" class="form-control" value="{{ !empty($cmsProfile['admin_id']) ? str_pad((string) $cmsProfile['admin_id'], 3, '0', STR_PAD_LEFT) : 'N/A' }}" disabled>
                        </div>

                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $cmsProfile['first_name'] ?? '') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $cmsProfile['middle_name'] ?? '') }}" placeholder="Middle name">
                        </div>

                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $cmsProfile['last_name'] ?? '') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Suffix Name</label>
                            <input type="text" name="suffix_name" class="form-control" value="{{ old('suffix_name', $cmsProfile['suffix_name'] ?? '') }}" placeholder="Jr., Sr., III">
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $cmsProfile['email'] ?? ($admin->email ?? '')) }}" required>
                        </div>
                    </div>

                    <div class="profile-grid">
                        <div class="form-group">
                            <label>Birthday</label>
                            <input type="date" id="cmsBirthdayInput" name="birthday" class="form-control" value="{{ old('birthday', $cmsProfile['birthday'] ?? '') }}">
                        </div>

                        <div class="form-group">
                            <label>Age</label>
                            <input type="text" id="cmsAgeInput" class="form-control" value="{{ old('age', $cmsProfile['age'] ?? '') }}" readonly>
                            <p class="readonly-helper">Auto-calculated from birthday.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $cmsProfile['address'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $cmsProfile['contact_number'] ?? '') }}">
                    </div>

                    <div class="profile-grid">
                        <div class="form-group">
                            <label>Gender</label>
                            <input type="text" name="gender" class="form-control" value="{{ old('gender', $cmsProfile['gender'] ?? '') }}">
                        </div>

                        <div class="form-group">
                            <label>Civil Status</label>
                            <input type="text" name="civil_status" class="form-control" value="{{ old('civil_status', $cmsProfile['civil_status'] ?? '') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Emergency Contact Person</label>
                        <input type="text" name="emergency_contact_person" class="form-control" value="{{ old('emergency_contact_person', $cmsProfile['emergency_contact_person'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Emergency Contact No.</label>
                        <input type="text" name="emergency_contact_no" class="form-control" value="{{ old('emergency_contact_no', $cmsProfile['emergency_contact_no'] ?? ($cmsProfile['contact_number'] ?? '')) }}">
                    </div>

                    <div class="form-group">
                        <label>Office</label>
                        <input type="text" name="office" class="form-control" value="{{ old('office', $cmsProfile['office'] ?? 'Admission Office') }}">
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active" {{ old('status', $cmsProfile['status'] ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $cmsProfile['status'] ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div style="border-top: 1px solid rgba(148, 163, 184, 0.18); margin: 18px 0; padding-top: 16px;">
                        <label style="font-size: 12px; color: #64748b; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase;">Change Password (Optional)</label>
                    </div>

                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Retype password">
>>>>>>> theirs
                    </div>
                </div>

                <div class="modal-actions">
<<<<<<< ours
                    <button type="button" class="btn-cancel" onclick="closeProfileModal()">Cancel</button>
=======
                    <button type="button" onclick="closeProfileModal()" style="background: rgba(255,255,255,0.9); color:#334155; border:1px solid rgba(148,163,184,0.22); padding: 10px 18px; border-radius: 12px; cursor: pointer; font-weight: 700;">Cancel</button>
>>>>>>> theirs
                    <button type="submit" class="btn-save">Save Profile</button>
                </div>
            </form>
        </div>
    </div>
<<<<<<< ours
</div>
=======

>>>>>>> theirs
@endsection

@push('scripts')
<script>
    function openProfileModal() {
        document.getElementById('profileModal').style.display = 'flex';
        syncCmsAge();
    }
    function closeProfileModal() {
        document.getElementById('profileModal').style.display = 'none';
    }
    window.onclick = function(e) {
<<<<<<< ours
        if (e.target == document.getElementById('profileModal')) closeProfileModal();
=======
        if(e.target == document.getElementById('profileModal')) closeProfileModal();
>>>>>>> theirs
    }

    function syncCmsAge() {
        const birthdayInput = document.getElementById('cmsBirthdayInput');
        const ageInput = document.getElementById('cmsAgeInput');
<<<<<<< ours
        if (!birthdayInput || !ageInput) return;
        if (!birthdayInput.value) { ageInput.value = ''; return; }

        const birthday = new Date(birthdayInput.value + 'T00:00:00');
        if (Number.isNaN(birthday.getTime())) { ageInput.value = ''; return; }
=======

        if (!birthdayInput || !ageInput) {
            return;
        }

        if (!birthdayInput.value) {
            ageInput.value = '';
            return;
        }

        const birthday = new Date(birthdayInput.value + 'T00:00:00');
        if (Number.isNaN(birthday.getTime())) {
            ageInput.value = '';
            return;
        }
>>>>>>> theirs

        const today = new Date();
        let age = today.getFullYear() - birthday.getFullYear();
        const monthDiff = today.getMonth() - birthday.getMonth();
<<<<<<< ours
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthday.getDate())) age -= 1;
=======

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthday.getDate())) {
            age -= 1;
        }

>>>>>>> theirs
        ageInput.value = age >= 0 ? age : '';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const birthdayInput = document.getElementById('cmsBirthdayInput');
        if (birthdayInput) {
            birthdayInput.addEventListener('change', syncCmsAge);
            syncCmsAge();
        }
    });
</script>
@endpush
