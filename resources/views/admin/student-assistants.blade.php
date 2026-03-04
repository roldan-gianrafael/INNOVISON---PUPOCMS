@extends('layouts.admin')

@section('title', 'Student Assistants')

@push('styles')
<style>
    .card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
    }

    .controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }

    .btn-add {
        background: #8B0000;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 16px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-add:hover {
        background: #600000;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        text-align: left;
        padding: 12px 16px;
        border-bottom: 2px solid #f1f5f9;
        color: #64748b;
        text-transform: uppercase;
        font-size: 12px;
    }

    td {
        padding: 14px 16px;
        border-bottom: 1px solid #f8fafc;
        font-size: 14px;
        color: #334155;
    }

    .btn-action {
        padding: 6px 10px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 12px;
        font-weight: 700;
        margin-right: 6px;
    }

    .btn-edit {
        background: #fff3f5;
        color: #70131B;
        border: 1px solid #f0d7dc;
    }

    .btn-delete {
        background: #fee2e2;
        color: #b91c1c;
    }

    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal-box {
        background: #fff;
        padding: 22px;
        border-radius: 12px;
        width: 460px;
        max-width: 92%;
    }

    .form-group {
        margin-bottom: 12px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
    }

    .form-control {
        width: 100%;
        padding: 10px 11px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 14px;
    }

    .alert {
        margin-bottom: 14px;
        padding: 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
    }

    .alert-success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .alert-error {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
</style>
@endpush

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (isset($errors) && $errors->any())
    <div class="alert alert-error">
        <ul style="margin:0; padding-left:18px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="controls">
    <h2 style="margin:0; color:#ffffff;">Student Assistant Accounts</h2>
    <button type="button" class="btn-add" onclick="openCreateAssistantModal()">+ Add Assistant</button>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Student ID</th>
                <th>Email</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assistants as $assistant)
                <tr>
                    <td style="font-weight:700;">{{ $assistant->name }}</td>
                    <td>{{ $assistant->student_id }}</td>
                    <td>{{ $assistant->email }}</td>
                    <td>{{ optional($assistant->created_at)->format('M d, Y') }}</td>
                    <td>
                        <button
                            type="button"
                            class="btn-action btn-edit"
                            onclick="openEditAssistantModal(
                                '{{ $assistant->id }}',
                                @json($assistant->student_id),
                                @json($assistant->first_name),
                                @json($assistant->last_name),
                                @json($assistant->email)
                            )"
                        >
                            Edit
                        </button>

                        <form action="{{ route('admin.student-assistants.destroy', $assistant) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action btn-delete" onclick="return confirm('Delete this student assistant account?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:28px; color:#94a3b8;">No student assistant accounts yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="assistantCreateModal" class="modal-overlay">
    <div class="modal-box">
        <h3 style="margin-top:0; color:#8B0000;">Create Student Assistant</h3>
        <form method="POST" action="{{ route('admin.student-assistants.store') }}">
            @csrf
            <div class="form-group">
                <label>Student ID</label>
                <input type="text" name="student_id" class="form-control" required>
            </div>
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:18px;">
                <button type="button" onclick="closeCreateAssistantModal()" style="padding:9px 14px; border:none; border-radius:6px; cursor:pointer;">Cancel</button>
                <button type="submit" class="btn-add">Create Account</button>
            </div>
        </form>
    </div>
</div>

<div id="assistantEditModal" class="modal-overlay">
    <div class="modal-box">
        <h3 style="margin-top:0; color:#8B0000;">Edit Student Assistant</h3>
        <form method="POST" id="assistantEditForm">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Student ID</label>
                <input type="text" name="student_id" id="edit_student_id" class="form-control" required>
            </div>
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" id="edit_first_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" id="edit_last_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="edit_email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>New Password (Optional)</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:18px;">
                <button type="button" onclick="closeEditAssistantModal()" style="padding:9px 14px; border:none; border-radius:6px; cursor:pointer;">Cancel</button>
                <button type="submit" class="btn-add">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openCreateAssistantModal() {
        document.getElementById('assistantCreateModal').style.display = 'flex';
    }

    function closeCreateAssistantModal() {
        document.getElementById('assistantCreateModal').style.display = 'none';
    }

    function openEditAssistantModal(id, studentId, firstName, lastName, email) {
        const form = document.getElementById('assistantEditForm');
        form.action = '{{ url('/admin/student-assistants') }}/' + id;
        document.getElementById('edit_student_id').value = studentId || '';
        document.getElementById('edit_first_name').value = firstName || '';
        document.getElementById('edit_last_name').value = lastName || '';
        document.getElementById('edit_email').value = email || '';
        document.getElementById('assistantEditModal').style.display = 'flex';
    }

    function closeEditAssistantModal() {
        document.getElementById('assistantEditModal').style.display = 'none';
    }

    window.addEventListener('click', function (event) {
        const createModal = document.getElementById('assistantCreateModal');
        const editModal = document.getElementById('assistantEditModal');
        if (event.target === createModal) {
            closeCreateAssistantModal();
        }
        if (event.target === editModal) {
            closeEditAssistantModal();
        }
    });
</script>
@endpush
