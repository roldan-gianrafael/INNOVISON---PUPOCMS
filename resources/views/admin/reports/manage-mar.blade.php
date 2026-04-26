@extends('layouts.admin')

@section('title', 'Manage MAR Conditions')

@push('styles')
<style>
    .card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
        margin-bottom: 24px;
    }

    h3 {
        color: #334155;
        font-size: 18px;
        margin-top: 10px;
    }

    /* 🔥 TABLE */
    .mar-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .mar-table th {
        background: #f8fafc;
        color: #64748b;
        padding: 12px;
        font-size: 13px;
    }

    .mar-table td {
        padding: 12px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: #334155;
    }

    /* 🔥 FORM SECTION */
    .manage-section {
        background: #fdfdfd;
        border: 1px dashed #cbd5e1;
        padding: 20px;
        border-radius: 10px;
    }

    /* 🔥 INPUTS (ROUNDED + YELLOW + SHADOW) */
    .form-control {
        padding: 12px 18px;
        border: 2px solid #facc15;
        border-radius: 999px;
        width: 100%;
        font-size: 14px;
        background: #fff;

        box-shadow: 0 4px 12px rgba(250, 204, 21, 0.15);
        transition: all 0.25s ease;
    }

    .form-control:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(250, 204, 21, 0.25);
    }

    .form-control:focus {
        outline: none;
        border-color: #eab308;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.25),
            0 8px 18px rgba(250, 204, 21, 0.35);
    }

    /* 🔥 FORM GRID */
    .manage-form {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 10px;
        margin-bottom: 20px;
        align-items: center;
    }

    /* 🔥 ADD BUTTON */
    .btn-save {
        height: 44px;
        padding: 0 20px;
        border-radius: 999px;
        font-weight: 800;
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #fff;
        border: none;
        cursor: pointer;

        box-shadow: 0 6px 16px rgba(112, 19, 27, 0.25);
        transition: 0.2s;
    }

    .btn-save:hover {
        transform: translateY(-1px);
    }

    /* 🔥 ACTION BUTTON FIX (IMPORTANT) */
    .table-action-wrap {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }

    .table-action-wrap form {
        margin: 0;
    }

    .btn-change,
    .btn-remove {
        height: 38px;
        padding: 0 16px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        min-width: 100px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: none;
    }

    .btn-change {
        background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
        color: #334155;
    }

    .btn-remove {
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #fff;
    }

    .btn-change:hover,
    .btn-remove:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(0,0,0,0.15);
    }

    /* 🔥 MODAL */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
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

    @media (max-width: 768px) {
        .manage-form {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<div style="margin-bottom: 20px;">
    <a href="{{ route('reports.mar') }}" style="color:#64748b;">← Back to MAR Report</a>
</div>

<div class="card manage-section">
    <h3>Manage Medical Conditions</h3>

    <form action="{{ route('conditions.store') }}" method="POST" class="manage-form">
        @csrf

        <select name="category_id" class="form-control" required>
            <option value="">Select Category</option>
            @foreach($categoryList as $c)
                <option value="{{ $c->id }}">
                    Category {{ $c->code }} - {{ $c->name }}
                </option>
            @endforeach
        </select>

        <input type="text" name="name" class="form-control"
               placeholder="Condition Name (e.g. Fever)" required>

        <button type="submit" class="btn-save">Add</button>
    </form>

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
                <span style="color:#64748b;"> - {{ $cond->category->name }}</span>
            </td>

            <td>{{ $cond->name }}</td>

            <td>
                <div class="table-action-wrap">

                    <button type="button"
                        class="btn-change"
                        onclick='openChangeModal(@json($cond->id), @json($cond->category_id), @json($cond->name))'>
                        Change
                    </button>

                    <form action="{{ route('conditions.destroy', $cond->id) }}"
                          method="POST"
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

<!-- 🔥 MODAL -->
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
                <button type="button" onclick="closeChangeModal()">Cancel</button>
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