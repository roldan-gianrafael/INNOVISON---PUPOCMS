<div class="um-section-block account-access" id="accountAccessSection">
    <div class="um-section-kicker">Users Table</div>
    <h4 class="um-section-title">Account Access</h4>
    <p class="um-section-copy">This controls the clinic login role, the student-side email, and whether the account can enter the clinic system.</p>
    <div class="um-field">
        <label>Clinic Role</label>
        <select name="user_role" id="detailRole">
            <option value="student">Student</option>
            <option value="student_assistant">Student Assistant</option>
            <option value="admin">Admin</option>
            <option value="super_admin">Super Admin</option>
        </select>
    </div>
    <div class="um-field">
        <label id="detailEmailLabel">Student Email</label>
        <input type="email" name="email" id="detailEditEmail" placeholder="Enter Gmail account">
        <div class="um-note" id="emailRoleNote" style="margin-top: 6px;">
            Keep this email for the student side.
        </div>
    </div>
    <div class="um-field">
        <label>Status</label>
        <select name="status" id="detailStatus">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>
</div>
