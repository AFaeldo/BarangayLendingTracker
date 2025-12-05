{{-- resources/views/residents/index.blade.php --}}
@extends('Layout.layout_lendingtracker')

@section('title', 'Borrowing — Brgy. San Antonio')
@section('page-title', 'Borrowing')

<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-uppercase fw-bold text-primary">
            <i class="fas fa-plus-circle me-2"></i>Add Borrowing Record
        </h2>
        <a asp-action="Index" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <!-- Main Form Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-secondary">Borrowing Details</h5>
        </div>
        <div class="card-body">
            <form asp-action="Create" method="post" id="borrowForm">
                
                <!-- Validation Summary -->
                <div asp-validation-summary="ModelOnly" class="alert alert-danger" role="alert"></div>

                <div class="row g-3">
                    <!-- Resident Selection -->
                    <div class="col-md-6">
                        <label asp-for="ResidentId" class="form-label fw-bold">Resident</label>
                        <select asp-for="ResidentId" asp-items="Model.ResidentList" class="form-select select2">
                            <option value="" disabled selected>-- Select Resident --</option>
                        </select>
                        <span asp-validation-for="ResidentId" class="text-danger"></span>
                    </div>

                    <!-- Item Selection -->
                    <div class="col-md-6">
                        <label asp-for="ItemId" class="form-label fw-bold">Item</label>
                        <select asp-for="ItemId" class="form-select" id="ItemDropdown">
                            <option value="" disabled selected data-max="0">-- Select Item --</option>
                            @if (Model.ItemList != null)
                            {
                                foreach (var item in Model.ItemList)
                                {
                                    // Disable items with 0 stock
                                    <option value="@item.Id" 
                                            data-max="@item.AvailableQuantity"
                                            disabled="@(item.AvailableQuantity == 0 ? "disabled" : null)">
                                        @item.Name (Available: @item.AvailableQuantity)
                                    </option>
                                }
                            }
                        </select>
                        <span asp-validation-for="ItemId" class="text-danger"></span>
                        
                        <!-- Stock Helper Text -->
                        <small id="stockHelp" class="form-text text-muted mt-1" style="display:none;">
                            Max available: <span id="maxStockDisplay" class="fw-bold text-dark">0</span>
                        </small>
                    </div>

                    <!-- Quantity -->
                    <div class="col-md-4">
                        <label asp-for="Quantity" class="form-label fw-bold">Quantity</label>
                        <input asp-for="Quantity" type="number" class="form-control" id="QuantityInput" min="1" max="1">
                        <span asp-validation-for="Quantity" class="text-danger"></span>
                        <div class="invalid-feedback" id="qtyError">
                            Cannot borrow more than available stock.
                        </div>
                    </div>

                    <!-- Date Borrowed -->
                    <div class="col-md-4">
                        <label asp-for="DateBorrowed" class="form-label fw-bold">Date Borrowed</label>
                        <input asp-for="DateBorrowed" type="date" class="form-control" value="@DateTime.Now.ToString("yyyy-MM-dd")">
                        <span asp-validation-for="DateBorrowed" class="text-danger"></span>
                    </div>

                    <!-- Due Date -->
                    <div class="col-md-4">
                        <label asp-for="DueDate" class="form-label fw-bold">Due Date (Optional)</label>
                        <input asp-for="DueDate" type="date" class="form-control">
                        <span asp-validation-for="DueDate" class="text-danger"></span>
                    </div>

                    <!-- Remarks -->
                    <div class="col-12">
                        <label asp-for="Remarks" class="form-label fw-bold">Remarks</label>
                        <textarea asp-for="Remarks" class="form-control" rows="3" placeholder="Enter reason or condition of items..."></textarea>
                        <span asp-validation-for="Remarks" class="text-danger"></span>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back();">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Save Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section Scripts {
    @{await Html.RenderPartialAsync("_ValidationScriptsPartial");}

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const itemDropdown = document.getElementById('ItemDropdown');
            const qtyInput = document.getElementById('QuantityInput');
            const maxStockDisplay = document.getElementById('maxStockDisplay');
            const stockHelp = document.getElementById('stockHelp');

            // Function to update quantity limits
            function updateStockLimits() {
                const selectedOption = itemDropdown.options[itemDropdown.selectedIndex];
                const maxQty = parseInt(selectedOption.getAttribute('data-max')) || 0;

                // Update UI Text
                maxStockDisplay.textContent = maxQty;
                
                if (itemDropdown.value) {
                    stockHelp.style.display = 'block';
                    // Update Input Attributes
                    qtyInput.setAttribute('max', maxQty);
                    
                    // Reset quantity to 1 if it's currently 0 or greater than max
                    if (parseInt(qtyInput.value) > maxQty || parseInt(qtyInput.value) < 1) {
                        qtyInput.value = 1;
                    }
                } else {
                    stockHelp.style.display = 'none';
                }
            }

            // Event Listeners
            itemDropdown.addEventListener('change', updateStockLimits);

            // Initial Check (in case of validation error reload)
            if(itemDropdown.value) {
                updateStockLimits();
            }

            // Client-side prevention of submitting more than max
            qtyInput.addEventListener('input', function() {
                const max = parseInt(this.getAttribute('max')) || 0;
                const current = parseInt(this.value);

                if (current > max) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        });
    </script>
}