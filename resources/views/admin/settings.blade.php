@extends('layouts.admin')

@section('title', 'Settings')

@push('styles')
<style>
    :root {
        --settings-maroon: #7f0000;
        --settings-maroon-deep: #4f0000;
        --settings-maroon-soft: rgba(127, 0, 0, 0.08);
        --settings-surface: rgba(255, 255, 255, 0.88);
        --settings-surface-strong: #ffffff;
        --settings-border: rgba(127, 0, 0, 0.12);
        --settings-text: #111827;
        --settings-muted: #64748b;
        --settings-shadow: 0 24px 70px rgba(15, 23, 42, 0.10);
    }

    /* Cards */
    .card {
        position: relative;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 255, 255, 0.90)),
            linear-gradient(135deg, rgba(127, 0, 0, 0.04), transparent 45%);
        border-radius: 28px;
        padding: 30px;
        box-shadow: var(--settings-shadow);
        border: 1px solid var(--settings-border);
        margin-bottom: 24px;
        overflow: hidden;
        backdrop-filter: blur(8px);
    }
    .card::before {
        content: '';
        position: absolute;
        inset: 0 auto auto 0;
        width: 100%;
        height: 5px;
        background: linear-gradient(90deg, var(--settings-maroon), #a61a2b 50%, #d4a373);
    }
    .card,
    .card *:not(.pill-status):not(.btn-save) {
        color: var(--settings-text);
    }
    .card h3 {
        margin-top: 0;
        color: var(--settings-maroon);
        margin-bottom: 20px;
        font-size: 19px;
        font-weight: 800;
        letter-spacing: 0.01em;
    }
    .profile-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px 20px; }
    .profile-grid-wide { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px 20px; }
    .profile-note {
        padding: 15px 18px;
        border-radius: 18px;
        background: linear-gradient(180deg, #fff, #f8fafc);
        color: var(--settings-text);
        font-size: 13px;
        line-height: 1.6;
        margin-bottom: 18px;
        border: 1px solid rgba(127, 0, 0, 0.10);
    }
    .pill-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        text-transform: capitalize;
        letter-spacing: 0.02em;
    }
    .pill-status.active { background: #dcfce7; color: #166534; }
    .pill-status.inactive { background: #fee2e2; color: #991b1b; }
    .pill-status.pending { background: #e2e8f0; color: #334155; }
    .readonly-helper { font-size: 12px; color: var(--settings-muted); margin-top: 6px; }

    /* Forms */
    .form-group { margin-bottom: 16px; }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--settings-muted);
    }
    .form-control {
        width: 100%;
        min-height: 50px;
        padding: 13px 15px;
        border: 1px solid rgba(127, 0, 0, 0.16);
        border-radius: 16px;
        font-size: 14px;
        color: var(--settings-text);
        background: var(--settings-surface-strong);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease, background 0.2s ease;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.75), 0 1px 0 rgba(15, 23, 42, 0.03);
    }
    .form-control:hover {
        border-color: rgba(127, 0, 0, 0.28);
    }
    .form-control:focus {
        border-color: var(--settings-maroon);
        box-shadow: 0 0 0 5px rgba(127, 0, 0, 0.13);
        outline: none;
        transform: translateY(-1px);
    }
    .form-control:disabled {
        background: #f8fafc;
        color: #64748b;
        cursor: not-allowed;
        box-shadow: none;
    }

    /* Switches */
    .switch-row {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 12px;
        padding: 15px 18px;
        border-radius: 18px;
        transition: 0.2s ease;
        background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,250,252,0.9));
        border: 1px solid rgba(127, 0, 0, 0.10);
        box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
    }
    .switch-row:hover {
        background: rgba(255, 255, 255, 0.96);
        border-color: rgba(127, 0, 0, 0.18);
        transform: translateY(-1px) scale(1.005);
    }
    .switch-row input { width: 18px; height: 18px; accent-color: var(--settings-maroon); cursor: pointer; }
    .switch-label { font-size: 14px; font-weight: 700; color: var(--settings-text); cursor: pointer; flex: 1; }

    /* Buttons */
    .btn-save {
        background: linear-gradient(135deg, var(--settings-maroon), #9a1010 48%, var(--settings-maroon-deep));
        color: white;
        padding: 12px 24px;
        border-radius: 16px;
        border: none;
        font-weight: 800;
        letter-spacing: 0.01em;
        cursor: pointer;
        box-shadow: 0 14px 26px rgba(127, 0, 0, 0.22), inset 0 1px 0 rgba(255,255,255,0.18);
        transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease, background 0.2s ease;
    }
    .btn-save:hover {
        filter: brightness(1.03);
        transform: translateY(-1px);
        box-shadow: 0 18px 34px rgba(127, 0, 0, 0.28), inset 0 1px 0 rgba(255,255,255,0.22);
    }
    .btn-save:active {
        transform: translateY(0);
    }
    
    .btn-edit {
        background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,250,252,0.92));
        color: var(--settings-maroon);
        padding: 10px 16px;
        border-radius: 14px;
        border: 1px solid rgba(127, 0, 0, 0.14);
        font-weight: 800;
        cursor: pointer;
        font-size: 13px;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }
    .btn-edit:hover {
        background: #fff;
        transform: translateY(-1px);
        box-shadow: 0 12px 22px rgba(15, 23, 42, 0.10);
    }

    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        padding: 24px 16px;
        background: rgba(15, 23, 42, 0.62);
        backdrop-filter: blur(10px);
        z-index: 1000;
        justify-content: center;
        align-items: flex-start;
        overflow-y: auto;
    }
    .modal-box {
        background: radial-gradient(circle at top right, rgba(127, 0, 0, 0.06), transparent 24%), linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(249, 250, 251, 0.96));
        padding: 0;
        border-radius: 28px;
        width: min(960px, 100%);
        max-width: 96vw;
        max-height: calc(100vh - 48px);
        overflow-y: auto;
        box-shadow: 0 32px 90px rgba(15, 23, 42, 0.32);
        border: 1px solid rgba(255, 255, 255, 0.78);
    }
    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 24px 26px 18px;
        margin-bottom: 0;
        border-bottom: 1px solid rgba(127, 0, 0, 0.10);
        background: linear-gradient(180deg, rgba(255,255,255,0.99), rgba(250,250,252,0.96));
        position: sticky;
        top: 0;
        z-index: 1;
    }
    .modal-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: var(--settings-maroon);
    }
    .modal-body { padding: 26px; }
    .modal-actions {
        position: sticky;
        bottom: 0;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
        margin-left: 0;
        margin-right: 0;
        margin-bottom: 0;
        padding: 18px 26px 26px;
        background: linear-gradient(to top, rgba(255,255,255,0.98) 74%, rgba(255,255,255,0.88) 90%, rgba(255,255,255,0));
        border-top: 1px solid rgba(127, 0, 0, 0.10);
        border-radius: 0 0 28px 28px;
    }
    
    /* Alerts */
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

    @media (max-width: 768px) {
        .profile-grid,
        .profile-grid-wide {
            grid-template-columns: 1fr;
        }

        .modal-overlay {
            padding: 12px;
        }

        .modal-box {
            padding: 18px;
            max-height: calc(100vh - 24px);
        }

        .modal-actions {
            bottom: -18px;
            margin-left: -18px;
            margin-right: -18px;
            margin-bottom: -18px;
            padding: 14px 18px 18px;
            flex-wrap: wrap;
        }

        .modal-actions button {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3>CMS Admin Profile</h3>
            <button class="btn-edit" onclick="openProfileModal()">Edit Profile</button>
        </div>

        <div class="profile-grid-wide">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['first_name'] ?? 'N/A' }}" disabled>
            </div>
            <div class="form-group">
                <label>Middle Name</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['middle_name'] ?? 'N/A' }}" disabled>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['last_name'] ?? 'N/A' }}" disabled>
            </div>
            <div class="form-group">
                <label>Suffix Name</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['suffix_name'] ?? 'N/A' }}" disabled>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" value="{{ $cmsProfile['email'] ?? ($admin->email ?? '') }}" disabled>
            </div>
            <div class="form-group">
                <label>Birthday</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['birthday'] ?? 'N/A' }}" disabled>
            </div>
            <div class="form-group">
                <label>Age</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['age'] ?? 'N/A' }}" disabled>
            </div>
            <div class="form-group">
                <label>Gender</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['gender'] ?? 'N/A' }}" disabled>
            </div>
            <div class="form-group">
                <label>Civil Status</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['civil_status'] ?? 'N/A' }}" disabled>
            </div>
            <div class="form-group">
                <label>Emergency Contact Person</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['emergency_contact_person'] ?? 'N/A' }}" disabled>
            </div>
            <div class="form-group">
                <label>Emergency Contact No.</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['emergency_contact_no'] ?? ($cmsProfile['contact_number'] ?? 'N/A') }}" disabled>
            </div>
            <div class="form-group">
                <label>Office</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['office'] ?? 'Admission Office' }}" disabled>
            </div>
            <div class="form-group">
                <label>Status</label>
                <input type="text" class="form-control" value="{{ ucfirst($cmsProfile['status'] ?? 'active') }}" disabled>
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Address</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['address'] ?? 'N/A' }}" disabled>
            </div>
        </div>
    </section>

    <form action="{{ url('/admin/settings/update') }}" method="POST">
        @csrf @method('PUT')
        
        <section class="card">
            <h3>Clinic Information</h3>

            <div class="form-group">
                <label>Clinic Name</label>
                <input type="text" name="clinic_name" class="form-control" value="{{ $settings->clinic_name }}">
            </div>

            <div class="form-group">
                <label>Location</label>
                <input type="text" name="clinic_location" class="form-control" value="{{ $settings->clinic_location }}">
            </div>
        </section>

        <section class="card">
            <h3>Clinic Hours</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Opening Time</label>
                    <input type="time" name="open_time" class="form-control" value="{{ $settings->open_time }}">
                </div>
                <div class="form-group">
                    <label>Closing Time</label>
                    <input type="time" name="close_time" class="form-control" value="{{ $settings->close_time }}">
                </div>
            </div>
        </section>

        <section class="card">
            <h3>System Preferences</h3>

            <div class="switch-row">
                <input type="checkbox" name="email_notifications" id="emailNotif" {{ $settings->email_notifications ? 'checked' : '' }}>
                <label for="emailNotif" class="switch-label">Enable Email Notifications</label>
            </div>

            <div class="switch-row">
                <input type="checkbox" name="auto_approve" id="autoApprove" {{ $settings->auto_approve ? 'checked' : '' }}>
                <label for="autoApprove" class="switch-label">Auto-approve Student Requests</label>
            </div>
        </section>

        <div style="text-align: right;">
            <button type="submit" class="btn-save">Save System Settings</button>
        </div>
    </form>

    <div id="profileModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Edit Profile</h3>
            </div>
            
            <form action="{{ url('/admin/profile/update') }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">

                    <div class="profile-grid-wide">
                        <div class="form-group">
                            <label>Admin ID</label>
                            <input type="text" class="form-control" value="{{ !empty($cmsProfile['admin_id']) ? str_pad((string) $cmsProfile['admin_id'], 3, '0', STR_PAD_LEFT) : 'N/A' }}" disabled>
                        </div>

                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $cmsProfile['first_name'] ?? '') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $cmsProfile['middle_name'] ?? '') }}" placeholder="Middle name">
                        </div>

                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $cmsProfile['last_name'] ?? '') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Suffix Name</label>
                            <input type="text" name="suffix_name" class="form-control" value="{{ old('suffix_name', $cmsProfile['suffix_name'] ?? '') }}" placeholder="Jr., Sr., III">
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $cmsProfile['email'] ?? ($admin->email ?? '')) }}" required>
                        </div>
                    </div>

                    <div class="profile-grid">
                        <div class="form-group">
                            <label>Birthday</label>
                            <input type="date" id="cmsBirthdayInput" name="birthday" class="form-control" value="{{ old('birthday', $cmsProfile['birthday'] ?? '') }}">
                        </div>

                        <div class="form-group">
                            <label>Age</label>
                            <input type="text" id="cmsAgeInput" class="form-control" value="{{ old('age', $cmsProfile['age'] ?? '') }}" readonly>
                            <p class="readonly-helper">Auto-calculated from birthday.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $cmsProfile['address'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $cmsProfile['contact_number'] ?? '') }}">
                    </div>

                    <div class="profile-grid">
                        <div class="form-group">
                            <label>Gender</label>
                            <input type="text" name="gender" class="form-control" value="{{ old('gender', $cmsProfile['gender'] ?? '') }}">
                        </div>

                        <div class="form-group">
                            <label>Civil Status</label>
                            <input type="text" name="civil_status" class="form-control" value="{{ old('civil_status', $cmsProfile['civil_status'] ?? '') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Emergency Contact Person</label>
                        <input type="text" name="emergency_contact_person" class="form-control" value="{{ old('emergency_contact_person', $cmsProfile['emergency_contact_person'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Emergency Contact No.</label>
                        <input type="text" name="emergency_contact_no" class="form-control" value="{{ old('emergency_contact_no', $cmsProfile['emergency_contact_no'] ?? ($cmsProfile['contact_number'] ?? '')) }}">
                    </div>

                    <div class="form-group">
                        <label>Office</label>
                        <input type="text" name="office" class="form-control" value="{{ old('office', $cmsProfile['office'] ?? 'Admission Office') }}">
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active" {{ old('status', $cmsProfile['status'] ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $cmsProfile['status'] ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div style="border-top: 1px solid rgba(148, 163, 184, 0.18); margin: 18px 0; padding-top: 16px;">
                        <label style="font-size: 12px; color: #64748b; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase;">Change Password (Optional)</label>
                    </div>

                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Retype password">
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" onclick="closeProfileModal()" style="background: rgba(255,255,255,0.9); color:#334155; border:1px solid rgba(148,163,184,0.22); padding: 10px 18px; border-radius: 12px; cursor: pointer; font-weight: 700;">Cancel</button>
                    <button type="submit" class="btn-save">Save Profile</button>
                </div>
            </form>
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
    window.onclick = function(e) {
        if(e.target == document.getElementById('profileModal')) closeProfileModal();
    }

    function syncCmsAge() {
        const birthdayInput = document.getElementById('cmsBirthdayInput');
        const ageInput = document.getElementById('cmsAgeInput');

        if (!birthdayInput || !ageInput) {
            return;
        }

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
