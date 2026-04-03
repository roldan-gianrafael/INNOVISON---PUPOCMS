@extends('layouts.admin')

@section('title', 'For API Testing')

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
                    <option value="admin_api" {{ ($source ?? 'faculty') === 'admin_api' ? 'selected' : '' }}>Our Admin API</option>
                    <option value="admin_options" {{ ($source ?? 'faculty') === 'admin_options' ? 'selected' : '' }}>Our Admin Options API</option>
                    <option value="custom" {{ ($source ?? 'faculty') === 'custom' ? 'selected' : '' }}>Custom Temp API</option>
                </select>
            </div>
            <div>
                <label for="search">Search by name, email, or ID</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Try a name, email address, or identifier"
                >
            </div>
            <button type="submit">Search API</button>
        </form>

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
            </p>
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
        {{-- Target the correct array depth --}}
        @foreach($results['fields']['faculties'] ?? [] as $faculty)
            <button
                type="button"
                class="admin-option-item" {{-- Ginamit ko yung class ng admin para sa styling --}}
                data-first-name="{{ $faculty['first_name'] ?? '' }}"
                data-last-name="{{ $faculty['last_name'] ?? '' }}"
                data-suffix-name="{{ $faculty['suffix_name'] ?? '' }}"
                data-email="{{ $faculty['email'] ?? '' }}"
                data-status="{{ $faculty['status'] ?? '' }}"
                data-office="{{ $faculty['department'] ?? 'N/A' }}"
                data-identifier="{{ $faculty['faculty_code'] ?? '' }}"
            >
                {{-- Match Admin's naming and layout --}}
                <p class="admin-option-name">
                    {{ ($faculty['first_name'] ?? '') }} {{ ($faculty['last_name'] ?? '') }}
                </p>
                <div class="admin-option-email">{{ $faculty['email'] ?? 'N/A' }}</div>
                
                <div class="admin-option-meta">
                    <span class="admin-option-chip">ID: {{ $faculty['faculty_code'] ?? 'N/A' }}</span>
                    <span class="admin-option-chip">Office: {{ $faculty['department'] ?? 'N/A' }}</span>
                    <span class="admin-option-chip">Status: {{ $faculty['status'] ?? 'Active' }}</span>
                </div>

                {{-- Raw Response Toggle --}}
                <details class="api-raw-toggle" style="margin-top: 10px;">
                    <summary onclick="event.stopPropagation()">Show raw response</summary>
                    <div class="api-json">{{ json_encode($faculty, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</div>
                </details>
            </button>
        @endforeach
    </div>

    {{-- Selected Faculty Panel --}}
    <div class="faculty-autofill-panel">
        <h3>Selected Faculty Details</h3>
        <div class="faculty-autofill-grid">
            <div class="faculty-autofill-field"><label>First Name</label><input type="text" id="selectedFacultyFirstName" readonly></div>
            <div class="faculty-autofill-field"><label>Last Name</label><input type="text" id="selectedFacultyLastName" readonly></div>
            <div class="faculty-autofill-field"><label>Suffix Name</label><input type="text" id="selectedFacultySuffixName" readonly></div>
            <div class="faculty-autofill-field"><label>Email</label><input type="text" id="selectedFacultyEmail" readonly></div>
            <div class="faculty-autofill-field"><label>Status</label><input type="text" id="selectedFacultyStatus" readonly></div>
            <div class="faculty-autofill-field"><label>Department/Office</label><input type="text" id="selectedFacultyOffice" readonly></div>
        </div>
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
    @else
        <p class="api-empty">Search results will appear here once you choose a source and enter a name, email, or ID.</p>
    @endif
</section>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('apiTestingForm');
        const sourceField = document.getElementById('source');
        const searchField = document.getElementById('search');

        if (!form || !sourceField || !searchField) return;

        let hasAutoSubmitted = false;

        // Auto-submit logic kapag nag-focus sa search field for specific sources
        searchField.addEventListener('focus', function () {
            const source = sourceField.value;
            const shouldAutoLoad = ['admin_api', 'admin_options'].includes(source);

            if (!shouldAutoLoad || searchField.value.trim() !== '' || hasAutoSubmitted) return;

            hasAutoSubmitted = true;
            form.requestSubmit();
        });

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
    });
</script>
@endpush
