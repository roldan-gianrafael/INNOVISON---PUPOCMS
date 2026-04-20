@extends('layouts.admin')

@section('title', 'Feedback Reports')

@push('styles')
<style>
    .feedback-report-shell {
        max-width: 1380px;
        margin: 0 auto;
        padding: 22px;
    }
    .feedback-report-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 24px;
    }
    .feedback-report-title {
        margin: 0;
        font-size: 30px;
        font-weight: 900;
        color: #ffffff;
        letter-spacing: -0.03em;
    }
    .feedback-report-copy {
        margin: 8px 0 0;
        color: rgba(255,255,255,0.78);
        font-size: 14px;
        line-height: 1.6;
        max-width: 720px;
    }
    .feedback-report-back {
        color: #d7dde8;
        text-decoration: none;
        font-weight: 700;
        font-size: 14px;
        white-space: nowrap;
    }
    .feedback-stat-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 24px;
    }
    .feedback-stat-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 20px 22px;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.12);
        border-top: 5px solid #7f1d2d;
    }
    .feedback-stat-card span {
        display: block;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #64748b;
    }
    .feedback-stat-card strong {
        display: block;
        margin-top: 8px;
        font-size: 28px;
        line-height: 1.1;
        font-weight: 900;
        color: #111827;
    }
    .feedback-layout {
        display: grid;
        grid-template-columns: 320px minmax(0, 1fr);
        gap: 22px;
        align-items: start;
    }
    .feedback-panel {
        background: #ffffff;
        border-radius: 20px;
        padding: 22px;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.12);
    }
    .feedback-panel h3 {
        margin: 0 0 14px;
        font-size: 17px;
        font-weight: 900;
        color: #7f1d2d;
    }
    .feedback-filter-form {
        display: grid;
        gap: 14px;
    }
    .feedback-field label {
        display: block;
        margin-bottom: 7px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #475569;
    }
    .feedback-field input,
    .feedback-field select {
        width: 100%;
        height: 46px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 0 14px;
        font-size: 14px;
        color: #111827;
        background: #ffffff;
    }
    .feedback-filter-actions {
        display: flex;
        gap: 10px;
    }
    .feedback-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        border-radius: 12px;
        padding: 0 16px;
        text-decoration: none;
        font-weight: 800;
        cursor: pointer;
        border: none;
    }
    .feedback-btn.primary {
        background: #7f1d2d;
        color: #ffffff;
    }
    .feedback-btn.secondary {
        background: #eef2f7;
        color: #334155;
    }
    .clinic-score-block {
        margin-top: 18px;
        padding: 18px;
        border-radius: 18px;
        background: linear-gradient(135deg, #7f1d2d, #56111e);
        color: #ffffff;
    }
    .clinic-score-kicker {
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        opacity: 0.8;
    }
    .clinic-score-number {
        margin-top: 8px;
        font-size: 42px;
        font-weight: 900;
        line-height: 1;
    }
    .clinic-score-copy {
        margin-top: 8px;
        font-size: 13px;
        line-height: 1.6;
        color: rgba(255,255,255,0.82);
    }
    .feedback-list {
        display: grid;
        gap: 16px;
    }
    .feedback-card {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 16px;
        align-items: start;
        background: #ffffff;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.12);
    }
    .feedback-avatar {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        background: linear-gradient(135deg, #7f1d2d, #56111e);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 900;
        letter-spacing: 0.04em;
        flex-shrink: 0;
    }
    .feedback-card-head {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        margin-bottom: 8px;
    }
    .feedback-card-name {
        font-size: 17px;
        font-weight: 900;
        color: #111827;
    }
    .feedback-card-meta {
        font-size: 12px;
        color: #64748b;
        font-weight: 700;
    }
    .feedback-chip-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 10px;
    }
    .feedback-chip {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #334155;
        font-size: 11px;
        font-weight: 800;
    }
    .feedback-message {
        margin: 0;
        font-size: 14px;
        line-height: 1.7;
        color: #111827;
        white-space: pre-line;
    }
    .feedback-side {
        text-align: right;
        min-width: 110px;
    }
    .feedback-rating {
        font-size: 26px;
        font-weight: 900;
        color: #7f1d2d;
        line-height: 1;
    }
    .feedback-rating-sub {
        margin-top: 6px;
        font-size: 12px;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .feedback-empty {
        background: #ffffff;
        border-radius: 20px;
        padding: 36px 24px;
        text-align: center;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.12);
        color: #475569;
        font-weight: 700;
    }
    .feedback-pagination {
        margin-top: 18px;
    }
    .feedback-pagination svg {
        width: 16px;
        height: 16px;
    }
    @media (max-width: 1100px) {
        .feedback-layout {
            grid-template-columns: 1fr;
        }
        .feedback-stat-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 720px) {
        .feedback-stat-grid {
            grid-template-columns: 1fr;
        }
        .feedback-card {
            grid-template-columns: 1fr;
        }
        .feedback-side {
            text-align: left;
            min-width: 0;
        }
    }
</style>
@endpush

@section('content')
@php
    $role = \App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '');
    $reportsHomeUrl = $role === \App\Models\User::ROLE_ADMIN ? url('/assistant/reports') : url('/admin/reports');
    $feedbackUrl = $role === \App\Models\User::ROLE_ADMIN ? url('/assistant/reports/feedbacks') : url('/admin/reports/feedbacks');
@endphp

<div class="feedback-report-shell">
    <div class="feedback-report-head">
        <div>
            <h1 class="feedback-report-title">Feedback Reports</h1>
            <p class="feedback-report-copy">Review all submitted clinic feedback, monitor the overall patient experience, and keep a simple clinic score out of 10 based on the ratings students and staff gave after consultations.</p>
        </div>
        <a href="{{ $reportsHomeUrl }}" class="feedback-report-back">&larr; Back to Reports</a>
    </div>

    <div class="feedback-stat-grid">
        <div class="feedback-stat-card">
            <span>Total Feedbacks</span>
            <strong>{{ $totalFeedbacks }}</strong>
        </div>
        <div class="feedback-stat-card">
            <span>Rate of Clinic</span>
            <strong>{{ number_format($clinicScore, 1) }}/10</strong>
        </div>
        <div class="feedback-stat-card">
            <span>Recommended</span>
            <strong>{{ $recommendedCount }}</strong>
        </div>
    </div>

    <div class="feedback-layout">
        <aside class="feedback-panel">
            <h3>Filter Feedbacks</h3>
            <form method="GET" action="{{ $feedbackUrl }}" class="feedback-filter-form">
                <div class="feedback-field">
                    <label for="feedbackSearch">Search</label>
                    <input id="feedbackSearch" type="text" name="q" value="{{ $search }}" placeholder="Name, service, comment">
                </div>
                <div class="feedback-field">
                    <label for="feedbackMonth">Month</label>
                    <input id="feedbackMonth" type="month" name="month" value="{{ $monthFilter }}">
                </div>
                <div class="feedback-filter-actions">
                    <button type="submit" class="feedback-btn primary">Apply</button>
                    <a href="{{ $feedbackUrl }}" class="feedback-btn secondary">Reset</a>
                </div>
            </form>

            <div class="clinic-score-block">
                <div class="clinic-score-kicker">Clinic Score</div>
                <div class="clinic-score-number">{{ number_format($clinicScore, 1) }}/10</div>
                <div class="clinic-score-copy">
                    rack patient satisfaction to identify and resolve operational bottlenecks.
                </div>
            </div>
        </aside>

        <section>
            @if($feedbackItems->count() > 0)
                <div class="feedback-list">
                    @foreach($feedbackItems as $feedback)
                        <article class="feedback-card">
                            <div class="feedback-avatar">{{ $feedback->initials }}</div>
                            <div>
                                <div class="feedback-card-head">
                                    <div class="feedback-card-name">{{ $feedback->name }}</div>
                                    <div class="feedback-card-meta">· {{ $feedback->role }} · {{ $feedback->time_ago }}</div>
                                </div>
                                <div class="feedback-chip-row">
                                    <span class="feedback-chip">{{ $feedback->service }}</span>
                                    <span class="feedback-chip">{{ ucfirst($feedback->appointment_type ?: 'consultation') }}</span>
                                    <span class="feedback-chip">{{ $feedback->student_number !== '' ? $feedback->student_number : 'No student number' }}</span>
                                </div>
                                <p class="feedback-message">{{ $feedback->message !== '' ? $feedback->message : 'No written comments were added.' }}</p>
                            </div>
                            <div class="feedback-side">
                                <div class="feedback-rating">{{ $feedback->score_out_of_ten }}/10</div>
                                <div class="feedback-rating-sub">{{ $feedback->rating }}/5 Rating</div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="feedback-pagination">
                    {{ $feedbackItems->links() }}
                </div>
            @else
                <div class="feedback-empty">No feedback submissions were found for the current filters.</div>
            @endif
        </section>
    </div>
</div>
@endsection
