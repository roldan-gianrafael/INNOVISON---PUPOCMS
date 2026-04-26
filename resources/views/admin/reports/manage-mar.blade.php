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
        position: relative;
        overflow: hidden;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20);
        transition: color .08s linear, transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        z-index: 0;
    }

    .btn-save::after,
    .btn-change::after,
    .btn-cancel::after,
    .btn-remove::after {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(120deg,
                rgba(255, 248, 196, 0) 0%,
                rgba(255, 239, 181, 0.14) 22%,
                rgba(255, 239, 181, 0.52) 48%,
                rgba(255, 239, 181, 0.14) 72%,
                rgba(255, 248, 196, 0) 100%);
        transform: translateX(-135%);
        transition: transform 1.5s ease;
        z-index: -1;
    }

    .btn-save:hover,
    .btn-change:hover,
    .btn-cancel:hover,
    .btn-remove:hover {
        transform: translateY(-1px);
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16);
    }

    .btn-save:hover::after,
    .btn-change:hover::after,
    .btn-cancel:hover::after,
    .btn-remove:hover::after {
        transform: translateX(135%);
    }

    /* 🔥 MODERN INPUT STYLE (ANIMATED) */
    .form-control {
        padding: 10px 16px;
        border: 2px solid #facc15;
        border-radius: 999px;
        width: 100%;
        background: #fff;
        box-shadow:
            0 4px 10px rgba(250, 204, 21, 0.12);
        transition: all 0.25s ease;
    }

    .form-control:hover {
        transform: translateY(-1px);
        box-shadow:
            0 6px 14px rgba(250, 204, 21, 0.18);
    }

    .form-control:focus {
        outline: none;
        border-color: #eab308;
        transform: translateY(-1px) scale(1.01);
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.25),
            0 8px 18px rgba(250, 204, 21, 0.25);
    }

    .manage-form {
        display: grid;
        grid-template-columns: minmax(280px, 1.35fr) minmax(220px, 1.65fr) auto;
        gap: 10px;
        margin-bottom: 20px;
        align-items: start;
    }

    .category-select,
    .condition-input,
    .modal-select {
        min-height: 44px;
        line-height: 1.3;
    }

    .category-select,
    .modal-select {
        padding-right: 36px;
    }

    .manage-form .btn-save {
        min-height: 44px;
        white-space: nowrap;
        align-self: stretch;
    }

    .add-new-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #ffffff;
        padding: 11px 18px;
        border-radius: 999px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 800;
        border: 1px solid #8f2230;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20);
        z-index: 0;
    }

    .table-action-cell {
        text-align: center;
        white-space: nowrap;
    }

    .table-action-wrap {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        flex-wrap: nowrap;
        white-space: nowrap;
    }

    /* 🔥 BUTTON FIX */
    .btn-change,
    .btn-cancel,
    .btn-filter,
    .btn-remove {
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        min-width: 100px;
        text-align: center;
    }

    .btn-change {
        background: linear-gradient(135deg, #e5e7eb, #cbd5e1);
        color: #334155;
        border: 1px solid #cbd5e1;
        box-shadow:
            0 0 0 3px rgba(148, 163, 184, 0.12),
            0 10px 20px rgba(148, 163, 184, 0.16);
    }

    .btn-remove {
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #fff;
    }

    .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
    .modal-box { background: #fff; padding: 24px; border-radius: 12px; width: min(560px, calc(100vw - 32px)); box-shadow: 0 10px 25px rgba(0,0,0,0.2); }

    @media (max-width: 900px) {
        .manage-form {
            grid-template-columns: 1fr;
        }

        .manage-form .btn-save {
            width: 100%;
        }
    }
 </style>
@endpush

@section('content')

<div style="margin-bottom: 20px;">
    <a href="{{ route('reports.mar') }}" style="text-decoration: none; color: #64748b; font-size: 14px;">&larr; Back to MAR Report</a>
</div>

<div class="card manage-section">
    <h3>Manage Medical Conditions (Sub-categories)</h3>
    <p style="font-size: 13px; color: #64748b;">Add or remove medical conditions under Categories A-N.</p>
    
    <form action="{{ route('conditions.store') }}" method="POST" class="manage-form">
        @csrf
        <select name="category_id" class="form-control category-select" required>
            <option value="">Select Category</option>
            @foreach($categoryList as $c)
                <option value="{{ $c->id }}">Category {{ $c->code }} - {{ $c->name }}</option>
            @endforeach
        </select>

        <input type="text" name="name" class="form-control condition-input" placeholder="Condition Name (e.g. Fever)" required>
        
        <button type="submit" class="btn-save add-new-btn"><span class="add-new-btn-label">Add New</span></button>
    </form>

    <table class="mar-table">
        <thead>
            <tr>
                <th>Categories</th>
                <th>Condition Name</th>
                <th style="text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allConditions as $cond)
<tr>
    <td>
    <strong>Category {{ $cond->category->code }}</strong> 
    <span style="color: #64748b; margin-left: 5px;">- {{ $cond->category->name }}</span>
    </td>

    <td>{{ $cond->name }}</td>

    <td class="table-action-cell">
        <div class="table-action-wrap">
            <button type="button" class="btn-change" 
                    onclick="openChangeModal('{{ $cond->id }}', '{{ $cond->category_id }}', '{{ $cond->name }}')">
                Change
            </button>

            <form action="{{ route('conditions.destroy', $cond->id) }}" method="POST" onsubmit="return confirm('Delete this condition?')">
                @csrf 
                @method('DELETE')
                <button type="submit" class="btn-remove">Remove</button>
            </form>
        </div>
    </td>
</tr>
@empty
...
@endforelse
        </tbody>
    </table>
</div>

<div id="changeModal" class="modal-overlay">
    <div class="modal-box">
        <h3 style="margin-top: 0; color: #1e293b;">Change Category</h3>
        <p id="conditionDisplayName" style="font-size: 14px; color: #64748b; margin-bottom: 20px; font-weight: 600;"></p>
        
        <form id="changeForm" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: 5px;">NEW CATEGORY</label>
                <select name="category_id" id="modalCategoryId" class="form-control modal-select" required>
                    @foreach($categoryList as $c)
                        <option value="{{ $c->id }}">Category {{ $c->code }} - {{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px;">
                <button type="button" class="btn-cancel" onclick="closeChangeModal()">Cancel</button>
                <button type="submit" class="btn-save">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

<script>
    function openChangeModal(id, categoryId, name) {
        document.getElementById('conditionDisplayName').innerText = "Condition: " + name;
        document.getElementById('modalCategoryId').value = categoryId;

        const changeRouteTemplate = @json(route('conditions.update', ['id' => '__ID__']));
        document.getElementById('changeForm').action = changeRouteTemplate.replace('__ID__', id);

        document.getElementById('changeModal').style.display = 'flex';
    }

    function closeChangeModal() {
        document.getElementById('changeModal').style.display = 'none';
    }

    window.onclick = function(event) {
        let modal = document.getElementById('changeModal');
        if (event.target == modal) {
            closeChangeModal();
        }
    }
</script>