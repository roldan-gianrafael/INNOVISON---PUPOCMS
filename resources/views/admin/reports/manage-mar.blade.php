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
    .btn-save { background: #70131B; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; }
    .form-control { padding: 8px; border: 1px solid #ddd; border-radius: 5px; width: 100%; }

    /* Modal Styles for .btn-change */
.modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
.modal-box { background: #fff; padding: 24px; border-radius: 12px; width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
.btn-change { background: #8f2230; color: white; border: none; padding: 4px 10px; border-radius: 5px; cursor: pointer; font-size: 12px; margin-right: 5px; }
.btn-cancel { background: #e2e8f0; color: #475569; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; }
.btn-filter { background: #70131B; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; }
 </style>
@endpush

@section('content')


<div style="margin-bottom: 20px;">
    <a href="{{ route('reports.mar') }}" style="text-decoration: none; color: #64748b; font-size: 14px;">&larr; Back to MAR Report</a>
</div>

<div class="card manage-section">
    <h3>Manage Medical Conditions (Sub-categories)</h3>
    <p style="font-size: 13px; color: #64748b;">Add or remove medical conditions under Categories A-E.</p>
    
    <form action="{{ route('conditions.store') }}" method="POST" style="display: flex; gap: 10px; margin-bottom: 20px;">
        @csrf
        <select name="category_id" class="form-control" style="flex: 1;" required>
            <option value="">Select Category</option>
            @foreach($categoryList as $c)
                <option value="{{ $c->id }}">Category {{ $c->code }} - {{ $c->name }}</option>
            @endforeach
        </select>

        <input type="text" name="name" class="form-control" style="flex: 2;" placeholder="Condition Name (e.g. Fever)" required>
        
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
    <strong>Category {{ $cond->category->code }}</strong> 
    <span style="color: #64748b; margin-left: 5px;">- {{ $cond->category->name }}</span>
    </td>


    <td>{{ $cond->name }}</td>
    <td style="text-align: center; display: flex; justify-content: center; gap: 5px;">
        <button type="button" class="btn-change" 
                onclick="openChangeModal('{{ $cond->id }}', '{{ $cond->category_id }}', '{{ $cond->name }}')">
            Change
        </button>

        <form action="{{ route('conditions.destroy', $cond->id) }}" method="POST" onsubmit="return confirm('Delete this condition?')">
            @csrf 
            @method('DELETE')
            <button type="submit" style="background:none; border:none; color:#ef4444; cursor:pointer; font-weight: bold; font-size: 12px; padding: 4px 0;">Remove</button>
        </form>
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
                <select name="category_id" id="modalCategoryId" class="form-control" required>
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
        // Set the Display Name
        document.getElementById('conditionDisplayName').innerText = "Condition: " + name;
        
        // Set the current category in dropdown
        document.getElementById('modalCategoryId').value = categoryId;
        
        const changeRouteTemplate = @json(route('conditions.update', ['id' => '__ID__']));
        document.getElementById('changeForm').action = changeRouteTemplate.replace('__ID__', id);
        
        // Show Modal
        document.getElementById('changeModal').style.display = 'flex';
    }

    function closeChangeModal() {
        document.getElementById('changeModal').style.display = 'none';
    }

    // Close modal when clicking outside the box
    window.onclick = function(event) {
        let modal = document.getElementById('changeModal');
        if (event.target == modal) {
            closeChangeModal();
        }
    }
</script>
