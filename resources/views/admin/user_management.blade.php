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
        font-size: 1.85rem;
        font-weight: 800;
        color: #111827;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 18px;
        border-bottom: 2px solid rgba(112, 19, 27, 0.72);
    }

    .um-hero p {
        margin: 6px 0 0;
        color: #475569;
    }

    .um-entry-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .um-entry-card {
        display: block;
        text-decoration: none;
        padding: 24px 22px;
        border-radius: 24px;
        border: 1px solid rgba(128, 0, 0, 0.12);
        background: rgba(255, 255, 255, 0.96);
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.08);
        color: inherit;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }

    .um-entry-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 24px 38px rgba(112, 19, 27, 0.12);
        border-color: rgba(112, 19, 27, 0.24);
    }

    .um-entry-icon {
        width: 58px;
        height: 58px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        margin-bottom: 16px;
        color: #70131b;
        background: linear-gradient(135deg, rgba(255, 247, 230, 0.96), rgba(255, 255, 255, 0.98));
        box-shadow: inset 0 0 0 1px rgba(112, 19, 27, 0.08);
    }

    .um-entry-icon svg {
        width: 28px;
        height: 28px;
    }

    .um-entry-card h2 {
        margin: 0 0 10px;
        font-size: 1.3rem;
        color: #111827;
    }

    .um-entry-card p {
        margin: 0;
        color: #475569;
        line-height: 1.65;
    }

    .um-entry-meta {
        margin-top: 18px;
        font-size: 0.92rem;
        font-weight: 700;
        color: #70131b;
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
        background: rgba(12, 18, 32, 0.96);
        border-color: rgba(148, 163, 184, 0.14);
        box-shadow: 0 18px 32px rgba(0, 0, 0, 0.28);
    }

    html[data-theme="dark"] .um-entry-card h2 {
        color: #f8fafc;
    }

    html[data-theme="dark"] .um-entry-icon {
        color: #f0d15a;
        background: linear-gradient(135deg, rgba(48, 31, 12, 0.84), rgba(20, 24, 38, 0.94));
        box-shadow: inset 0 0 0 1px rgba(240, 209, 90, 0.18);
    }

    html[data-theme="dark"] .um-entry-meta {
        color: #f0d15a;
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
