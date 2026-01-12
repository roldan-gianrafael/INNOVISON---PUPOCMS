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
</style>
@endpush

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>⚠️ {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3>Admin Profile</h3>
            <button class="btn-edit" onclick="openProfileModal()">✏️ Edit Profile</button>
        </div>

        <p style="font-size: 13px; color: #94a3b8; margin-bottom: 15px;">
            These fields are read-only. Click the edit button above to make changes.
        </p>

        <div class="form-group">
            <label>Admin Name</label>
            <input type="text" class="form-control" value="{{ $admin->name ?? 'Admin' }}" disabled>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" value="{{ $admin->email ?? 'admin@pup.edu.ph' }}" disabled>
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
                    <label>Admin Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $admin->name ?? '' }}" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $admin->email ?? '' }}" required>
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
    }
    function closeProfileModal() {
        document.getElementById('profileModal').style.display = 'none';
    }
    window.onclick = function(e) {
        if(e.target == document.getElementById('profileModal')) closeProfileModal();
    }
</script>
@endpush