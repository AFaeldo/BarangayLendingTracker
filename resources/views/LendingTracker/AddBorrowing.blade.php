@extends('Layout.layout_lendingtracker')

@section('title', 'Add Borrowing — Brgy. San Antonio')
@section('page-title', 'Borrowing')

@section('content')

    {{-- Header --}}
    <div class="d-flex justify-between align-center mb-15">
        <h2 class="uppercase">Add Borrowing Record</h2>
        <a href="{{ route('borrowing.index') }}" class="btn btn-secondary text-decoration-none">
            <i class="fas fa-arrow-left mr-8"></i> Back to List
        </a>
    </div>

    {{-- Main Form Card --}}
    <div class="card p-20">
        <h3 class="mb-15 text-gray">Borrowing Details</h3>

        <form action="{{ route('borrowing.store') }}" method="post" id="borrowForm" class="d-flex flex-col flex-gap-12">
            @csrf
            
            {{-- Validation Summary --}}
            @if ($errors->any())
                <div class="error-message mb-12">
                    <ul class="error-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-row">
                {{-- Resident Selection --}}
                <div class="flex-col d-flex flex-1" style="min-width: 300px;">
                    <label for="resident_id" class="mb-10 text-bold">Resident <span class="text-danger">*</span></label>
                    <select name="resident_id" id="resident_id" class="select w-full @error('resident_id') is-invalid @enderror" required>
                        <option value="" disabled selected>-- Select Resident --</option>
                        @foreach ($residents as $resident)
                            <option value="{{ $resident->id }}" {{ old('resident_id') == $resident->id ? 'selected' : '' }}>
                                {{ $resident->last_name }}, {{ $resident->first_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Item Selection --}}
                <div class="flex-col d-flex flex-1" style="min-width: 300px;">
                    <label for="item_id" class="mb-10 text-bold">Item <span class="text-danger">*</span></label>
                    <select name="item_id" id="ItemDropdown" class="select w-full @error('item_id') is-invalid @enderror" required>
                        <option value="" disabled selected data-max="0">-- Select Item --</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" 
                                    data-max="{{ $item->available_quantity }}"
                                    {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }} (Available: {{ $item->available_quantity }})
                            </option>
                        @endforeach
                    </select>
                    
                    {{-- Stock Helper Text --}}
                    <small id="stockHelp" class="text-sm mt-4 text-gray" style="display:none;">
                        Max available: <span id="maxStockDisplay" class="text-bold">0</span>
                    </small>
                </div>
            </div>

            <div class="form-row">
                {{-- Quantity --}}
                <div class="flex-col d-flex flex-1" style="min-width: 200px;">
                    <label for="quantity" class="mb-10 text-bold">Quantity <span class="text-danger">*</span></label>
                    <input name="quantity" type="number" class="input w-full @error('quantity') is-invalid @enderror" 
                           id="QuantityInput" min="1" max="1" value="{{ old('quantity', 1) }}" required placeholder="Enter quantity">
                    <div class="text-danger text-sm mt-2" id="qtyError" style="display:none;">
                        Cannot borrow more than available stock.
                    </div>
                </div>

                {{-- Date Borrowed --}}
                <div class="flex-col d-flex flex-1" style="min-width: 200px;">
                    <label for="date_borrowed" class="mb-10 text-bold">Date Borrowed <span class="text-danger">*</span></label>
                    <input name="date_borrowed" type="date" class="input w-full @error('date_borrowed') is-invalid @enderror" 
                           value="{{ old('date_borrowed', date('Y-m-d')) }}" required>
                </div>

                {{-- Due Date --}}
                <div class="flex-col d-flex flex-1" style="min-width: 200px;">
                    <label for="due_date" class="mb-10 text-bold">Due Date (Optional)</label>
                    <input name="due_date" type="date" class="input w-full @error('due_date') is-invalid @enderror" 
                           value="{{ old('due_date') }}">
                </div>
            </div>

            {{-- Toggle for Custom Remarks --}}
            <div class="form-row align-center mt-10">
                 <label class="checkbox-label" style="font-size: 1rem;">
                    <input type="checkbox" id="toggleOtherReason" class="checkbox-input mr-8">
                    Other (please specify reason/condition)
                </label>
            </div>

            {{-- Remarks (Hidden by default unless toggled) --}}
            <div class="flex-col d-none w-full" id="remarksContainer">
                <label for="remarks" class="mb-10 text-bold">Remarks / Other Details</label>
                <textarea name="remarks" id="remarksInput" class="input w-full @error('remarks') is-invalid @enderror" 
                          rows="3" placeholder="Enter reason or specific condition...">{{ old('remarks') }}</textarea>
            </div>

            <hr class="divider my-16" style="background: rgba(0,0,0,0.1);">

            <div class="d-flex justify-between">
                <a href="{{ route('borrowing.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-success px-20">
                    <i class="fas fa-save mr-8"></i> Save Record
                </button>
            </div>
        </form>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const itemDropdown = document.getElementById('ItemDropdown');
        const qtyInput = document.getElementById('QuantityInput');
        const maxStockDisplay = document.getElementById('maxStockDisplay');
        const stockHelp = document.getElementById('stockHelp');
        const qtyError = document.getElementById('qtyError');

        // Toggle 'Other' reason
        const toggleOther = document.getElementById('toggleOtherReason');
        const remarksContainer = document.getElementById('remarksContainer');
        const remarksInput = document.getElementById('remarksInput');

        if(toggleOther) {
            toggleOther.addEventListener('change', function() {
                if(this.checked) {
                    remarksContainer.classList.remove('d-none');
                    remarksContainer.classList.add('d-flex');
                    remarksInput.focus();
                } else {
                    remarksContainer.classList.add('d-none');
                    remarksContainer.classList.remove('d-flex');
                }
            });
        }

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
                this.classList.add('is-invalid'); // You might want to define this style or use a border color utility
                this.style.borderColor = 'var(--danger)';
                qtyError.style.display = 'block';
            } else {
                this.classList.remove('is-invalid');
                this.style.borderColor = '';
                qtyError.style.display = 'none';
            }
        });
    });
</script>
@endsection
