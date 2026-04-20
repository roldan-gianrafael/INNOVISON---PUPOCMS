@extends('layouts.student')

@section('title', 'Appointment Feedback')

@push('styles')
<style>
    .feedback-shell {
        max-width: 760px;
        margin: 0 auto;
        padding: 20px 20px 60px;
    }

    .feedback-card {
        background: #fff;
        border: 1px solid #eef2f3;
        border-radius: 18px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        padding: 28px;
    }

    .feedback-title {
        margin: 0;
        font-size: 28px;
        font-weight: 800;
        color: #600000;
    }

    .feedback-subtitle {
        margin: 8px 0 0;
        color: #64748b;
        line-height: 1.6;
        font-size: 14px;
    }

    .feedback-meta {
        margin-top: 20px;
        padding: 16px 18px;
        border-radius: 14px;
        background: #fff8f6;
        border: 1px solid #f7d7d2;
    }

    .feedback-meta strong {
        color: #7f1d1d;
    }

    .feedback-label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 800;
        color: #7c2d12;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .feedback-rating {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin: 12px 0 22px;
    }

    .feedback-rating input {
        display: none;
    }

    .feedback-rating label {
        min-width: 54px;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid #d6dbe0;
        background: #fff;
        color: #475569;
        font-weight: 800;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .feedback-rating label:hover {
        border-color: #8b0000;
        color: #8b0000;
        background: #fff7f7;
    }

    .feedback-rating input:checked + label {
        background: #8b0000;
        border-color: #8b0000;
        color: #fff;
        box-shadow: 0 10px 22px rgba(139, 0, 0, 0.18);
    }

    .feedback-textarea {
        width: 100%;
        min-height: 140px;
        border-radius: 14px;
        border: 1px solid #d6dbe0;
        padding: 14px 16px;
        font-size: 14px;
        color: #334155;
        resize: vertical;
    }

    .feedback-textarea:focus {
        outline: none;
        border-color: #8b0000;
        box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.08);
    }

    .feedback-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 22px;
        flex-wrap: wrap;
    }

    .feedback-btn {
        padding: 12px 18px;
        border-radius: 999px;
        font-weight: 800;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .feedback-btn.primary {
        background: #8b0000;
        color: #fff;
    }

    .feedback-btn.secondary {
        background: #f1f5f9;
        color: #334155;
    }

    .feedback-errors {
        margin-top: 18px;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid #fecaca;
        background: #fff1f2;
        color: #b91c1c;
        font-size: 14px;
    }

    .feedback-readonly-banner {
        margin-top: 18px;
        padding: 14px 16px;
        border-radius: 12px;
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #166534;
        font-size: 14px;
        line-height: 1.6;
    }

    .feedback-text-static {
        width: 100%;
        min-height: 140px;
        border-radius: 14px;
        border: 1px solid #d6dbe0;
        padding: 14px 16px;
        font-size: 14px;
        color: #334155;
        background: #f8fafc;
        white-space: pre-wrap;
    }
</style>
@endpush

@section('content')
@php
    $isReadonly = (bool) optional($existingFeedback)->submitted_at;
@endphp
<div class="feedback-shell">
    <div class="feedback-card">
        <h1 class="feedback-title">Appointment Feedback</h1>
        <p class="feedback-subtitle">
            {{ $isReadonly
                ? 'Your feedback has already been submitted. You can review it here anytime from your notifications.'
                : 'Your appointment is complete. A short review helps the clinic improve service quality and student experience.' }}
        </p>

        <div class="feedback-meta">
            <strong>{{ $appointment->service }}</strong><br>
            {{ \Carbon\Carbon::parse($appointment->date)->format('M d, Y') }}
            at {{ \Carbon\Carbon::parse($appointment->time)->format('g:i A') }}
        </div>

        @if($isReadonly)
            <div class="feedback-readonly-banner">
                Feedback submitted {{ optional($existingFeedback->submitted_at)->format('M d, Y g:i A') }}. Editing is disabled after submission.
            </div>
        @endif

        @if($errors->any())
            <div class="feedback-errors">{{ $errors->first() }}</div>
        @endif

        @if($isReadonly)
            <div style="margin-top: 24px;">
                <label class="feedback-label">Your Rating</label>
                <div class="feedback-rating">
                    @for($i = 1; $i <= 5; $i++)
                        <input type="radio" id="ratingRead{{ $i }}" value="{{ $i }}" {{ (string) optional($existingFeedback)->rating === (string) $i ? 'checked' : '' }} disabled>
                        <label for="ratingRead{{ $i }}" style="{{ (string) optional($existingFeedback)->rating === (string) $i ? '' : 'cursor:default;' }}">{{ $i }}</label>
                    @endfor
                </div>

                <label class="feedback-label">Comments</label>
                <div class="feedback-text-static">{{ trim((string) optional($existingFeedback)->feedback) !== '' ? $existingFeedback->feedback : 'No written comments were added.' }}</div>

                <div class="feedback-actions">
                    <a href="{{ url('/student/account?view=notifications') }}" class="feedback-btn secondary">Back to Notifications</a>
                </div>
            </div>
        @else
            <form action="{{ route('student.feedback.store', ['appointment' => $appointment->id]) }}" method="POST" style="margin-top: 24px;">
                @csrf

                <label class="feedback-label">How would you rate your appointment?</label>
                <div class="feedback-rating">
                    @for($i = 1; $i <= 5; $i++)
                        <input type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" {{ (string) old('rating', optional($existingFeedback)->rating) === (string) $i ? 'checked' : '' }}>
                        <label for="rating{{ $i }}">{{ $i }}</label>
                    @endfor
                </div>

                <label class="feedback-label" for="feedbackText">Comments</label>
                <textarea id="feedbackText" name="feedback" class="feedback-textarea" placeholder="Share anything helpful about your clinic experience.">{{ old('feedback', optional($existingFeedback)->feedback) }}</textarea>

                <div class="feedback-actions">
                    <a href="{{ url('/student/account?view=notifications') }}" class="feedback-btn secondary">Back</a>
                    <button type="submit" class="feedback-btn primary">Submit Feedback</button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
