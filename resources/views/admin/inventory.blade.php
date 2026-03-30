@extends('layouts.admin')

@section('title', 'Inventory')

@push('styles')
<style>
    /* Card & Table */
    .card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
    }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th { text-align: left; padding: 12px 16px; border-bottom: 2px solid #f1f5f9; color: #64748b; text-transform: uppercase; font-size: 12px; }
    td { padding: 16px; border-bottom: 1px solid #f8fafc; font-size: 14px; color: #334155; }

    /* Controls */
    .controls { display: flex; justify-content: space-between; margin-bottom: 20px; }
    .btn-add { background: #8B0000; color: white; padding: 10px 16px; border-radius: 8px; border: none; font-weight: 700; cursor: pointer; }
    .btn-add:hover { background: #600000; }

    /* Status Badges */
    .status { padding: 4px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; }
    .status.in { background: #dcfce7; color: #15803d; }
    .status.low { background: #fff7ed; color: #c2410c; }
    .status.out { background: #fee2e2; color: #b91c1c; }

    /* Action Buttons */
    .btn-icon { padding: 6px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; margin-right: 4px; }
    .btn-edit { background: #fff3f5; color: #70131B; border: 1px solid #f0d7dc; }
    .btn-delete { background: #fee2e2; color: #b91c1c; }

    /* Modal */
    .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
    .modal-box { background: #fff; padding: 24px; border-radius: 12px; width: 400px; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-size: 13px; font-weight: 600; color: #64748b; }
    .form-control { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; }
</style>
@endpush

@section('content')
    @php
        $role = \App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '');
        $canManageInventory = $role === \App\Models\User::ROLE_SUPERADMIN;
    @endphp

    <div class="controls">
        <h2 style="margin:0; color:#000000;">Clinic Inventory</h2>
        @if($canManageInventory)
            <button class="btn-add" onclick="openModal()">+ Add New Item</button>
        @endif
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>    
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Stock Status</th>
                    <th>{{ $canManageInventory ? 'Actions' : 'Access' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td style="font-weight: 600;">{{ $item->name }}</td>
                        <td>
                            {{ $item->category }}
                            @if($item->category == 'Medicine' && $item->medicine_type)
                                <small style="display:block; color:#64748b; font-style: italic;">({{ $item->medicine_type }})</small>
                            @endif
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>
                            @if($item->quantity == 0)
                                <span class="status out">Out of Stock</span>
                            @elseif($item->quantity < 10)
                                <span class="status low">Low Stock</span>
                            @else
                                <span class="status in">In Stock</span>
                            @endif
                        </td>
                        <td>
                            @if($canManageInventory)
                                <button class="btn-icon btn-edit" 
                                    onclick="editItem('{{ $item->id }}', '{{ $item->name }}', '{{ $item->category }}', '{{ $item->quantity }}', '{{ $item->medicine_type }}')">
                                    Edit
                                </button>

                                <form action="{{ url('/admin/inventory/'.$item->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon btn-delete" onclick="return confirm('Delete this item?')">Delete</button>
                                </form>
                            @else
                                <span style="font-size: 12px; color: #64748b; font-weight: 700;">View Only</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align: center; padding: 30px; color: #888;">No items in inventory.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($canManageInventory)
        <div id="itemModal" class="modal-overlay">
            <div class="modal-box">
                <h3 id="modalTitle" style="margin-top:0; color:#8B0000;">Add New Item</h3>
                
                <form id="itemForm" method="POST" action="{{ url('/admin/inventory/store') }}">
                    @csrf
                    <div id="methodField"></div> 
                    
                    <div class="form-group">
                        <label>Item Name</label>
                        <input name="name" id="iName" class="form-control" required placeholder="e.g. Paracetamol">
                    </div>

                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" id="iCategory" class="form-control" onchange="toggleMedicineType()">
                            <option value="Medicine">Medicine</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Supplies">Supplies</option>
                        </select>
                    </div>

                    <div class="form-group" id="medicineTypeGroup" style="display: none;">
                        <label>Medicine Type</label>
                        <select name="medicine_type" id="iMedicineType" class="form-control">
                            <option value="">-- Select Type --</option>
                            <option value="Antibiotic">Antibiotic</option>
                            <option value="Asthma">For Asthma</option>
                            <option value="Analgesic">Analgesic</option>
                            <option value="Antipyretic">Antipyretic</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" id="iQty" class="form-control" required min="0">
                    </div>

                    <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                        <button type="button" onclick="closeModal()" style="padding: 8px 16px; border: none; background: #eee; cursor: pointer; border-radius: 6px;">Cancel</button>
                        <button type="submit" class="btn-add">Save Item</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    const itemModal = document.getElementById('itemModal');
    const medicineGroup = document.getElementById('medicineTypeGroup');
    const medicineSelect = document.getElementById('iMedicineType');

    function toggleMedicineType() {
        const category = document.getElementById('iCategory').value;
        if (category === 'Medicine') {
            medicineGroup.style.display = 'block';
            medicineSelect.setAttribute('required', 'required');
        } else {
            medicineGroup.style.display = 'none';
            medicineSelect.removeAttribute('required');
            medicineSelect.value = ''; 
        }
    }

    function openModal() {
        if (!itemModal) return;
        itemModal.style.display = 'flex';
        document.getElementById('modalTitle').innerText = 'Add New Item';
        document.getElementById('itemForm').action = "{{ url('/admin/inventory/store') }}";
        document.getElementById('methodField').innerHTML = ''; 
        
        // Reset inputs
        document.getElementById('iName').value = '';
        document.getElementById('iCategory').value = 'Medicine';
        document.getElementById('iQty').value = '';
        toggleMedicineType();
    }

    function editItem(id, name, category, qty, medicineType) {
        if (!itemModal) return;
        itemModal.style.display = 'flex';
        document.getElementById('modalTitle').innerText = 'Edit Item';
        document.getElementById('itemForm').action = "/admin/inventory/" + id;
        
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('iName').value = name;
        document.getElementById('iCategory').value = category;
        document.getElementById('iQty').value = qty;
        
        // Fill and show medicine type if applicable
        toggleMedicineType();
        if(category === 'Medicine') {
            document.getElementById('iMedicineType').value = medicineType;
        }
    }

    function closeModal() {
        if (!itemModal) return;
        itemModal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (itemModal && event.target == itemModal) {
            closeModal();
        }
    }
</script>
@endpush