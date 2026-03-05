@extends('layouts.admin')

@section('title', 'System Logs')

@push('styles')
<style>
    /* Container Styling */
    .logs-wrapper {
        padding: 2rem;
        background: #f4f7f6;
        min-height: 100vh;
    }

    /* Card Design */
    .logs-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        background: #ffffff;
    }

    .logs-header {
        background: linear-gradient(135deg, #8B0000 0%, #5a0f15 100%);
        color: white;
        padding: 1.5rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logs-header h4 {
        margin: 0;
        font-weight: 700;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Table Styling */
    .table-responsive {
        padding: 0;
        margin: 0;
    }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .custom-table thead th {
        background: #f8fafc;
        padding: 1.2rem;
        font-size: 0.85rem;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 700;
        border-bottom: 2px solid #edf2f7;
    }

    .custom-table tbody tr {
        transition: all 0.2s;
    }

    .custom-table tbody tr:hover {
        background-color: #f1f5f9;
    }

    .custom-table td {
        padding: 1.2rem;
        vertical-align: middle;
        border-bottom: 1px solid #edf2f7;
        color: #334155;
        font-size: 0.95rem;
    }

    /* Component Styles */
    .user-pill {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar-circle {
        width: 40px;
        height: 40px;
        background: #fee2e2;
        color: #8B0000;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.1rem;
    }

    .action-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .text-description {
        line-height: 1.5;
        color: #4b5563;
        max-width: 400px;
    }

    .timestamp {
        font-weight: 600;
        color: #1e293b;
    }

    .ip-tag {
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.8rem;
        color: #94a3b8;
    }

    /* Pagination Styling */
    .pagination-container {
        padding: 1.5rem;
        display: flex;
        justify-content: center;
    }
</style>
@endpush

@section('content')
<div class="logs-wrapper">
    <div class="logs-card">
        <div class="logs-header">
            <h4><i class="fas fa-terminal"></i> System Activity Logs</h4>
            <div style="font-size: 0.9rem; opacity: 0.8;">
                Showing {{ $logs->count() }} of {{ $logs->total() }} events
            </div>
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Performed By</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>
                            <div class="user-pill">
                                <div class="avatar-circle">
                                    {{ strtoupper(substr($log->user_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 700;">{{ $log->user_name }}</div>
                                    <div style="font-size: 0.75rem; color: #94a3b8;">ID: #{{ $log->user_id ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="action-badge">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td>
                            <div class="text-description">
                                {{ $log->description }}
                            </div>
                        </td>
                        <td>
                            <span class="ip-tag">{{ $log->ip_address }}</span>
                        </td>
                        <td>
                            <div class="timestamp">{{ $log->created_at->format('M d, Y') }}</div>
                            <div style="font-size: 0.8rem; color: #94a3b8;">{{ $log->created_at->format('g:i A') }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 3rem; color: #94a3b8;">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>No system activities found in the records.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="pagination-container">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection