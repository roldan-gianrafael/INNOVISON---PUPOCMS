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
    .inventory-summary-card {
        position: relative;
        overflow: hidden;
    }
    .inventory-summary-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 14px;
        right: 14px;
        height: 5px;
        background: #70131B;
        border-radius: 999px;
        pointer-events: none;
        z-index: 1;
    }
    .card,
    .card *:not(.status):not(.btn-add):not(.btn-icon) {
        color: #111827;
    }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th { text-align: left; padding: 12px 16px; border-bottom: 2px solid #f1f5f9; color: #000000; text-transform: uppercase; font-size: 12px; }
    td { padding: 16px; border-bottom: 1px solid #f8fafc; font-size: 14px; color: #111827; }

    /* Controls */
    .controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
        padding: 16px 18px;
        border-radius: 0 0 20px 20px;
        border: 0;
        border-bottom: 2px solid rgba(234, 215, 160, 0.9);
        background: linear-gradient(135deg, rgba(255, 253, 246, 0.76) 0%, rgba(255, 249, 231, 0.58) 42%, rgba(255, 255, 255, 0.82) 100%);
        box-shadow: 0 14px 26px rgba(112, 19, 27, 0.05);
    }
    .inventory-page-title {
        margin: 0;
        color: #000000;
        display: inline-flex;
        align-items: center;
        padding: 10px 18px;
        border-radius: 0 0 14px 14px;
        border: 0;
        border-bottom: 2px solid rgba(112, 19, 27, 0.72);
        background: transparent;
        box-shadow: none;
    }
    .inventory-page-title svg {
        width: 18px;
        height: 18px;
        margin-right: 10px;
        flex: 0 0 auto;
    }
    .inventory-toolbar-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-left: auto;
        flex-wrap: wrap;
    }
    .inventory-search-shell {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        justify-content: flex-end;
    }
    .inventory-search-wrap {
        width: 0;
        max-width: 100%;
        flex: 0 0 0;
        opacity: 0;
        overflow: hidden;
        pointer-events: none;
        transform: translateX(12px) scaleX(0.96);
        transform-origin: right center;
        transition:
            width .32s cubic-bezier(.22, 1, .36, 1),
            flex-basis .32s cubic-bezier(.22, 1, .36, 1),
            opacity .24s ease,
            transform .28s cubic-bezier(.22, 1, .36, 1);
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        padding: 0 !important;
        border-radius: 0 !important;
    }
    .inventory-search-shell.is-open .inventory-search-wrap {
        width: 320px;
        flex: 0 0 320px;
        opacity: 1;
        pointer-events: auto;
        transform: translateX(0) scaleX(1);
    }
    .inventory-search-input {
        width: 100%;
        min-height: 48px;
        height: 48px;
        padding: 12px 18px;
        border-radius: 0 0 14px 14px;
        border: 0 !important;
        border-bottom: 3px solid #8f2230 !important;
        color: #111827;
        background: transparent !important;
        box-shadow: none !important;
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        appearance: none;
        -webkit-appearance: none;
    }
    .inventory-search-input::placeholder {
        color: #7f1d2d;
        font-weight: 700;
    }
    .inventory-search-input:focus {
        outline: none;
        border-bottom-color: #70131B;
        box-shadow: none !important;
        transform: translateY(-1px);
    }
    .inventory-search-toggle {
        width: 50px !important;
        height: 50px !important;
        min-width: 50px !important;
        min-height: 50px !important;
        flex: 0 0 50px !important;
        padding: 0 !important;
        gap: 0 !important;
        border-radius: 999px !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        background: linear-gradient(135deg, #70131B, #8f2230) !important;
        border: 1px solid #8f2230 !important;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20) !important;
        outline: none !important;
    }
    .inventory-search-toggle svg {
        width: 28px !important;
        height: 28px !important;
        stroke-width: 2 !important;
        display: block;
    }
    .inventory-search-toggle:hover,
    .inventory-search-toggle:focus {
        border-color: #facc15 !important;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16) !important;
        outline: none !important;
    }
    .btn-add,
    .inventory-btn-cancel {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #70131B, #8f2230);
        color: #ffffff;
        padding: 11px 18px;
        border-radius: 999px;
        border: 1px solid #8f2230;
        font-weight: 800;
        cursor: pointer;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.12),
            0 10px 22px rgba(112, 19, 27, 0.20);
        transition: color .08s linear, transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        z-index: 0;
    }
    .btn-add::after,
    .inventory-btn-cancel::after {
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
    .btn-add:hover,
    .inventory-btn-cancel:hover {
        transform: translateY(-1px);
        border-color: #facc15;
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.18),
            0 14px 24px rgba(112, 19, 27, 0.16);
    }
    .btn-add:hover::after,
    .inventory-btn-cancel:hover::after {
        transform: translateX(135%);
    }

    /* Status Badges */
    .status { padding: 4px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; }
    .status.in { background: #dcfce7; color: #15803d; }
    .status.low { background: #fff7ed; color: #c2410c; }
    .status.out { background: #fee2e2; color: #b91c1c; }

    /* Action Buttons */
    .inventory-actions {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 96px;
        padding: 9px 14px;
        border-radius: 999px;
        border: 1px solid;
        cursor: pointer;
        font-size: 13px;
        font-weight: 800;
        line-height: 1;
        text-decoration: none;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease, color .18s ease;
        margin-right: 0;
        background: transparent;
    }
    .btn-icon svg {
        width: 15px;
        height: 15px;
        flex: 0 0 auto;
    }
    .btn-edit {
        background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
        color: #475569;
        border-color: rgba(112, 19, 27, 0.22);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
    }
    .btn-edit:hover {
        transform: translateY(-1px);
        color: #70131B;
        border-color: rgba(112, 19, 27, 0.46);
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.08),
            0 10px 22px rgba(112, 19, 27, 0.12);
    }
    .btn-delete {
        background: linear-gradient(180deg, #fff1f2 0%, #ffe4e6 100%);
        color: #b91c1c;
        border-color: rgba(220, 38, 38, 0.22);
        box-shadow: 0 8px 18px rgba(127, 29, 29, 0.08);
    }
    .btn-delete:hover {
        transform: translateY(-1px);
        background: linear-gradient(180deg, #fee2e2 0%, #fecaca 100%);
        border-color: rgba(220, 38, 38, 0.42);
        box-shadow:
            0 0 0 3px rgba(248, 113, 113, 0.12),
            0 10px 22px rgba(127, 29, 29, 0.14);
    }
    .inventory-row-highlight {
        background: #fff7cc;
        outline: 2px solid #f59e0b;
        box-shadow: inset 0 0 0 1px rgba(245, 158, 11, 0.25);
        animation: inventoryHighlightPulse 1.4s ease-in-out 3;
    }
    .inventory-row-highlight-expired {
        background: #fee2e2;
        outline: 2px solid #dc2626;
        box-shadow: inset 0 0 0 1px rgba(220, 38, 38, 0.25);
        animation: inventoryHighlightPulseExpired 1.4s ease-in-out 3;
    }
    @keyframes inventoryHighlightPulse {
        0%, 100% { background: #fff7cc; }
        50% { background: #fde68a; }
    }
    @keyframes inventoryHighlightPulseExpired {
        0%, 100% { background: #fee2e2; }
        50% { background: #fecaca; }
    }

    /* Modal */
    .modal-overlay { 
        display: none; 
        position: fixed; 
        top: 0; left: 0; 
        width: 100%; 
        height: 100%; 
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 1000; 
        justify-content: center; 
        align-items: center; 
    }
    .modal-box {
        background:
            linear-gradient(145deg, rgba(255, 255, 255, 0.24), rgba(255, 255, 255, 0.12)) !important;
        width: 760px;
        max-width: 94vw;
        border-left: 1px solid rgba(112, 19, 27, 0.12) !important;
        border-right: 1px solid rgba(112, 19, 27, 0.12) !important;
        border-top: 2px solid #facc15 !important;
        border-bottom: 2px solid #facc15 !important;
        border-radius: 18px !important;
        backdrop-filter: blur(18px) saturate(150%) !important;
        -webkit-backdrop-filter: blur(18px) saturate(150%) !important;
        box-shadow: 0 24px 46px rgba(15, 23, 42, 0.24);
        overflow: hidden;
        padding: 0;
    }
    .inventory-modal-head {
        padding: 16px 20px;
        background: #70131B;
        border-bottom: 1px solid rgba(255, 255, 255, 0.22);
    }
    .inventory-modal-title {
        margin: 0;
        color: #ffffff !important;
        font-size: 18px;
        font-weight: 800;
        line-height: 1.2;
    }
    .inventory-modal-copy {
        margin: 4px 0 0;
        color: #ffffff !important;
        font-size: 12px;
        font-weight: 700;
    }
    .inventory-modal-body {
        padding: 18px 20px 20px;
    }
    .modal-form-grid { 
        display: grid; 
        grid-template-columns: 1fr 1fr; 
        gap: 18px; 
    }
    .modal-form-panel {
        border: 1px solid rgba(112, 19, 27, 0.16);
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.26);
        padding: 16px;
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.38),
            0 8px 18px rgba(112, 19, 27, 0.06);
    }
    .modal-panel-title {
        margin: 0 0 14px;
        font-size: 15px;
        font-weight: 800;
        color: #70131B;
    }
    .form-group {
        margin-bottom: 12px;
        display: flex;
        flex-direction: column;
        border: 1px solid rgba(127, 29, 45, 0.12);
        background: rgba(255, 255, 255, 0.4);
        border-radius: 12px;
        padding: 10px 12px;
    }
    .form-group label {
        display: block;
        margin-bottom: 4px;
        font-size: 0.74rem;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .form-control,
    .form-select {
        width: 100%;
        min-height: 24px;
        padding: 0 0 2px;
        border-radius: 0 0 6px 6px;
        border: 0;
        border-bottom: 2px solid #8f2230;
        color: #111827;
        background: transparent;
        box-shadow: none;
        font-weight: 700;
        transition: color .18s ease, box-shadow .18s ease, border-color .18s ease;
    }
    .form-control:focus,
    .form-select:focus {
        outline: none;
        border: 0;
        border-bottom: 2px solid #70131B;
        background: transparent;
        box-shadow: none;
    }
    .form-control::placeholder {
        color: #9ca3af;
        font-weight: 600;
    }

    html[data-theme="light"] .modal-box {
        background:
            linear-gradient(145deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.08)) !important;
    }

    html[data-theme="light"] .modal-form-panel {
        background: rgba(255, 255, 255, 0.22);
    }

    html[data-theme="light"] .form-group {
        background: rgba(255, 255, 255, 0.34);
    }
    .inventory-subgroup {
        display: none;
        border-left: 3px solid #8B0000;
        padding-left: 15px;
        margin-bottom: 15px;
    }
    .modal-actions-row {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
    }

    @media (max-width: 760px) {
        .modal-form-grid {
            grid-template-columns: 1fr;
        }
    }

    html[data-theme="dark"] .inventory-page-title {
        color: #ffffff;
        border-bottom-color: rgba(143, 34, 48, 0.70);
        background: transparent;
        box-shadow: none;
    }

    html[data-theme="dark"] .controls {
        border-bottom-color: rgba(143, 34, 48, 0.70);
        background: linear-gradient(135deg, rgba(112, 19, 27, 0.68) 0%, rgba(86, 16, 26, 0.64) 48%, rgba(44, 14, 18, 0.72) 100%);
        box-shadow: 0 16px 28px rgba(0, 0, 0, 0.22);
    }

    html[data-theme="dark"] .inventory-search-input {
        background: transparent !important;
        color: #ffffff;
        border-bottom-color: rgba(143, 34, 48, 0.92);
        box-shadow: none !important;
    }

    html[data-theme="dark"] .inventory-search-input::placeholder {
        color: #e5e7eb;
    }

    html[data-theme="dark"] .inventory-summary-card::before {
        background: #facc15;
    }

    html[data-theme="dark"] .inventory-search-toggle {
        background: linear-gradient(135deg, #70131B, #8f2230) !important;
        border-color: rgba(250, 204, 21, 0.28) !important;
        box-shadow:
            0 0 0 3px rgba(112, 19, 27, 0.16),
            0 12px 22px rgba(0, 0, 0, 0.24) !important;
    }

    @media (max-width: 920px) {
        .controls {
            flex-direction: column;
            align-items: stretch;
            border-radius: 0 0 18px 18px;
        }

        .inventory-toolbar-actions {
            width: 100%;
            justify-content: stretch;
            margin-left: 0;
        }

        .inventory-search-shell {
            width: 100%;
        }

        .inventory-search-wrap,
        .inventory-search-shell.is-open .inventory-search-wrap {
            width: 100%;
            flex: 1 1 100%;
        }

        .inventory-search-shell:not(.is-open) .inventory-search-wrap {
            width: 0;
            flex-basis: 0;
        }

        .btn-add {
            width: 100%;
        }
    }

    html[data-theme="dark"] .modal-box {
        background: linear-gradient(180deg, rgba(31, 12, 18, 0.94), rgba(20, 8, 12, 0.94));
        border-left: 1px solid rgba(250, 204, 21, 0.2) !important;
        border-right: 1px solid rgba(250, 204, 21, 0.2) !important;
        border-top: 2px solid #facc15 !important;
        border-bottom: 2px solid #facc15 !important;
        box-shadow: 0 28px 70px rgba(0, 0, 0, 0.38);
    }

    html[data-theme="dark"] .inventory-modal-head {
        background: #4d0d17;
        border-bottom-color: rgba(250, 204, 21, 0.2);
    }

    html[data-theme="dark"] .inventory-modal-title,
    html[data-theme="dark"] .modal-panel-title {
        color: #ffffff !important;
    }

    html[data-theme="dark"] .inventory-modal-copy {
        color: rgba(255, 255, 255, 0.8);
    }

    html[data-theme="dark"] .modal-form-panel {
        background: rgba(15, 23, 42, 0.34);
        border-color: rgba(148, 163, 184, 0.24);
    }

    html[data-theme="dark"] .form-group {
        background: rgba(15, 23, 42, 0.5);
        border-color: rgba(148, 163, 184, 0.28);
    }

    html[data-theme="dark"] .form-group label {
        color: #cbd5e1 !important;
    }

    html[data-theme="dark"] .form-control,
    html[data-theme="dark"] .form-select,
    html[data-theme="dark"] .form-control option {
        background: transparent;
        color: #ffffff !important;
        border-color: transparent;
        border-bottom: 2px solid #8f2230;
    }

    html[data-theme="dark"] .form-control::placeholder {
        color: #94a3b8;
    }

    html[data-theme="dark"] #medicineFields,
    html[data-theme="dark"] #medicineExpiryField {
        border-left-color: #facc15 !important;
    }

    html[data-theme="dark"] table td,
    html[data-theme="dark"] table td div,
    html[data-theme="dark"] table td small,
    html[data-theme="dark"] table td span:not(.status),
    html[data-theme="dark"] table td[style],
    html[data-theme="dark"] table td div[style],
    html[data-theme="dark"] table td small[style] {
        color: #ffffff !important;
    }

    html[data-theme="dark"] .btn-edit {
        background: linear-gradient(180deg, rgba(51, 65, 85, 0.92) 0%, rgba(30, 41, 59, 0.96) 100%);
        color: #f8fafc;
        border-color: rgba(250, 204, 21, 0.18);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.26);
    }

    html[data-theme="dark"] .btn-edit:hover {
        color: #ffffff;
        border-color: rgba(250, 204, 21, 0.34);
        box-shadow:
            0 0 0 3px rgba(250, 204, 21, 0.10),
            0 12px 24px rgba(0, 0, 0, 0.30);
    }

    html[data-theme="dark"] .btn-delete {
        background: linear-gradient(180deg, rgba(127, 29, 29, 0.92) 0%, rgba(69, 10, 10, 0.96) 100%);
        color: #fee2e2;
        border-color: rgba(248, 113, 113, 0.22);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.28);
    }

    html[data-theme="dark"] .btn-delete:hover {
        color: #ffffff;
        border-color: rgba(248, 113, 113, 0.40);
        box-shadow:
            0 0 0 3px rgba(248, 113, 113, 0.12),
            0 12px 24px rgba(0, 0, 0, 0.32);
    }
</style>
@endpush

@section('content')
    @php
        $role = \App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '');
        $canManageInventory = $role === \App\Models\User::ROLE_SUPERADMIN;
        $highlightItemId = (string) request()->query('highlight_item', '');
    @endphp

    <div class="controls">
        <h2 class="inventory-page-title"><x-outline-icon name="cube" />Clinic Inventory</h2>
        <div class="inventory-toolbar-actions">
            <div class="inventory-search-shell" id="inventorySearchShell">
                <div class="inventory-search-wrap">
                    <input type="text" id="inventorySearchInput" class="inventory-search-input" placeholder="Search by item, category, or unit...">
                </div>
                <button type="button" class="btn-add inventory-search-toggle" id="inventorySearchToggle" aria-label="Open search" aria-expanded="false" aria-controls="inventorySearchInput">
                    <x-outline-icon name="magnifying-glass" />
                </button>
            </div>
            @if($canManageInventory)
                <button class="btn-add" onclick="openModal()">+ Add New Item</button>
            @endif
        </div>
    </div>

    <div class="card inventory-summary-card">
        <table id="inventoryTable">
            <thead>
                <tr>    
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Unit</th>
                    <th>Quantity & Dates</th>
                    <th>Stock Status</th>
                    <th>{{ $canManageInventory ? 'Actions' : 'Access' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    @php
                        $isHighlightedItem = $highlightItemId !== '' && $highlightItemId === (string) $item->id;
                        $isExpiredMedicine = $item->category == 'Medicine' && $item->expiration_date && \Carbon\Carbon::parse($item->expiration_date)->isPast();
                        $highlightClass = $isHighlightedItem
                            ? ($isExpiredMedicine ? 'inventory-row-highlight-expired' : 'inventory-row-highlight')
                            : '';
                    @endphp
                    <tr
                        id="inventory-item-{{ $item->id }}"
                        data-inventory-row
                        class="{{ $highlightClass }}"
                    >
                        <td style="font-weight: 600;">{{ $item->name }}</td>
                        <td>
                            {{ $item->category }}
                            @if($item->category == 'Medicine' && $item->medicine_type)
                                <small style="display:block; color:#64748b; font-style: italic;">({{ $item->medicine_type }})</small>
                            @endif
                        </td>
                        <td>{{ $item->unit ?: 'pcs' }}</td>
                        <td>
                            <div style="font-weight: 700;">{{ $item->quantity }} {{ $item->unit ?: 'pcs' }}</div>
                            <small style="display:block; color:#64748b; margin-top:4px;">
                                📅 Added: {{ $item->date_added ? \Carbon\Carbon::parse($item->date_added)->format('M d, Y') : 'N/A' }}
                            </small>
                            @if($item->category == 'Medicine' && $item->expiration_date)
                                <small style="display:block; color: {{ \Carbon\Carbon::parse($item->expiration_date)->isPast() ? '#b91c1c' : '#c2410c' }}; font-weight:600;">
                                    ⌛ Exp: {{ \Carbon\Carbon::parse($item->expiration_date)->format('M d, Y') }}
                                </small>
                            @endif
                        </td>
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
                                <div class="inventory-actions">
                                    <button class="btn-icon btn-edit" 
                                        onclick="editItem('{{ $item->id }}', '{{ $item->name }}', '{{ $item->category }}', '{{ $item->quantity }}', '{{ $item->unit }}', '{{ $item->medicine_type }}', '{{ $item->date_added }}', '{{ $item->expiration_date }}')">
                                        <x-outline-icon name="pencil-square" />
                                        <span>Edit</span>
                                    </button>

                                    <form action="{{ url('/admin/inventory/'.$item->id) }}" method="POST" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon btn-delete" onclick="return confirm('Delete this item?')">
                                            <x-outline-icon name="trash" />
                                            <span>Delete</span>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span style="font-size: 12px; color: #64748b; font-weight: 700;">View Only</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align: center; padding: 30px; color: #888;">No items in inventory.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($canManageInventory)
        <div id="itemModal" class="modal-overlay">
            <div class="modal-box">
                <div class="inventory-modal-head">
                    <h3 id="modalTitle" class="inventory-modal-title">Add New Item</h3>
                    <p class="inventory-modal-copy">Provide inventory details and save to update clinic stock records.</p>
                </div>

                <form id="itemForm" method="POST" action="{{ url('/admin/inventory/store') }}">
                    <div class="inventory-modal-body">
                        @csrf
                        <div id="methodField"></div>

                        <div class="modal-form-grid">
                            <div class="modal-form-panel">
                                <h4 class="modal-panel-title">Item Information</h4>

                                <div class="form-group">
                                    <label>Item Name</label>
                                    <input name="name" id="iName" class="form-control" required placeholder="e.g. Paracetamol">
                                </div>

                                <div class="form-group">
                                    <label>Category</label>
                                    <select name="category" id="iCategory" class="form-control" onchange="toggleMedicineFields()">
                                        <option value="Medicine">Medicine</option>
                                        <option value="Equipment">Equipment</option>
                                        <option value="Supplies">Supplies</option>
                                    </select>
                                </div>

                                <div id="medicineFields" class="inventory-subgroup">
                                    <div class="form-group">
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
                                </div>

                            </div>

                            <div class="modal-form-panel">
                                <h4 class="modal-panel-title">Stock Details</h4>

                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="number" name="quantity" id="iQty" class="form-control" required min="0">
                                </div>

                                <div class="form-group">
                                    <label>Unit</label>
                                    <input type="text" name="unit" id="iUnit" class="form-control" list="inventoryUnitSuggestions" required placeholder="e.g. pcs, box, bottle, vial">
                                    <datalist id="inventoryUnitSuggestions">
                                        <option value="pcs">
                                        <option value="box">
                                        <option value="bottle">
                                        <option value="vial">
                                        <option value="ampule">
                                        <option value="tablet">
                                        <option value="capsule">
                                        <option value="pack">
                                        <option value="set">
                                        <option value="tube">
                                        <option value="sachet">
                                        <option value="roll">
                                        <option value="pair">
                                        <option value="ml">
                                        <option value="mg">
                                    </datalist>
                                </div>

                                <div class="form-group">
                                    <label>Date Added</label>
                                    <input type="date" name="date_added" id="iDateAdded" class="form-control" required>
                                </div>

                                <div id="medicineExpiryField" class="inventory-subgroup">
                                    <div class="form-group">
                                        <label>Expiration Date</label>
                                        <input type="date" name="expiration_date" id="iExpDate" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-actions-row">
                            <button type="button" class="inventory-btn-cancel" onclick="closeModal()">Cancel</button>
                            <button type="submit" class="btn-add">Save Item</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    const itemModal = document.getElementById('itemModal');
    const medicineFields = document.getElementById('medicineFields');
    const medicineExpiryField = document.getElementById('medicineExpiryField');
    const medicineSelect = document.getElementById('iMedicineType');
    const expDateInput = document.getElementById('iExpDate');
    const highlightedRow = document.querySelector('.inventory-row-highlight');
    const highlightedExpiredRow = document.querySelector('.inventory-row-highlight-expired');
    const inventorySearchInput = document.getElementById('inventorySearchInput');
    const inventorySearchShell = document.getElementById('inventorySearchShell');
    const inventorySearchToggle = document.getElementById('inventorySearchToggle');
    const inventoryRows = Array.from(document.querySelectorAll('#inventoryTable tbody tr[data-inventory-row]'));

    function toggleMedicineFields() {
        const category = document.getElementById('iCategory').value;
        if (category === 'Medicine') {
            medicineFields.style.display = 'block';
            medicineExpiryField.style.display = 'block';
            medicineSelect.setAttribute('required', 'required');
            expDateInput.setAttribute('required', 'required');
        } else {
            medicineFields.style.display = 'none';
            medicineExpiryField.style.display = 'none';
            medicineSelect.removeAttribute('required');
            expDateInput.removeAttribute('required');
            medicineSelect.value = ''; 
            expDateInput.value = '';
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
        document.getElementById('iUnit').value = 'pcs';
        document.getElementById('iDateAdded').value = new Date().toISOString().split('T')[0]; // Set today as default
        document.getElementById('iExpDate').value = '';
        
        toggleMedicineFields();
    }

    function editItem(id, name, category, qty, unit, medicineType, dateAdded, expDate) {
        if (!itemModal) return;
        itemModal.style.display = 'flex';
        document.getElementById('modalTitle').innerText = 'Edit Item';
        document.getElementById('itemForm').action = "/admin/inventory/" + id;
        
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('iName').value = name;
        document.getElementById('iCategory').value = category;
        document.getElementById('iQty').value = qty;
        document.getElementById('iUnit').value = unit || 'pcs';
        document.getElementById('iDateAdded').value = dateAdded;
        
        toggleMedicineFields();
        if(category === 'Medicine') {
            document.getElementById('iMedicineType').value = medicineType;
            document.getElementById('iExpDate').value = expDate;
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

    if (highlightedRow) {
        setTimeout(function () {
            highlightedRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 180);
    }

    if (highlightedExpiredRow) {
        setTimeout(function () {
            highlightedExpiredRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 180);
    }

    if (inventorySearchInput) {
        inventorySearchInput.addEventListener('input', function () {
            const searchTerm = this.value.trim().toLowerCase();

            inventoryRows.forEach(function (row) {
                const rowText = row.innerText.toLowerCase();
                row.style.display = rowText.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    if (inventorySearchShell && inventorySearchInput && inventorySearchToggle) {
        const setInventorySearchOpenState = function (isOpen) {
            inventorySearchShell.classList.toggle('is-open', isOpen);
            inventorySearchToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        };

        setInventorySearchOpenState(inventorySearchInput.value.trim() !== '');

        inventorySearchToggle.addEventListener('click', function () {
            const shouldOpen = !inventorySearchShell.classList.contains('is-open');
            setInventorySearchOpenState(shouldOpen);

            if (shouldOpen) {
                window.requestAnimationFrame(function () {
                    inventorySearchInput.focus();
                });
            }
        });
    }
</script>
@endpush
