<div class="um-section-block admin-hub" id="adminHubSection">
    <div class="um-section-kicker">Admins Table</div>
    <h4 class="um-section-title">Admin Hub Profile</h4>
    <p class="um-section-copy">This is clinic-only data for admin-side access. It can stay separate from the student login profile while still being managed here and shared through the existing admin profile API.</p>
    <div class="um-field">
        <label>Hub Record</label>
        <div class="um-note" id="detailAdminProfileStatus">
            No linked admin hub record yet. One will be created when you save an admin-side role.
        </div>
    </div>
    <div class="um-field" id="adminEmailWrap">
        <label>Admin Login Email</label>
        <input type="email" name="admin_email" id="detailAdminEmail" placeholder="Enter admin-side login email">
        <div class="um-note" id="adminEmailNote" style="margin-top: 6px;">
            Use a separate login email only for Student Assistant accounts.
        </div>
    </div>
    <div class="um-field" id="adminOfficeWrap">
        <label>Office</label>
        <input type="text" name="office" id="detailOffice" placeholder="Office or Department">
    </div>
</div>
