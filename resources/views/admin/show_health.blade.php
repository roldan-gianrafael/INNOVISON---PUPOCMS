@extends('layouts.admin')

@section('title', 'Student Health Profile Review')

@push('styles')
<style>
    .doc-review-wrap { max-width: 1100px; margin: 0 auto; display: grid; gap: 18px; }
    .doc-review-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06); padding: 18px; }
    .doc-review-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
    .doc-review-title { font-size: 20px; font-weight: 800; color: #0f172a; margin: 0; }
    .doc-review-sub { font-size: 13px; color: #64748b; margin: 6px 0 0; }
    .doc-btn { display: inline-flex; align-items: center; gap: 8px; border-radius: 10px; padding: 10px 14px; font-weight: 700; font-size: 13px; text-decoration: none; border: 1px solid transparent; }
    .doc-btn-back { background: #e2e8f0; color: #1e293b; }
    .doc-btn-verify { background: #70131b; color: #ffffff; }
    .doc-meta { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-top: 14px; }
    .doc-meta-item { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 12px; }
    .doc-meta-k { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 4px; }
    .doc-meta-v { font-size: 14px; color: #0f172a; font-weight: 700; word-break: break-word; }
    .doc-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
    .doc-file { border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; background: #fff; }
    .doc-file h4 { margin: 0 0 10px; font-size: 14px; font-weight: 800; color: #1e293b; }
    .doc-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 10px; }
    .doc-link { display: inline-flex; align-items: center; gap: 6px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 7px 10px; color: #1e293b; font-size: 12px; font-weight: 700; text-decoration: none; background: #fff; }
    .doc-preview { width: 100%; height: 300px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; background: #f8fafc; }
    .doc-preview iframe, .doc-preview img { width: 100%; height: 100%; border: 0; object-fit: contain; background: #fff; }
    .doc-missing { border: 1px dashed #cbd5e1; color: #64748b; border-radius: 8px; padding: 14px; font-size: 13px; font-weight: 600; background: #f8fafc; }

    [data-theme="dark"] .doc-review-card,
    [data-theme="dark"] .doc-file { background: #0f172a; border-color: #334155; box-shadow: none; }
    [data-theme="dark"] .doc-review-title,
    [data-theme="dark"] .doc-meta-v,
    [data-theme="dark"] .doc-file h4 { color: #f8fafc; }
    [data-theme="dark"] .doc-review-sub,
    [data-theme="dark"] .doc-meta-k,
    [data-theme="dark"] .doc-missing { color: #cbd5e1; }
    [data-theme="dark"] .doc-meta-item { background: #111827; border-color: #334155; }
    [data-theme="dark"] .doc-link { background: #111827; border-color: #475569; color: #f8fafc; }
    [data-theme="dark"] .doc-btn-back { background: #1e293b; color: #f8fafc; }

    @media (max-width: 1024px) { .doc-meta { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 768px) {
        .doc-grid { grid-template-columns: 1fr; }
        .doc-meta { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
@php
    $adminUser = auth('admin')->user();
    $isSuperAdmin = strtolower((string) ($adminUser->user_role ?? '')) === 'superadmin';
@endphp

<div class="doc-review-wrap">
    <div class="doc-review-card">
        <div class="doc-review-head">
            <div>
                <h1 class="doc-review-title">Student Health Upload Review</h1>
                <p class="doc-review-sub">Review uploaded files before verification/approval.</p>
            </div>
            <div class="doc-actions">
                <a href="{{ route('admin.health_records') }}" class="doc-btn doc-btn-back">
                    <x-outline-icon name="arrow-left-on-rectangle" />
                    Back
                </a>
                @if($isSuperAdmin)
                    <a href="{{ route('admin.sign_page', $profile->id) }}" class="doc-btn doc-btn-verify">
                        <x-outline-icon name="check" />
                        Verify / Approve
                    </a>
                @endif
            </div>
        </div>

        <div class="doc-meta">
            <div class="doc-meta-item">
                <div class="doc-meta-k">Student</div>
                <div class="doc-meta-v">{{ $profile->user->name ?? 'N/A' }}</div>
            </div>
            <div class="doc-meta-item">
                <div class="doc-meta-k">Student Number</div>
                <div class="doc-meta-v">{{ $profile->user->student_number ?: ($profile->user->student_id ?? 'N/A') }}</div>
            </div>
            <div class="doc-meta-item">
                <div class="doc-meta-k">Course</div>
                <div class="doc-meta-v">{{ $profile->course_college ?: ($profile->user->course ?? 'N/A') }}</div>
            </div>
            <div class="doc-meta-item">
                <div class="doc-meta-k">Status</div>
                <div class="doc-meta-v">{{ in_array($profile->clearance_status, ['Pending', 'For Verification'], true) ? 'For Verification' : ($profile->clearance_status ?: 'Not Processed') }}</div>
            </div>
        </div>
    </div>

    <div class="doc-review-card">
        <div class="doc-grid">
            <div class="doc-file">
                <h4>Health Form Upload (PDF)</h4>
                @if(!empty($profile->health_form_upload))
                    <div class="doc-actions">
                        <a class="doc-link" href="{{ asset('storage/' . $profile->health_form_upload) }}" target="_blank" rel="noopener">
                            <x-outline-icon name="document-text" />
                            Open
                        </a>
                    </div>
                    <div class="doc-preview">
                        <iframe src="{{ asset('storage/' . $profile->health_form_upload) }}"></iframe>
                    </div>
                @else
                    <div class="doc-missing">No health form upload found.</div>
                @endif
            </div>

            <div class="doc-file">
                <h4>Medical Certificate (PDF)</h4>
                @if(!empty($profile->medical_certificate))
                    <div class="doc-actions">
                        <a class="doc-link" href="{{ asset('storage/' . $profile->medical_certificate) }}" target="_blank" rel="noopener">
                            <x-outline-icon name="document-text" />
                            Open
                        </a>
                    </div>
                    <div class="doc-preview">
                        <iframe src="{{ asset('storage/' . $profile->medical_certificate) }}"></iframe>
                    </div>
                @else
                    <div class="doc-missing">No medical certificate uploaded.</div>
                @endif
            </div>

            <div class="doc-file">
                <h4>Chest X-ray Result (PDF)</h4>
                @if(!empty($profile->chest_xray_result))
                    <div class="doc-actions">
                        <a class="doc-link" href="{{ asset('storage/' . $profile->chest_xray_result) }}" target="_blank" rel="noopener">
                            <x-outline-icon name="document-text" />
                            Open
                        </a>
                    </div>
                    <div class="doc-preview">
                        <iframe src="{{ asset('storage/' . $profile->chest_xray_result) }}"></iframe>
                    </div>
                @else
                    <div class="doc-missing">No chest X-ray result uploaded.</div>
                @endif
            </div>

            <div class="doc-file">
                <h4>PWD ID Proof (PDF)</h4>
                @if(($profile->has_disability ?? 'No') !== 'Yes')
                    <div class="doc-missing">Not required (PWD is set to No).</div>
                @elseif(!empty($profile->pwd_id_proof))
                    <div class="doc-actions">
                        <a class="doc-link" href="{{ asset('storage/' . $profile->pwd_id_proof) }}" target="_blank" rel="noopener">
                            <x-outline-icon name="document-text" />
                            Open
                        </a>
                    </div>
                    <div class="doc-preview">
                        <iframe src="{{ asset('storage/' . $profile->pwd_id_proof) }}"></iframe>
                    </div>
                @else
                    <div class="doc-missing">PWD is Yes but no proof uploaded.</div>
                @endif
            </div>

            <div class="doc-file">
                <h4>2x2 Student Photo</h4>
                @if(!empty($profile->student_photo))
                    <div class="doc-actions">
                        <a class="doc-link" href="{{ asset('storage/' . $profile->student_photo) }}" target="_blank" rel="noopener">
                            <x-outline-icon name="eye" />
                            Open
                        </a>
                    </div>
                    <div class="doc-preview">
                        <img src="{{ asset('storage/' . $profile->student_photo) }}" alt="2x2 Student Photo">
                    </div>
                @else
                    <div class="doc-missing">No 2x2 student photo uploaded.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
