@extends('layouts.admin')

@section('title', 'Manage Inventory Illness Categories')

@push('styles')
<style>
    .card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; margin-bottom: 24px; }
    h3 { color: #334155; font-size: 18px; margin-top: 10px; }
    .mar-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .mar-table th { background: #f8fafc; color: #64748b; text-align: left; padding: 12px; border-bottom: 2px solid #eee; font-size: 13px; }
    .mar-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; }
    .manage-section { background: #fdfdfd; border: 1px dashed #cbd5e1; padding: 20px; border-radius: 10px; }
    .btn-save { background: linear-gradient(135deg, #70131B, #8f2230); color: #ffffff; border: 1px solid #8f2230; padding: 10px 16px; border-radius: 999px; cursor: pointer; font-weight: 800; }
    .form-control { padding: 8px; border: 1px solid #ddd; border-radius: 5px; width: 100%; }
    .manage-form { display: grid; grid-template-columns: minmax(320px, 1fr) auto; gap: 10px; margin-bottom: 20px; align-items: start; }
    .table-action-cell { text-align: center; white-space: nowrap; }
    .table-action-wrap { display: inline-flex; align-items: center; justify-content: center; gap: 6px; }
    .btn-view, .btn-remove, .btn-cancel { border-radius: 999px; padding: 8px 14px; font-size: 12px; font-weight: 800; cursor: pointer; transition: all 0.2s ease; }
    .btn-view { background-color: #cbd5e1; color: #334155; border: 1px solid #5bf846; }
    .btn-remove { background: #70131B; color: #fff; border: none; }
    .btn-cancel { background: #e5e7eb; border: 1px solid #cbd5e1; }
    .btn-view:hover, .btn-remove:hover, .btn-cancel:hover { transform: translateY(-1px); }
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
    .modal-box { background: #fff; padding: 24px; border-radius: 12px; width: 100%; max-width: 500px; }
    .linked-medicine-list { margin: 16px 0 0; padding-left: 18px; color: #334155; }
    .linked-medicine-list li + li { margin-top: 8px; }
    .linked-medicine-empty { margin-top: 14px; color: #64748b; font-size: 14px; }
</style>
@endpush

@section('content')
@php
    $role = \App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '');
    $backUrl = $role === \App\Models\User::ROLE_ADMIN
        ? url('/assistant/reports/inventory-summary?month=' . $month)
        : url('/admin/reports/inventory-summary?month=' . $month);
@endphp

<div style="margin-bottom: 20px;">
    <a href="{{ $backUrl }}" style="text-decoration: none; color: #64748b; font-size: 14px;">
        &larr; Back to Inventory Summary
    </a>
</div>

<div class="card manage-section">
    <h3>Manage Inventory Illness Categories</h3>

    <form action="{{ route('inventory-illness-categories.store') }}" method="POST" class="manage-form">
        @csrf
        <input type="text" name="name" class="form-control" placeholder="Illness Category Name (e.g. Pain Reliever, Asthma)" required>
        <button type="submit" class="btn-save">Add New</button>
    </form>

    <table class="mar-table">
        <thead>
            <tr>
                <th>Illness Category</th>
                <th>Linked Medicines</th>
                <th style="text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
        @forelse($categories as $category)
            <tr>
                <td><strong>{{ $category->name }}</strong></td>
                <td>{{ $category->items_count }}</td>
                <td class="table-action-cell">
                    <div class="table-action-wrap">
                        <button type="button" class="btn-view" onclick='openViewModal(@json([
                            "name" => $category->name,
                            "items" => $category->items->pluck("name")->values(),
                        ]))'>
                            View
                        </button>
                        <form action="{{ route('inventory-illness-categories.destroy', $category->id) }}" method="POST" onsubmit="return confirmDelete(event)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-remove">Remove</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" style="text-align:center;">No illness categories found</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div id="viewModal" class="modal-overlay">
    <div class="modal-box">
        <h3>Linked Medicines</h3>
        <p id="categoryDisplayName"></p>
        <ul id="linkedMedicineList" class="linked-medicine-list"></ul>
        <p id="linkedMedicineEmpty" class="linked-medicine-empty" style="display:none;">No medicines are linked to this illness category yet.</p>

        <div style="margin-top:20px; text-align:right;">
            <button type="button" class="btn-cancel" onclick="closeViewModal()">Close</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openViewModal(category) {
    const listNode = document.getElementById('linkedMedicineList');
    const emptyNode = document.getElementById('linkedMedicineEmpty');
    document.getElementById('categoryDisplayName').innerText = 'Category: ' + (category.name || '');

    listNode.innerHTML = '';

    const items = Array.isArray(category.items) ? category.items : [];
    if (items.length === 0) {
        emptyNode.style.display = 'block';
        listNode.style.display = 'none';
    } else {
        emptyNode.style.display = 'none';
        listNode.style.display = 'block';

        items.forEach(function (name) {
            const li = document.createElement('li');
            li.textContent = name;
            listNode.appendChild(li);
        });
    }

    document.getElementById('viewModal').style.display = 'flex';
}

function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

function confirmDelete(e) {
    if (!confirm('Are you sure you want to delete this illness category? Linked medicines will be unset.')) {
        e.preventDefault();
        return false;
    }
    return true;
}

window.onclick = function(e) {
    let modal = document.getElementById('viewModal');
    if (e.target === modal) closeViewModal();
};
</script>
@endpush
