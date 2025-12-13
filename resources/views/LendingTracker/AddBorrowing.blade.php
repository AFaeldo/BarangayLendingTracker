@extends('Layout.layout_lendingtracker')

@section('title', 'Add Borrowing — Brgy. San Antonio')
@section('page-title', 'Borrowing')

@section('content')

{{-- PAGE HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-semibold">Add Borrowing Record</h2>
    <a href="{{ route('borrowing.index') }}" class="btn btn-outline-secondary">
        ← Back to List
    </a>
</div>

{{-- FORM CARD --}}
<div class="card shadow-sm">
    <div class="card-body">

        <h5 class="border-start border-4 ps-3 mb-4 text-secondary">
            Borrowing Details
        </h5>

        <form action="{{ route('borrowing.store') }}" method="POST" id="borrowForm">
            @csrf

            {{-- VALIDATION ERRORS --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- RESIDENT & ITEM --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Resident <span class="text-danger">*</span>
                    </label>
                    <select name="resident_id" class="form-select" required>
                        <option value="" disabled selected>— Select Resident —</option>
                        @foreach ($residents as $resident)
                            <option value="{{ $resident->id }}"
                                {{ old('resident_id') == $resident->id ? 'selected' : '' }}>
                                {{ $resident->last_name }}, {{ $resident->first_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Item <span class="text-danger">*</span>
                    </label>
                    <select name="item_id" id="ItemDropdown" class="form-select" required>
                        <option value="" disabled selected data-max="0">
                            — Select Item —
                        </option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}"
                                    data-max="{{ $item->available_quantity }}">
                                {{ $item->name }}
                                (Available: {{ $item->available_quantity }})
                            </option>
                        @endforeach
                    </select>
                    <small id="stockHelp" class="text-muted d-none">
                        Maximum available:
                        <strong id="maxStockDisplay">0</strong>
                    </small>
                </div>
            </div>

            {{-- QUANTITY & DATES --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Quantity <span class="text-danger">*</span>
                    </label>
                    <input type="number"
                           name="quantity"
                           id="QuantityInput"
                           class="form-control"
                           min="1"
                           value="{{ old('quantity', 1) }}"
                           required>

                    <small id="qtyError" class="text-danger d-none">
                        Quantity exceeds available stock.
                    </small>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Date Borrowed <span class="text-danger">*</span>
                    </label>
                    <input type="date"
                           name="date_borrowed"
                           class="form-control"
                           value="{{ old('date_borrowed', date('Y-m-d')) }}"
                           required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Due Date
                    </label>
                    <input type="date"
                           name="due_date"
                           class="form-control"
                           value="{{ old('due_date') }}">
                </div>
            </div>

            {{-- OTHER / REMARKS --}}
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="toggleRemarks">
                <label class="form-check-label fw-semibold" for="toggleRemarks">
                    Other (specify reason or condition)
                </label>
            </div>

            <div id="remarksContainer" class="border rounded p-3 bg-light d-none mb-3">
                <label class="form-label fw-semibold">
                    Remarks / Other Details
                </label>
                <textarea name="remarks"
                          rows="3"
                          class="form-control"
                          placeholder="Enter reason or special condition...">{{ old('remarks') }}</textarea>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('borrowing.index') }}"
                   class="btn btn-outline-secondary">
                    Cancel
                </a>

                <button type="submit" class="btn btn-success px-4">
                    <i class="fas fa-save me-1"></i> Save Record
                </button>
            </div>

        </form>
    </div>
</div>

{{-- PAGE SCRIPT --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const item = document.getElementById('ItemDropdown');
    const qty = document.getElementById('QuantityInput');
    const stockHelp = document.getElementById('stockHelp');
    const maxDisplay = document.getElementById('maxStockDisplay');
    const qtyError = document.getElementById('qtyError');

    const toggle = document.getElementById('toggleRemarks');
    const remarksBox = document.getElementById('remarksContainer');

    toggle.addEventListener('change', () => {
        remarksBox.classList.toggle('d-none', !toggle.checked);
    });

    item.addEventListener('change', () => {
        const max = parseInt(item.selectedOptions[0].dataset.max || 0);
        maxDisplay.textContent = max;
        qty.max = max;
        stockHelp.classList.remove('d-none');
        if (qty.value > max) qty.value = max;
    });

    qty.addEventListener('input', () => {
        const max = parseInt(qty.max || 0);
        qtyError.classList.toggle('d-none', qty.value <= max);
    });

});
</script>
@endpush

@endsection
