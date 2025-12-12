@extends('Layout.layout_lendingtracker')

@section('title', 'Add Borrowing — Brgy. San Antonio')
@section('page-title', 'Borrowing')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-uppercase fw-bold text-primary">
            <i class="fas fa-plus-circle me-2"></i>Add Borrowing Record
        </h2>
        <a href="{{ route('borrowing.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <!-- Main Form Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-secondary">Borrowing Details</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('borrowing.store') }}" method="post" id="borrowForm">
                @csrf
                
                <!-- Validation Summary -->
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row g-3">
                    <!-- Resident Selection -->
                    <div class="col-md-6">
                        <label for="resident_id" class="form-label fw-bold">Resident</label>
                        <select name="resident_id" id="resident_id" class="form-select select2 @error('resident_id') is-invalid @enderror">
                            <option value="" disabled selected>-- Select Resident --</option>
                            @foreach ($residents as $resident)
                                <option value="{{ $resident->id }}" {{ old('resident_id') == $resident->id ? 'selected' : '' }}>
                                    {{ $resident->last_name }}, {{ $resident->first_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('resident_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Item Selection -->
                    <div class="col-md-6">
                        <label for="item_id" class="form-label fw-bold">Item</label>
                        <select name="item_id" id="ItemDropdown" class="form-select @error('item_id') is-invalid @enderror">
                            <option value="" disabled selected data-max="0">-- Select Item --</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}" 
                                        data-max="{{ $item->available_quantity }}"
                                        {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }} (Available: {{ $item->available_quantity }})
                                </option>
                            @endforeach
                        </select>
                        @error('item_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <!-- Stock Helper Text -->
                        <small id="stockHelp" class="form-text text-muted mt-1" style="display:none;">
                            Max available: <span id="maxStockDisplay" class="fw-bold text-dark">0</span>
                        </small>
                    </div>

                    <!-- Quantity -->
                    <div class="col-md-4">
                        <label for="quantity" class="form-label fw-bold">Quantity</label>
                        <input name="quantity" type="number" class="form-control @error('quantity') is-invalid @enderror" 
                               id="QuantityInput" min="1" max="1" value="{{ old('quantity', 1) }}">
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="invalid-feedback" id="qtyError" style="display:none;">
                            Cannot borrow more than available stock.
                        </div>
                    </div>

                    <!-- Date Borrowed -->
                    <div class="col-md-4">
                        <label for="date_borrowed" class="form-label fw-bold">Date Borrowed</label>
                        <input name="date_borrowed" type="date" class="form-control @error('date_borrowed') is-invalid @enderror" 
                               value="{{ old('date_borrowed', date('Y-m-d')) }}">
                        @error('date_borrowed')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Due Date -->
                    <div class="col-md-4">
                        <label for="due_date" class="form-label fw-bold">Due Date (Optional)</label>
                        <input name="due_date" type="date" class="form-control @error('due_date') is-invalid @enderror" 
                               value="{{ old('due_date') }}">
                        @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remarks -->
                    <div class="col-12">
                        <label for="remarks" class="form-label fw-bold">Remarks</label>
                        <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" 
                                  rows="3" placeholder="Enter reason or condition of items...">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('borrowing.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Save Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const itemDropdown = document.getElementById('ItemDropdown');
        const qtyInput = document.getElementById('QuantityInput');
        const maxStockDisplay = document.getElementById('maxStockDisplay');
        const stockHelp = document.getElementById('stockHelp');
        const qtyError = document.getElementById('qtyError');

        // Function to update quantity limits
        function updateStockLimits() {
            const selectedOption = itemDropdown.options[itemDropdown.selectedIndex];
            // If no option selected (or default), default to 0
            const maxQty = selectedOption ? (parseInt(selectedOption.getAttribute('data-max')) || 0) : 0;

            // Update UI Text
            maxStockDisplay.textContent = maxQty;
            
            if (itemDropdown.value) {
                stockHelp.style.display = 'block';
                // Update Input Attributes
                qtyInput.setAttribute('max', maxQty);
                
                // If current value is invalid given the new max, reset it
                // Logic: if current > max, set to max. If current < 1, set to 1.
                // However, we must respect that if max is 0, we can't really borrow anything.
                // The controller filters out items with 0 stock, but good to be safe.
                let currentVal = parseInt(qtyInput.value) || 0;
                
                if (maxQty > 0) {
                     if (currentVal > maxQty) {
                        qtyInput.value = maxQty;
                    } else if (currentVal < 1) {
                        qtyInput.value = 1;
                    }
                }
            } else {
                stockHelp.style.display = 'none';
                qtyInput.removeAttribute('max');
            }
        }

        // Event Listeners
        itemDropdown.addEventListener('change', updateStockLimits);

        // Initial Check (in case of sticky data on validation failure)
        if(itemDropdown.value) {
            updateStockLimits();
        }

        // Client-side prevention of submitting more than max
        qtyInput.addEventListener('input', function() {
            const max = parseInt(this.getAttribute('max')) || 0;
            const current = parseInt(this.value);

            if (max > 0 && current > max) {
                this.classList.add('is-invalid');
                qtyError.style.display = 'block';
            } else {
                this.classList.remove('is-invalid');
                qtyError.style.display = 'none';
            }
        });
    });
</script>
@endsection
