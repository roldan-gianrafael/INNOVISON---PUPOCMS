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
            <div class="api-results">
                @foreach($results as $result)
                    <article class="api-result-card">
                        <h3 style="margin: 0; color: #7f1d2d;">{{ $result['name'] }}</h3>
                        <div class="api-result-grid">
                            <div class="api-field">
                                <small>ID</small>
                                <strong>{{ $result['admin_id'] ?? $result['identifier'] }}</strong>
                            </div>
                            <div class="api-field">
                                <small>First Name</small>
                                <strong>{{ $result['first_name'] ?? 'N/A' }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Middle Name</small>
                                <strong>{{ $result['middle_name'] ?? 'N/A' }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Last Name</small>
                                <strong>{{ $result['last_name'] ?? 'N/A' }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Suffix Name</small>
                                <strong>{{ $result['suffix_name'] ?? 'N/A' }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Email</small>
                                <strong>{{ $result['email'] }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Birthday</small>
                                <strong>{{ $result['birthday'] }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Age</small>
                                <strong>{{ $result['age'] ?? 'N/A' }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Gender</small>
                                <strong>{{ $result['gender'] ?? 'N/A' }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Civil Status</small>
                                <strong>{{ $result['civil_status'] ?? 'N/A' }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Name</small>
                                <strong>{{ $result['name'] }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Access Level</small>
                                <strong>{{ $result['access_level'] ?? $result['role'] }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Office</small>
                                <strong>{{ $result['office'] }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Contact Number</small>
                                <strong>{{ $result['contact_number'] }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Status</small>
                                <strong>{{ $result['status'] }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Emergency Contact Person</small>
                                <strong>{{ $result['emergency_contact_person'] ?? 'N/A' }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Emergency Contact No</small>
                                <strong>{{ $result['emergency_contact_no'] ?? 'N/A' }}</strong>
                            </div>
                            <div class="api-field">
                                <small>Last Updated</small>
                                <strong>{{ $result['last_updated'] ?? 'N/A' }}</strong>
                            </div>
                            <div class="api-field" style="grid-column: 1 / -1;">
                                <small>Address</small>
                                <strong>{{ $result['address'] }}</strong>
                            </div>
                        </div>

                        <details class="api-raw-toggle">
                            <summary>Show raw response</summary>
                            <div class="api-json">{{ json_encode($result['fields'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</div>
                        </details>
                    </article>
                @endforeach
            </div>
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

        if (!form || !sourceField || !searchField) {
            return;
        }

        let hasAutoSubmitted = false;

        searchField.addEventListener('focus', function () {
            const source = sourceField.value;
            const shouldAutoLoad = source === 'admin_api' || source === 'admin_options';

            if (!shouldAutoLoad || searchField.value.trim() !== '' || hasAutoSubmitted) {
                return;
            }

            hasAutoSubmitted = true;
            form.requestSubmit();
        });
    });
</script>
@endpush
