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
        background: rgba(15, 23, 42, 0.94);
        color: #e2e8f0;
        border-color: rgba(148, 163, 184, 0.15);
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

    .um-recent-wrap {
        padding: 0 20px 18px;
    }

    .um-recent-grid {
        display: flex;
        gap: 12px;
        margin-top: 12px;
        overflow-x: auto;
        padding-bottom: 4px;
        scrollbar-width: thin;
    }

    .um-recent-card {
        min-width: 240px;
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 16px;
        padding: 14px;
        background: rgba(248, 250, 252, 0.95);
        flex: 0 0 240px;
    }

    .um-recent-top {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .um-recent-name {
        font-weight: 800;
        color: #111827;
        line-height: 1.2;
    }

    .um-recent-meta {
        margin-top: 8px;
        display: flex;
        justify-content: space-between;
        gap: 8px;
        flex-wrap: wrap;
        color: #64748b;
        font-size: .85rem;
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

    .um-user {
        display: flex;
        align-items: center;
        gap: 12px;
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

    .um-modal-head {
        padding: 18px 20px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.14);
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
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

    .um-modal-grid {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 20px;
    }

    .um-detail-card {
        background: rgba(248, 250, 252, 0.85);
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 18px;
        padding: 18px;
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

        .um-recent-card {
            min-width: 220px;
            flex-basis: 220px;
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

        .um-summary-card,
        .um-recent-card {
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
            <h1>User Management</h1>
            <p>Search and manage students, faculty, and admin accounts from one place.</p>
        </div>
        <button type="button" class="um-btn um-btn-primary" data-open-lookup>
            <span>+</span> Add New User
        </button>
    </div>

    <div class="um-grid">
        <div class="um-stat">
            <div class="label">Students</div>
            <div class="value">{{ $stats['students'] }}</div>
        </div>
        <div class="um-stat">
            <div class="label">Admins</div>
            <div class="value">{{ $stats['admins'] }}</div>
        </div>
        <div class="um-stat">
            <div class="label">Faculty</div>
            <div class="value">{{ $stats['faculty'] }}</div>
        </div>
        <div class="um-stat">
            <div class="label">Active / Inactive</div>
            <div class="value">{{ $stats['active'] }} / {{ $stats['inactive'] }}</div>
        </div>
    </div>

    <div class="um-card">
        <div class="um-summary-grid">
            <div class="um-summary-card">
                <div class="um-summary-label">Total Users</div>
                <div class="um-summary-value">{{ count($records) }}</div>
                <p class="um-summary-note">Combined students, admins, faculty, and assistants.</p>
            </div>
            <div class="um-summary-card">
                <div class="um-summary-label">Active</div>
                <div class="um-summary-value">{{ $stats['active'] }}</div>
                <p class="um-summary-note">Accounts currently enabled.</p>
            </div>
            <div class="um-summary-card">
                <div class="um-summary-label">Inactive</div>
                <div class="um-summary-value">{{ $stats['inactive'] }}</div>
                <p class="um-summary-note">Accounts temporarily disabled.</p>
            </div>
            <div class="um-summary-card">
                <div class="um-summary-label">Admin Hub</div>
                <div class="um-summary-value">{{ $stats['admins'] }}</div>
                <p class="um-summary-note">Super admins, clinic staff, and student assistants.</p>
            </div>
        </div>

        <div class="um-recent-wrap">
            <div class="um-summary-label" style="margin-bottom:8px;">Recently Updated</div>
            <div class="um-recent-grid">
                @forelse($recentRecords as $record)
                    <div class="um-recent-card">
                        <div class="um-recent-top">
                            <div class="um-avatar" style="width:40px;height:40px;flex-basis:40px;">
                                @if(!empty($record['avatar_url']))
                                    <img src="{{ $record['avatar_url'] }}" alt="{{ $record['name'] }}">
                                @else
                                    {{ $record['avatar_letter'] }}
                                @endif
                            </div>
                            <div>
                                <div class="um-recent-name">{{ $record['name'] }}</div>
                                <div class="um-sub">{{ $record['role'] }}</div>
                            </div>
                        </div>
                        <div class="um-recent-meta">
                            <span>{{ $record['source_label'] }}</span>
                            <span>{{ $record['status'] === 'inactive' ? 'Inactive' : 'Active' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="um-recent-card" style="grid-column: 1 / -1;">
                        <div class="um-empty" style="padding: 18px 0;">No recent users available.</div>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="um-directory-toggle">
            <div class="hint">Click the search field to open the full user directory. The list stays hidden until you need it.</div>
            <button type="button" class="um-btn um-btn-soft" id="toggleDirectoryBtn">Show Full Directory</button>
        </div>

        <div class="um-card-head">
            <form class="um-search" method="GET" action="{{ route('admin.user-management') }}">
                <input type="search" name="search" value="{{ $search }}" placeholder="Search by email, name, or student ID" id="userManagementSearch">
                <button class="um-btn um-btn-soft" type="submit">Search</button>
            </form>
        </div>

        <div class="um-directory-panel {{ $search !== '' ? 'is-open' : '' }}" id="directoryPanel">
        <div class="um-table-wrap">
            <table class="um-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Source</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
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
                                        <div class="um-sub">
                                            {{ $record['student_id'] ?: 'ID not available' }}
                                        </div>
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
                            <td>
                                <span class="um-badge source">{{ $record['source_label'] }}</span>
                            </td>
                            <td>
                                <button
                                    type="button"
                                    class="um-action-btn"
                                    data-user-settings
                                    data-update-url="{{ $record['can_edit'] ? route('admin.user-management.update', $record['id']) : '' }}"
                                    data-delete-url="{{ $record['can_edit'] ? route('admin.user-management.destroy', $record['id']) : '' }}"
                                    data-can-edit="{{ $record['can_edit'] ? '1' : '0' }}"
                                    data-id="{{ $record['record_id'] }}"
                                    data-name="{{ $record['name'] }}"
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
                                    Settings
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="um-empty">No users matched the current search.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>
    </div>
</div>

<div class="um-modal-backdrop" id="lookupModal">
    <div class="um-modal-content">
        <div class="um-modal-head">
            <div>
                <h3>Add New User</h3>
                <div class="um-note">Search existing students, faculty, or admin profiles. Click the search field to reveal the results list.</div>
            </div>
            <button type="button" class="um-btn um-btn-soft" data-close-lookup>Close</button>
        </div>
        <div class="um-modal-body">
            <form class="um-search" method="GET" action="{{ route('admin.user-management') }}">
                <input type="search" name="search" value="{{ $search }}" placeholder="Search by email, name, or student ID" id="lookupSearchField">
                <button class="um-btn um-btn-primary" type="submit">Search</button>
            </form>
            <div class="um-directory-toggle" style="padding: 14px 0 10px;">
                <div class="hint">Click the search field to show the matching users below.</div>
                <button type="button" class="um-btn um-btn-soft" id="toggleLookupDirectoryBtn">Show Search Results</button>
            </div>
            <div style="margin-top: 16px;" class="um-directory-panel {{ $search !== '' ? 'is-open' : '' }}" id="lookupDirectoryPanel">
            <div class="um-table-wrap">
                <table class="um-table" style="min-width: 900px;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Source</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
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
                                <td>
                                    <button
                                        type="button"
                                        class="um-action-btn"
                                        data-user-settings
                                        data-update-url="{{ $record['can_edit'] ? route('admin.user-management.update', $record['id']) : '' }}"
                                        data-delete-url="{{ $record['can_edit'] ? route('admin.user-management.destroy', $record['id']) : '' }}"
                                        data-can-edit="{{ $record['can_edit'] ? '1' : '0' }}"
                                        data-id="{{ $record['record_id'] }}"
                                        data-name="{{ $record['name'] }}"
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
                                        Open Settings
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"><div class="um-empty">No users matched the current search.</div></td>
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
                        <label>Student / Faculty ID</label>
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
                        @method('PUT')
                        <div class="um-field">
                            <label>Role</label>
                            <select name="user_role" id="detailRole">
                                <option value="student">Student</option>
                                <option value="student_assistant">Student Assistant</option>
                                <option value="admin">Admin</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                        <div class="um-field" id="accessLevelWrap" style="display:none;">
                            <label id="detailAccessLevelLabel">Admin Type</label>
                            <select name="access_level" id="detailAccessLevel">
                                <option value="clinic_staff">Clinic Staff</option>
                                <option value="designee">Designee</option>
                            </select>
                        </div>
                        <div class="um-field">
                            <label id="detailEmailLabel">Gmail Account</label>
                            <input type="email" name="email" id="detailEditEmail" placeholder="Enter Gmail account">
                            <div class="um-note" id="emailRoleNote" style="margin-top: 6px;">
                                Use a separate Gmail account for the selected account type.
                            </div>
                        </div>
                        <div class="um-field">
                            <label>Status</label>
                            <select name="status" id="detailStatus">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="um-note" id="externalNote" style="display:none; margin-top: 6px;">
                            This faculty profile is managed externally, so it is read-only here.
                        </div>
                        <div class="um-actions">
            <button type="button" class="um-btn um-btn-soft" id="deactivateBtn">Deactivate Account</button>
                            <button type="submit" class="um-btn um-btn-primary" id="saveSettingsBtn">Save Changes</button>
                        </div>
                    </form>

                    <form method="POST" id="deleteForm" style="margin-top: 10px;">
                        @csrf
                        @method('DELETE')
                        <div class="um-actions" style="justify-content: flex-start;">
                    <button type="submit" class="um-btn" style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;" onclick="return confirm('Delete this account permanently?')">Delete Account</button>
                </div>
            </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const lookupModal = document.getElementById('lookupModal');
    const settingsModal = document.getElementById('settingsModal');
    const settingsForm = document.getElementById('settingsForm');
    const deleteForm = document.getElementById('deleteForm');
    const detailAvatar = document.getElementById('detailAvatar');
    const detailName = document.getElementById('detailName');
    const detailEmail = document.getElementById('detailEmail');
    const detailEditEmail = document.getElementById('detailEditEmail');
    const detailEmailLabel = document.getElementById('detailEmailLabel');
    const accessLevelWrap = document.getElementById('accessLevelWrap');
    const detailAccessLevel = document.getElementById('detailAccessLevel');
    const detailAccessLevelLabel = document.getElementById('detailAccessLevelLabel');
    const detailIdentifier = document.getElementById('detailIdentifier');
    const detailSource = document.getElementById('detailSource');
    const detailUpdated = document.getElementById('detailUpdated');
    const detailRole = document.getElementById('detailRole');
    const detailStatus = document.getElementById('detailStatus');
    const externalNote = document.getElementById('externalNote');
    const deactivateBtn = document.getElementById('deactivateBtn');
    const directoryPanel = document.getElementById('directoryPanel');
    const userManagementSearch = document.getElementById('userManagementSearch');
    const toggleDirectoryBtn = document.getElementById('toggleDirectoryBtn');
    const lookupDirectoryPanel = document.getElementById('lookupDirectoryPanel');
    const lookupSearchField = document.getElementById('lookupSearchField');
    const toggleLookupDirectoryBtn = document.getElementById('toggleLookupDirectoryBtn');

    const openDirectory = () => {
        if (directoryPanel) {
            directoryPanel.classList.add('is-open');
        }
        if (toggleDirectoryBtn) {
            toggleDirectoryBtn.textContent = 'Directory Open';
        }
    };

    const openLookupDirectory = () => {
        if (lookupDirectoryPanel) {
            lookupDirectoryPanel.classList.add('is-open');
        }
        if (toggleLookupDirectoryBtn) {
            toggleLookupDirectoryBtn.textContent = 'Results Open';
        }
    };

    if (userManagementSearch) {
        userManagementSearch.addEventListener('focus', openDirectory);
        userManagementSearch.addEventListener('click', openDirectory);
    }

    if (toggleDirectoryBtn) {
        toggleDirectoryBtn.addEventListener('click', openDirectory);
    }

    if (lookupSearchField) {
        lookupSearchField.addEventListener('focus', openLookupDirectory);
        lookupSearchField.addEventListener('click', openLookupDirectory);
    }

    if (toggleLookupDirectoryBtn) {
        toggleLookupDirectoryBtn.addEventListener('click', openLookupDirectory);
    }

    document.querySelectorAll('[data-open-lookup]').forEach((button) => {
        button.addEventListener('click', () => lookupModal.classList.add('show'));
    });

    document.querySelectorAll('[data-close-lookup]').forEach((button) => {
        button.addEventListener('click', () => lookupModal.classList.remove('show'));
    });

    document.querySelectorAll('[data-close-settings]').forEach((button) => {
        button.addEventListener('click', () => settingsModal.classList.remove('show'));
    });

    document.querySelectorAll('[data-user-settings]').forEach((button) => {
        button.addEventListener('click', () => {
            const canEdit = button.dataset.canEdit === '1';
            const avatarUrl = button.dataset.avatarUrl || '';
            const avatarLetter = button.dataset.avatarLetter || 'U';

            detailName.value = button.dataset.name || '';
            detailEmail.value = button.dataset.email || '';
            detailIdentifier.value = button.dataset.studentId || button.dataset.id || '';
            detailSource.value = button.dataset.sourceLabel || button.dataset.source || '';
            detailUpdated.value = button.dataset.updated || 'N/A';
            const normalizedRole = (() => {
                const raw = (button.dataset.role || 'student').toLowerCase();
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
            detailStatus.value = button.dataset.status || 'active';
            const meta = (() => {
                try {
                    return JSON.parse(button.dataset.meta || '{}') || {};
                } catch (error) {
                    return {};
                }
            })();
            const accessLevel = (meta.access_level || '').toLowerCase();
            detailAccessLevel.value = ['clinic_staff', 'designee'].includes(accessLevel) ? accessLevel : 'clinic_staff';
            const showAdminAccessLevel = normalizedRole === 'admin';
            accessLevelWrap.style.display = showAdminAccessLevel ? 'block' : 'none';

            detailEditEmail.value = button.dataset.email || '';
            detailEmailLabel.textContent = normalizedRole === 'student'
                ? 'Student Gmail Account'
                : (normalizedRole === 'student_assistant' ? 'Admin Gmail Account' : 'Admin Gmail Account');
            emailRoleNote.textContent = normalizedRole === 'student'
                ? 'This email stays with the student account.'
                : 'Use a separate Gmail account for the selected account type.';
            detailAccessLevelLabel.textContent = 'Admin Type';

            if (avatarUrl) {
                detailAvatar.innerHTML = `<img src="${avatarUrl}" alt="">`;
            } else {
                detailAvatar.textContent = avatarLetter;
            }

            settingsForm.action = button.dataset.updateUrl || '#';
            deleteForm.action = button.dataset.deleteUrl || '#';

            settingsForm.querySelectorAll('input, select, button').forEach((field) => {
                if (field.id === 'deactivateBtn') {
                    return;
                }
                field.disabled = !canEdit;
            });
            deactivateBtn.disabled = !canEdit;
            externalNote.style.display = canEdit ? 'none' : 'block';
            detailEditEmail.readOnly = !canEdit;

            deleteForm.style.display = canEdit ? 'block' : 'none';
            settingsModal.classList.add('show');
        });
    });

    detailRole.addEventListener('change', () => {
        const isStudent = detailRole.value === 'student';
        const isAdmin = detailRole.value === 'admin';
        const isSuperAdmin = detailRole.value === 'super_admin';

        if (isStudent) {
            detailEmailLabel.textContent = 'Student Gmail Account';
            emailRoleNote.textContent = 'This email stays with the student account.';
            accessLevelWrap.style.display = 'none';
        } else {
            detailEmailLabel.textContent = isSuperAdmin
                ? 'Super Admin Gmail Account'
                : 'Admin Gmail Account';
            emailRoleNote.textContent = 'Use a separate Gmail account for the selected account type.';
            accessLevelWrap.style.display = isAdmin ? 'block' : 'none';
            detailAccessLevelLabel.textContent = 'Admin Type';
        }
    });

    deactivateBtn.addEventListener('click', () => {
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
