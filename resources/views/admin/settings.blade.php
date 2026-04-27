@extends('layouts.admin')

@section('title', 'Settings')

@push('styles')
<style>
    :root {
        --stg-maroon: #7f0000;
        --stg-maroon-deep: #4f0000;
        --stg-text: #111827;
        --stg-muted: #64748b;
        --stg-border: rgba(127, 0, 0, 0.12);
        --stg-surface: rgba(255, 255, 255, 0.94);
    }

    .settings-page {
        position: relative;
        isolation: isolate;
    }
    .settings-page::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at top left, rgba(127, 0, 0, 0.08), transparent 24%),
            radial-gradient(circle at bottom right, rgba(15, 23, 42, 0.06), transparent 24%),
            linear-gradient(180deg, rgba(248, 250, 252, 0.96), rgba(255, 255, 255, 0.92));
        pointer-events: none;
        z-index: -1;
        border-radius: 28px;
    }
    .settings-page > * {
        position: relative;
        z-index: 1;
    }

    .hero {
        background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(250,247,249,0.96));
        color: var(--stg-text);
        border: 0;
        border-bottom: 2px solid rgba(127, 0, 0, 0.72);
        border-radius: 0 0 20px 20px;
        padding: 28px;
        margin-bottom: 22px;
        box-shadow: 0 16px 32px rgba(15,23,42,0.08);
    }
    .hero-top {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        align-items: flex-start;
        flex-wrap: wrap;
    }
    .hero h1 {
        margin: 0;
        font-size: clamp(28px, 3vw, 36px);
        line-height: 1.05;
        font-weight: 900;
        letter-spacing: -0.03em;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 10px 18px;
        border-radius: 0 0 14px 14px;
        border-bottom: 2px solid rgba(127, 0, 0, 0.72);
        background: transparent;
    }
    .hero h1 svg {
        width: 28px;
        height: 28px;
        flex: 0 0 auto;
    }
    .hero p {
        margin: 12px 0 0;
        max-width: 760px;
        color: var(--stg-muted);
        line-height: 1.7;
        font-size: 14px;
    }
    .badges {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 18px;
    }
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 12px;
        border-radius: 999px;
        background: rgba(127,0,0,0.06);
        border: 1px solid rgba(127,0,0,0.12);
        color: var(--stg-maroon);
        font-size: 12px;
        font-weight: 800;
    }
    .badge span {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #f6c36a;
    }

    .grid {
        display: grid;
        grid-template-columns: 1.05fr 1.25fr;
        gap: 26px;
    }

    .panel {
        position: relative;
        background: var(--stg-surface);
        border: 1px solid var(--stg-border);
        border-radius: 26px;
        box-shadow: 0 24px 70px rgba(15,23,42,0.10);
        overflow: hidden;
    }
    .panel::before {
        content: '';
        position: absolute;
        inset: 0 auto auto 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--stg-maroon), #ad2234 55%, #d4a373);
    }
    .panel-head {
        padding: 22px 24px 18px;
        border-bottom: 1px solid rgba(127,0,0,0.10);
        background: linear-gradient(180deg, rgba(127,0,0,0.03), rgba(255,255,255,0));
    }
    .panel-head-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }
    .section-spot {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        padding: 7px 11px;
        border-radius: 999px;
        color: var(--stg-maroon);
        background: rgba(127, 0, 0, 0.08);
        border: 1px solid rgba(127, 0, 0, 0.12);
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        box-shadow: 0 8px 18px rgba(15,23,42,0.06);
    }
    .section-spot::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--stg-maroon);
        box-shadow: 0 0 0 4px rgba(127,0,0,0.10);
    }
    .panel-head h3 {
        margin: 0;
        font-size: 18px;
        color: var(--stg-maroon);
        font-weight: 900;
    }
    .panel-head p {
        margin: 6px 0 0;
        color: var(--stg-muted);
        line-height: 1.6;
        font-size: 13px;
    }
    .panel-body {
        padding: 28px;
    }
    .btn-edit-profile {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(127,0,0,0.12);
        background: rgba(127,0,0,0.06);
        color: var(--stg-maroon);
        padding: 10px 14px;
        border-radius: 14px;
        font-weight: 800;
        cursor: pointer;
        white-space: nowrap;
        box-shadow: 0 10px 20px rgba(15,23,42,0.06);
    }
    .btn-edit-profile:hover {
        background: rgba(127,0,0,0.10);
    }
    .mini-edit-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(127,0,0,0.12);
        background: rgba(127,0,0,0.06);
        color: var(--stg-maroon);
        padding: 9px 13px;
        border-radius: 13px;
        font-weight: 800;
        cursor: pointer;
        white-space: nowrap;
    }
    .mini-edit-btn:hover {
        background: rgba(127,0,0,0.10);
    }

    .profile-top {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        margin-bottom: 14px;
    }
    .profile-id {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }
    .profile-avatar {
        width: 60px;
        height: 60px;
        border-radius: 20px;
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 16px;
        font-weight: 900;
        letter-spacing: 0.02em;
        background: linear-gradient(135deg, var(--stg-maroon), var(--stg-maroon-deep));
        box-shadow: 0 16px 32px rgba(127,0,0,0.24);
    }
    .profile-name {
        margin: 0;
        font-size: 16px;
        font-weight: 900;
        color: var(--stg-text);
    }
    .profile-role {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
    }
    .profile-role.active {
        color: #166534;
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        border: 1px solid rgba(34, 197, 94, 0.24);
    }
    .profile-role.inactive {
        color: #b91c1c;
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        border: 1px solid rgba(239, 68, 68, 0.24);
    }
    .profile-role svg {
        width: 14px;
        height: 14px;
        flex: 0 0 auto;
    }
    .profile-list {
        display: grid;
        gap: 12px;
    }
    .profile-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 13px 14px;
        border-radius: 16px;
        background: rgba(255,255,255,0.95);
        border: 1px solid rgba(127,0,0,0.10);
    }
    .profile-row .key {
        color: var(--stg-muted);
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .profile-row .val {
        color: var(--stg-text);
        font-size: 13px;
        font-weight: 700;
        text-align: right;
    }
    .editable-list {
        display: grid;
        gap: 12px;
    }
    .editable-row {
        display: grid;
        grid-template-columns: minmax(120px, 160px) minmax(0, 1fr);
        align-items: center;
        gap: 16px;
        padding: 14px 16px;
        border-radius: 18px;
        background: rgba(255,255,255,0.95);
        border: 1px solid rgba(127,0,0,0.10);
    }
    .editable-key {
        color: var(--stg-muted);
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .editable-field input {
        width: 100%;
        min-height: 54px;
        padding: 14px 16px;
        border-radius: 16px;
        border: 1px solid rgba(127,0,0,0.10);
        background: linear-gradient(180deg, rgba(255,255,255,0.99), rgba(250,251,252,0.96));
        color: var(--stg-text);
        box-shadow: 0 12px 28px rgba(15,23,42,0.06), inset 0 1px 0 rgba(255,255,255,0.96);
        transition: 0.2s ease;
    }
    .editable-field input:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 32px rgba(15,23,42,0.08), inset 0 1px 0 rgba(255,255,255,0.98);
    }
    .editable-field input:focus {
        outline: none;
        border-color: var(--stg-maroon);
        background: #fff;
        box-shadow: 0 0 0 5px rgba(127,0,0,0.10), 0 18px 34px rgba(15,23,42,0.10);
        transform: translateY(-1px);
    }

    .field-grid {
        display: grid;
        gap: 16px;
    }
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
        position: absolute;
        left: 14px;
        top: 0;
        transform: translateY(-10px);
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
    .field:focus-within label {
        color: var(--stg-maroon);
        border-color: rgba(127,0,0,0.20);
    }
    .field input,
    .field select {
        width: 100%;
        min-height: 60px;
        padding: 22px 16px 14px;
        border-radius: 18px;
        border: 1px solid rgba(127,0,0,0.10);
        background: linear-gradient(180deg, rgba(255,255,255,0.99), rgba(250,251,252,0.96));
        color: var(--stg-text);
        box-shadow: 0 14px 32px rgba(15,23,42,0.08), inset 0 1px 0 rgba(255,255,255,0.96);
        transition: 0.2s ease;
        appearance: none;
    }
    .field input:hover,
    .field select:hover {
        transform: translateY(-1px);
        box-shadow: 0 16px 34px rgba(15,23,42,0.10), inset 0 1px 0 rgba(255,255,255,0.98);
    }
    .field input:focus,
    .field select:focus {
        outline: none;
        border-color: var(--stg-maroon);
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
        background-image:
            linear-gradient(45deg, transparent 50%, #7f0000 50%),
            linear-gradient(135deg, #7f0000 50%, transparent 50%);
        background-position:
            calc(100% - 20px) calc(50% - 3px),
            calc(100% - 14px) calc(50% - 3px);
        background-size: 6px 6px, 6px 6px;
        background-repeat: no-repeat;
    }
    .field-help {
        margin: 8px 0 0 6px;
        font-size: 12px;
        color: var(--stg-muted);
    }

    .switch-list {
        display: grid;
        gap: 14px;
    }
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
    .switch-item input {
        width: 18px;
        height: 18px;
        accent-color: var(--stg-maroon);
    }
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
        color: var(--stg-text);
    }
    .actions-row {
        display: flex;
        justify-content: flex-end;
        margin-top: 18px;
    }
    .btn-save {
        background: linear-gradient(135deg, var(--stg-maroon), #9a1010 48%, var(--stg-maroon-deep));
        color: #fff;
        padding: 13px 24px;
        border: 1px solid #8f2230;
        border-radius: 999px;
        font-weight: 900;
        position: relative;
        overflow: hidden;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20);
        cursor: pointer;
        transition: color .08s linear, transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        z-index: 0;
    }
    .btn-save::after {
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
    .btn-save:hover {
        transform: translateY(-1px);
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16);
    }
    .btn-save:hover::after {
        transform: translateX(135%);
    }

    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        padding: 24px 16px;
        background: rgba(15,23,42,0.34);
        backdrop-filter: blur(8px);
        z-index: 1000;
        justify-content: center;
        align-items: flex-start;
        overflow-y: auto;
    }
    .modal-box {
        width: min(640px, 100%);
        max-width: 96vw;
        border-radius: 24px;
        overflow: hidden;
        background: #ffffff;
        border: 1px solid rgba(148,163,184,0.16);
        box-shadow: 0 26px 70px rgba(15,23,42,0.18);
    }
    .modal-head {
        padding: 30px 32px 10px;
        border-bottom: 1px solid rgba(148,163,184,0.14);
        background: #ffffff;
        text-align: center;
    }
    .modal-head h3 {
        margin: 0;
        color: var(--stg-maroon);
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .modal-head p {
        margin: 8px auto 0;
        color: var(--stg-muted);
        font-size: 13px;
        line-height: 1.6;
        max-width: 420px;
    }
    .modal-body {
        padding: 22px 32px 28px;
    }
    .modal-actions {
        position: sticky;
        bottom: 0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 14px 32px 28px;
        background: #ffffff;
        border-top: 1px solid rgba(148,163,184,0.14);
    }
    .btn-cancel {
        padding: 11px 16px;
        border-radius: 12px;
        border: 1px solid rgba(127,0,0,0.10);
        background: rgba(127,0,0,0.04);
        color: var(--stg-maroon);
        font-weight: 800;
        cursor: pointer;
    }
    .modal-head .section-spot {
        display: none;
    }
    .modal-body .field-grid,
    .modal-body .field-grid.two,
    .modal-body .field-grid.three {
        grid-template-columns: 1fr;
        gap: 14px;
    }
    .modal-body .field {
        padding: 0;
        border-radius: 0;
        background: transparent;
        border: none;
        box-shadow: none;
        backdrop-filter: none;
    }
    .modal-body .field label {
        position: static;
        transform: none;
        display: block;
        min-height: 0;
        margin-bottom: 8px;
        padding: 0;
        border: none;
        border-radius: 0;
        background: transparent;
        box-shadow: none;
        color: #5f6677;
        font-size: 12px;
        letter-spacing: 0.04em;
    }
    .modal-body .field input,
    .modal-body .field select {
        min-height: 56px;
        padding: 14px 16px;
        border-radius: 14px;
        border: 1px solid #d7dde5;
        background: #ffffff;
        box-shadow: none;
        transform: none !important;
    }
    .modal-body .field input:hover,
    .modal-body .field select:hover {
        border-color: #c4ccd6;
        box-shadow: none;
    }
    .modal-body .field input:focus,
    .modal-body .field select:focus {
        border-color: var(--stg-maroon);
        box-shadow: 0 0 0 4px rgba(127,0,0,0.10);
        transform: none !important;
    }
    .modal-body .field input:disabled {
        background: #f8fafc;
        color: #64748b;
    }
    .modal-body .field-help {
        margin: 8px 0 0;
        padding-left: 2px;
    }
    .modal-section {
        padding: 18px;
        border-radius: 20px;
        border: 1px solid rgba(127,0,0,0.10);
        background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,250,252,0.94));
        box-shadow: 0 12px 26px rgba(15,23,42,0.06);
    }
    .modal-section + .modal-section {
        margin-top: 18px;
    }
    .modal-section-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(127, 0, 0, 0.08);
        color: var(--stg-maroon);
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.10em;
        text-transform: uppercase;
    }
    .modal-section-kicker::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--stg-maroon);
        box-shadow: 0 0 0 4px rgba(127,0,0,0.10);
    }
    .modal-section-title {
        margin: 0 0 6px;
        color: var(--stg-maroon);
        font-size: 18px;
        font-weight: 900;
    }
    .modal-section-copy {
        margin: 0 0 14px;
        color: var(--stg-muted);
        font-size: 13px;
        line-height: 1.6;
    }

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

    .profile-summary .panel-head,
    .field-grid + .field-grid {
        margin-top: 0;
    }
    .profile-stack {
        display: grid;
        gap: 16px;
    }
    .profile-stack .panel {
        margin-bottom: 0;
    }

    html[data-theme="dark"] .settings-page::before {
        background:
            radial-gradient(circle at top left, rgba(255, 184, 28, 0.06), transparent 22%),
            radial-gradient(circle at bottom right, rgba(127, 0, 0, 0.18), transparent 24%),
            linear-gradient(180deg, rgba(35, 11, 18, 0.98), rgba(24, 8, 14, 0.96));
    }
    html[data-theme="dark"] .hero {
        background: linear-gradient(180deg, rgba(57, 22, 31, 0.96), rgba(39, 14, 21, 0.98));
        color: #fff1f4;
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.28);
    }
    html[data-theme="dark"] .hero h1 {
        color: #fff4f7;
    }
    html[data-theme="dark"] .hero p {
        color: #e8cad2;
    }
    html[data-theme="dark"] .badge {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.10);
        color: #ffd9e1;
    }
    html[data-theme="dark"] .panel {
        background: linear-gradient(180deg, rgba(58, 23, 32, 0.96), rgba(38, 14, 21, 0.94));
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 24px 70px rgba(0, 0, 0, 0.26);
    }
    html[data-theme="dark"] .panel-head {
        border-bottom-color: rgba(255, 255, 255, 0.08);
        background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0));
    }
    html[data-theme="dark"] .panel-head h3,
    html[data-theme="dark"] .section-spot,
    html[data-theme="dark"] .btn-edit-profile,
    html[data-theme="dark"] .mini-edit-btn {
        color: #ffd7df;
    }
    html[data-theme="dark"] .panel-head p,
    html[data-theme="dark"] .field-help,
    html[data-theme="dark"] .profile-row .key,
    html[data-theme="dark"] .section-subtitle {
        color: #d7b3bc !important;
    }
    html[data-theme="dark"] .section-spot,
    html[data-theme="dark"] .btn-edit-profile,
    html[data-theme="dark"] .mini-edit-btn {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.10);
        box-shadow: none;
    }
    html[data-theme="dark"] .section-spot::before {
        background: #ffb8c6;
        box-shadow: 0 0 0 4px rgba(255, 184, 198, 0.12);
    }
    html[data-theme="dark"] .profile-name,
    html[data-theme="dark"] .profile-row .val,
    html[data-theme="dark"] .switch-item label {
        color: #fff1f4;
    }
    html[data-theme="dark"] .profile-role.active {
        color: #bbf7d0;
        background: rgba(22, 101, 52, 0.22);
        border-color: rgba(34, 197, 94, 0.24);
    }
    html[data-theme="dark"] .profile-role.inactive {
        color: #fecaca;
        background: rgba(153, 27, 27, 0.22);
        border-color: rgba(248, 113, 113, 0.24);
    }
    html[data-theme="dark"] .profile-row,
    html[data-theme="dark"] .switch-item,
    html[data-theme="dark"] .editable-row {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.08);
    }
    html[data-theme="dark"] .editable-key {
        color: #d7b3bc;
    }
    html[data-theme="dark"] .editable-field input,
    html[data-theme="dark"] .field input,
    html[data-theme="dark"] .field select {
        background: rgba(20, 9, 13, 0.92);
        color: #fff1f4;
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.04);
    }
    html[data-theme="dark"] .editable-field input::placeholder,
    html[data-theme="dark"] .field input::placeholder {
        color: #b7929d;
    }
    html[data-theme="dark"] .field label {
        background: rgba(32, 12, 19, 0.96);
        border-color: rgba(255, 255, 255, 0.08);
        color: #e1c0c8;
        box-shadow: none;
    }
    html[data-theme="dark"] .field:focus-within label {
        color: #ffd7df;
        border-color: rgba(255, 184, 198, 0.16);
    }
    html[data-theme="dark"] .editable-field input:hover,
    html[data-theme="dark"] .field input:hover,
    html[data-theme="dark"] .field select:hover {
        box-shadow: 0 0 0 1px rgba(255,255,255,0.04);
    }
    html[data-theme="dark"] .editable-field input:focus,
    html[data-theme="dark"] .field input:focus,
    html[data-theme="dark"] .field select:focus {
        background: rgba(28, 11, 17, 0.98);
        border-color: #ffb8c6;
        box-shadow: 0 0 0 5px rgba(255, 184, 198, 0.10), 0 12px 30px rgba(0,0,0,0.24);
    }
    html[data-theme="dark"] .field input:disabled {
        background: rgba(53, 32, 39, 0.84);
        color: #c9aab3;
    }
    html[data-theme="dark"] .modal-overlay {
        background: rgba(5, 2, 4, 0.62);
    }
    html[data-theme="dark"] .modal-box {
        background: linear-gradient(180deg, rgba(43, 17, 25, 0.98), rgba(28, 10, 16, 0.98));
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 34px 90px rgba(0,0,0,0.34);
    }
    html[data-theme="dark"] .modal-head,
    html[data-theme="dark"] .modal-actions {
        background: rgba(43, 17, 25, 0.98);
        border-color: rgba(255, 255, 255, 0.08);
    }
    html[data-theme="dark"] .modal-head h3 {
        color: #ffd7df;
    }
    html[data-theme="dark"] .modal-head p {
        color: #d7b3bc;
    }
    html[data-theme="dark"] .btn-cancel {
        background: rgba(255,255,255,0.06);
        color: #fff1f4;
        border-color: rgba(255,255,255,0.10);
    }
    html[data-theme="dark"] .modal-body .field label {
        color: #d7b3bc;
    }
    html[data-theme="dark"] .modal-body .field input,
    html[data-theme="dark"] .modal-body .field select {
        background: rgba(20, 9, 13, 0.92);
        color: #fff1f4;
        border-color: rgba(255,255,255,0.10);
    }
    html[data-theme="dark"] .modal-body .field input:hover,
    html[data-theme="dark"] .modal-body .field select:hover {
        border-color: rgba(255,255,255,0.18);
    }
    html[data-theme="dark"] .modal-body .field input:focus,
    html[data-theme="dark"] .modal-body .field select:focus {
        border-color: #ffb8c6;
        box-shadow: 0 0 0 4px rgba(255,184,198,0.10);
    }
    html[data-theme="dark"] .modal-section {
        background: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.08);
        box-shadow: 0 18px 34px rgba(0,0,0,0.18);
    }
    html[data-theme="dark"] .modal-section-kicker {
        background: rgba(255,255,255,0.08);
        color: #ffd7df;
    }
    html[data-theme="dark"] .modal-section-kicker::before {
        background: #ffb8c6;
        box-shadow: 0 0 0 4px rgba(255,184,198,0.10);
    }
    html[data-theme="dark"] .modal-section-title {
        color: #ffd7df;
    }
    html[data-theme="dark"] .modal-section-copy {
        color: #d7b3bc;
    }
    html[data-theme="dark"] .alert-success {
        background: rgba(22, 101, 52, 0.18);
        color: #b7f7cd;
        border-color: rgba(34, 197, 94, 0.24);
    }
    html[data-theme="dark"] .alert-error {
        background: rgba(153, 27, 27, 0.18);
        color: #fecaca;
        border-color: rgba(248, 113, 113, 0.24);
    }

    @media (max-width: 1080px) {
        .grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .hero { padding: 24px 20px; }
        .panel-body, .modal-body, .modal-head { padding-left: 18px; padding-right: 18px; }
        .field-grid.two, .field-grid.three { grid-template-columns: 1fr; }
        .editable-row { grid-template-columns: 1fr; gap: 10px; }
        .modal-overlay { padding: 12px; }
        .modal-actions { padding: 14px 18px 18px; flex-wrap: wrap; }
        .modal-actions button { width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="settings-page">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="hero">
        <div class="hero-top">
            <div>
                <h1><x-outline-icon name="cog-6-tooth" />Settings</h1>
                <p>Manage the clinic identity, operating hours, and system preferences from one modern control panel.</p>
                <div class="badges">
                    <div class="badge"><span></span> CMS Admin Profile</div>
                    <div class="badge"><span></span> Clinic Operations</div>
                    <div class="badge"><span></span> System Preferences</div>
                </div>
            </div>
        </div>
    </section>

    <div class="grid">
        <div class="profile-stack">
        <section class="panel">
            <div class="panel-head">
                <div class="panel-head-top">
                    <div>
                        <h3>CMS Admin Profile</h3>
                        <p>Read-only hub profile for the current clinic administrator.</p>
                    </div>
                    <button type="button" class="btn-edit-profile" onclick="openProfileModal()">Edit Profile</button>
                </div>
            </div>
            <div class="panel-body">
                <div class="profile-top">
                    @php
                        $profileName = trim(($cmsProfile['first_name'] ?? '') . ' ' . ($cmsProfile['last_name'] ?? ''));
                        $profileInitials = '';
                        foreach (preg_split('/\s+/', trim($profileName ?: 'NA')) as $part) {
                            if ($part === '') continue;
                            $profileInitials .= strtoupper(mb_substr($part, 0, 1));
                            if (strlen($profileInitials) >= 2) break;
                        }
                        $profileStatus = strtolower($cmsProfile['status'] ?? 'active');
                        $statusIcon = $profileStatus === 'inactive'
                            ? '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="10" fill="currentColor"/><path d="M8 8L16 16M16 8L8 16" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg>'
                            : '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="10" fill="currentColor"/><path d="M8 12.5L10.9 15.4L16.5 9.5" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                    @endphp
                    <div class="profile-id">
                        <div class="profile-avatar">{{ $profileInitials ?: 'NA' }}</div>
                        <div>
                            <p class="profile-name">{{ $cmsProfile['first_name'] ?? 'N/A' }} {{ $cmsProfile['last_name'] ?? '' }}</p>
                            <div class="section-subtitle" style="margin-top:4px; color:var(--stg-muted);">{{ $cmsProfile['email'] ?? ($admin->email ?? 'N/A') }}</div>
                        </div>
                    </div>
                    <div class="profile-role {{ $profileStatus === 'inactive' ? 'inactive' : 'active' }}">{!! $statusIcon !!} {{ ucfirst($profileStatus) }}</div>
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
                </div>
            </div>
        </section>

        @if($canManageClearanceSignature)
        <section class="panel">
            <div class="panel-head">
                <div class="panel-head-top">
                    <div>
                        <div class="section-spot">Clearance Signature</div>
                        <h3>Nurse Digital Signature</h3>
                        <p>Upload your digital signature to be used for official clearances..</p>
                    </div>
                    <button type="button" class="mini-edit-btn" onclick="openSettingsModal('clearanceSignatureModal')">Upload</button>
                </div>
            </div>
            <div class="panel-body">
                <div class="profile-list">
                    <div class="profile-row">
                        <div class="key">Current Signature</div>
                        <div class="val">
                            @if($clearanceSignatureUrl)
                                <img src="{{ $clearanceSignatureUrl }}" alt="Current clearance signature" style="max-height:72px; width:auto; display:block; margin-left:auto;">
                            @else
                                No signature uploaded yet
                            @endif
                        </div>
                    </div>
                    <div class="profile-row">
                        <div class="key">Usage</div>
                        <div class="val">Shown on issued clearance preview and printable health forms.</div>
                    </div>
                </div>
            </div>
        </section>
        @endif
        </div>

        <div style="display:grid; gap:22px;">
            <section class="panel">
                <div class="panel-head">
                    <div class="panel-head-top">
                        <div>
                            <div class="section-spot">Clinic Data</div>
                            <h3>Clinic Information</h3>
                            <p>Read-only clinic identity details for this workspace.</p>
                        </div>
                        <button type="button" class="mini-edit-btn" onclick="openSettingsModal('clinicInfoModal')">Edit</button>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="profile-list">
                        <div class="profile-row"><div class="key">Clinic Name</div><div class="val">{{ $settings->clinic_name ?: 'N/A' }}</div></div>
                        <div class="profile-row"><div class="key">Location</div><div class="val">{{ $settings->clinic_location ?: 'N/A' }}</div></div>
                    </div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div class="panel-head-top">
                        <div>
                            <div class="section-spot">Clinic Schedule</div>
                            <h3>Clinic Hours</h3>
                            <p>Read-only daily operating schedule for the clinic.</p>
                        </div>
                        <button type="button" class="mini-edit-btn" onclick="openSettingsModal('clinicHoursModal')">Edit</button>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="profile-list">
                        <div class="profile-row"><div class="key">Opening Time</div><div class="val">{{ $settings->open_time ?: 'N/A' }}</div></div>
                        <div class="profile-row"><div class="key">Closing Time</div><div class="val">{{ $settings->close_time ?: 'N/A' }}</div></div>
                    </div>
                </div>
            </section>

            <form action="{{ url('/admin/settings/update') }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="preferences_form" value="1">
                <section class="panel">
                    <div class="panel-head">
                        <div class="section-spot">Workflow</div>
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

    <div id="clinicInfoModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-head">
                <div class="section-spot">Clinic Data</div>
                <h3>Edit Clinic Information</h3>
                <p>Update the clinic identity details shown across the system.</p>
            </div>
            <form action="{{ url('/admin/settings/update') }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
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
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeSettingsModal('clinicInfoModal')">Cancel</button>
                    <button type="submit" class="btn-save">Save Clinic Information</button>
                </div>
            </form>
        </div>
    </div>

    <div id="clinicHoursModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-head">
                <div class="section-spot">Clinic Schedule</div>
                <h3>Edit Clinic Hours</h3>
                <p>Update the daily opening and closing schedule for the clinic.</p>
            </div>
            <form action="{{ url('/admin/settings/update') }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
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
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeSettingsModal('clinicHoursModal')">Cancel</button>
                    <button type="submit" class="btn-save">Save Clinic Hours</button>
                </div>
            </form>
        </div>
    </div>

    @if($canManageClearanceSignature)
    <div id="clearanceSignatureModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-head">
                <div class="section-spot">Clearance Signature</div>
                <h3>Upload Nurse Digital Signature</h3>
                <p>This signature will appear automatically once a health clearance is marked as issued.</p>
            </div>
            <form action="{{ url('/admin/settings/update') }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body">
                    @if($clearanceSignatureUrl)
                        <div style="margin-bottom:16px; text-align:center;">
                            <img src="{{ $clearanceSignatureUrl }}" alt="Current clearance signature" style="max-height:110px; width:auto;">
                        </div>
                    @endif
                    <div class="field-grid">
                        <div class="field">
                            <label>Digital Signature File</label>
                            <input type="file" name="clearance_signature" accept="image/png,image/jpeg,image/jpg" required>
                            <p class="field-help">Use a clear PNG or JPG image. Transparent background works best.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeSettingsModal('clearanceSignatureModal')">Cancel</button>
                    <button type="submit" class="btn-save">Save Signature</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div id="profileModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-head">
                <div class="section-spot">Edit Admin</div>
                <h3>Edit Profile</h3>
                <p>Keep your admin identity and clinic contact details aligned with the hub record.</p>
            </div>

            <form action="{{ url('/admin/profile/update') }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <section class="modal-section">
                        <div class="modal-section-kicker">Profile Details</div>
                        <h4 class="modal-section-title">Personal Information</h4>
                        <p class="modal-section-copy">Update the main identity details shown in the CMS Admin Profile card.</p>

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
                                <label>Gender</label>
                                <input type="text" name="gender" value="{{ old('gender', $cmsProfile['gender'] ?? '') }}" placeholder="Gender">
                            </div>
                            <div class="field">
                                <label>Civil Status</label>
                                <input type="text" name="civil_status" value="{{ old('civil_status', $cmsProfile['civil_status'] ?? '') }}" placeholder="Civil status">
                            </div>
                        </div>
                    </section>

                    <section class="modal-section">
                        <div class="modal-section-kicker">Clinic Access</div>
                        <h4 class="modal-section-title">Contact, Office, and Security</h4>
                        <p class="modal-section-copy">Keep clinic-facing contact details and login-related settings in one place.</p>

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
                            <p class="field-help" style="margin:0; font-weight:900; letter-spacing:0.06em; text-transform:uppercase;">Change Password (Optional)</p>
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
                        </div>
                    </section>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeProfileModal()">Cancel</button>
                    <button type="submit" class="btn-save">Save Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openSettingsModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function closeSettingsModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function openProfileModal() {
        document.getElementById('profileModal').style.display = 'flex';
        syncCmsAge();
    }

    function closeProfileModal() {
        document.getElementById('profileModal').style.display = 'none';
    }

    window.addEventListener('click', function (e) {
        if (e.target === document.getElementById('profileModal')) {
            closeProfileModal();
        }
        if (e.target === document.getElementById('clinicInfoModal')) {
            closeSettingsModal('clinicInfoModal');
        }
        if (e.target === document.getElementById('clinicHoursModal')) {
            closeSettingsModal('clinicHoursModal');
        }
        if (e.target === document.getElementById('clearanceSignatureModal')) {
            closeSettingsModal('clearanceSignatureModal');
        }
    });

    function syncCmsAge() {
        const birthdayInput = document.getElementById('cmsBirthdayInput');
        const ageInput = document.getElementById('cmsAgeInput');

        if (!birthdayInput || !ageInput) return;

        if (!birthdayInput.value) {
            ageInput.value = '';
            return;
        }

        const birthday = new Date(birthdayInput.value + 'T00:00:00');
        if (Number.isNaN(birthday.getTime())) {
            ageInput.value = '';
            return;
        }

        const today = new Date();
        let age = today.getFullYear() - birthday.getFullYear();
        const monthDiff = today.getMonth() - birthday.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthday.getDate())) {
            age -= 1;
        }
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
