@extends('layouts.admin')

@section('title', 'User Management')

@push('styles')
<style>
    .user-management-shell {
        max-width: 1480px;
        margin: 0 auto;
        padding: 20px 24px 40px;
        color: #0f172a;
    }

    .um-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .um-hero h1 {
        margin: 0;
        font-size: 1.85rem;
        font-weight: 800;
        color: #111827;
    }

    .um-hero p {
        margin: 6px 0 0;
        color: #475569;
    }


    .um-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .um-stat {
        background: rgba(255,255,255,0.96);
        border: 1px solid rgba(128, 0, 0, 0.10);
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.05);
    }

    html[data-theme="dark"] .um-stat,
    html[data-theme="dark"] .um-card,
    html[data-theme="dark"] .um-modal-content {
        background: rgba(12, 18, 32, 0.96);
        color: #e5eefb;
        border-color: rgba(148, 163, 184, 0.14);
        box-shadow: 0 18px 32px rgba(0, 0, 0, 0.28);
    }


    html[data-theme="dark"] .um-hero {
    }

    html[data-theme="dark"] .user-management-shell,
    html[data-theme="dark"] .um-hero h1,
    html[data-theme="dark"] .um-name,
    html[data-theme="dark"] .um-modal-head h3,
    html[data-theme="dark"] .um-field input,
    html[data-theme="dark"] .um-field select,
    html[data-theme="dark"] .um-field textarea {
        color: #e5eefb;
    }

    html[data-theme="dark"] .um-hero p,
    html[data-theme="dark"] .um-summary-note,
    html[data-theme="dark"] .um-directory-toggle .hint,
    html[data-theme="dark"] .um-sub,
    html[data-theme="dark"] .um-note,
    html[data-theme="dark"] .um-empty,
    html[data-theme="dark"] .um-stat .label,
    html[data-theme="dark"] .um-summary-label,
    html[data-theme="dark"] .um-field label {
        color: #ffffff;
    }

    html[data-theme="dark"] .user-management-shell,
    html[data-theme="dark"] .user-management-shell * {
        color: #ffffff;
    }

    html[data-theme="dark"] .um-stat .value,
    html[data-theme="dark"] .um-summary-value {
        color: #fca5a5;
    }

    html[data-theme="dark"] .um-card,
    html[data-theme="dark"] .um-detail-card {
        background: rgba(9, 14, 26, 0.96);
        border-color: rgba(148, 163, 184, 0.16);
    }

    html[data-theme="dark"] .um-summary-card {
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(17, 24, 39, 0.94));
        border-color: rgba(148, 163, 184, 0.14);
    }

    html[data-theme="dark"] .um-mode-btn {
        background: linear-gradient(145deg, #5f0012 0%, #7d0b17 45%, #5a0010 100%);
        border-color: rgba(255, 214, 102, 0.5);
        box-shadow:
            0 0 0 1px rgba(255, 214, 102, 0.16),
            0 24px 38px rgba(95, 0, 18, 0.34),
            0 52px 72px -38px rgba(193, 138, 16, 0.56);
    }

    html[data-theme="dark"] .um-mode-btn.active {
        border-color: rgba(255, 214, 102, 0.68);
        background: linear-gradient(145deg, #6d0014 0%, #8a0d19 42%, #670012 100%);
        box-shadow:
            0 0 0 1px rgba(255, 214, 102, 0.22),
            0 26px 40px rgba(95, 0, 18, 0.38),
            0 56px 76px -38px rgba(193, 138, 16, 0.52);
    }

    html[data-theme="dark"] .um-mode-btn::before {
        background: linear-gradient(180deg, rgba(193, 138, 16, 0.16), rgba(125, 11, 23, 0.06) 42%, rgba(95, 0, 18, 0.14));
    }

    html[data-theme="dark"] .um-mode-btn .eyebrow {
        background: rgba(193, 138, 16, 0.22);
        color: #ffd86b;
    }

    html[data-theme="dark"] .um-mode-btn h3,
    html[data-theme="dark"] .um-panel-intro {
        color: #ffffff;
    }

    html[data-theme="dark"] .um-mode-btn p {
        color: #ffe7a6;
    }

    html[data-theme="dark"] .um-mode-btn::after {
        background: radial-gradient(circle, rgba(193, 138, 16, 0.34) 0%, rgba(95, 0, 18, 0.18) 42%, transparent 78%);
    }

    html[data-theme="dark"] .um-mode-icon {
        background: linear-gradient(145deg, rgba(255, 214, 102, 0.26), rgba(193, 138, 16, 0.52));
        border-color: rgba(255, 214, 102, 0.34);
        color: #111827;
        box-shadow:
            inset 0 1px 0 rgba(255, 210, 90, 0.12),
            0 18px 28px rgba(95, 0, 18, 0.28);
    }

    html[data-theme="dark"] .um-panel-header h2,
    html[data-theme="dark"] .um-panel-header p {
        color: #fff;
    }

    html[data-theme="dark"] .um-card-head,
    html[data-theme="dark"] .um-modal-head {
        border-color: rgba(148, 163, 184, 0.14);
    }

    html[data-theme="dark"] .um-table thead th {
        background: rgba(15, 23, 42, 0.98);
        color: #cbd5e1;
        border-bottom-color: rgba(148, 163, 184, 0.14);
    }

    html[data-theme="dark"] .um-table tbody td {
        border-bottom-color: rgba(148, 163, 184, 0.12);
        color: #e5eefb;
    }

    html[data-theme="dark"] .um-search input,
    html[data-theme="dark"] .um-field input,
    html[data-theme="dark"] .um-field select,
    html[data-theme="dark"] .um-field textarea {
        background: rgba(15, 23, 42, 0.92);
        border-color: rgba(148, 163, 184, 0.24);
    }

    html[data-theme="dark"] .um-search input::placeholder,
    html[data-theme="dark"] .um-field input::placeholder,
    html[data-theme="dark"] .um-field textarea::placeholder {
        color: #64748b;
    }

    html[data-theme="dark"] .um-btn-soft {
        background: rgba(15, 23, 42, 0.92);
        color: #e5eefb;
        border-color: rgba(148, 163, 184, 0.22);
    }

    html[data-theme="dark"] .um-action-btn {
        background: rgba(15, 23, 42, 0.92);
        color: #fca5a5;
        border-color: rgba(248, 113, 113, 0.28);
    }

    html[data-theme="dark"] .um-action-btn:hover {
        background: rgba(127, 29, 29, 0.26);
    }

    html[data-theme="dark"] .um-badge.source {
        background: rgba(127, 29, 29, 0.22);
        color: #fda4af;
    }

    html[data-theme="dark"] .um-badge.active {
        background: rgba(34, 197, 94, 0.16);
        color: #86efac;
    }

    html[data-theme="dark"] .um-badge.inactive {
        background: rgba(239, 68, 68, 0.16);
        color: #fca5a5;
    }

    html[data-theme="dark"] .um-cursor-hint {
        background: rgba(226, 232, 240, 0.96);
        color: #0f172a;
    }

    .um-stat .label {
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #64748b;
        margin-bottom: 6px;
    }

    .um-stat .value {
        font-size: 1.65rem;
        font-weight: 800;
        color: #800000;
    }

    .um-card {
        background: rgba(255,255,255,0.98);
        border: 1px solid rgba(100, 116, 139, 0.16);
        border-radius: 18px;
        box-shadow: 0 18px 32px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .um-mode-picker {
        display: flex;
        justify-content: center;
        gap: 18px;
        margin: 18px 0 24px;
        flex-wrap: wrap;
    }

    .um-mode-btn {
        width: min(360px, 100%);
        min-height: 280px;
        border: 1px solid rgba(234, 179, 8, 0.42);
        border-radius: 28px;
        padding: 28px 24px 30px;
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 48%, #e5e7eb 100%);
        box-shadow:
            0 0 0 1px rgba(250, 204, 21, 0.12),
            0 22px 36px rgba(234, 179, 8, 0.18),
            0 48px 60px -36px rgba(202, 138, 4, 0.36);
        text-align: left;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease, background .22s ease;
    }


    .um-mode-btn::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(255,255,255,0.92), rgba(255,255,255,0.28) 42%, rgba(255,255,255,0.10));
        pointer-events: none;
    }

    .um-mode-btn::after {
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

    .um-mode-btn:hover {
        transform: translateY(-4px);
        border-color: rgba(234, 179, 8, 0.62);
        box-shadow:
            0 0 0 1px rgba(250, 204, 21, 0.22),
            0 26px 42px rgba(234, 179, 8, 0.22),
            0 54px 76px -38px rgba(202, 138, 4, 0.42);
    }

    .um-mode-btn.active {
        border-color: rgba(202, 138, 4, 0.72);
        box-shadow:
            0 0 0 1px rgba(250, 204, 21, 0.26),
            0 28px 48px rgba(234, 179, 8, 0.24),
            0 56px 78px -38px rgba(202, 138, 4, 0.48);
    }

    .um-mode-btn .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(241, 245, 249, 0.94);
        color: #64748b;
        font-size: .76rem;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
        position: relative;
        z-index: 1;
    }

    .um-mode-icon {
        width: 68px;
        height: 68px;
        margin: 18px 0 10px;
        border-radius: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(145deg, rgba(254, 240, 138, 0.98), rgba(250, 204, 21, 0.9));
        border: 1px solid rgba(234, 179, 8, 0.34);
        color: #111827;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,0.96),
            0 18px 28px rgba(234, 179, 8, 0.18);
        position: relative;
        z-index: 1;
        animation: umModeFloat 3.8s ease-in-out infinite;
    }

    .um-mode-icon::after {
        content: "";
        position: absolute;
        left: 12%;
        right: 12%;
        bottom: -14px;
        height: 16px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(148, 163, 184, 0.24) 0%, rgba(148, 163, 184, 0.1) 44%, transparent 82%);
        filter: blur(8px);
        opacity: 0.72;
        z-index: -1;
        pointer-events: none;
    }

    .um-mode-icon svg {
        width: 30px;
        height: 30px;
        display: block;
        stroke: currentColor;
    }

    .um-mode-btn h3 {
        margin: 14px 0 8px;
        font-size: 1.24rem;
        font-weight: 900;
        color: #0f172a;
        position: relative;
        z-index: 1;
    }

    .um-mode-btn p {
        margin: 0;
        color: #64748b;
        line-height: 1.6;
        font-size: .95rem;
        position: relative;
        z-index: 1;
    }

    .um-mode-panel {
        display: none;
    }

    .um-mode-panel.is-active {
        display: block;
    }

    .um-mode-picker.is-hidden {
        display: none;
    }

    .um-panel-intro {
        padding: 16px 20px 0;
        color: #64748b;
        line-height: 1.6;
    }

    .um-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 20px 0;
    }

    .um-panel-header h2 {
        margin: 0;
        font-size: 1.3rem;
        font-weight: 900;
        color: #111827;
    }

    .um-panel-header p {
        margin: 6px 0 0;
        color: #64748b;
    }

    .um-btn-ghost {
        background: transparent;
        color: #800000;
        border: 1px solid rgba(128, 0, 0, 0.16);
    }

    @keyframes umModeFloat {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-8px);
        }
    }

    .um-summary-grid {
        display: flex;
        gap: 12px;
        padding: 16px 20px 8px;
        overflow-x: auto;
        scrollbar-width: thin;
    }

    .um-summary-card {
        min-width: 220px;
        flex: 0 0 220px;
        background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,250,252,0.94));
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 16px;
        padding: 12px 14px;
        box-shadow: 0 8px 16px rgba(15, 23, 42, 0.04);
    }

    .um-summary-label {
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #64748b;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .um-summary-value {
        font-size: 1.35rem;
        font-weight: 900;
        color: #800000;
        line-height: 1;
    }

    .um-summary-note {
        margin: 4px 0 0;
        color: #64748b;
        font-size: .8rem;
    }

    .um-directory-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 0 20px 18px;
    }

    .um-directory-toggle .hint {
        color: #64748b;
        font-size: .92rem;
    }

    .um-directory-panel {
        display: none;
    }

    .um-directory-panel.is-open {
        display: block;
    }

    .um-card-head {
        padding: 18px 20px;
        border-bottom: 1px solid rgba(100, 116, 139, 0.12);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .um-search {
        display: flex;
        gap: 10px;
        align-items: center;
        flex: 1;
    }

    .um-search input {
        width: 100%;
        border-radius: 12px;
        border: 1px solid rgba(148, 163, 184, 0.45);
        padding: 12px 14px;
        font-size: 0.95rem;
        color: #111827;
        background: #fff;
    }

    .um-search input:focus {
        outline: none;
        border-color: #800000;
        box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.12);
    }

    .um-btn {
        border: none;
        border-radius: 12px;
        padding: 11px 16px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: transform .18s ease, box-shadow .18s ease, opacity .18s ease;
    }

    .um-btn:hover {
        transform: translateY(-1px);
    }

    .um-btn-primary {
        background: linear-gradient(135deg, #800000, #b11b1b);
        color: #fff;
        box-shadow: 0 12px 20px rgba(128, 0, 0, 0.24);
    }

    .um-btn-soft {
        background: #f8fafc;
        color: #111827;
        border: 1px solid rgba(148, 163, 184, 0.25);
    }

    .um-table-wrap {
        overflow-x: auto;
    }

    .um-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1080px;
    }

    .um-table thead th {
        text-align: left;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #64748b;
        padding: 14px 18px;
        background: rgba(248, 250, 252, 0.9);
        border-bottom: 1px solid rgba(148, 163, 184, 0.14);
    }

    .um-table tbody td {
        padding: 16px 18px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        vertical-align: middle;
        color: #0f172a;
    }

    .um-table thead th:last-child,
    .um-table tbody td:last-child {
        width: 190px;
        min-width: 190px;
        white-space: nowrap;
    }

    .um-user {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .um-user-card {
        cursor: pointer;
    }

    tr[data-user-card][data-can-edit="1"],
    tr[data-user-card][data-can-onboard="1"] {
        cursor: pointer;
    }

    tr[data-user-card][data-can-edit="1"]:hover .um-user,
    tr[data-user-card][data-can-onboard="1"]:hover .um-user {
        background: rgba(128, 0, 0, 0.04);
        border-radius: 12px;
    }

    .um-avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        overflow: hidden;
        flex: 0 0 46px;
        background: linear-gradient(135deg, #800000, #d97706);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        letter-spacing: .04em;
        box-shadow: 0 8px 14px rgba(128, 0, 0, 0.18);
    }

    .um-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .um-name {
        font-weight: 800;
        color: #111827;
        line-height: 1.2;
    }

    .um-sub {
        font-size: .82rem;
        color: #64748b;
        margin-top: 2px;
    }

    .um-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 11px;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 700;
    }

    .um-badge.active {
        background: rgba(34, 197, 94, 0.12);
        color: #166534;
    }

    .um-badge.inactive {
        background: rgba(239, 68, 68, 0.12);
        color: #991b1b;
    }

    .um-badge.source {
        background: rgba(128, 0, 0, 0.10);
        color: #800000;
    }

    .um-cursor-hint {
        position: fixed;
        z-index: 9999;
        display: none;
        pointer-events: none;
        background: rgba(17, 24, 39, 0.92);
        color: #fff;
        border-radius: 999px;
        padding: 8px 12px;
        font-size: .78rem;
        font-weight: 800;
        letter-spacing: .03em;
        box-shadow: 0 10px 18px rgba(15, 23, 42, 0.18);
        white-space: nowrap;
    }

    .um-action-btn {
        border: 1px solid rgba(128, 0, 0, 0.18);
        background: #fff;
        color: #800000;
        border-radius: 10px;
        padding: 9px 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .um-action-btn:hover {
        background: rgba(128, 0, 0, 0.06);
    }

    .um-empty {
        padding: 56px 18px;
        text-align: center;
        color: #64748b;
    }

    .um-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(10px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 5000;
        padding: 18px;
    }

    .um-modal-backdrop.show {
        display: flex;
    }

    .um-modal-content {
        width: min(1200px, 100%);
        max-height: 92vh;
        overflow: auto;
        background: #fff;
        border-radius: 22px;
        border: 1px solid rgba(148, 163, 184, 0.18);
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.28);
    }

    #lookupModal .um-modal-content {
        width: min(920px, 100%);
        max-height: 88vh;
        border-radius: 18px;
    }

    #settingsModal .um-modal-content {
        width: min(980px, 100%);
        max-height: 88vh;
        border-radius: 18px;
    }

    .um-modal-head {
        padding: 18px 20px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.14);
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    #lookupModal .um-modal-head {
        padding: 14px 16px;
    }

    #settingsModal .um-modal-head {
        padding: 14px 16px;
    }

    .um-modal-head h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 800;
        color: #111827;
    }

    .um-modal-body {
        padding: 18px 20px 22px;
    }

    #lookupModal .um-modal-body {
        padding: 14px 16px 18px;
    }

    #settingsModal .um-modal-body {
        padding: 14px 16px 18px;
    }

    .um-modal-grid {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 20px;
    }

    #lookupModal .um-modal-grid {
        grid-template-columns: 240px 1fr;
        gap: 14px;
    }

    #settingsModal .um-modal-grid {
        grid-template-columns: 240px 1fr;
        gap: 14px;
    }

    #settingsModal .um-detail-card {
        padding: 14px;
        border-radius: 16px;
    }

    #settingsModal .um-detail-photo {
        width: 84px;
        height: 84px;
        font-size: 1.65rem;
        margin-bottom: 12px;
        border-radius: 16px;
    }

    #settingsModal .um-field {
        margin-bottom: 12px;
    }

    #lookupModal .um-table {
        min-width: 760px;
    }

    #lookupModal .um-table thead th,
    #lookupModal .um-table tbody td {
        padding: 12px 14px;
    }

    #lookupModal .um-table thead th:last-child,
    #lookupModal .um-table tbody td:last-child {
        width: 200px;
        min-width: 200px;
    }

    #lookupModal .um-search input {
        padding: 10px 12px;
    }

    .um-detail-card {
        background: rgba(248, 250, 252, 0.85);
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 18px;
        padding: 18px;
    }

    .um-section-block {
        padding: 18px;
        border-radius: 18px;
        border: 1px solid rgba(148, 163, 184, 0.16);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 250, 252, 0.9));
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.05);
    }

    .um-section-block + .um-section-block {
        margin-top: 18px;
    }

    .um-section-block.account-access {
        border-color: rgba(128, 0, 0, 0.14);
    }

    .um-section-block.admin-hub {
        border-color: rgba(30, 64, 175, 0.16);
        background: linear-gradient(180deg, rgba(239, 246, 255, 0.92), rgba(248, 250, 252, 0.95));
    }

    .um-section-block.is-hidden {
        display: none;
    }

    #settingsModal.admin-hub-mode #accountAccessSection {
        display: none !important;
    }

    #settingsModal.account-access-mode #accountAccessSection {
        display: block !important;
    }

    .um-section-title {
        margin: 0 0 4px;
        font-size: 1rem;
        font-weight: 800;
        color: #800000;
    }

    .um-section-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(128, 0, 0, 0.08);
        color: #800000;
        font-size: .76rem;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .um-section-block.admin-hub .um-section-kicker {
        background: rgba(30, 64, 175, 0.10);
        color: #1d4ed8;
    }

    .um-section-copy {
        margin: 0 0 14px;
        color: #64748b;
        font-size: .9rem;
        line-height: 1.55;
    }

    .um-profile-list {
        display: grid;
        gap: 12px;
    }

    .um-profile-row {
        display: grid;
        grid-template-columns: 150px 1fr;
        gap: 12px;
        align-items: center;
        padding: 12px 14px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(148, 163, 184, 0.14);
    }

    .um-profile-row .label {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 800;
        color: #64748b;
    }

    .um-profile-row .value {
        font-weight: 700;
        color: #0f172a;
        word-break: break-word;
    }

    .um-detail-photo {
        width: 100px;
        height: 100px;
        border-radius: 18px;
        overflow: hidden;
        background: linear-gradient(135deg, #800000, #d97706);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 14px;
    }

    .um-detail-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .um-field {
        margin-bottom: 14px;
    }

    .um-field label {
        display: block;
        font-size: .8rem;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .um-field input,
    .um-field select,
    .um-field textarea {
        width: 100%;
        border: 1px solid rgba(148, 163, 184, 0.45);
        border-radius: 12px;
        padding: 11px 12px;
        color: #111827;
        background: #fff;
    }

    .um-field input[readonly],
    .um-field textarea[readonly] {
        background: #f8fafc;
        color: #475569;
    }

    .um-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
        margin-top: 16px;
    }

    .um-note {
        color: #64748b;
        font-size: .92rem;
        line-height: 1.55;
    }

    html[data-theme="dark"] .um-section-copy,
    html[data-theme="dark"] .um-profile-row .label {
        color: #cbd5e1;
    }

    html[data-theme="dark"] .um-section-block {
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(17, 24, 39, 0.94));
        border-color: rgba(148, 163, 184, 0.14);
        box-shadow: 0 18px 32px rgba(0, 0, 0, 0.22);
    }

    html[data-theme="dark"] .um-section-block.admin-hub {
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(17, 24, 39, 0.94));
        border-color: rgba(59, 130, 246, 0.24);
    }

    html[data-theme="dark"] .um-profile-row {
        background: rgba(15, 23, 42, 0.84);
        border-color: rgba(148, 163, 184, 0.14);
    }

    html[data-theme="dark"] .um-profile-row .value,
    html[data-theme="dark"] .um-section-title {
        color: #fff;
    }

    html[data-theme="dark"] .um-section-kicker {
        background: rgba(248, 113, 113, 0.16);
        color: #fecaca;
    }

    html[data-theme="dark"] .um-section-block.admin-hub .um-section-kicker {
        background: rgba(96, 165, 250, 0.16);
        color: #bfdbfe;
    }

    @media (max-width: 1024px) {
        .um-grid,
        .um-modal-grid {
            grid-template-columns: 1fr;
        }

        .um-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .um-summary-card {
            min-width: 200px;
            flex-basis: 200px;
        }
    }

    @media (max-width: 768px) {
        .user-management-shell {
            padding: 14px 14px 30px;
        }

        .um-card-head {
            flex-direction: column;
            align-items: stretch;
        }

        .um-directory-toggle {
            align-items: flex-start;
            flex-direction: column;
        }

        .um-summary-card {
            min-width: 180px;
            flex-basis: 180px;
        }
    }
</style>
@endpush

@section('content')
<div class="user-management-shell">
    <div class="um-hero">
        <div>
            <h1>Users Management</h1>
            <p>Search and manage roles of accounts from one place.</p>
        </div>
    </div>

    <div class="um-mode-picker">
        <button type="button" class="um-mode-btn" data-mode-button="account-access">
            <span class="um-mode-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M19 8v6"/>
                    <path d="M22 11h-6"/>
                </svg>
            </span>
            <h3>Account Access</h3>
            <p>Open the clinic login view for managed users. This is where we control the student email, clinic role, and active or inactive access.</p>
        </button>
        <button type="button" class="um-mode-btn" data-mode-button="admin-hub">
            <span class="um-mode-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z"/>
                    <path d="M9.5 12l1.7 1.7 3.3-3.4"/>
                </svg>
            </span>
            <h3>Admin Hub Profile</h3>
            <p>Open the clinic-only admin hub view. This is where we handle admin login email, admin type, office, and shared admin profile context.</p>
        </button>
    </div>

    <div class="um-mode-panel" id="account-access-panel">
        <div class="um-card">
            <div class="um-panel-header">
                <div>
                    <h2>Account Access</h2>
                    <p>Managed users already added in the clinic CMS are listed here.</p>
                </div>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <button type="button" class="um-btn um-btn-ghost" data-back-to-modes>Back</button>
                    <button type="button" class="um-btn um-btn-primary" data-open-lookup="account-access">
                        <span>+</span> Add User Roles
                    </button>
                </div>
            </div>
            <div class="um-directory-panel is-open" id="directoryPanel">
            <div class="um-table-wrap">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Source</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($localRecords as $record)
                            <tr
                                data-user-card
                                data-update-url="{{ $record['can_edit'] ? route('admin.user-management.update', $record['id']) : '' }}"
                                data-create-url="{{ !$record['can_edit'] && !empty($record['can_onboard']) ? route('admin.user-management.store-from-lookup') : '' }}"
                                data-delete-url="{{ $record['can_edit'] ? route('admin.user-management.destroy', $record['id']) : '' }}"
                                data-can-edit="{{ $record['can_edit'] ? '1' : '0' }}"
                                data-can-onboard="{{ !empty($record['can_onboard']) ? '1' : '0' }}"
                            data-id="{{ $record['record_id'] }}"
                            data-name="{{ $record['name'] }}"
                            data-first-name="{{ $record['first_name'] }}"
                            data-last-name="{{ $record['last_name'] }}"
                            data-email="{{ $record['email'] }}"
                                data-role="{{ $record['raw_role'] }}"
                                data-role-label="{{ $record['role'] }}"
                                data-status="{{ $record['status'] }}"
                                data-source="{{ $record['source'] }}"
                                data-source-label="{{ $record['source_label'] }}"
                                data-student-id="{{ $record['student_id'] }}"
                            data-avatar-url="{{ $record['avatar_url'] ?? '' }}"
                            data-avatar-letter="{{ $record['avatar_letter'] }}"
                            data-updated="{{ $record['meta']['updated_at'] ?? '' }}"
                            data-management-view="account-access"
                            data-meta='@json($record["meta"])'
                        >
                                <td>
                                    <div class="um-user">
                                        <div class="um-avatar">
                                            @if(!empty($record['avatar_url']))
                                                <img src="{{ $record['avatar_url'] }}" alt="{{ $record['name'] }}">
                                            @else
                                                {{ $record['avatar_letter'] }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="um-name">{{ $record['name'] }}</div>
                                            <div class="um-sub">{{ $record['student_id'] ?: 'ID not available' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $record['email'] ?: 'N/A' }}</td>
                                <td>{{ $record['role'] }}</td>
                                <td>
                                    <span class="um-badge {{ $record['status'] === 'inactive' ? 'inactive' : 'active' }}">
                                        {{ ucfirst($record['status']) }}
                                    </span>
                                </td>
                                <td><span class="um-badge source">{{ $record['source_label'] }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"><div class="um-empty">No managed clinic users found yet.</div></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>

    <div class="um-mode-panel" id="admin-hub-panel">
        <div class="um-card">
            <div class="um-panel-header">
                <div>
                    <h2>Admin Hub Profile</h2>
                    <p>This view only shows admin-designee records stored in the clinic `admins` table.</p>
                </div>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <button type="button" class="um-btn um-btn-ghost" data-back-to-modes>Back</button>
                    <button type="button" class="um-btn um-btn-primary" data-open-lookup="admin-hub">
                        <span>+</span> Add User Roles
                    </button>
                </div>
            </div>
            <div class="um-directory-panel is-open">
            <div class="um-table-wrap">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Admin Type</th>
                            <th>Office</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($adminHubRecords as $record)
                            <tr
                                data-user-card
                                data-update-url="{{ $record['update_url'] ?? ($record['can_edit'] ? route('admin.user-management.update', $record['id']) : '') }}"
                                data-delete-url="{{ $record['delete_url'] ?? ($record['can_edit'] ? route('admin.user-management.destroy', $record['id']) : '') }}"
                                data-delete-admin-hub-url="{{ $record['delete_admin_hub_url'] ?? '' }}"
                                data-can-edit="{{ $record['can_edit'] ? '1' : '0' }}"
                                data-id="{{ $record['record_id'] }}"
                                data-name="{{ $record['name'] }}"
                                data-first-name="{{ $record['first_name'] }}"
                                data-last-name="{{ $record['last_name'] }}"
                                data-email="{{ $record['email'] }}"
                                data-role="{{ $record['raw_role'] }}"
                                data-role-label="{{ $record['role'] }}"
                                data-status="{{ $record['status'] }}"
                                data-source="{{ $record['source'] }}"
                                data-source-label="{{ $record['source_label'] }}"
                                data-student-id="{{ $record['student_id'] }}"
                            data-avatar-url="{{ $record['avatar_url'] ?? '' }}"
                            data-avatar-letter="{{ $record['avatar_letter'] }}"
                            data-updated="{{ $record['meta']['updated_at'] ?? '' }}"
                            data-management-view="admin-hub"
                            data-meta='@json($record["meta"])'
                        >
                                <td>
                                    <div class="um-user">
                                        <div class="um-avatar">
                                            @if(!empty($record['avatar_url']))
                                                <img src="{{ $record['avatar_url'] }}" alt="{{ $record['name'] }}">
                                            @else
                                                {{ $record['avatar_letter'] }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="um-name">{{ $record['name'] }}</div>
                                            <div class="um-sub">{{ $record['student_id'] ? 'ID: ' . $record['student_id'] : 'No ID yet' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $record['meta']['admin_login_email'] ?: 'Not assigned yet' }}</td>
                                <td>{{ $record['meta']['access_level'] ?: 'Not assigned yet' }}</td>
                                <td>{{ $record['meta']['office'] ?: 'Not assigned yet' }}</td>
                                <td>
                                    <span class="um-badge {{ $record['status'] === 'inactive' ? 'inactive' : 'active' }}">
                                        {{ ucfirst($record['status']) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"><div class="um-empty">No admin-hub-linked users are available yet.</div></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
</div>

<div class="um-modal-backdrop {{ $lookupSearch !== '' ? 'show' : '' }}" id="lookupModal">
    <div class="um-modal-content">
        <div class="um-modal-head">
            <div>
                <h3>Add User Roles</h3>
                <div class="um-note">Search across students, faculty, or admin profiles to add roles.</div>
            </div>
            <button type="button" class="um-btn um-btn-soft" data-close-lookup>Close</button>
        </div>
        <div class="um-modal-body">
            <form class="um-search" method="GET" action="{{ route('admin.user-management') }}">
                <input type="hidden" name="management_view" value="{{ $managementView ?: 'account-access' }}" id="lookupManagementViewField">
                <input type="search" name="lookup_search" value="{{ $lookupSearch }}" placeholder="Search users by email, name, or ID" id="lookupSearchField">
                <button class="um-btn um-btn-primary" type="submit">Search</button>
            </form>
            <div class="um-directory-toggle" style="padding: 14px 0 10px;">
                <div class="hint">Type a search term to show matching users below, or find users from the list manually.</div>
            </div>
            <div style="margin-top: 16px;" class="um-directory-panel {{ $lookupSearch !== '' ? 'is-open' : '' }}" id="lookupDirectoryPanel">
            <div class="um-table-wrap">
                <table class="um-table" style="min-width: 900px;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Source</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lookupRecords as $record)
                            <tr
                                data-user-card
                                data-update-url="{{ $record['can_edit'] ? route('admin.user-management.update', $record['id']) : '' }}"
                                data-delete-url="{{ $record['can_edit'] ? route('admin.user-management.destroy', $record['id']) : '' }}"
                                data-create-url="{{ !$record['can_edit'] && !empty($record['can_onboard']) ? route('admin.user-management.store-from-lookup') : '' }}"
                                data-can-edit="{{ $record['can_edit'] ? '1' : '0' }}"
                                data-can-onboard="{{ !empty($record['can_onboard']) ? '1' : '0' }}"
                                data-id="{{ $record['record_id'] }}"
                                data-name="{{ $record['name'] }}"
                                data-first-name="{{ $record['first_name'] }}"
                                data-last-name="{{ $record['last_name'] }}"
                                data-email="{{ $record['email'] }}"
                                data-role="{{ $record['raw_role'] }}"
                                data-role-label="{{ $record['role'] }}"
                                data-status="{{ $record['status'] }}"
                                data-source="{{ $record['source'] }}"
                                data-source-label="{{ $record['source_label'] }}"
                                data-student-id="{{ $record['student_id'] }}"
                                data-avatar-url="{{ $record['avatar_url'] ?? '' }}"
                                data-avatar-letter="{{ $record['avatar_letter'] }}"
                                data-updated="{{ $record['meta']['updated_at'] ?? '' }}"
                                data-meta='@json($record["meta"])'
                            >
                                <td>
                                    <div class="um-user">
                                        <div class="um-avatar">
                                            @if(!empty($record['avatar_url']))
                                                <img src="{{ $record['avatar_url'] }}" alt="{{ $record['name'] }}">
                                            @else
                                                {{ $record['avatar_letter'] }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="um-name">{{ $record['name'] }}</div>
                                            <div class="um-sub">{{ $record['student_id'] ?: 'ID not available' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $record['email'] ?: 'N/A' }}</td>
                                <td>{{ $record['role'] }}</td>
                                <td><span class="um-badge {{ $record['status'] === 'inactive' ? 'inactive' : 'active' }}">{{ ucfirst($record['status']) }}</span></td>
                                <td><span class="um-badge source">{{ $record['source_label'] }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"><div class="um-empty">No Users matched the current search.</div></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
</div>

<div class="um-modal-backdrop" id="settingsModal">
    <div class="um-modal-content">
        <div class="um-modal-head">
            <div>
                <h3>User Settings</h3>
                <div class="um-note">Review the account, adjust the role or status, deactivate if needed, or delete the account.</div>
            </div>
            <button type="button" class="um-btn um-btn-soft" data-close-settings>Close</button>
        </div>
        <div class="um-modal-body">
            <div class="um-modal-grid">
                <div class="um-detail-card">
                    <div class="um-detail-photo" id="detailAvatar">U</div>
                    <div class="um-field">
                        <label>Name</label>
                        <input type="text" id="detailName" readonly>
                    </div>
                    <div class="um-field">
                        <label>Email</label>
                        <input type="text" id="detailEmail" readonly>
                    </div>
                    <div class="um-field">
                        <label id="detailIdentifierLabel">Student / Faculty ID</label>
                        <input type="text" id="detailIdentifier" readonly>
                    </div>
                    <div class="um-field">
                        <label>Source</label>
                        <input type="text" id="detailSource" readonly>
                    </div>
                    <div class="um-field">
                        <label>Last Updated</label>
                        <input type="text" id="detailUpdated" readonly>
                    </div>
                </div>
                <div class="um-detail-card">
                    <form method="POST" id="settingsForm">
                        @csrf
                        <input type="hidden" name="_method" id="settingsMethod" value="PUT">
                        <input type="hidden" name="management_view" id="detailManagementView" value="account-access">
                        <input type="hidden" name="lookup_source" id="detailLookupSource" value="">
                        <input type="hidden" name="first_name" id="detailFirstName" value="">
                        <input type="hidden" name="last_name" id="detailLastName" value="">
                        <input type="hidden" name="full_name" id="detailFullName" value="">
                        <input type="hidden" name="external_identifier" id="detailExternalIdentifier" value="">
                        <div class="um-section-block account-access" id="accountAccessSection">
                            <div class="um-section-kicker">Users Table</div>
                            <h4 class="um-section-title">Account Access</h4>
                            <p class="um-section-copy">This controls the clinic login role, the student-side email, and whether the account can enter the clinic system.</p>
                            <div class="um-field">
                                <label>Clinic Role</label>
                                <select name="user_role" id="detailRole">
                                    <option value="student">Student</option>
                                    <option value="student_assistant">Student Assistant</option>
                                    <option value="admin">Admin</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                            </div>
                            <div class="um-field">
                                <label id="detailEmailLabel">Student Email</label>
                                <input type="email" name="email" id="detailEditEmail" placeholder="Enter Gmail account">
                                <div class="um-note" id="emailRoleNote" style="margin-top: 6px;">
                                    Keep this email for the student side.
                                </div>
                            </div>
                            <div class="um-field">
                                <label>Status</label>
                                <select name="status" id="detailStatus">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="um-section-block admin-hub" id="adminHubSection">
                            <div class="um-section-kicker">Admins Table</div>
                            <h4 class="um-section-title">Admin Hub Profile</h4>
                            <p class="um-section-copy">This is clinic-only data for admin-side access. It can stay separate from the student login profile while still being managed here.</p>
                            <div class="um-profile-list">
                                <div class="um-profile-row">
                                    <div class="label">Hub Record</div>
                                    <div class="value" id="detailAdminProfileStatus">No linked admin hub record yet</div>
                                </div>
                            </div>
                            <div class="um-field" id="accessLevelWrap" style="display:none; margin-top: 14px;">
                                <label id="detailAccessLevelLabel">Admin Type</label>
                                <select name="access_level" id="detailAccessLevel">
                                    <option value="clinic_staff">Clinic Staff</option>
                                    <option value="designee">Designee</option>
                                </select>
                            </div>
                            <div class="um-field" id="adminEmailWrap" style="display:none;">
                                <label id="detailAdminEmailLabel">Admin Login Email</label>
                                <input type="email" name="admin_email" id="detailAdminEmail" placeholder="Enter separate admin login email">
                                <div class="um-note" id="adminEmailNote" style="margin-top: 6px;">
                                    This email is used for the admin side only.
                                </div>
                            </div>
                            <div class="um-field" id="adminOfficeWrap" style="display:none;">
                                <label>Office</label>
                                <input type="text" name="office" id="detailOffice" placeholder="Office or Department">
                            </div>
                        </div>
                        <div class="um-note" id="externalNote" style="display:none; margin-top: 6px;">
                            This faculty profile comes from the external source. Saving here will add a clinic-side user and admin hub record without changing the source system.
                        </div>
                        <div class="um-actions">
                            <button type="button" class="um-btn um-btn-soft" id="deactivateBtn">Deactivate Account</button>
                            <button
                                type="submit"
                                form="deleteForm"
                                class="um-btn"
                                style="background:#fef3c7;color:#92400e;border:1px solid #fcd34d;"
                                onclick="return confirm('Remove this account access and return it to the default student role?')"
                            >
                                Remove Access
                            </button>
                            <button
                                type="submit"
                                form="deleteAdminHubForm"
                                class="um-btn"
                                id="deleteAdminHubBtn"
                                style="display:none; background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;"
                                onclick="return confirm('Delete this admin hub record from the admins table? This cannot be undone.')"
                            >
                                Delete Admin Record
                            </button>
                            <button type="submit" class="um-btn um-btn-primary" id="saveSettingsBtn">Save Changes</button>
                        </div>
                    </form>

                    <form method="POST" id="deleteForm" style="margin-top: 10px;">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="management_view" id="deleteManagementView" value="account-access">
                        <input type="hidden" name="admin_profile_id" id="deleteAdminProfileId">
                    </form>

                    <form method="POST" id="deleteAdminHubForm" style="display:none;">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="management_view" id="deleteAdminHubManagementView" value="account-access">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="um-cursor-hint" id="userHoverHint">Click to enter</div>

@push('scripts')
<script>
    const lookupModal = document.getElementById('lookupModal');
    const settingsModal = document.getElementById('settingsModal');
    const settingsForm = document.getElementById('settingsForm');
    const settingsMethod = document.getElementById('settingsMethod');
    const deleteForm = document.getElementById('deleteForm');
    const detailAvatar = document.getElementById('detailAvatar');
    const detailName = document.getElementById('detailName');
    const detailEmail = document.getElementById('detailEmail');
    const detailIdentifierLabel = document.getElementById('detailIdentifierLabel');
    const detailEditEmail = document.getElementById('detailEditEmail');
    const detailEmailLabel = document.getElementById('detailEmailLabel');
    const emailRoleNote = document.getElementById('emailRoleNote');
    const accountAccessSection = document.getElementById('accountAccessSection');
    const accessLevelWrap = document.getElementById('accessLevelWrap');
    const detailAccessLevel = document.getElementById('detailAccessLevel');
    const detailAccessLevelLabel = document.getElementById('detailAccessLevelLabel');
    const detailIdentifier = document.getElementById('detailIdentifier');
    const detailSource = document.getElementById('detailSource');
    const detailUpdated = document.getElementById('detailUpdated');
    const detailRole = document.getElementById('detailRole');
    const detailStatus = document.getElementById('detailStatus');
    const detailManagementView = document.getElementById('detailManagementView');
    const detailLookupSource = document.getElementById('detailLookupSource');
    const detailFirstName = document.getElementById('detailFirstName');
    const detailLastName = document.getElementById('detailLastName');
    const detailFullName = document.getElementById('detailFullName');
    const detailExternalIdentifier = document.getElementById('detailExternalIdentifier');
    const adminEmailWrap = document.getElementById('adminEmailWrap');
    const detailAdminEmail = document.getElementById('detailAdminEmail');
    const detailOffice = document.getElementById('detailOffice');
    const adminHubSection = document.getElementById('adminHubSection');
    const adminOfficeWrap = document.getElementById('adminOfficeWrap');
    const detailAdminProfileStatus = document.getElementById('detailAdminProfileStatus');
    const adminEmailNote = document.getElementById('adminEmailNote');
    const deleteAdminProfileId = document.getElementById('deleteAdminProfileId');
    const deleteManagementView = document.getElementById('deleteManagementView');
    const deleteAdminHubManagementView = document.getElementById('deleteAdminHubManagementView');
    const deleteAdminHubForm = document.getElementById('deleteAdminHubForm');
    const deleteAdminHubBtn = document.getElementById('deleteAdminHubBtn');
    const externalNote = document.getElementById('externalNote');
    const deactivateBtn = document.getElementById('deactivateBtn');
    const saveSettingsBtn = document.getElementById('saveSettingsBtn');
    const directoryPanel = document.getElementById('directoryPanel');
    const lookupDirectoryPanel = document.getElementById('lookupDirectoryPanel');
    const lookupSearchField = document.getElementById('lookupSearchField');
    const lookupManagementViewField = document.getElementById('lookupManagementViewField');
    const userHoverHint = document.getElementById('userHoverHint');
    const modePicker = document.querySelector('.um-mode-picker');
    const modeButtons = document.querySelectorAll('[data-mode-button]');
    const backToModesButtons = document.querySelectorAll('[data-back-to-modes]');
    const modePanels = {
        'account-access': document.getElementById('account-access-panel'),
        'admin-hub': document.getElementById('admin-hub-panel'),
    };
    let currentLookupContext = @json($managementView ?: '');

    const syncModeUrl = (mode) => {
        const url = new URL(window.location.href);
        if (mode && ['account-access', 'admin-hub'].includes(mode)) {
            url.searchParams.set('management_view', mode);
        } else {
            url.searchParams.delete('management_view');
        }

        if (!lookupSearchField || !lookupSearchField.value.trim()) {
            url.searchParams.delete('lookup_search');
        }

        window.history.replaceState({}, '', url.toString());
    };

    const applySettingsSectionMode = (managementView, canEdit, canOnboard) => {
        const isAdminHubOnly = managementView === 'admin-hub';
        if (settingsModal) {
            settingsModal.classList.toggle('admin-hub-mode', isAdminHubOnly);
            settingsModal.classList.toggle('account-access-mode', !isAdminHubOnly);
        }

        if (accountAccessSection) {
            accountAccessSection.classList.toggle('is-hidden', isAdminHubOnly);
            accountAccessSection.style.display = isAdminHubOnly ? 'none' : '';
        }

        if (adminHubSection) {
            adminHubSection.classList.remove('is-hidden');
            adminHubSection.style.display = (canEdit || canOnboard) ? '' : 'none';
        }
    };

    const syncRoleUi = (options = {}) => {
        const canEdit = options.canEdit === true;
        const canOnboard = options.canOnboard === true;
        const managementView = detailManagementView ? detailManagementView.value : 'account-access';
        const isStudent = detailRole.value === 'student';
        const isStudentAssistant = detailRole.value === 'student_assistant';
        const isAdmin = detailRole.value === 'admin';
        const isSuperAdmin = detailRole.value === 'super_admin';
        const hasAdminHub = isAdmin || isSuperAdmin;
        const usesSeparateAdminEmail = managementView !== 'admin-hub' && isStudentAssistant;

        applySettingsSectionMode(managementView, canEdit, canOnboard);

        if (isStudent) {
            detailEmailLabel.textContent = 'Student Email';
            emailRoleNote.textContent = 'This email stays with the student account.';
            accessLevelWrap.style.display = 'none';
            detailAccessLevel.disabled = true;
            adminEmailWrap.style.display = 'none';
            adminOfficeWrap.style.display = 'none';
        } else {
            detailEmailLabel.textContent = 'Student Email';
            emailRoleNote.textContent = 'Keep this email for the student side.';
            accessLevelWrap.style.display = isAdmin ? 'block' : 'none';
            detailAccessLevel.disabled = !isAdmin || !(canEdit || canOnboard);
            adminEmailWrap.style.display = usesSeparateAdminEmail ? 'block' : 'none';
            adminOfficeWrap.style.display = hasAdminHub ? 'block' : 'none';
            detailAccessLevelLabel.textContent = 'Admin Type';
        }

        if (adminEmailNote) {
            adminEmailNote.textContent = 'Use a separate login email only for Student Assistant accounts.';
        }

        if (detailAdminProfileStatus && !hasAdminHub) {
            detailAdminProfileStatus.textContent = 'Not needed while this account stays on the student side only.';
        }
    };

    const setUserManagementMode = (mode) => {
        currentLookupContext = mode || 'account-access';
        if (lookupManagementViewField) {
            lookupManagementViewField.value = currentLookupContext;
        }
        syncModeUrl(currentLookupContext);

        if (modePicker) {
            modePicker.classList.add('is-hidden');
        }

        modeButtons.forEach((button) => {
            button.classList.toggle('active', button.dataset.modeButton === mode);
        });

        Object.entries(modePanels).forEach(([panelMode, panel]) => {
            if (!panel) {
                return;
            }

            panel.classList.toggle('is-active', panelMode === mode);
        });
    };

    const resetUserManagementModes = () => {
        currentLookupContext = 'account-access';
        if (lookupManagementViewField) {
            lookupManagementViewField.value = currentLookupContext;
        }
        syncModeUrl(null);

        if (modePicker) {
            modePicker.classList.remove('is-hidden');
        }

        modeButtons.forEach((button) => {
            button.classList.remove('active');
        });

        Object.values(modePanels).forEach((panel) => {
            if (!panel) {
                return;
            }

            panel.classList.remove('is-active');
        });
    };

    modeButtons.forEach((button) => {
        button.addEventListener('click', () => setUserManagementMode(button.dataset.modeButton));
    });

    backToModesButtons.forEach((button) => {
        button.addEventListener('click', resetUserManagementModes);
    });

    const openSettingsFromRow = (row) => {
        if (!row) {
            return;
        }

        const canEdit = row.dataset.canEdit === '1';
        const canOnboard = row.dataset.canOnboard === '1';
        const avatarUrl = row.dataset.avatarUrl || '';
        const avatarLetter = row.dataset.avatarLetter || 'U';
        const managementView = row.dataset.managementView || currentLookupContext || 'account-access';
        if (detailManagementView) {
            detailManagementView.value = managementView;
        }
        if (deleteManagementView) {
            deleteManagementView.value = managementView;
        }
        if (deleteAdminHubManagementView) {
            deleteAdminHubManagementView.value = managementView;
        }

        detailName.value = row.dataset.name || '';
        detailEmail.value = row.dataset.email || '';
        detailIdentifier.value = row.dataset.studentId || row.dataset.id || '';
        if (detailIdentifierLabel) {
            detailIdentifierLabel.textContent = managementView === 'admin-hub' ? 'Faculty / External ID' : 'Student / Faculty ID';
        }
        detailSource.value = row.dataset.sourceLabel || row.dataset.source || '';
        detailUpdated.value = row.dataset.updated || 'N/A';
        const normalizedRole = (() => {
            const raw = (row.dataset.role || 'student').toLowerCase();
            if (raw === 'admin') {
                return 'admin';
            }
            if (raw === 'student_assistant' || raw === 'studentassistant' || raw === 'assistant') {
                return 'student_assistant';
            }
            if (raw === 'superadmin' || raw === 'super_admin') {
                return 'super_admin';
            }
            return 'student';
        })();
        detailRole.value = normalizedRole;
        detailStatus.value = row.dataset.status || 'active';
        const meta = (() => {
            try {
                return JSON.parse(row.dataset.meta || '{}') || {};
            } catch (error) {
                return {};
            }
        })();
        const accessLevel = (meta.access_level || '').toLowerCase();
        const adminLoginEmail = meta.admin_login_email || '';
        const office = meta.office || '';
        const adminProfileId = meta.admin_profile_id || '';
        const lookupSource = meta.lookup_source || '';
        if (deleteAdminProfileId) {
            deleteAdminProfileId.value = adminProfileId;
        }
        detailAccessLevel.value = ['clinic_staff', 'designee'].includes(accessLevel) ? accessLevel : 'clinic_staff';
        applySettingsSectionMode(managementView, canEdit, canOnboard);

        detailEditEmail.value = row.dataset.email || '';
        detailEmailLabel.textContent = 'Student Email';
        emailRoleNote.textContent = normalizedRole === 'student'
            ? 'This email stays with the student account.'
            : 'Keep this email for the student side.';
        if (detailAdminEmail) {
            detailAdminEmail.value = adminLoginEmail;
        }
        if (detailOffice) {
            detailOffice.value = office;
        }
        if (detailLookupSource) {
            detailLookupSource.value = lookupSource;
        }
        if (detailFirstName) {
            detailFirstName.value = row.dataset.firstName || '';
        }
        if (detailLastName) {
            detailLastName.value = row.dataset.lastName || '';
        }
        if (detailFullName) {
            detailFullName.value = row.dataset.name || '';
        }
        if (detailExternalIdentifier) {
            detailExternalIdentifier.value = row.dataset.studentId || row.dataset.id || '';
        }
        if (detailAdminProfileStatus) {
            detailAdminProfileStatus.textContent = adminProfileId
                ? `Linked to admin hub record #${adminProfileId}${meta.admin_profile_name ? ` | ${meta.admin_profile_name}` : ''}`
                : (managementView === 'admin-hub'
                    ? 'No linked admin hub record yet. Saving here will create a designee-only admin hub record.'
                    : 'No linked admin hub record yet. One will be created when you save an admin-side role.');
        }
        if (avatarUrl) {
            detailAvatar.innerHTML = `<img src="${avatarUrl}" alt="">`;
        } else {
            detailAvatar.textContent = avatarLetter;
        }

        settingsForm.action = canEdit ? (row.dataset.updateUrl || '#') : (row.dataset.createUrl || '#');
        if (settingsMethod) {
            settingsMethod.value = canEdit ? 'PUT' : 'POST';
        }
        deleteForm.action = row.dataset.deleteUrl || '#';
        if (deleteAdminHubForm) {
            deleteAdminHubForm.action = row.dataset.deleteAdminHubUrl || '#';
        }

        settingsForm.querySelectorAll('input, select, button').forEach((field) => {
            if (field.id === 'deactivateBtn') {
                return;
            }
            if (field.id === 'settingsMethod' || field.id === 'detailLookupSource' || field.id === 'detailFirstName' || field.id === 'detailLastName' || field.id === 'detailFullName' || field.id === 'detailExternalIdentifier') {
                field.disabled = false;
                return;
            }
            field.disabled = !(canEdit || canOnboard);
        });
        deactivateBtn.disabled = !canEdit;
        deactivateBtn.style.display = canEdit ? '' : 'none';
        externalNote.style.display = canOnboard ? 'block' : 'none';
        detailEditEmail.readOnly = !(canEdit || canOnboard);

        deleteForm.style.display = canEdit ? 'block' : 'none';
        if (deleteAdminHubBtn) {
            const showDeleteAdminHub = managementView === 'admin-hub' && canEdit && adminProfileId;
            deleteAdminHubBtn.style.display = showDeleteAdminHub ? '' : 'none';
            deleteAdminHubBtn.disabled = !showDeleteAdminHub;
        }
        if (deleteAdminHubForm) {
            deleteAdminHubForm.style.display = 'none';
        }
        if (!canEdit && canOnboard) {
            detailRole.value = 'admin';
            detailAccessLevel.value = 'designee';
            detailStatus.value = row.dataset.status || 'active';
        }
        syncRoleUi({ canEdit, canOnboard });
        if (saveSettingsBtn) {
            saveSettingsBtn.textContent = canEdit ? 'Save Changes' : 'Add to Clinic';
        }
        settingsModal.classList.add('show');
    };

    if (currentLookupContext && ['account-access', 'admin-hub'].includes(currentLookupContext)) {
        setUserManagementMode(currentLookupContext);
    }

    if (lookupModal && lookupSearchField && lookupSearchField.value.trim() !== '') {
        lookupModal.classList.add('show');
    }

    document.querySelectorAll('[data-open-lookup]').forEach((button) => {
        button.addEventListener('click', () => {
            currentLookupContext = button.dataset.openLookup || 'account-access';
            if (lookupManagementViewField) {
                lookupManagementViewField.value = currentLookupContext;
            }
            setUserManagementMode(currentLookupContext);
            lookupModal.classList.add('show');
        });
    });

    document.querySelectorAll('[data-close-lookup]').forEach((button) => {
        button.addEventListener('click', () => lookupModal.classList.remove('show'));
    });

    document.querySelectorAll('[data-close-settings]').forEach((button) => {
        button.addEventListener('click', () => settingsModal.classList.remove('show'));
    });

    document.querySelectorAll('tr[data-user-card]').forEach((row) => {
        if (row.dataset.canEdit !== '1' && row.dataset.canOnboard !== '1') {
            return;
        }

        const moveHint = (event) => {
            if (!userHoverHint) {
                return;
            }
            userHoverHint.style.display = 'block';
            const offsetX = 18;
            const offsetY = 18;
            const maxX = window.innerWidth - userHoverHint.offsetWidth - 12;
            const maxY = window.innerHeight - userHoverHint.offsetHeight - 12;
            const left = Math.min(event.clientX + offsetX, maxX);
            const top = Math.min(event.clientY + offsetY, maxY);
            userHoverHint.style.left = `${Math.max(left, 12)}px`;
            userHoverHint.style.top = `${Math.max(top, 12)}px`;
        };

        row.addEventListener('mouseenter', moveHint);
        row.addEventListener('mousemove', moveHint);
        row.addEventListener('mouseleave', () => {
            if (userHoverHint) {
                userHoverHint.style.display = 'none';
            }
        });

        row.addEventListener('click', () => openSettingsFromRow(row));
    });

    detailRole.addEventListener('change', () => {
        const canEdit = !deactivateBtn.disabled;
        const canOnboard = externalNote.style.display !== 'none';
        syncRoleUi({ canEdit, canOnboard });
    });

    deactivateBtn.addEventListener('click', () => {
        const confirmDeactivate = window.confirm('Deactivate this account? The user will lose access until reactivated.');
        if (!confirmDeactivate) {
            return;
        }
        detailStatus.value = 'inactive';
        settingsForm.submit();
    });

    document.getElementById('settingsModal').addEventListener('click', function (event) {
        if (event.target === this) {
            this.classList.remove('show');
        }
    });

    document.getElementById('lookupModal').addEventListener('click', function (event) {
        if (event.target === this) {
            this.classList.remove('show');
        }
    });
</script>
@endpush
@endsection
