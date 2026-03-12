@extends('layouts.admin')

@section('title', 'Audit Trail')

@push('styles')
<style>
    .audit-wrap {
        display: grid;
        gap: 16px;
    }

    .audit-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        padding: 18px;
    }

    .audit-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        flex-wrap: wrap;
    }

    .audit-title {
        margin: 0;
        color: #0f172a;
        font-size: 23px;
        font-weight: 800;
    }

    .audit-subtitle {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .audit-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .audit-stat {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
        background: #f8fafc;
    }

    .audit-stat-label {
        color: #64748b;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .audit-stat-value {
        color: #0f172a;
        font-size: 24px;
        font-weight: 800;
        line-height: 1;
    }

    .audit-filters {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
    }

    .audit-filter-group {
        display: grid;
        gap: 6px;
    }

    .audit-filter-label {
        color: #334155;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
    }

    .audit-input,
    .audit-select {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        padding: 9px 10px;
        font-size: 13px;
        color: #1e293b;
        background: #fff;
    }

    .audit-input:focus,
    .audit-select:focus {
        outline: none;
        border-color: #8B0000;
        box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.08);
    }

    .audit-filter-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 4px;
    }

    .audit-btn {
        border: 1px solid transparent;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        padding: 8px 12px;
        text-decoration: none;
        cursor: pointer;
    }

    .audit-btn-primary {
        background: #70131B;
        color: #fff;
    }

    .audit-btn-primary:hover {
        background: #8B0000;
    }

    .audit-btn-light {
        background: #fff;
        border-color: #cbd5e1;
        color: #334155;
    }

    .audit-breakdowns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .audit-list-title {
        margin: 0 0 10px;
        color: #0f172a;
        font-size: 14px;
        font-weight: 800;
    }

    .audit-mini-list {
        margin: 0;
        padding: 0;
        list-style: none;
        display: grid;
        gap: 7px;
    }

    .audit-mini-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 10px;
        gap: 8px;
    }

    .audit-mini-label {
        color: #334155;
        font-size: 12px;
        font-weight: 700;
    }

    .audit-mini-count {
        color: #70131B;
        font-size: 12px;
        font-weight: 800;
    }

    .audit-table-wrap {
        overflow-x: auto;
    }

    .audit-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1100px;
    }

    .audit-table th {
        text-align: left;
        font-size: 11px;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 800;
        letter-spacing: 0.04em;
        border-bottom: 2px solid #e2e8f0;
        padding: 10px;
        background: #f8fafc;
    }

    .audit-table td {
        border-bottom: 1px solid #edf2f7;
        padding: 10px;
        vertical-align: top;
        font-size: 13px;
        color: #1e293b;
    }

    .audit-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #fee2e2;
        color: #8B0000;
        font-size: 12px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .audit-user {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .audit-role-badge,
    .audit-event-badge,
    .audit-status-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 2px 9px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .audit-role-admin { background: #dbeafe; color: #1e40af; }
    .audit-role-superadmin { background: #ede9fe; color: #5b21b6; }
    .audit-role-super_admin { background: #ede9fe; color: #5b21b6; }
    .audit-role-student_assistant { background: #dbeafe; color: #1e40af; }
    .audit-role-student { background: #dcfce7; color: #166534; }
    .audit-role-unknown { background: #e2e8f0; color: #334155; }

    .audit-event-view { background: #e0f2fe; color: #0369a1; }
    .audit-event-create { background: #dcfce7; color: #166534; }
    .audit-event-update { background: #fef3c7; color: #92400e; }
    .audit-event-delete { background: #fee2e2; color: #991b1b; }
    .audit-event-auth { background: #ede9fe; color: #5b21b6; }
    .audit-event-error { background: #fecaca; color: #7f1d1d; }
    .audit-event-action { background: #e2e8f0; color: #334155; }

    .audit-status-ok { background: #dcfce7; color: #166534; }
    .audit-status-failed { background: #fee2e2; color: #991b1b; }
    .audit-status-unknown { background: #e2e8f0; color: #334155; }

    .audit-mono {
        font-family: Consolas, "Courier New", monospace;
        font-size: 12px;
    }

    .audit-empty {
        text-align: center;
        color: #64748b;
        font-size: 13px;
        padding: 28px 12px;
    }

    .audit-pagination {
        margin-top: 12px;
        display: flex;
        justify-content: center;
    }

    @media (max-width: 1200px) {
        .audit-stats,
        .audit-filters {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .audit-stats,
        .audit-filters,
        .audit-breakdowns {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
@php
    $roleLabelMap = [
        'superadmin' => 'Super Admin',
        'admin' => 'Admin',
        'super_admin' => 'Super Admin (Legacy)',
        'student_assistant' => 'Admin (Legacy)',
        'student' => 'Student',
        'unknown' => 'Unknown',
    ];
@endphp

<div class="audit-wrap">
    <section class="audit-card">
        <div class="audit-head">
            <div>
                <h1 class="audit-title">Professional Audit Trail</h1>
                <p class="audit-subtitle">Monitor all user activities across students, student assistants, and nurse-level administrators.</p>
            </div>
            <div class="audit-subtitle">
                Records shown: {{ $logs->count() }} of {{ number_format($logs->total()) }}
            </div>
        </div>
    </section>

    <section class="audit-stats">
        <article class="audit-stat">
            <div class="audit-stat-label">Total Events</div>
            <div class="audit-stat-value">{{ number_format($totalEvents) }}</div>
        </article>
        <article class="audit-stat">
            <div class="audit-stat-label">Events Today</div>
            <div class="audit-stat-value">{{ number_format($todayEvents) }}</div>
        </article>
        <article class="audit-stat">
            <div class="audit-stat-label">Unique Actors</div>
            <div class="audit-stat-value">{{ number_format($uniqueActors) }}</div>
        </article>
        <article class="audit-stat">
            <div class="audit-stat-label">Failed Events</div>
            <div class="audit-stat-value">{{ number_format($failedEvents) }}</div>
        </article>
    </section>

    <section class="audit-card">
        <form method="GET" action="{{ route('admin.logs') }}">
            <div class="audit-filters">
                <div class="audit-filter-group">
                    <label class="audit-filter-label" for="audit_q">Search</label>
                    <input id="audit_q" name="q" type="text" class="audit-input" value="{{ request('q') }}" placeholder="Name, action, module, route, IP...">
                </div>

                <div class="audit-filter-group">
                    <label class="audit-filter-label" for="audit_actor_role">Actor Role</label>
                    <select id="audit_actor_role" name="actor_role" class="audit-select">
                        <option value="">All Roles</option>
                        @foreach($roleOptions as $roleOption)
                            <option value="{{ $roleOption }}" @selected(request('actor_role') === $roleOption)>
                                {{ $roleLabelMap[$roleOption] ?? ucwords(str_replace('_', ' ', $roleOption)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="audit-filter-group">
                    <label class="audit-filter-label" for="audit_event_type">Event Type</label>
                    <select id="audit_event_type" name="event_type" class="audit-select">
                        <option value="">All Types</option>
                        @foreach($eventTypeOptions as $eventTypeOption)
                            <option value="{{ $eventTypeOption }}" @selected(request('event_type') === $eventTypeOption)>
                                {{ strtoupper($eventTypeOption) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="audit-filter-group">
                    <label class="audit-filter-label" for="audit_module">Module</label>
                    <select id="audit_module" name="module" class="audit-select">
                        <option value="">All Modules</option>
                        @foreach($moduleOptions as $moduleOption)
                            <option value="{{ $moduleOption }}" @selected(request('module') === $moduleOption)>
                                {{ $moduleOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="audit-filter-group">
                    <label class="audit-filter-label" for="audit_http_method">HTTP Method</label>
                    <select id="audit_http_method" name="http_method" class="audit-select">
                        <option value="">All Methods</option>
                        @foreach(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $methodOption)
                            <option value="{{ $methodOption }}" @selected(request('http_method') === $methodOption)>
                                {{ $methodOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="audit-filter-group">
                    <label class="audit-filter-label" for="audit_status_class">Status Class</label>
                    <select id="audit_status_class" name="status_class" class="audit-select">
                        <option value="">All Statuses</option>
                        <option value="success" @selected(request('status_class') === 'success')>Success (&lt; 400)</option>
                        <option value="error" @selected(request('status_class') === 'error')>Error (400+)</option>
                    </select>
                </div>

                <div class="audit-filter-group">
                    <label class="audit-filter-label" for="audit_date_from">Date From</label>
                    <input id="audit_date_from" name="date_from" type="date" class="audit-input" value="{{ request('date_from') }}">
                </div>

                <div class="audit-filter-group">
                    <label class="audit-filter-label" for="audit_date_to">Date To</label>
                    <input id="audit_date_to" name="date_to" type="date" class="audit-input" value="{{ request('date_to') }}">
                </div>

                <div class="audit-filter-group">
                    <label class="audit-filter-label" for="audit_per_page">Rows</label>
                    <select id="audit_per_page" name="per_page" class="audit-select">
                        @foreach([25, 50, 100] as $rowsOption)
                            <option value="{{ $rowsOption }}" @selected((int) request('per_page', 25) === $rowsOption)>
                                {{ $rowsOption }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="audit-filter-actions">
                <a href="{{ route('admin.logs') }}" class="audit-btn audit-btn-light">Reset</a>
                <button type="submit" class="audit-btn audit-btn-primary">Apply Filters</button>
            </div>
        </form>
    </section>

    <section class="audit-breakdowns">
        <article class="audit-card">
            <h3 class="audit-list-title">Activity by Role</h3>
            <ul class="audit-mini-list">
                @forelse($roleBreakdown as $row)
                    <li class="audit-mini-item">
                        <span class="audit-mini-label">{{ $roleLabelMap[$row->role] ?? ucwords(str_replace('_', ' ', $row->role)) }}</span>
                        <span class="audit-mini-count">{{ number_format($row->total) }}</span>
                    </li>
                @empty
                    <li class="audit-empty">No role activity yet.</li>
                @endforelse
            </ul>
        </article>
        <article class="audit-card">
            <h3 class="audit-list-title">Top Modules</h3>
            <ul class="audit-mini-list">
                @forelse($moduleBreakdown as $row)
                    <li class="audit-mini-item">
                        <span class="audit-mini-label">{{ $row->module_name }}</span>
                        <span class="audit-mini-count">{{ number_format($row->total) }}</span>
                    </li>
                @empty
                    <li class="audit-empty">No module activity yet.</li>
                @endforelse
            </ul>
        </article>
    </section>

    <section class="audit-card">
        <div class="audit-table-wrap">
            <table class="audit-table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Actor</th>
                        <th>Role</th>
                        <th>Module</th>
                        <th>Event</th>
                        <th>Action</th>
                        <th>Subject</th>
                        <th>HTTP</th>
                        <th>Status</th>
                        <th>IP</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php
                            $roleKey = strtolower((string) ($log->user_role ?? 'unknown'));
                            $eventKey = strtolower((string) ($log->event_type ?? 'action'));
                            $statusCode = $log->status_code;
                            $statusClass = is_null($statusCode) ? 'unknown' : ((int) $statusCode >= 400 ? 'failed' : 'ok');
                            $subjectText = trim((string) (($log->subject_type ?? '-') . ((string) ($log->subject_id ?? '') !== '' ? ' #' . $log->subject_id : '')));
                        @endphp
                        <tr>
                            <td>
                                <div>{{ optional($log->created_at)->format('M d, Y') }}</div>
                                <div style="color:#64748b; font-size:12px;">{{ optional($log->created_at)->format('g:i:s A') }}</div>
                            </td>
                            <td>
                                <div class="audit-user">
                                    <span class="audit-avatar">{{ strtoupper(substr((string) ($log->user_name ?? 'U'), 0, 1)) }}</span>
                                    <div>
                                        <div style="font-weight:700;">{{ $log->user_name ?? 'Unknown User' }}</div>
                                        <div style="font-size:11px; color:#64748b;">UID: {{ $log->user_id ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="audit-role-badge audit-role-{{ $roleKey }}">
                                    {{ $roleLabelMap[$roleKey] ?? ucwords(str_replace('_', ' ', $roleKey)) }}
                                </span>
                            </td>
                            <td>{{ $log->module ?? 'Uncategorized' }}</td>
                            <td>
                                <span class="audit-event-badge audit-event-{{ $eventKey }}">
                                    {{ strtoupper($eventKey) }}
                                </span>
                            </td>
                            <td>{{ $log->action }}</td>
                            <td>{{ $subjectText !== '' ? $subjectText : '-' }}</td>
                            <td>
                                <div class="audit-mono">{{ $log->http_method ?? '-' }}</div>
                                <div class="audit-mono" style="color:#64748b;">{{ $log->request_path ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="audit-status-badge audit-status-{{ $statusClass }}">
                                    {{ is_null($statusCode) ? 'N/A' : $statusCode }}
                                </span>
                            </td>
                            <td class="audit-mono">{{ $log->ip_address ?? '-' }}</td>
                            <td style="min-width:250px;">
                                <div>{{ $log->description }}</div>
                                @if($log->route_name)
                                    <div style="margin-top:4px; font-size:11px; color:#64748b;">Route: {{ $log->route_name }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="audit-empty">No audit records found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="audit-pagination">
                {{ $logs->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
