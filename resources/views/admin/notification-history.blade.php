@extends('layouts.admin')

@section('title', 'Notification History')

@push('styles')
<style>
    .notification-history-shell {
        display: grid;
        gap: 18px;
    }

    .notification-history-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 20px 22px;
        border-radius: 22px;
        background: linear-gradient(135deg, #7f1d2d 0%, #54111d 100%);
        color: #fff7ed;
        box-shadow: 0 18px 34px rgba(95, 0, 18, 0.18);
    }

    .notification-history-head h2,
    .notification-history-head p {
        margin: 0;
    }

    .notification-history-head p {
        margin-top: 6px;
        color: rgba(255, 247, 237, 0.82);
    }

    .notification-history-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 58px;
        padding: 10px 16px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(250, 204, 21, 0.35);
        font-weight: 800;
        letter-spacing: 0.02em;
    }

    .notification-history-list {
        display: grid;
        gap: 12px;
    }

    .notification-history-card {
        display: block;
        padding: 18px;
        border-radius: 18px;
        text-decoration: none;
        border: 1px solid rgba(127, 29, 45, 0.12);
        background: #ffffff;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        color: inherit;
    }

    .notification-history-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 32px rgba(15, 23, 42, 0.12);
        border-color: rgba(127, 29, 45, 0.24);
    }

    .notification-history-card.is-read {
        opacity: 0.82;
    }

    .notification-history-meta {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 10px;
    }

    .notification-history-title {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: #111827;
    }

    .notification-history-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .notification-history-status.is-unread {
        background: #fef3c7;
        color: #92400e;
    }

    .notification-history-status.is-read {
        background: #e5e7eb;
        color: #4b5563;
    }

    .notification-history-hint {
        margin: 0;
        font-size: 13px;
        line-height: 1.65;
        color: #475569;
    }

    .notification-history-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }

    .notification-history-chip {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        background: #fff7ea;
        color: #7c2d12;
        font-size: 12px;
        font-weight: 700;
    }

    .notification-history-empty {
        padding: 32px 24px;
        border-radius: 22px;
        background: #ffffff;
        border: 1px dashed rgba(127, 29, 45, 0.22);
        text-align: center;
        color: #64748b;
    }

    html[data-theme="dark"] .notification-history-card,
    html[data-theme="dark"] .notification-history-empty {
        background: rgba(17, 24, 39, 0.92);
        border-color: rgba(250, 204, 21, 0.18);
        box-shadow: 0 18px 32px rgba(0, 0, 0, 0.24);
    }

    html[data-theme="dark"] .notification-history-title {
        color: #f8fafc;
    }

    html[data-theme="dark"] .notification-history-hint,
    html[data-theme="dark"] .notification-history-empty {
        color: #cbd5e1;
    }

    html[data-theme="dark"] .notification-history-chip {
        background: rgba(127, 29, 45, 0.3);
        color: #fde68a;
    }
</style>
@endpush

@section('content')
<div class="notification-history-shell">
    <section class="notification-history-head">
        <div>
            <h2>Notification History</h2>
            <p>Review unread and previously opened admin notifications in one place.</p>
        </div>
        <span class="notification-history-badge">{{ $unreadCount }} unread</span>
    </section>

    @if(collect($notifications ?? [])->isEmpty())
        <div class="notification-history-empty">
            No notification history is available right now.
        </div>
    @else
        <div class="notification-history-list">
            @foreach($notifications as $notification)
                <a href="{{ $notification['link'] }}" class="notification-history-card {{ ($notification['is_unread'] ?? false) ? '' : 'is-read' }}">
                    <div class="notification-history-meta">
                        <p class="notification-history-title">{{ $notification['title'] ?? 'Notification' }}</p>
                        <span class="notification-history-status {{ ($notification['is_unread'] ?? false) ? 'is-unread' : 'is-read' }}">
                            {{ ($notification['is_unread'] ?? false) ? 'Unread' : 'Read' }}
                        </span>
                    </div>
                    <p class="notification-history-hint">{{ $notification['hover_hint'] ?? 'No extra details available.' }}</p>
                    @if(!empty($notification['chips']))
                        <div class="notification-history-chips">
                            @foreach($notification['chips'] as $chip)
                                <span class="notification-history-chip">{{ $chip }}</span>
                            @endforeach
                        </div>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
