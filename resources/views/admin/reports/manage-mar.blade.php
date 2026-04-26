@extends('layouts.admin')

@section('title', 'Manage MAR Conditions')

@push('styles')

<style>
    .card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; margin-bottom: 24px; }
    h2 { color: #8B0000; font-size: 22px; margin-bottom: 20px; border-bottom: 2px solid #8B0000; padding-bottom: 10px; }
    h3 { color: #334155; font-size: 18px; margin-top: 10px; }

    .mar-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .mar-table th { background: #f8fafc; color: #64748b; text-align: left; padding: 12px; border-bottom: 2px solid #eee; font-size: 13px; }
    .mar-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; }
    
    .manage-section { background: #fdfdfd; border: 1px dashed #cbd5e1; padding: 20px; border-radius: 10px; }

    .btn-save {
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #ffffff;
        border: 1px solid #8f2230;
        padding: 10px 16px;
        border-radius: 999px;
        cursor: pointer;
        font-weight: 800;
    }

    .form-control { padding: 8px; border: 1px solid #ddd; border-radius: 5px; width: 100%; }

    .manage-form {
        display: grid;
        grid-template-columns: minmax(280px, 1.35fr) minmax(220px, 1.65fr) auto;
        gap: 10px;
        margin-bottom: 20px;
        align-items: start;
    }

    .table-action-cell { text-align: center; white-space: nowrap; }
    .table-action-wrap {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    /* BUTTONS */
    .btn-change,
    .btn-remove,
    .btn-cancel {
        border-radius: 999px;
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-change {
        background-color: #cbd5e1;
        color: #334155;
        border: 1px solid #5bf846;
    }

    .btn-remove {
        background: #70131B;
        color: #fff;
        border: none;
    }

    .btn-cancel {
        background: #e5e7eb;
        border: 1px solid #cbd5e1;
    }

    .btn-change:hover,
    .btn-remove:hover,
    .btn-cancel:hover {
        transform: translateY(-1px);
    }

    /* MODAL */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
    .modal-box { background: #fff; padding: 24px; border-radius: 12px; width: 100%; max-width: 500px; }

</style>

@endpush

@section('content')

<div style="margin-bottom: 20px;">
    <a href="{{ route('reports.mar') }}" style="text-decoration: none; color: #64748b; font-size: 14px;">
        &larr; Back to MAR Report
    </a>
</div>

<div class="card manage-section">
    <h3>Manage Medical Conditions (Sub-categories)</h3>

```
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

    <input type="text" name="name" class="form-control" placeholder="Condition Name" required>

    <button type="submit" class="btn-save">Add New</button>
</form>

<table class="mar-table">
    <thead>
        <tr>
            <th>Category</th>
            <th>Condition Name</th>
            <th style="text-align: center;">Action</th>
        </tr>
    </thead>

    <tbody>
    @forelse($allConditions as $cond)
    <tr>
        <td>
            <strong>Category {{ $cond->category->code }}</strong> -
            {{ $cond->category->name }}
        </td>

        <td>{{ $cond->name }}</td>

        <td class="table-action-cell">
            <div class="table-action-wrap">

                <!-- CHANGE -->
                <button type="button" class="btn-change"
                    onclick="openChangeModal('{{ $cond->id }}','{{ $cond->category_id }}','{{ $cond->name }}')">
                    ✏️ Change
                </button>

                <!-- REMOVE -->
                <form action="{{ route('conditions.destroy', $cond->id) }}"
                      method="POST"
                      onsubmit="return confirmDelete(event)">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn-remove">
                        🗑 Remove
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
```

</div>

<!-- MODAL -->

<div id="changeModal" class="modal-overlay">
    <div class="modal-box">

```
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
```

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

function confirmDelete(e) {
    if (!confirm("Are you sure you want to delete this condition?")) {
        e.preventDefault();
        return false;
    }
    return true;
}

window.onclick = function(e) {
    let modal = document.getElementById('changeModal');
    if (e.target === modal) closeChangeModal();
};
</script>

@endpush
