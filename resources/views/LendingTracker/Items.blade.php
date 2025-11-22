@extends('Layout.layout_lendingtracker')

@section('title', 'Items — Brgy. San Antonio')
@section('page-title', 'Items')

@section('content')

    {{-- Success message --}}
    @if (session('success'))
        <div class="success-message" style="margin-bottom:12px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="error-message" style="margin-bottom:12px;">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Search + Add --}}
    <div class="top-bar">
        <div class="form-row">
            <input class="input"
                   type="text"
                   placeholder="Search item..."
                   data-filter-input
                   data-filter-target="#items-table tbody tr">
            <button class="btn" type="button">Search</button>
        </div>

        <button class="btn" type="button" id="btn-open-add-item">
            <i class="fas fa-plus" style="margin-right:8px"></i>
            Add Item
        </button>
    </div>

    {{-- Items table --}}
    <div class="card table-card" style="margin-top:20px; padding:18px;">
        <h3>Item Inventory</h3>

        <table class="table" id="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Available</th>
                    <th>Condition</th>
                    <th>Status</th>
                    <th style="width:140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->available_quantity }}</td>
                        <td>{{ $item->condition }}</td>
                        <td>{{ $item->status }}</td>
                        <td style="display:flex; gap:6px;">

                            {{-- VIEW --}}
                            <button
                                type="button"
                                class="btn btn-secondary"
                                style="padding:4px 8px;font-size:0.8rem;background:#5a5a5a;"
                                data-view-item
                                data-item='@json($item)'
                            >
                                View
                            </button>

                            {{-- EDIT --}}
                            <button
                                type="button"
                                class="btn"
                                style="padding:4px 8px;font-size:0.8rem;"
                                data-edit-item
                                data-update-url="{{ route('items.update', $item) }}"
                                data-id="{{ $item->id }}"
                                data-name="{{ $item->name }}"
                                data-quantity="{{ $item->quantity }}"
                                data-available="{{ $item->available_quantity }}"
                                data-description="{{ $item->description }}"
                                data-condition="{{ $item->condition }}"
                                data-status="{{ $item->status }}"
                            >
                                Edit
                            </button>

                            {{-- DELETE --}}
                            <form method="POST"
                                  action="{{ route('items.destroy', $item) }}"
                                  onsubmit="return confirm('Delete this item?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-secondary"
                                        style="padding:4px 8px;font-size:0.8rem;background:#b3261e;">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:20px; color:gray;">
                            No items found. Click <strong>Add Item</strong> to register a new asset.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ADD / EDIT ITEM MODAL --}}
    <div class="modal-backdrop" id="item-backdrop">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="itemModalTitle">

            <div class="modal-header">
                <h3 id="itemModalTitle">Add Item</h3>
                <button type="button" class="modal-close" id="btn-close-item">&times;</button>
            </div>

            <form class="modal-body" method="POST" id="item-form"
                  action="{{ route('items.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="modal-row">
                    <label>Item Name</label>
                    <input type="text" name="name" class="input" required>
                </div>

                <div class="modal-row">
                    <label>Photo (optional)</label>
                    <input type="file" name="photo" class="input">
                </div>

                <div class="modal-row">
                    <label>Total Quantity</label>
                    <input type="number" name="quantity" class="input" min="0" required>
                </div>

                <div class="modal-row">
                    <label>Condition</label>
                    <select name="condition" class="select" required>
                        <option value="Good">Good</option>
                        <option value="For Repair">For Repair</option>
                        <option value="Damaged">Damaged</option>
                    </select>
                </div>

                <div class="modal-row">
                    <label>Status</label>
                    <select name="status" class="select" required>
                        <option value="Available">Available</option>
                        <option value="Borrowed">Borrowed</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                </div>

                <div class="modal-row">
                    <label>Description</label>
                    <textarea name="description" class="input" rows="3"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btn-cancel-item">Cancel</button>
                    <button type="submit" class="btn">Save Item</button>
                </div>
            </form>

        </div>
    </div>

    {{-- VIEW ITEM MODAL --}}
    <div class="modal-backdrop" id="view-item-backdrop">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="viewItemTitle">
            <div class="modal-header">
                <h3 id="viewItemTitle">Item Details</h3>
                <button type="button" class="modal-close" id="btn-close-view-item">&times;</button>
            </div>

            <div class="modal-body">
                <p><strong>ID:</strong> <span id="view-item-id"></span></p>
                <p><strong>Name:</strong> <span id="view-item-name"></span></p>
                <p><strong>Quantity:</strong> <span id="view-item-qty"></span></p>
                <p><strong>Available:</strong> <span id="view-item-avail"></span></p>
                <p><strong>Condition:</strong> <span id="view-item-cond"></span></p>
                <p><strong>Status:</strong> <span id="view-item-status"></span></p>
                <p><strong>Description:</strong> <span id="view-item-desc"></span></p>

                <hr style="margin:16px 0;">

                <h4>Borrowing Logs</h4>
                <table class="table" style="margin-top:8px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Resident</th>
                            <th>Date Borrowed</th>
                            <th>Due Date</th>
                            <th>Date Returned</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="item-logs-body">
                        <tr>
                            <td colspan="6" style="text-align:center; padding:10px; color:gray;">
                                No logs yet for this item.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btn-close-view-item-2">Close</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ----- Add/Edit Item Modal -----
    const openBtn   = document.getElementById('btn-open-add-item');
    const closeBtn  = document.getElementById('btn-close-item');
    const cancelBtn = document.getElementById('btn-cancel-item');
    const backdrop  = document.getElementById('item-backdrop');
    const form      = document.getElementById('item-form');
    const title     = document.getElementById('itemModalTitle');

    function openModal() { backdrop.classList.add('show'); }
    function closeModal() { backdrop.classList.remove('show'); }

    function setFormForCreate() {
        title.textContent = 'Add Item';
        form.action = "{{ route('items.store') }}";
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
        form.reset();
    }

    if (openBtn) {
        openBtn.addEventListener('click', function () {
            setFormForCreate();
            openModal();
        });
    }

    if (closeBtn)  closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    backdrop.addEventListener('click', function(e){
        if (e.target === backdrop) closeModal();
    });

    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') closeModal();
    });

    document.querySelectorAll('[data-edit-item]').forEach(btn => {
        btn.addEventListener('click', function () {
            setFormForCreate();
            title.textContent = 'Edit Item';
            form.action = this.dataset.updateUrl;

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);

            form.elements['name'].value        = this.dataset.name || '';
            form.elements['quantity'].value    = this.dataset.quantity || 0;
            form.elements['condition'].value   = this.dataset.condition || 'Good';
            form.elements['status'].value      = this.dataset.status || 'Available';
            form.elements['description'].value = this.dataset.description || '';

            openModal();
        });
    });

    // ----- View Item Modal -----
    const viewBackdrop = document.getElementById('view-item-backdrop');

    function openViewModal() { viewBackdrop.classList.add('show'); }
    function closeViewModal() { viewBackdrop.classList.remove('show'); }

    document.getElementById('btn-close-view-item')?.addEventListener('click', closeViewModal);
    document.getElementById('btn-close-view-item-2')?.addEventListener('click', closeViewModal);

    viewBackdrop?.addEventListener('click', function(e){
        if (e.target === viewBackdrop) closeViewModal();
    });

    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') closeViewModal();
    });

    document.querySelectorAll('[data-view-item]').forEach(btn => {
        btn.addEventListener('click', function () {
            const i = JSON.parse(this.dataset.item);

            document.getElementById('view-item-id').textContent    = i.id ?? '';
            document.getElementById('view-item-name').textContent  = i.name ?? '';
            document.getElementById('view-item-qty').textContent   = i.quantity ?? 0;
            document.getElementById('view-item-avail').textContent = i.available_quantity ?? 0;
            document.getElementById('view-item-cond').textContent  = i.condition ?? '';
            document.getElementById('view-item-status').textContent= i.status ?? '';
            document.getElementById('view-item-desc').textContent  = i.description ?? '';

            // Fill logs
            const logsBody = document.getElementById('item-logs-body');
            logsBody.innerHTML = '';

            if (i.borrowings && i.borrowings.length) {
                i.borrowings.forEach(log => {
                    const residentName = log.resident
                        ? (log.resident.last_name + ', ' + log.resident.first_name)
                        : '—';

                    const row = `
                        <tr>
                            <td>${log.id ?? ''}</td>
                            <td>${residentName}</td>
                            <td>${log.date_borrowed ?? ''}</td>
                            <td>${log.due_date ?? '—'}</td>
                            <td>${log.returned_at ?? '—'}</td>
                            <td>${log.status ?? ''}</td>
                        </tr>
                    `;
                    logsBody.insertAdjacentHTML('beforeend', row);
                });
            } else {
                logsBody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align:center; padding:10px; color:gray;">
                            No logs yet for this item.
                        </td>
                    </tr>`;
            }

            openViewModal();
        });
    });

    // Auto-open add/edit modal if validation errors exist
    @if ($errors->any())
        openModal();
    @endif
});
</script>
@endpush
