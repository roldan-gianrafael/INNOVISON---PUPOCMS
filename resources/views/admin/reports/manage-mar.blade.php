@extends('layouts.admin')

@section('title', 'Manage MAR Conditions')

@push('styles')
<style>

/* ================= CARD ================= */
.card {
    background: #fff;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    border: 1px solid #f0f0f0;
}

/* ================= TABLE ================= */
.mar-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.mar-table th {
    background: #f8fafc;
    padding: 12px;
    font-size: 13px;
    color: #64748b;
    text-align: left;
}

.mar-table td {
    padding: 12px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
    color: #334155;
}

/* ================= FORM ================= */
.manage-form {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 14px;
    align-items: end;
    margin-bottom: 20px;
}

/* labels */
.input-group label {
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 6px;
    display: block;
}

/* wrapper */
.input-group {
    display: flex;
    flex-direction: column;
}

/* ================= INPUT (YELLOW + ROUND) ================= */
.form-control {
    height: 44px;
    padding: 12px 18px;
    border-radius: 999px;
    border: 2px solid #facc15;
    background: #fff;
    font-size: 14px;
    box-shadow: 0 4px 12px rgba(250, 204, 21, 0.15);
    transition: 0.25s ease;
}

.form-control:focus {
    outline: none;
    border-color: #eab308;
    box-shadow: 0 0 0 3px rgba(250, 204, 21, 0.25);
}

/* ================= ADD BUTTON ================= */
.btn-save {
    height: 44px;
    padding: 0 20px;
    border-radius: 999px;
    border: none;
    cursor: pointer;
    font-weight: 800;
    color: #fff;
    background: linear-gradient(135deg, #70131B, #8f2230);
}

/* ================= ACTION BUTTONS ================= */
.action-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.inline-form {
    margin: 0;
    display: inline-flex;
}

/* ================= CHANGE (GREY) ================= */
.btn-change {
    height: 38px;
    min-width: 110px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;

    border: 1px solid #cbd5e1;
    background: #e5e7eb;
    color: #334155;

    cursor: pointer;
    transition: 0.2s ease;
}

.btn-change:hover {
    background: #d1d5db;
    transform: translateY(-1px);
}

/* ================= REMOVE (RED) ================= */
.btn-remove {
    height: 38px;
    min-width: 110px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;

    border: none;
    background: #70131B;
    color: #fff;

    cursor: pointer;
    transition: 0.2s ease;
}

.btn-remove:hover {
    transform: translateY(-1px);
    opacity: 0.9;
}

/* ================= MODAL ================= */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
}

.modal-box {
    background: #fff;
    padding: 24px;
    border-radius: 12px;
    width: 100%;
    max-width: 500px;
}

/* ================= CANCEL BUTTON ================= */
.btn-cancel {
    height: 38px;
    padding: 0 16px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;

    border: 1px solid #cbd5e1;
    background: #e5e7eb;
    color: #334155;

    cursor: pointer;
}

.btn-cancel:hover {
    background: #d1d5db;
}

</style>
@endpush

@section('content')

<div class="card">

    <h3>Manage Medical Conditions</h3>

    {{-- FORM --}}
    <form action="{{ route('conditions.store') }}" method="POST" class="manage-form">
        @csrf

        <div class="input-group">
            <label>Category</label>
            <select name="category_id" class="form-control" required>
                <option value="">Select Category</option>
                @foreach($categoryList as $c)
                    <option value="{{ $c->id }}">
                        Category {{ $c->code }} - {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="input-group">
            <label>Condition Name</label>
            <input type="text" name="name" class="form-control"
                   placeholder="e.g. Fever, Headache" required>
        </div>

        <div class="input-group">
            <label>&nbsp;</label>
            <button type="submit" class="btn-save">Add New</button>
        </div>
    </form>

    {{-- TABLE --}}
    <table class="mar-table">
        <thead>
        <tr>
            <th>Category</th>
            <th>Condition</th>
            <th style="text-align:center;">Action</th>
        </tr>
        </thead>

        <tbody>
        @forelse($allConditions as $cond)
        <tr>
            <td>
                <strong>Category {{ $cond->category->code }}</strong>
                - {{ $cond->category->name }}
            </td>

            <td>{{ $cond->name }}</td>

            <td>
                <div class="action-wrapper">

                    <button type="button"
                        class="btn-change"
                        onclick='openChangeModal(@json($cond->id), @json($cond->category_id), @json($cond->name))'>
                        Change
                    </button>

                    <form action="{{ route('conditions.destroy', $cond->id) }}"
                          method="POST"
                          class="inline-form"
                          onsubmit="return confirm('Delete this condition?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn-remove">
                            Remove
                        </button>
                    </form>

                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="3" style="text-align:center;">No data found</td>
        </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- MODAL --}}
<div id="changeModal" class="modal-overlay">
    <div class="modal-box">

        <h3>Change Category</h3>

        <p id="conditionDisplayName"></p>

        <form id="changeForm" method="POST">
            @csrf
            @method('PUT')

            <select name="category_id" id="modalCategoryId" class="form-control" required>
                @foreach($categoryList as $c)
                    <option value="{{ $c->id }}">
                        Category {{ $c->code }} - {{ $c->name }}
                    </option>
                @endforeach
            </select>

            <div style="margin-top:20px; text-align:right;">
                <button type="button" class="btn-cancel" onclick="closeChangeModal()">Cancel</button>
                <button type="submit" class="btn-save">Save</button>
            </div>
        </form>

    </div>
</div>

@endsection

@push('scripts')
<script>
function openChangeModal(id, categoryId, name) {
    document.getElementById('conditionDisplayName').innerText = "Condition: " + name;
    document.getElementById('modalCategoryId').value = categoryId;

    let route = @json(route('conditions.update', ['id' => '__ID__']));
    document.getElementById('changeForm').action = route.replace('__ID__', id);

    document.getElementById('changeModal').style.display = 'flex';
}

function closeChangeModal() {
    document.getElementById('changeModal').style.display = 'none';
}

window.onclick = function(e) {
    let modal = document.getElementById('changeModal');
    if (e.target === modal) closeChangeModal();
};
</script>
@endpush