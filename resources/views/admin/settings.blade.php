@extends('layouts.admin')

@section('title', 'Settings')

@push('styles')
<style>
    :root {
        --stg-maroon: #7f0000;
        --stg-maroon-deep: #4f0000;
        --stg-text: #111827;
        --stg-muted: #64748b;
        --stg-border: rgba(127, 0, 0, 0.12);
        --stg-surface: rgba(255, 255, 255, 0.94);
    }

    .settings-page {
        position: relative;
    }
    .settings-page::before {
        content: '';
        position: fixed;
        inset: 0;
        background:
            radial-gradient(circle at top left, rgba(127, 0, 0, 0.08), transparent 24%),
            radial-gradient(circle at bottom right, rgba(15, 23, 42, 0.06), transparent 24%),
            linear-gradient(180deg, rgba(248, 250, 252, 0.96), rgba(255, 255, 255, 0.92));
        pointer-events: none;
        z-index: 0;
    }
    .settings-page > * {
        position: relative;
        z-index: 1;
    }

    .hero {
        background: linear-gradient(135deg, rgba(127, 0, 0, 0.98), rgba(79, 0, 0, 0.94));
        color: #fff;
        border-radius: 28px;
        padding: 28px;
        margin-bottom: 22px;
        box-shadow: 0 24px 60px rgba(127, 0, 0, 0.18);
    }
    .hero-top {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        align-items: flex-start;
        flex-wrap: wrap;
    }
    .hero h1 {
        margin: 0;
        font-size: clamp(28px, 3vw, 36px);
        line-height: 1.05;
        font-weight: 900;
        letter-spacing: -0.03em;
    }
    .hero p {
        margin: 12px 0 0;
        max-width: 760px;
        color: rgba(255,255,255,0.82);
        line-height: 1.7;
        font-size: 14px;
    }
    .hero-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .hero-btn {
        border: 1px solid rgba(255,255,255,0.18);
        background: rgba(255,255,255,0.12);
        color: #fff;
        padding: 11px 16px;
        border-radius: 14px;
        font-weight: 800;
        cursor: pointer;
    }
    .hero-btn.primary {
        background: #fff;
        color: var(--stg-maroon);
    }
    .badges {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 18px;
    }
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 12px;
        border-radius: 999px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.15);
        font-size: 12px;
        font-weight: 800;
    }
    .badge span {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #f6c36a;
    }

    .grid {
        display: grid;
        grid-template-columns: 1.05fr 1.25fr;
        gap: 22px;
    }

    .panel {
        position: relative;
        background: var(--stg-surface);
        border: 1px solid var(--stg-border);
        border-radius: 26px;
        box-shadow: 0 24px 70px rgba(15,23,42,0.10);
        overflow: hidden;
    }
    .panel::before {
        content: '';
        position: absolute;
        inset: 0 auto auto 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--stg-maroon), #ad2234 55%, #d4a373);
    }
    .panel-head {
        padding: 22px 24px 16px;
        border-bottom: 1px solid rgba(127,0,0,0.08);
    }
    .panel-head h3 {
        margin: 0;
        font-size: 18px;
        color: var(--stg-maroon);
        font-weight: 900;
    }
    .panel-head p {
        margin: 6px 0 0;
        color: var(--stg-muted);
        line-height: 1.6;
        font-size: 13px;
    }
    .panel-body {
        padding: 24px;
    }

    .profile-top {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        margin-bottom: 14px;
    }
    .profile-name {
        margin: 0;
        font-size: 16px;
        font-weight: 900;
        color: var(--stg-text);
    }
    .profile-role {
        padding: 7px 10px;
        border-radius: 999px;
        color: #fff;
        background: linear-gradient(135deg, var(--stg-maroon), var(--stg-maroon-deep));
        font-size: 11px;
        font-weight: 900;
    }
    .profile-list {
        display: grid;
        gap: 10px;
    }
    .profile-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 16px;
        background: rgba(255,255,255,0.90);
        border: 1px solid rgba(148,163,184,0.16);
    }
    .profile-row .key {
        color: var(--stg-muted);
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .profile-row .val {
        color: var(--stg-text);
        font-size: 13px;
        font-weight: 700;
        text-align: right;
    }

    .field-grid {
        display: grid;
        gap: 16px;
    }
    .field-grid.two { grid-template-columns: repeat(2, minmax(0,1fr)); }
    .field-grid.three { grid-template-columns: repeat(3, minmax(0,1fr)); }

    .field {
        position: relative;
        padding: 14px 14px 12px;
        border-radius: 22px;
        background: linear-gradient(180deg, rgba(255,255,255,0.88), rgba(248,250,252,0.72));
        border: 1px solid rgba(127,0,0,0.08);
        box-shadow: 0 12px 28px rgba(15,23,42,0.06);
        backdrop-filter: blur(8px);
    }
    .field label {
        position: absolute;
        left: 14px;
        top: 0;
        transform: translateY(-10px);
        padding: 0 12px;
        min-height: 26px;
        border-radius: 999px;
        border: 1px solid rgba(127,0,0,0.10);
        background: linear-gradient(180deg, rgba(255,255,255,0.99), rgba(249,250,252,0.98));
        color: #5f6677;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        pointer-events: none;
        box-shadow: 0 8px 18px rgba(15,23,42,0.08);
    }
    .field:focus-within label {
        color: var(--stg-maroon);
        border-color: rgba(127,0,0,0.20);
    }
    .field input,
    .field select {
        width: 100%;
        min-height: 60px;
        padding: 22px 16px 14px;
        border-radius: 18px;
        border: 1px solid rgba(127,0,0,0.10);
        background: linear-gradient(180deg, rgba(255,255,255,0.99), rgba(250,251,252,0.96));
        color: var(--stg-text);
        box-shadow: 0 14px 32px rgba(15,23,42,0.08), inset 0 1px 0 rgba(255,255,255,0.96);
        transition: 0.2s ease;
        appearance: none;
    }
    .field input:hover,
    .field select:hover {
        transform: translateY(-1px);
        box-shadow: 0 16px 34px rgba(15,23,42,0.10), inset 0 1px 0 rgba(255,255,255,0.98);
    }
    .field input:focus,
    .field select:focus {
        outline: none;
        border-color: var(--stg-maroon);
        background: #fff;
        box-shadow: 0 0 0 6px rgba(127,0,0,0.10), 0 20px 40px rgba(15,23,42,0.12), 0 0 28px rgba(127,0,0,0.10);
        transform: translateY(-2px);
    }
    .field input::placeholder { color: #94a3b8; }
    .field input:disabled {
        cursor: not-allowed;
        background: linear-gradient(180deg, #f8fafc, #eef2f7);
        color: #64748b;
    }
    .field select {
        padding-right: 44px;
        background-image:
            linear-gradient(45deg, transparent 50%, #7f0000 50%),
            linear-gradient(135deg, #7f0000 50%, transparent 50%);
        background-position:
            calc(100% - 20px) calc(50% - 3px),
            calc(100% - 14px) calc(50% - 3px);
        background-size: 6px 6px, 6px 6px;
        background-repeat: no-repeat;
    }
    .field-help {
        margin: 8px 0 0 6px;
        font-size: 12px;
        color: var(--stg-muted);
    }

    .switch-list {
        display: grid;
        gap: 14px;
    }
    .switch-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        border-radius: 20px;
        background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,250,252,0.92));
        border: 1px solid rgba(127,0,0,0.10);
        box-shadow: 0 12px 28px rgba(15,23,42,0.05);
    }
    .switch-item input {
        width: 18px;
        height: 18px;
        accent-color: var(--stg-maroon);
    }
    .switch-item label {
        position: static;
        transform: none;
        background: transparent;
        border: none;
        box-shadow: none;
        pointer-events: auto;
        padding: 0;
        min-height: auto;
        text-transform: none;
        letter-spacing: 0;
        font-size: 14px;
        color: var(--stg-text);
    }
    .actions-row {
        display: flex;
        justify-content: flex-end;
        margin-top: 18px;
    }
    .btn-save {
        background: linear-gradient(135deg, var(--stg-maroon), #9a1010 48%, var(--stg-maroon-deep));
        color: #fff;
        padding: 13px 24px;
        border: none;
        border-radius: 16px;
        font-weight: 900;
        box-shadow: 0 16px 30px rgba(127,0,0,0.22);
        cursor: pointer;
    }

    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        padding: 24px 16px;
        background: rgba(15,23,42,0.62);
        backdrop-filter: blur(10px);
        z-index: 1000;
        justify-content: center;
        align-items: flex-start;
        overflow-y: auto;
    }
    .modal-box {
        width: min(980px, 100%);
        max-width: 96vw;
        border-radius: 28px;
        overflow: hidden;
        background: linear-gradient(180deg, rgba(255,255,255,0.99), rgba(249,250,251,0.96));
        border: 1px solid rgba(255,255,255,0.76);
        box-shadow: 0 34px 90px rgba(15,23,42,0.32);
    }
    .modal-head {
        padding: 24px 26px 18px;
        border-bottom: 1px solid rgba(127,0,0,0.10);
    }
    .modal-head h3 {
        margin: 0;
        color: var(--stg-maroon);
        font-size: 20px;
        font-weight: 900;
    }
    .modal-head p {
        margin: 6px 0 0;
        color: var(--stg-muted);
        font-size: 13px;
        line-height: 1.6;
    }
    .modal-body { padding: 26px; }
    .modal-actions {
        position: sticky;
        bottom: 0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 18px 26px 26px;
        background: linear-gradient(to top, rgba(255,255,255,0.98) 74%, rgba(255,255,255,0.88) 90%, rgba(255,255,255,0));
        border-top: 1px solid rgba(127,0,0,0.10);
    }
    .btn-cancel {
        padding: 12px 18px;
        border-radius: 14px;
        border: 1px solid rgba(148,163,184,0.22);
        background: rgba(255,255,255,0.92);
        color: #334155;
        font-weight: 800;
        cursor: pointer;
    }

    .alert {
        padding: 14px 16px;
        border-radius: 16px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 700;
        border: 1px solid transparent;
    }
    .alert-success { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }
    .alert-error { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }

    @media (max-width: 1080px) {
        .grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .hero { padding: 24px 20px; }
        .panel-body, .modal-body, .modal-head { padding-left: 18px; padding-right: 18px; }
        .field-grid.two, .field-grid.three { grid-template-columns: 1fr; }
        .modal-overlay { padding: 12px; }
        .modal-actions { padding: 14px 18px 18px; flex-wrap: wrap; }
        .modal-actions button { width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="settings-page">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="hero">
        <div class="hero-top">
            <div>
                <h1>Settings</h1>
                <p>Manage the clinic identity, operating hours, and system preferences from one modern control panel.</p>
                <div class="badges">
                    <div class="badge"><span></span> CMS Admin Profile</div>
                    <div class="badge"><span></span> Clinic Operations</div>
                    <div class="badge"><span></span> System Preferences</div>
                </div>
            </div>
            <div class="hero-actions">
                <button class="hero-btn primary" onclick="openProfileModal()">Edit Profile</button>
            </div>
        </div>
    </section>

    <div class="grid">
        <section class="panel">
            <div class="panel-head">
                <h3>CMS Admin Profile</h3>
                <p>Read-only hub profile for the current clinic administrator.</p>
            </div>
            <div class="panel-body">
                <div class="profile-top">
                    <div>
                        <p class="profile-name">{{ $cmsProfile['first_name'] ?? 'N/A' }} {{ $cmsProfile['last_name'] ?? '' }}</p>
                        <div class="section-subtitle" style="margin-top:4px; color:var(--stg-muted);">{{ $cmsProfile['email'] ?? ($admin->email ?? 'N/A') }}</div>
                    </div>
                    <div class="profile-role">{{ ucfirst($cmsProfile['status'] ?? 'active') }}</div>
                </div>
                <div class="profile-list">
                    <div class="profile-row"><div class="key">Admin ID</div><div class="val">{{ !empty($cmsProfile['admin_id']) ? str_pad((string) $cmsProfile['admin_id'], 3, '0', STR_PAD_LEFT) : 'N/A' }}</div></div>
                    <div class="profile-row"><div class="key">Middle Name</div><div class="val">{{ $cmsProfile['middle_name'] ?? 'N/A' }}</div></div>
                    <div class="profile-row"><div class="key">Suffix</div><div class="val">{{ $cmsProfile['suffix_name'] ?? 'N/A' }}</div></div>
                    <div class="profile-row"><div class="key">Birthday</div><div class="val">{{ $cmsProfile['birthday'] ?? 'N/A' }}</div></div>
                    <div class="profile-row"><div class="key">Age</div><div class="val">{{ $cmsProfile['age'] ?? 'N/A' }}</div></div>
                    <div class="profile-row"><div class="key">Gender</div><div class="val">{{ $cmsProfile['gender'] ?? 'N/A' }}</div></div>
                    <div class="profile-row"><div class="key">Civil Status</div><div class="val">{{ $cmsProfile['civil_status'] ?? 'N/A' }}</div></div>
                    <div class="profile-row"><div class="key">Office</div><div class="val">{{ $cmsProfile['office'] ?? 'Admission Office' }}</div></div>
                    <div class="profile-row"><div class="key">Emergency Contact</div><div class="val">{{ $cmsProfile['emergency_contact_person'] ?? 'N/A' }}</div></div>
                    <div class="profile-row"><div class="key">Contact No.</div><div class="val">{{ $cmsProfile['emergency_contact_no'] ?? ($cmsProfile['contact_number'] ?? 'N/A') }}</div></div>
                    <div class="profile-row"><div class="key">Address</div><div class="val">{{ $cmsProfile['address'] ?? 'N/A' }}</div></div>
                </div>
            </div>
        </section>

        <div style="display:grid; gap:22px;">
            <form action="{{ url('/admin/settings/update') }}" method="POST">
                @csrf @method('PUT')
                <section class="panel">
                    <div class="panel-head">
                        <h3>Clinic Information</h3>
                        <p>Update the clinic name and location shown throughout the system.</p>
                    </div>
                    <div class="panel-body">
                        <div class="field-grid">
                            <div class="field">
                                <label>Clinic Name</label>
                                <input type="text" name="clinic_name" value="{{ $settings->clinic_name }}" placeholder="Clinic name">
                            </div>
                            <div class="field">
                                <label>Location</label>
                                <input type="text" name="clinic_location" value="{{ $settings->clinic_location }}" placeholder="Clinic location">
                            </div>
                        </div>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-head">
                        <h3>Clinic Hours</h3>
                        <p>Set the daily opening and closing time for the clinic.</p>
                    </div>
                    <div class="panel-body">
                        <div class="field-grid two">
                            <div class="field">
                                <label>Opening Time</label>
                                <input type="time" name="open_time" value="{{ $settings->open_time }}">
                            </div>
                            <div class="field">
                                <label>Closing Time</label>
                                <input type="time" name="close_time" value="{{ $settings->close_time }}">
                            </div>
                        </div>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-head">
                        <h3>System Preferences</h3>
                        <p>Control reminder and auto-approval behavior for the clinic workflow.</p>
                    </div>
                    <div class="panel-body">
                        <div class="switch-list">
                            <div class="switch-item">
                                <input type="checkbox" name="email_notifications" id="emailNotif" {{ $settings->email_notifications ? 'checked' : '' }}>
                                <label for="emailNotif">Enable Email Notifications</label>
                            </div>
                            <div class="switch-item">
                                <input type="checkbox" name="auto_approve" id="autoApprove" {{ $settings->auto_approve ? 'checked' : '' }}>
                                <label for="autoApprove">Auto-approve Student Requests</label>
                            </div>
                        </div>
                        <div class="actions-row">
                            <button type="submit" class="btn-save">Save System Settings</button>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </div>

    <div id="profileModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-head">
                <h3>Edit Profile</h3>
                <p>Keep your admin identity and clinic contact details aligned with the hub record.</p>
            </div>

            <form action="{{ url('/admin/profile/update') }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="field-grid three">
                        <div class="field">
                            <label>Admin ID</label>
                            <input type="text" value="{{ !empty($cmsProfile['admin_id']) ? str_pad((string) $cmsProfile['admin_id'], 3, '0', STR_PAD_LEFT) : 'N/A' }}" disabled>
                        </div>
                        <div class="field">
                            <label>First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $cmsProfile['first_name'] ?? '') }}" required>
                        </div>
                        <div class="field">
                            <label>Middle Name</label>
                            <input type="text" name="middle_name" value="{{ old('middle_name', $cmsProfile['middle_name'] ?? '') }}" placeholder="Middle name">
                        </div>
                        <div class="field">
                            <label>Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $cmsProfile['last_name'] ?? '') }}" required>
                        </div>
                        <div class="field">
                            <label>Suffix Name</label>
                            <input type="text" name="suffix_name" value="{{ old('suffix_name', $cmsProfile['suffix_name'] ?? '') }}" placeholder="Jr., Sr., III">
                        </div>
                        <div class="field">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ old('email', $cmsProfile['email'] ?? ($admin->email ?? '')) }}" required>
                        </div>
                    </div>

                    <div class="field-grid two" style="margin-top:16px;">
                        <div class="field">
                            <label>Birthday</label>
                            <input type="date" id="cmsBirthdayInput" name="birthday" value="{{ old('birthday', $cmsProfile['birthday'] ?? '') }}">
                        </div>
                        <div class="field">
                            <label>Age</label>
                            <input type="text" id="cmsAgeInput" value="{{ old('age', $cmsProfile['age'] ?? '') }}" readonly>
                            <p class="field-help">Auto-calculated from birthday.</p>
                        </div>
                    </div>

                    <div class="field-grid two">
                        <div class="field">
                            <label>Address</label>
                            <input type="text" name="address" value="{{ old('address', $cmsProfile['address'] ?? '') }}" placeholder="Complete address">
                        </div>
                        <div class="field">
                            <label>Contact Number</label>
                            <input type="text" name="contact_number" value="{{ old('contact_number', $cmsProfile['contact_number'] ?? '') }}" placeholder="Contact number">
                        </div>
                    </div>

                    <div class="field-grid two">
                        <div class="field">
                            <label>Gender</label>
                            <input type="text" name="gender" value="{{ old('gender', $cmsProfile['gender'] ?? '') }}" placeholder="Gender">
                        </div>
                        <div class="field">
                            <label>Civil Status</label>
                            <input type="text" name="civil_status" value="{{ old('civil_status', $cmsProfile['civil_status'] ?? '') }}" placeholder="Civil status">
                        </div>
                    </div>

                    <div class="field-grid two">
                        <div class="field">
                            <label>Emergency Contact Person</label>
                            <input type="text" name="emergency_contact_person" value="{{ old('emergency_contact_person', $cmsProfile['emergency_contact_person'] ?? '') }}" placeholder="Emergency contact person">
                        </div>
                        <div class="field">
                            <label>Emergency Contact No.</label>
                            <input type="text" name="emergency_contact_no" value="{{ old('emergency_contact_no', $cmsProfile['emergency_contact_no'] ?? ($cmsProfile['contact_number'] ?? '')) }}" placeholder="Emergency contact number">
                        </div>
                    </div>

                    <div class="field-grid two">
                        <div class="field">
                            <label>Office</label>
                            <input type="text" name="office" value="{{ old('office', $cmsProfile['office'] ?? 'Admission Office') }}" placeholder="Office">
                        </div>
                        <div class="field">
                            <label>Status</label>
                            <select name="status">
                                <option value="active" {{ old('status', $cmsProfile['status'] ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $cmsProfile['status'] ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin: 6px 0 4px; padding-top: 10px; border-top: 1px solid rgba(127,0,0,0.10);">
                        <p class="field-help" style="margin:0; font-weight:900; letter-spacing:0.06em; text-transform:uppercase;">Change Password (Optional)</p>
                    </div>

                    <div class="field-grid two">
                        <div class="field">
                            <label>New Password</label>
                            <input type="password" name="password" placeholder="Leave blank to keep current">
                        </div>
                        <div class="field">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" placeholder="Retype password">
                        </div>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeProfileModal()">Cancel</button>
                    <button type="submit" class="btn-save">Save Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openProfileModal() {
        document.getElementById('profileModal').style.display = 'flex';
        syncCmsAge();
    }

    function closeProfileModal() {
        document.getElementById('profileModal').style.display = 'none';
    }

    window.addEventListener('click', function (e) {
        if (e.target === document.getElementById('profileModal')) {
            closeProfileModal();
        }
    });

    function syncCmsAge() {
        const birthdayInput = document.getElementById('cmsBirthdayInput');
        const ageInput = document.getElementById('cmsAgeInput');

        if (!birthdayInput || !ageInput) return;

        if (!birthdayInput.value) {
            ageInput.value = '';
            return;
        }

        const birthday = new Date(birthdayInput.value + 'T00:00:00');
        if (Number.isNaN(birthday.getTime())) {
            ageInput.value = '';
            return;
        }

        const today = new Date();
        let age = today.getFullYear() - birthday.getFullYear();
        const monthDiff = today.getMonth() - birthday.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthday.getDate())) {
            age -= 1;
        }
        ageInput.value = age >= 0 ? age : '';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const birthdayInput = document.getElementById('cmsBirthdayInput');
        if (birthdayInput) {
            birthdayInput.addEventListener('change', syncCmsAge);
            syncCmsAge();
        }
    });
</script>
@endpush
