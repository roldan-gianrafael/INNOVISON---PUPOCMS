@extends('layouts.admin')

@section('title', 'User Management')

@push('styles')
<style>
    .user-management-shell {
        max-width: 1180px;
        margin: 0 auto;
        padding: 20px 24px 40px;
        color: #0f172a;
    }

    .um-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 22px;
        padding: 16px 18px;
        border-radius: 0 0 20px 20px;
        border-bottom: 2px solid rgba(234, 215, 160, 0.9);
        background: linear-gradient(135deg, rgba(255, 253, 246, 0.76) 0%, rgba(255, 249, 231, 0.58) 42%, rgba(255, 255, 255, 0.82) 100%);
        box-shadow: 0 14px 26px rgba(112, 19, 27, 0.05);
    }

    .um-hero h1 {
        margin: 0;
        font-size: 1.55rem;
        font-weight: 800;
        color: #111827;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-bottom: 2px solid rgba(112, 19, 27, 0.72);
    }

    .um-hero h1 svg {
        width: 18px;
        height: 18px;
        flex: 0 0 auto;
    }

    .um-hero p {
        margin: 6px 0 0;
        color: #475569;
    }

    .um-entry-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(280px, 360px));
        justify-content: center;
        gap: 22px;
    }

    .um-entry-card {
        display: block;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        width: min(360px, 100%);
        min-height: 360px;
        padding: 28px 24px 30px;
        border-radius: 28px;
        border: 1px solid rgba(128, 0, 0, 0.14);
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 48%, #e5e7eb 100%);
        box-shadow:
            0 0 0 1px rgba(112, 19, 27, 0.06),
            0 24px 38px rgba(112, 19, 27, 0.10),
            0 52px 72px -38px rgba(15, 23, 42, 0.24);
        color: inherit;
        transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease, background .22s ease;
        justify-self: center;
    }

    .um-entry-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(255,255,255,0.92), rgba(255,255,255,0.28) 42%, rgba(255,255,255,0.10));
        pointer-events: none;
    }

    .um-entry-card::after {
        content: "";
        position: absolute;
        left: 10%;
        right: 10%;
        bottom: -24px;
        height: 38px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(148, 163, 184, 0.34) 0%, rgba(148, 163, 184, 0.14) 42%, transparent 78%);
        filter: blur(12px);
        pointer-events: none;
    }

    .um-entry-card:hover {
        transform: translateY(-4px);
        border-color: rgba(112, 19, 27, 0.26);
        box-shadow:
            0 0 0 1px rgba(112, 19, 27, 0.12),
            0 28px 44px rgba(112, 19, 27, 0.14),
            0 58px 78px -38px rgba(15, 23, 42, 0.28);
    }

    .um-entry-icon {
        width: 68px;
        height: 68px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 22px;
        margin: 18px 0 12px;
        color: #ffffff;
        background: linear-gradient(145deg, rgba(128, 0, 0, 0.96), rgba(112, 19, 27, 0.92));
        border: 1px solid rgba(112, 19, 27, 0.30);
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,0.18),
            0 18px 28px rgba(112, 19, 27, 0.22);
        position: relative;
        z-index: 1;
        animation: umEntryFloat 3.8s ease-in-out infinite;
    }

    .um-entry-icon::after {
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

    .um-entry-icon svg {
        width: 30px;
        height: 30px;
        display: block;
        stroke: currentColor;
    }

    .um-entry-card h2 {
        margin: 0 0 10px;
        font-size: 1.26rem;
        font-weight: 900;
        color: #111827;
        position: relative;
        z-index: 1;
    }

    .um-entry-card p {
        margin: 0;
        color: #475569;
        line-height: 1.65;
        position: relative;
        z-index: 1;
    }

    .um-entry-meta {
        margin-top: 18px;
        font-size: 0.92rem;
        font-weight: 700;
        color: #70131b;
        position: relative;
        z-index: 1;
    }

    html[data-theme="dark"] .user-management-shell {
        color: #e5eefb;
    }

    html[data-theme="dark"] .um-hero {
        background: linear-gradient(135deg, rgba(18, 24, 38, 0.96), rgba(10, 15, 28, 0.92));
        border-bottom-color: rgba(240, 209, 90, 0.82);
        box-shadow: 0 18px 34px rgba(0, 0, 0, 0.28);
    }

    html[data-theme="dark"] .um-hero h1 {
        color: #f8fafc;
        border-bottom-color: rgba(240, 209, 90, 0.82);
    }

    html[data-theme="dark"] .um-hero p,
    html[data-theme="dark"] .um-entry-card p {
        color: #cbd5e1;
    }

    html[data-theme="dark"] .um-entry-card {
        background: linear-gradient(145deg, #5f0012 0%, #7d0b17 45%, #5a0010 100%);
        border-color: rgba(148, 163, 184, 0.14);
        box-shadow:
            0 0 0 1px rgba(148, 163, 184, 0.08),
            0 24px 38px rgba(95, 0, 18, 0.34),
            0 52px 72px -38px rgba(0, 0, 0, 0.52);
    }

    html[data-theme="dark"] .um-entry-card::before {
        background: linear-gradient(180deg, rgba(193, 138, 16, 0.16), rgba(125, 11, 23, 0.06) 42%, rgba(95, 0, 18, 0.14));
    }

    html[data-theme="dark"] .um-entry-card::after {
        background: radial-gradient(circle, rgba(193, 138, 16, 0.34) 0%, rgba(95, 0, 18, 0.18) 42%, transparent 78%);
    }

    html[data-theme="dark"] .um-entry-card h2 {
        color: #f8fafc;
    }

    html[data-theme="dark"] .um-entry-card:hover {
        border-color: rgba(240, 209, 90, 0.24);
        background: linear-gradient(145deg, #6d0014 0%, #8a0d19 42%, #670012 100%);
        box-shadow:
            0 0 0 1px rgba(240, 209, 90, 0.10),
            0 26px 40px rgba(95, 0, 18, 0.38),
            0 56px 76px -38px rgba(193, 138, 16, 0.52);
    }

    html[data-theme="dark"] .um-entry-icon {
        background: linear-gradient(145deg, rgba(128, 0, 0, 0.92), rgba(95, 0, 18, 0.88));
        border-color: rgba(240, 209, 90, 0.22);
        color: #f8fafc;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,0.12),
            0 18px 28px rgba(95, 0, 18, 0.28);
    }

    html[data-theme="dark"] .um-entry-meta {
        color: #f0d15a;
    }

    @keyframes umEntryFloat {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-5px);
        }
    }

    @media (max-width: 900px) {
        .um-entry-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="user-management-shell">
    <div class="um-hero">
        <div>
            <h1><x-outline-icon name="users" />Users Management</h1>
            <p>Choose which user-management workspace you want to open.</p>
        </div>
    </div>

    <div class="um-entry-grid">
        <a href="{{ route('admin.user-management.account-access') }}" class="um-entry-card">
            <span class="um-entry-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M19 8v6"/>
                    <path d="M22 11h-6"/>
                </svg>
            </span>
            <h2>Account Access</h2>
            <p>Manage clinic login role, student-side email, and active or inactive access for users already inside the clinic system.</p>
            <div class="um-entry-meta">Open Account Access</div>
        </a>

        <a href="{{ route('admin.user-management.admin-hub') }}" class="um-entry-card">
            <span class="um-entry-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z"/>
                    <path d="M9.5 12l1.7 1.7 3.3-3.4"/>
                </svg>
            </span>
            <h2>Admin Hub Profile</h2>
            <p>Manage clinic-only admin hub records, including admin login email, admin type, office, and the same shared API-backed profile fields.</p>
            <div class="um-entry-meta">Open Admin Hub Profile</div>
        </a>
    </div>
</div>
@endsection
