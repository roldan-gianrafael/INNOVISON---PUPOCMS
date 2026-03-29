@extends('layouts.admin')

@section('title', 'Settings')

@push('styles')
<style>
    /* Cards */
    .card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
        margin-bottom: 24px;
    }
    .card h3 { margin-top: 0; color: #8B0000; margin-bottom: 20px; font-size: 18px; }
    .profile-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 18px; }
    .profile-note { padding: 12px 14px; border-radius: 10px; background: #f8fafc; color: #64748b; font-size: 13px; line-height: 1.5; margin-bottom: 16px; }
    .pill-status { display:inline-flex; align-items:center; gap:8px; padding:6px 10px; border-radius:999px; font-size:12px; font-weight:700; text-transform:capitalize; }
    .pill-status.active { background:#dcfce7; color:#166534; }
    .pill-status.inactive { background:#fee2e2; color:#991b1b; }
    .pill-status.pending { background:#e2e8f0; color:#334155; }
    .readonly-helper { font-size: 12px; color: #94a3b8; margin-top: 4px; }

    /* Forms */
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; margin-bottom: 6px; font-size: 13px; font-weight: 700; color: #64748b; }
    .form-control { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; color: #334155; }
    .form-control:disabled { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; }

    /* Switches */
    .switch-row { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; padding: 10px; border-radius: 8px; transition: 0.2s; }
    .switch-row:hover { background: #f8fafc; }
    .switch-row input { width: 16px; height: 16px; accent-color: #8B0000; cursor: pointer; }
    .switch-label { font-size: 14px; font-weight: 600; color: #334155; cursor: pointer; flex: 1; }

    /* Buttons */
    .btn-save { background: #8B0000; color: white; padding: 10px 20px; border-radius: 8px; border: none; font-weight: 700; cursor: pointer; }
    .btn-save:hover { background: #600000; }
    
    .btn-edit { background: #e2e8f0; color: #334155; padding: 8px 16px; border-radius: 6px; border: none; font-weight: 600; cursor: pointer; font-size: 13px; }
    .btn-edit:hover { background: #cbd5e1; }

    /* Modal */
    .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
    .modal-box { background: #fff; padding: 24px; border-radius: 12px; width: 450px; max-width: 90%; }
    
    /* Alerts */
    .alert { padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 600; }
    .alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .alert-error { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

    @media (max-width: 768px) {
        .profile-grid {
            grid-template-columns: 1fr;
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

        

        <div class="profile-grid">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['name'] ?? ($admin->name ?? 'Admin User') }}" disabled>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" value="{{ $cmsProfile['email'] ?? ($admin->email ?? '') }}" disabled>
            </div>

            <div class="form-group">
                <label>Suffix Name</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['suffix_name'] ?? 'N/A' }}" disabled>
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
                <label>Address</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['address'] ?? 'N/A' }}" disabled>
            </div>

            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['contact_number'] ?? 'N/A' }}" disabled>
            </div>

            <div class="form-group">
                <label>Role</label>
                <input type="text" class="form-control" value="{{ $cmsProfile['role'] ?? \App\Models\User::normalizeRole($admin->user_role ?? '') }}" disabled>
            </div>

            <div class="form-group">
                <label>Status</label>
                <div>
                    @php
                        $statusValue = strtolower((string) ($cmsProfile['status'] ?? 'pending'));
                        $statusClass = in_array($statusValue, ['active', 'inactive'], true) ? $statusValue : 'pending';
                    @endphp
                    <span class="pill-status {{ $statusClass }}">{{ $cmsProfile['status'] ?? 'Pending' }}</span>
                </div>
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
            <h3 style="margin-top:0;">Edit Profile</h3>
            
            <form action="{{ url('/admin/profile/update') }}" method="POST">
                @csrf @method('PUT')

                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $cmsProfile['name'] ?? ($admin->name ?? '')) }}" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $cmsProfile['email'] ?? ($admin->email ?? '')) }}" required>
                </div>

                <div class="form-group">
                    <label>Suffix Name</label>
                    <input type="text" name="suffix_name" class="form-control" value="{{ old('suffix_name', $cmsProfile['suffix_name'] ?? '') }}" placeholder="Jr., Sr., III">
                </div>

                <div class="form-group">
                    <label>Birthday</label>
                    <input type="date" id="cmsBirthdayInput" name="birthday" class="form-control" value="{{ old('birthday', $cmsProfile['birthday'] ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Age</label>
                    <input type="text" id="cmsAgeInput" class="form-control" value="{{ old('age', $cmsProfile['age'] ?? '') }}" readonly>
                    <p class="readonly-helper">Auto-calculated from birthday.</p>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $cmsProfile['address'] ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $cmsProfile['contact_number'] ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Role</label>
                    <select name="role" class="form-control">
                        @php
                            $selectedRole = old('role', strtolower((string) ($cmsProfile['role'] ?? \App\Models\User::normalizeRole($admin->user_role ?? 'admin'))));
                        @endphp
                        <option value="superadmin" @selected($selectedRole === 'superadmin')>Superadmin</option>
                        <option value="admin" @selected($selectedRole === 'admin')>Admin</option>
                        <option value="student_assistant" @selected(in_array($selectedRole, ['student_assistant', 'assistant', 'studentassistant'], true))>Student Assistant</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    @php
                        $selectedStatus = old('status', strtolower((string) ($cmsProfile['status'] ?? 'active')));
                    @endphp
                    <select name="status" class="form-control">
                        <option value="active" @selected($selectedStatus === 'active')>Active</option>
                        <option value="inactive" @selected($selectedStatus === 'inactive')>Inactive</option>
                    </select>
                </div>

                <div style="border-top: 1px solid #eee; margin: 15px 0; padding-top: 15px;">
                    <label style="font-size: 12px; color: #888;">Change Password (Optional)</label>
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Retype password">
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" onclick="closeProfileModal()" style="background: #eee; border:none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">Cancel</button>
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
