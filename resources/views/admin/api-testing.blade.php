@extends('layouts.admin')

@section('title', 'For API Testing')
@section('disable_voice_inputs', 'true')

@section('content')
<style>
    .api-testing-shell {
        display: grid;
        gap: 22px;
    }

    .api-testing-card {
        background: rgba(255, 255, 255, 0.96);
        border-radius: 24px;
        padding: 26px;
        box-shadow: 0 22px 50px rgba(15, 23, 42, 0.14);
        border: 1px solid rgba(128, 0, 0, 0.08);
    }

    html[data-theme="dark"] .api-testing-card {
        background: rgba(35, 17, 25, 0.94);
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 22px 50px rgba(0, 0, 0, 0.28);
    }

    .api-testing-head h2 {
        margin: 0 0 8px;
        color: #7f1d2d;
    }

    html[data-theme="dark"] .api-testing-head h2 {
        color: #f3d6da;
    }

    .api-testing-head p,
    .api-testing-meta,
    .api-empty {
        margin: 0;
        color: #6b7280;
    }

    html[data-theme="dark"] .api-testing-head p,
    html[data-theme="dark"] .api-testing-meta,
    html[data-theme="dark"] .api-empty {
        color: #cbd5e1;
    }

    .api-search-form {
        display: grid;
        grid-template-columns: 220px minmax(0, 1fr) auto;
        gap: 14px;
        align-items: end;
        margin-top: 18px;
    }

    .api-system-group.is-hidden {
        display: none;
    }

    .api-db-switch {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 18px;
    }

    .api-db-btn {
        border: 1px solid rgba(127, 29, 45, 0.16);
        border-radius: 16px;
        padding: 12px 18px;
        background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(250,244,246,0.98));
        color: #7f1d2d;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 12px 24px rgba(127, 29, 45, 0.08);
    }

    .api-db-btn.is-active {
        background: linear-gradient(135deg, #7f1d2d, #5b0c0e);
        color: #fff;
    }

    .api-db-list {
        display: grid;
        gap: 18px;
    }

    .api-db-card {
        border-radius: 20px;
        padding: 20px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(250, 244, 246, 0.98));
        border: 1px solid rgba(127, 29, 45, 0.12);
    }

    .api-db-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 16px;
    }

    .api-db-head h3 {
        margin: 0;
        color: #7f1d2d;
    }

    .api-db-head p {
        margin: 4px 0 0;
        color: #6b7280;
        font-size: 13px;
    }

    .api-db-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .api-db-action-btn {
        border: 1px solid rgba(127, 29, 45, 0.16);
        border-radius: 12px;
        padding: 10px 14px;
        background: #fff;
        color: #7f1d2d;
        font-weight: 800;
        cursor: pointer;
    }

    .api-db-action-btn.delete {
        border-color: rgba(185, 28, 28, 0.18);
        background: #fee2e2;
        color: #991b1b;
    }

    .api-db-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .api-edit-modal {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.48);
        backdrop-filter: blur(8px);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        z-index: 5000;
    }

    .api-edit-modal.show {
        display: flex;
    }

    .api-edit-content {
        width: min(720px, 100%);
        border-radius: 24px;
        background: rgba(255,255,255,0.98);
        border: 1px solid rgba(127, 29, 45, 0.12);
        box-shadow: 0 24px 54px rgba(15,23,42,0.18);
        padding: 22px;
    }

    .api-edit-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-top: 16px;
    }

    .api-edit-field label {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: #6b7280;
    }

    .api-edit-field input,
    .api-edit-field select {
        width: 100%;
        border-radius: 14px;
        border: 1px solid rgba(127, 29, 45, 0.16);
        padding: 12px 14px;
    }

    .api-edit-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 18px;
    }

    .api-search-form label {
        display: block;
        margin-bottom: 8px;
        font-weight: 700;
        color: #374151;
    }

    html[data-theme="dark"] .api-search-form label {
        color: #f8fafc;
    }

    .api-search-form input,
    .api-search-form select {
        width: 100%;
        border-radius: 16px;
        border: 1px solid rgba(127, 29, 45, 0.2);
        padding: 14px 16px;
        font-size: 15px;
        outline: none;
    }

    .api-search-form button {
        min-width: 148px;
        border: none;
        border-radius: 16px;
        padding: 14px 18px;
        background: linear-gradient(135deg, #7f1d2d, #5b0c0e);
        color: #fff;
        font-weight: 700;
    }

    .api-alert {
        margin-top: 16px;
        padding: 14px 16px;
        border-radius: 16px;
        background: rgba(127, 29, 45, 0.08);
        border: 1px solid rgba(127, 29, 45, 0.18);
        color: #7f1d2d;
    }

    .api-results {
        display: grid;
        gap: 18px;
    }

    .api-result-card {
        border-radius: 20px;
        padding: 22px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(247, 244, 245, 0.98));
        border: 1px solid rgba(127, 29, 45, 0.1);
    }

    html[data-theme="dark"] .api-result-card {
        background: linear-gradient(180deg, rgba(59, 24, 33, 0.96), rgba(35, 17, 25, 0.98));
        border-color: rgba(255, 255, 255, 0.08);
    }

    .api-result-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-top: 16px;
    }

    .api-field {
        border-radius: 14px;
        padding: 12px 14px;
        background: rgba(127, 29, 45, 0.06);
    }

    html[data-theme="dark"] .api-field {
        background: rgba(255, 255, 255, 0.05);
    }

    .api-field small {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
    }

    .api-field strong {
        color: #111827;
    }

    html[data-theme="dark"] .api-field strong {
        color: #f8fafc;
    }

    .api-raw-toggle {
        margin-top: 16px;
    }

    .api-raw-toggle summary {
        cursor: pointer;
        font-weight: 700;
        color: #7f1d2d;
        list-style: none;
    }

    .api-raw-toggle summary::-webkit-details-marker {
        display: none;
    }

    html[data-theme="dark"] .api-raw-toggle summary {
        color: #f3d6da;
    }

    .api-json {
        margin-top: 12px;
        border-radius: 16px;
        padding: 16px;
        background: #111827;
        color: #f8fafc;
        overflow: auto;
        font-size: 12px;
        line-height: 1.5;
    }

    .admin-option-list {
        display: grid;
        gap: 12px;
    }

    .admin-option-item,
    .faculty-option-item {
        width: 100%;
        text-align: left;
        border: 1px solid rgba(127, 29, 45, 0.16);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(250, 244, 246, 0.98));
        border-radius: 18px;
        padding: 16px 18px;
        cursor: pointer;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }

    .admin-option-item:hover,
    .faculty-option-item:hover {
        transform: translateY(-1px);
        border-color: rgba(127, 29, 45, 0.28);
        box-shadow: 0 12px 24px rgba(127, 29, 45, 0.08);
    }

    .admin-option-name,
    .faculty-option-name {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: #7f1d2d;
    }

    .admin-option-email,
    .faculty-option-email {
        margin-top: 4px;
        font-size: 13px;
        color: #6b7280;
    }

    .admin-option-meta,
    .faculty-option-meta {
        margin-top: 10px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .admin-option-chip,
    .faculty-option-chip {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(127, 29, 45, 0.08);
        color: #7f1d2d;
        font-size: 12px;
        font-weight: 700;
    }

    .admin-autofill-panel {
        margin-top: 20px;
        border-radius: 20px;
        padding: 20px;
        background: rgba(127, 29, 45, 0.05);
        border: 1px solid rgba(127, 29, 45, 0.12);
    }

    .admin-autofill-panel h3 {
        margin: 0 0 14px;
        color: #7f1d2d;
    }

    .admin-autofill-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .admin-autofill-field label {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
    }

    .admin-autofill-field input {
        width: 100%;
        border-radius: 14px;
        border: 1px solid rgba(127, 29, 45, 0.16);
        padding: 12px 14px;
        font-size: 14px;
        color: #111827;
        background: rgba(255, 255, 255, 0.96);
    }

    .faculty-autofill-panel {
        margin-top: 20px;
        border-radius: 20px;
        padding: 20px;
        background: rgba(127, 29, 45, 0.05);
        border: 1px solid rgba(127, 29, 45, 0.12);
    }

    .faculty-autofill-panel h3 {
        margin: 0 0 14px;
        color: #7f1d2d;
    }

    .faculty-autofill-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .faculty-autofill-field label {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
    }

    .faculty-autofill-field input {
        width: 100%;
        border-radius: 14px;
        border: 1px solid rgba(127, 29, 45, 0.16);
        padding: 12px 14px;
        font-size: 14px;
        color: #111827;
        background: rgba(255, 255, 255, 0.96);
    }

    html[data-theme="dark"] .admin-option-item,
    html[data-theme="dark"] .faculty-option-item {
        background: linear-gradient(180deg, rgba(59, 24, 33, 0.96), rgba(35, 17, 25, 0.98));
        border-color: rgba(255, 255, 255, 0.08);
    }

    html[data-theme="dark"] .api-db-btn:not(.is-active),
    html[data-theme="dark"] .api-db-card,
    html[data-theme="dark"] .api-edit-content {
        background: linear-gradient(180deg, rgba(59, 24, 33, 0.96), rgba(35, 17, 25, 0.98));
        border-color: rgba(255, 255, 255, 0.08);
        color: #f8fafc;
    }

    html[data-theme="dark"] .api-db-head h3 {
        color: #f3d6da;
    }

    html[data-theme="dark"] .api-db-head p,
    html[data-theme="dark"] .api-edit-field label {
        color: #cbd5e1;
    }

    html[data-theme="dark"] .api-db-action-btn {
        background: rgba(255,255,255,0.08);
        color: #fff;
        border-color: rgba(255,255,255,0.12);
    }

    html[data-theme="dark"] .api-edit-field input,
    html[data-theme="dark"] .api-edit-field select {
        background: rgba(18, 18, 18, 0.55);
        border-color: rgba(255, 255, 255, 0.08);
        color: #f8fafc;
    }

    html[data-theme="dark"] .admin-option-name,
    html[data-theme="dark"] .faculty-option-name {
        color: #f3d6da;
    }

    html[data-theme="dark"] .admin-option-email,
    html[data-theme="dark"] .faculty-option-email,
    html[data-theme="dark"] .admin-autofill-field label,
    html[data-theme="dark"] .faculty-autofill-field label {
        color: #cbd5e1;
    }

    html[data-theme="dark"] .admin-option-chip,
    html[data-theme="dark"] .faculty-option-chip,
    html[data-theme="dark"] .admin-autofill-panel,
    html[data-theme="dark"] .faculty-autofill-panel {
        background: rgba(255, 255, 255, 0.05);
        color: #f8fafc;
        border-color: rgba(255, 255, 255, 0.08);
    }

    html[data-theme="dark"] .admin-autofill-panel h3,
    html[data-theme="dark"] .faculty-autofill-panel h3 {
        color: #f3d6da;
    }

    html[data-theme="dark"] .admin-autofill-field input,
    html[data-theme="dark"] .faculty-autofill-field input {
        background: rgba(18, 18, 18, 0.55);
        border-color: rgba(255, 255, 255, 0.08);
        color: #f8fafc;
    }

    @media (max-width: 900px) {
        .api-result-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 680px) {
        .api-search-form {
            grid-template-columns: 1fr;
        }

        .api-result-grid {
            grid-template-columns: 1fr;
        }

        .admin-autofill-grid {
            grid-template-columns: 1fr;
        }

        .faculty-autofill-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="api-testing-shell">
    <section class="api-testing-card">
        <div class="api-testing-head">
            <h2>For API Testing</h2>
            <p>Temporary admin tool for checking if another site's API is reachable and returning faculty profile information.</p>
        </div>

        <form method="GET" class="api-search-form" id="apiTestingForm">
            <div>
                <label for="source">API Source</label>
                <select id="source" name="source">
                    <option value="faculty" {{ ($source ?? 'faculty') === 'faculty' ? 'selected' : '' }}>Faculty API</option>
                    <option value="puptas_applicant" {{ ($source ?? 'faculty') === 'puptas_applicant' ? 'selected' : '' }}>PUPTAS Applicant API</option>
                    <option value="admin_api" {{ ($source ?? 'faculty') === 'admin_api' ? 'selected' : '' }}>Our Admin API</option>
                    <option value="admin_options" {{ ($source ?? 'faculty') === 'admin_options' ? 'selected' : '' }}>Our Admin Options API</option>
                    <option value="database_info" {{ ($source ?? 'faculty') === 'database_info' ? 'selected' : '' }}>Database Info</option>
                    <option value="custom" {{ ($source ?? 'faculty') === 'custom' ? 'selected' : '' }}>Custom Temp API</option>
                </select>
            </div>
            <div id="apiSystemGroup" class="api-system-group {{ in_array(($source ?? 'faculty'), ['admin_api', 'admin_options'], true) ? '' : 'is-hidden' }}">
                <label for="system">External System</label>
                <select id="system" name="system">
                    <option value="">Choose system</option>
                    @foreach(($availableSystems ?? []) as $systemOption)
                        <option value="{{ $systemOption }}" {{ ($selectedSystem ?? '') === $systemOption ? 'selected' : '' }}>
                            {{ strtoupper(str_replace('_', ' ', $systemOption)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="search">{{ ($source ?? 'faculty') === 'puptas_applicant' ? 'Search by Student Number' : 'Search by name, email, or ID' }}</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="{{ ($source ?? 'faculty') === 'puptas_applicant' ? 'Try a student number' : 'Try a name, email address, or identifier' }}"
                >
            </div>
            <button type="submit">Search API's</button>
        </form>

        @if(($source ?? '') === 'database_info')
            <div class="api-db-switch">
                <a href="{{ route('admin.api-testing', ['source' => 'database_info', 'db_table' => 'users', 'search' => $search]) }}" class="api-db-btn {{ ($dbTable ?? 'users') === 'users' ? 'is-active' : '' }}">Users</a>
                <a href="{{ route('admin.api-testing', ['source' => 'database_info', 'db_table' => 'admins', 'search' => $search]) }}" class="api-db-btn {{ ($dbTable ?? 'users') === 'admins' ? 'is-active' : '' }}">Admins</a>
            </div>
        @endif

        @if($apiResponseMeta)
            <p class="api-testing-meta" style="margin-top: 16px;">
                Endpoint: <strong>{{ $apiResponseMeta['endpoint'] }}</strong>
                | Status: <strong>{{ $apiResponseMeta['status'] }}</strong>
                | Matches: <strong>{{ $apiResponseMeta['result_count'] }}</strong>
                @if(!empty($apiResponseMeta['auth_mode']))
                | Auth: <strong>{{ $apiResponseMeta['auth_mode'] }}</strong>
                @endif
                @if(!empty($apiResponseMeta['source']))
                | Source: <strong>{{ $apiResponseMeta['source'] }}</strong>
                @endif
                @if(!empty($apiResponseMeta['system']))
                | System: <strong>{{ $apiResponseMeta['system'] }}</strong>
                @endif
            </p>
            @if(!empty($apiResponseMeta['header_name']) || !empty($apiResponseMeta['system_header_name']) || !empty($apiResponseMeta['api_key_preview']))
                <p class="api-testing-meta" style="margin-top: 8px;">
                    @if(!empty($apiResponseMeta['system_header_name']))
                    {{ $apiResponseMeta['system_header_name'] }}: <strong>{{ $apiResponseMeta['system'] ?? 'N/A' }}</strong>
                    @endif
                    @if(!empty($apiResponseMeta['header_name']))
                    | {{ $apiResponseMeta['header_name'] }}: <strong>{{ $apiResponseMeta['api_key_preview'] ?? 'configured' }}</strong>
                    @endif
                </p>
            @endif
        @endif

        @if($errorMessage)
            <div class="api-alert">{{ $errorMessage }}</div>
            @if(!empty($errorDetails))
                <details class="api-raw-toggle">
                    <summary>Show error details</summary>
                    <div class="api-json">{{ $errorDetails }}</div>
                </details>
            @endif
        @endif
    </section>

   

<section class="api-testing-card">
    @if(!empty($results))
        @if(($source ?? '') === 'admin_options')
            <div class="admin-option-list">
                @foreach($results as $result)
                    <button
                        type="button"
                        class="admin-option-item"
                        data-first-name="{{ $result['first_name'] ?? '' }}"
                        data-last-name="{{ $result['last_name'] ?? '' }}"
                        data-suffix-name="{{ ($result['suffix_name'] ?? '') === 'N/A' ? '' : ($result['suffix_name'] ?? '') }}"
                        data-email="{{ $result['email'] ?? '' }}"
                        data-status="{{ $result['status'] ?? '' }}"
                    >
                        <p class="admin-option-name">{{ $result['name'] ?? 'N/A' }}</p>
                        <div class="admin-option-email">{{ $result['email'] ?? 'N/A' }}</div>
                        <div class="admin-option-meta">
                            <span class="admin-option-chip">ID: {{ $result['admin_id'] ?? ($result['identifier'] ?? 'N/A') }}</span>
                            <span class="admin-option-chip">Status: {{ $result['status'] ?? 'N/A' }}</span>
                        </div>

                        {{-- Raw Response para sa Admin Options --}}
                        <details class="api-raw-toggle" style="margin-top: 10px;">
                            <summary onclick="event.stopPropagation()">Show raw response</summary>
                            <div class="api-json">{{ json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</div>
                        </details>
                    </button>
                @endforeach
            </div>

            {{-- ... (Selected Admin Panel stays the same) ... --}}
            <div class="admin-autofill-panel">
                <h3>Selected Admin</h3>
                <div class="admin-autofill-grid">
                    <div class="admin-autofill-field"><label>First Name</label><input type="text" id="selectedFirstName" readonly></div>
                    <div class="admin-autofill-field"><label>Last Name</label><input type="text" id="selectedLastName" readonly></div>
                    <div class="admin-autofill-field"><label>Suffix Name</label><input type="text" id="selectedSuffixName" readonly></div>
                    <div class="admin-autofill-field"><label>Email</label><input type="text" id="selectedEmail" readonly></div>
                    <div class="admin-autofill-field"><label>Status</label><input type="text" id="selectedStatus" readonly></div>
                </div>
            </div>

       @elseif(($source ?? '') === 'faculty')
    <div class="admin-option-list">
        @foreach($results as $result)
            @php
                $facultyFields = $result['fields'] ?? [];
                $facultyFirstName = $result['first_name'] ?? ($facultyFields['first_name'] ?? '');
                $facultyLastName = $result['last_name'] ?? ($facultyFields['last_name'] ?? '');
                $facultySuffixName = $result['suffix_name'] ?? ($facultyFields['suffix_name'] ?? '');
                $facultyEmail = $result['email'] ?? ($facultyFields['email'] ?? 'N/A');
                $facultyStatus = $result['status'] ?? ($facultyFields['status'] ?? 'Active');
                $facultyOffice = $result['office'] ?? ($facultyFields['department'] ?? $facultyFields['office'] ?? 'N/A');
                $facultyIdentifier = $result['identifier'] ?? ($facultyFields['faculty_id'] ?? ($facultyFields['faculty_code'] ?? 'N/A'));
            @endphp
            <button
                type="button"
                class="faculty-option-item"
                data-first-name="{{ $facultyFirstName }}"
                data-last-name="{{ $facultyLastName }}"
                data-suffix-name="{{ ($facultySuffixName ?? '') === 'N/A' ? '' : ($facultySuffixName ?? '') }}"
                data-email="{{ $facultyEmail }}"
                data-status="{{ $facultyStatus }}"
                data-office="{{ $facultyOffice }}"
                data-identifier="{{ $facultyIdentifier }}"
            >
                <p class="faculty-option-name">{{ $result['name'] ?? trim($facultyFirstName . ' ' . $facultyLastName) ?: 'N/A' }}</p>
                <div class="faculty-option-email">{{ $facultyEmail }}</div>

                <div class="faculty-option-meta">
                    <span class="faculty-option-chip">ID: {{ $facultyIdentifier }}</span>
                    <span class="faculty-option-chip">Office: {{ $facultyOffice }}</span>
                    <span class="faculty-option-chip">Status: {{ $facultyStatus }}</span>
                </div>

                <details class="api-raw-toggle" style="margin-top: 10px;">
                    <summary onclick="event.stopPropagation()">Show raw response</summary>
                    <div class="api-json">{{ json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</div>
                </details>
            </button>
        @endforeach
    </div>

    <div class="faculty-autofill-panel">
        <h3>Selected Faculty Details</h3>
        <div class="faculty-autofill-grid">
            <div class="faculty-autofill-field"><label>Faculty ID</label><input type="text" id="selectedFacultyIdentifier" readonly></div>
            <div class="faculty-autofill-field"><label>First Name</label><input type="text" id="selectedFacultyFirstName" readonly></div>
            <div class="faculty-autofill-field"><label>Last Name</label><input type="text" id="selectedFacultyLastName" readonly></div>
            <div class="faculty-autofill-field"><label>Suffix Name</label><input type="text" id="selectedFacultySuffixName" readonly></div>
            <div class="faculty-autofill-field"><label>Email</label><input type="text" id="selectedFacultyEmail" readonly></div>
            <div class="faculty-autofill-field"><label>Status</label><input type="text" id="selectedFacultyStatus" readonly></div>
            <div class="faculty-autofill-field"><label>Department/Office</label><input type="text" id="selectedFacultyOffice" readonly></div>
        </div>
    </div>
        @elseif(($source ?? '') === 'puptas_applicant')
            <div class="api-results">
                @foreach($results as $result)
                    <article class="api-result-card">
                        <h3 style="margin: 0; color: #7f1d2d;">{{ trim(implode(' ', array_filter([$result['first_name'] ?? null, $result['last_name'] ?? null]))) ?: 'PUPTAS Applicant' }}</h3>
                        <div class="api-result-grid">
                            <div class="api-field">
                                <small>Student Number</small>
                                <strong>{{ $result['student_number'] ?? 'N/A' }}</strong>
                            </div>
                            <div class="api-field">
                                <small>IDP User ID</small>
                                <strong>{{ $result['idp_user_id'] ?? 'N/A' }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Email</small>
                                <strong>{{ $result['email'] ?? 'N/A' }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Program Code</small>
                                <strong>{{ data_get($result, 'program.code', 'N/A') }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Program Name</small>
                                <strong>{{ data_get($result, 'program.name', 'N/A') }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Lifecycle Status</small>
                                <strong>{{ $result['lifecycle_status'] ?? 'N/A' }}</strong>
                            </div>
                        </div>

                        <details class="api-raw-toggle">
                            <summary>Show raw response</summary>
                            <div class="api-json">{{ json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</div>
                        </details>
                    </article>
                @endforeach
            </div>
        @else
            {{-- Unified Display for Other APIs (Custom / Admin API) --}}
            <div class="api-results">
                @foreach($results as $result)
                    <article class="api-result-card">
                        <h3 style="margin: 0; color: #7f1d2d;">{{ $result['name'] ?? 'N/A' }}</h3>
                        <div class="api-result-grid">
                            {{-- ... existing fields (ID, Name, Email, etc.) ... --}}
                            {{-- Siguraduhin lang na $result gamit mo dito, hindi $result['fields'] kung hindi consistent ang API --}}
                        </div>

                        <details class="api-raw-toggle">
                            <summary>Show raw response</summary>
                            <div class="api-json">{{ json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</div>
                        </details>
                    </article>
                @endforeach
            </div>
        @endif
    @elseif(($source ?? '') === 'database_info')
        <div class="api-db-list">
            @forelse(($databaseInfo ?? []) as $record)
                <article class="api-db-card">
                    <div class="api-db-head">
                        <div>
                            <h3>{{ $record['name'] ?: 'Unnamed Record' }}</h3>
                            <p>{{ $record['email'] ?: 'No email available' }}</p>
                        </div>
                        @if(\App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '') === \App\Models\User::ROLE_SUPERADMIN)
                            <div class="api-db-actions">
                                <button
                                    type="button"
                                    class="api-db-action-btn"
                                    data-db-edit
                                    data-table="{{ $dbTable ?? 'users' }}"
                                    data-id="{{ $record['id'] }}"
                                    data-raw='@json($record["raw"])'
                                >
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.api-testing.database.delete', ['table' => $dbTable ?? 'users', 'id' => $record['id']]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="api-db-action-btn delete" onclick="return confirm('Delete this database record?')">Delete</button>
                                </form>
                            </div>
                        @endif
                    </div>
                    <div class="api-db-grid">
                        @foreach(($record['primary'] ?? []) as $label => $value)
                            <div class="api-field">
                                <small>{{ $label }}</small>
                                <strong>{{ $value ?: 'N/A' }}</strong>
                            </div>
                        @endforeach
                    </div>
                    <details class="api-raw-toggle">
                        <summary>Show raw response</summary>
                        <div class="api-json">{{ json_encode($record['raw'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</div>
                    </details>
                </article>
            @empty
                <p class="api-empty">No database records matched the current filter.</p>
            @endforelse
        </div>
    @else
        <p class="api-empty">Search results will appear here once you choose a source and enter a name, email, or ID.</p>
    @endif
</section>
</div>

<div class="api-edit-modal" id="databaseEditModal">
    <div class="api-edit-content">
        <div class="api-testing-head">
            <h2>Edit Database Record</h2>
            <p>Temporary local editor for API Testing.</p>
        </div>
        <form method="POST" id="databaseEditForm">
            @csrf
            @method('PUT')
            <div class="api-edit-grid" id="databaseEditFields"></div>
            <div class="api-edit-actions">
                <button type="button" class="api-db-action-btn" id="closeDatabaseEditModal">Cancel</button>
                <button type="submit" class="api-db-action-btn" style="background:linear-gradient(135deg,#7f1d2d,#5b0c0e);color:#fff;">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('apiTestingForm');
        const sourceField = document.getElementById('source');
        const searchField = document.getElementById('search');
        const systemField = document.getElementById('system');
        const systemGroup = document.getElementById('apiSystemGroup');
        const dbEditModal = document.getElementById('databaseEditModal');
        const dbEditForm = document.getElementById('databaseEditForm');
        const dbEditFields = document.getElementById('databaseEditFields');
        const closeDbEditModal = document.getElementById('closeDatabaseEditModal');

        if (!form || !sourceField || !searchField || !systemField || !systemGroup) return;

        let hasAutoSubmitted = false;
        const syncSystemVisibility = () => {
            const needsSystem = ['admin_api', 'admin_options'].includes(sourceField.value);
            systemField.disabled = !needsSystem;
            systemGroup.classList.toggle('is-hidden', !needsSystem);

            if (!needsSystem) {
                systemField.value = '';
            }
        };

        // Auto-submit logic kapag nag-focus sa search field for specific sources
        searchField.addEventListener('focus', function () {
            const source = sourceField.value;
            const shouldAutoLoad = ['admin_api', 'admin_options'].includes(source);

            if (!shouldAutoLoad || searchField.value.trim() !== '' || hasAutoSubmitted) return;

            hasAutoSubmitted = true;
            form.requestSubmit();
        });

        sourceField.addEventListener('change', function () {
            syncSystemVisibility();
            const searchLabel = document.querySelector('label[for="search"]');
            const isPuptasApplicant = sourceField.value === 'puptas_applicant';
            if (searchLabel) {
                searchLabel.textContent = isPuptasApplicant ? 'Search by Student Number' : 'Search by name, email, or ID';
            }
            searchField.placeholder = isPuptasApplicant ? 'Try a student number' : 'Try a name, email address, or identifier';
        });

        syncSystemVisibility();

        const fieldSets = {
            users: [
                { name: 'first_name', label: 'First Name', type: 'text' },
                { name: 'last_name', label: 'Last Name', type: 'text' },
                { name: 'email', label: 'Email', type: 'email' },
                { name: 'student_id', label: 'Student ID', type: 'text' },
                { name: 'user_role', label: 'Role', type: 'select', options: ['student', 'student_assistant', 'admin', 'superadmin'] },
                { name: 'status', label: 'Status', type: 'select', options: ['active', 'inactive'] },
            ],
            admins: [
                { name: 'first_name', label: 'First Name', type: 'text' },
                { name: 'last_name', label: 'Last Name', type: 'text' },
                { name: 'email', label: 'Email', type: 'email' },
                { name: 'office', label: 'Office', type: 'text' },
                { name: 'access_level', label: 'Access Level', type: 'text' },
                { name: 'status', label: 'Status', type: 'select', options: ['active', 'inactive'] },
            ]
        };

        document.querySelectorAll('[data-db-edit]').forEach((button) => {
            button.addEventListener('click', () => {
                if (!dbEditModal || !dbEditForm || !dbEditFields) return;
                const table = button.dataset.table || 'users';
                const id = button.dataset.id;
                let raw = {};
                try {
                    raw = JSON.parse(button.dataset.raw || '{}') || {};
                } catch (error) {
                    raw = {};
                }

                dbEditForm.action = `{{ url('/admin/api-testing/database') }}/${table}/${id}`;
                dbEditFields.innerHTML = '';

                (fieldSets[table] || []).forEach((field) => {
                    const wrap = document.createElement('div');
                    wrap.className = 'api-edit-field';

                    const label = document.createElement('label');
                    label.textContent = field.label;
                    wrap.appendChild(label);

                    let control;
                    if (field.type === 'select') {
                        control = document.createElement('select');
                        field.options.forEach((optionValue) => {
                            const option = document.createElement('option');
                            option.value = optionValue;
                            option.textContent = optionValue;
                            if ((raw[field.name] || '') === optionValue) {
                                option.selected = true;
                            }
                            control.appendChild(option);
                        });
                    } else {
                        control = document.createElement('input');
                        control.type = field.type;
                        control.value = raw[field.name] || '';
                    }
                    control.name = field.name;
                    wrap.appendChild(control);
                    dbEditFields.appendChild(wrap);
                });

                dbEditModal.classList.add('show');
            });
        });

        if (closeDbEditModal && dbEditModal) {
            closeDbEditModal.addEventListener('click', () => dbEditModal.classList.remove('show'));
            dbEditModal.addEventListener('click', (event) => {
                if (event.target === dbEditModal) {
                    dbEditModal.classList.remove('show');
                }
            });
        }

        // Handler para sa Admin Options & Admin API (Unified)
        const handleAdminSelection = (button) => {
            const fields = {
                'selectedFirstName': button.dataset.firstName,
                'selectedLastName': button.dataset.lastName,
                'selectedSuffixName': button.dataset.suffixName,
                'selectedEmail': button.dataset.email,
                'selectedStatus': button.dataset.status
            };

            Object.keys(fields).forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = fields[id] || '';
            });
        };

        // Handler para sa Faculty Selection
        const handleFacultySelection = (button) => {
            const fields = {
                'selectedFacultyIdentifier': button.dataset.identifier,
                'selectedFacultyFirstName': button.dataset.firstName,
                'selectedFacultyLastName': button.dataset.lastName,
                'selectedFacultySuffixName': button.dataset.suffixName,
                'selectedFacultyEmail': button.dataset.email,
                'selectedFacultyStatus': button.dataset.status,
                'selectedFacultyOffice': button.dataset.office
            };

            Object.keys(fields).forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = fields[id] || '';
            });
        };

        // Event Delegation para mas malinis at gumana kahit dynamic ang results
        document.addEventListener('click', function (e) {
            const adminBtn = e.target.closest('.admin-option-item');
            const facultyBtn = e.target.closest('.faculty-option-item');

            if (adminBtn) handleAdminSelection(adminBtn);
            if (facultyBtn) handleFacultySelection(facultyBtn);
        });

        const firstFacultyButton = document.querySelector('.faculty-option-item');
        if (firstFacultyButton) {
            handleFacultySelection(firstFacultyButton);
        }
    });
</script>
@endpush
