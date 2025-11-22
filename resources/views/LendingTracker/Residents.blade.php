@extends('Layout.layout_lendingtracker')

@section('title', 'Residents — Brgy. San Antonio')
@section('page-title', 'Residents')

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

<!-- Search + Add -->
<div class="top-bar">
    <div class="form-row">
        <input class="input"
               type="text"
               placeholder="Search Resident Name or ID..."
               data-filter-input
               data-filter-target="#residents-table tbody tr">
        <button class="btn" type="button">Search</button>
    </div>

    <button class="btn" type="button" id="btn-open-add-resident">
        <i class="fas fa-user-plus" style="margin-right:8px"></i>
        Add New Resident
    </button>
</div>

<!-- Residents Table -->
<div class="card table-card" style="margin-top:20px; padding:18px;">
    <h3>Resident List</h3>
    <table class="table" id="residents-table">
        <thead>
            <tr>
                <th>Resident ID</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>M.I</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Sitio / Purok</th>
                <th>Contact</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($residents as $resident)
                <tr>
                    <td>{{ $resident->id }}</td>
                    <td>{{ $resident->last_name }}</td>
                    <td>{{ $resident->first_name }}</td>
                    <td>{{ $resident->middle_initial }}</td>
                    <td>{{ $resident->gender }}</td>
                    <td>{{ $resident->age }}</td>
                    <td>{{ $resident->purok ?? $resident->sitio }}</td>
                    <td>{{ $resident->contact }}</td>
                    <td>{{ $resident->status }}</td>
                    <td style="display:flex; gap:6px; flex-wrap:wrap;">
                        {{-- VIEW --}}
                        <button type="button"
                                class="btn btn-secondary"
                                style="padding:4px 8px;font-size:0.8rem;background:#5a5a5a;"
                                data-view-resident
                                data-resident='@json($resident)'>
                            View
                        </button>

                        {{-- EDIT --}}
                        <button
                            type="button"
                            class="btn"
                            style="padding:4px 8px;font-size:0.8rem;"
                            data-edit-resident
                            data-id="{{ $resident->id }}"
                            data-update-url="{{ route('residents.update', $resident) }}"
                            data-last_name="{{ $resident->last_name }}"
                            data-first_name="{{ $resident->first_name }}"
                            data-middle_name="{{ $resident->middle_name }}"
                            data-middle_initial="{{ $resident->middle_initial }}"
                            data-alias="{{ $resident->alias }}"
                            data-gender="{{ $resident->gender }}"
                            data-marital_status="{{ $resident->marital_status }}"
                            data-spouse_name="{{ $resident->spouse_name }}"
                            data-purok="{{ $resident->purok ?? $resident->sitio }}"
                            data-employment_status="{{ $resident->employment_status }}"
                            data-birthdate="{{ $resident->birthdate }}"
                            data-place_of_birth="{{ $resident->place_of_birth }}"
                            data-age="{{ $resident->age }}"
                            data-age_month="{{ $resident->age_month }}"
                            data-height_cm="{{ $resident->height_cm }}"
                            data-weight_kg="{{ $resident->weight_kg }}"
                            data-religion="{{ $resident->religion }}"
                            data-voter_status="{{ $resident->voter_status }}"
                            data-is_pwd="{{ $resident->is_pwd }}"
                            data-contact="{{ $resident->contact }}"
                            data-status="{{ $resident->status }}"
                            data-remarks="{{ $resident->remarks }}"
                        >
                            Edit
                        </button>

                        {{-- DELETE --}}
                        <form method="POST"
                              action="{{ route('residents.destroy', $resident) }}"
                              onsubmit="return confirm('Delete this resident?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-secondary"
                                    style="padding:4px 8px;font-size:0.8rem;background:#b3261e;">
                                Delete
                            </button>
                        </form>

                       <button
    type="button"
    class="btn btn-secondary"
    style="padding:4px 8px;font-size:0.8rem;background:#5a5a5a;"
    data-borrow-resident
    data-id="{{ $resident->id }}"
    data-name="{{ $resident->last_name }}, {{ $resident->first_name }}"
>
    Add Borrowing
</button>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center; padding:20px; color:gray;">
                        No residents found. Click <strong>Add New Resident</strong> to register a new one.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- ADD / EDIT RESIDENT MODAL -->
<div class="modal-backdrop" id="add-resident-backdrop">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="addResidentTitle">

        <div class="modal-header">
            <h3 id="addResidentTitle">Add New Resident</h3>
            <button type="button" class="modal-close" id="btn-close-add-resident">&times;</button>
        </div>

        <form class="modal-body modal-grid" method="POST" id="resident-form" action="{{ route('residents.store') }}">
            @csrf

            <div class="modal-row full-width" id="resident-id-row" style="display:none;">
                <label>Resident ID</label>
                <input type="text" name="resident_id_display" class="input" readonly>
            </div>

            <!-- LEFT COLUMN -->
            <div class="modal-col">
                <div class="modal-row">
                    <label>Last Name</label>
                    <input type="text" name="last_name" class="input" required>
                </div>

                <div class="modal-row">
                    <label>First Name</label>
                    <input type="text" name="first_name" class="input" required>
                </div>

                <div class="modal-row">
                    <label>Middle Name</label>
                    <input type="text" name="middle_name" class="input">
                </div>

                <div class="modal-row">
                    <label>Alias</label>
                    <input type="text" name="alias" class="input">
                </div>

                <div class="modal-row">
                    <label>Gender</label>
                    <select name="gender" class="select" required>
                        <option value="" disabled selected>Select gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Non-binary">Non-binary</option>
                        <option value="Prefer not to say">Prefer not to say</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="modal-row">
                    <label>Marital Status</label>
                    <select name="marital_status" class="select">
                        <option value="" selected>-- Select --</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Separated">Separated</option>
                        <option value="Widowed">Widowed</option>
                    </select>
                </div>

                <div class="modal-row">
                    <label>Name of Spouse</label>
                    <input type="text" name="spouse_name" class="input">
                </div>

                <div class="modal-row">
                    <label>Purok / Sitio</label>
                    <input type="text" name="purok" class="input">
                </div>

                <div class="modal-row">
                    <label>Employment Status</label>
                    <select name="employment_status" class="select">
                        <option value="" selected>-- Select --</option>
                        <option value="Employed">Employed</option>
                        <option value="Self-employed">Self-employed</option>
                        <option value="Unemployed">Unemployed</option>
                        <option value="Student">Student</option>
                        <option value="Retired">Retired</option>
                    </select>
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="modal-col">
                <div class="modal-row">
                    <label>Birthdate</label>
                    <input type="date" name="birthdate" class="input">
                </div>

                <div class="modal-row">
                    <label>Place of Birth</label>
                    <input type="text" name="place_of_birth" class="input">
                </div>

                <div class="modal-row">
                    <label>Age</label>
                    <input type="number" name="age" class="input" min="0">
                </div>

                <div class="modal-row">
                    <label>Age (Months)</label>
                    <input type="number" name="age_month" class="input" min="0">
                </div>

                <div class="modal-row">
                    <label>Height (cm)</label>
                    <input type="number" name="height_cm" class="input" min="0">
                </div>

                <div class="modal-row">
                    <label>Weight (kg)</label>
                    <input type="number" name="weight_kg" class="input" min="0">
                </div>

                <div class="modal-row">
                    <label>Religion</label>
                    <input type="text" name="religion" class="input">
                </div>

                <div class="modal-row">
                    <label>Voter Status</label>
                    <select name="voter_status" class="select">
                        <option value="" selected>-- Select --</option>
                        <option value="Registered">Registered</option>
                        <option value="Not Registered">Not Registered</option>
                    </select>
                </div>

                <div class="modal-row">
                    <label>Person with Disability (PWD)</label>
                    <input type="checkbox" name="is_pwd" value="1" style="width:auto;">
                </div>

                <div class="modal-row">
                    <label>Contact Number</label>
                    <input type="text" name="contact" class="input">
                </div>

                <div class="modal-row">
                    <label>Status</label>
                    <select name="status" class="select" required>
                        <option value="Active" selected>Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Full width remarks + buttons -->
            <div class="modal-row full-width">
                <label>Remarks</label>
                <textarea name="remarks" class="input" rows="3"></textarea>
            </div>

            <div class="modal-footer full-width">
                <button type="button" class="btn btn-secondary" id="btn-cancel-add-resident">Cancel</button>
                <button type="submit" class="btn">Save Resident</button>
            </div>
        </form>
    </div>
</div>

<!-- VIEW RESIDENT MODAL -->
<div class="modal-backdrop" id="view-resident-backdrop">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="viewResidentTitle">
        <div class="modal-header">
            <h3 id="viewResidentTitle">Resident Details</h3>
            <button type="button" class="modal-close" id="btn-close-view-resident">&times;</button>
        </div>

        <div class="modal-body modal-grid">
            <div class="modal-col">
                <p><strong>Resident ID:</strong> <span id="view-id"></span></p>
                <p><strong>Last Name:</strong> <span id="view-last-name"></span></p>
                <p><strong>First Name:</strong> <span id="view-first-name"></span></p>
                <p><strong>Middle Name:</strong> <span id="view-middle-name"></span></p>
                <p><strong>Alias:</strong> <span id="view-alias"></span></p>
                <p><strong>Gender:</strong> <span id="view-gender"></span></p>
                <p><strong>Marital Status:</strong> <span id="view-marital"></span></p>
                <p><strong>Name of Spouse:</strong> <span id="view-spouse"></span></p>
                <p><strong>Purok / Sitio:</strong> <span id="view-purok"></span></p>
                <p><strong>Employment Status:</strong> <span id="view-employment"></span></p>
            </div>

            <div class="modal-col">
                <p><strong>Birthdate:</strong> <span id="view-birthdate"></span></p>
                <p><strong>Place of Birth:</strong> <span id="view-birthplace"></span></p>
                <p><strong>Age:</strong> <span id="view-age"></span></p>
                <p><strong>Age (Months):</strong> <span id="view-age-month"></span></p>
                <p><strong>Height (cm):</strong> <span id="view-height"></span></p>
                <p><strong>Weight (kg):</strong> <span id="view-weight"></span></p>
                <p><strong>Religion:</strong> <span id="view-religion"></span></p>
                <p><strong>Voter Status:</strong> <span id="view-voter"></span></p>
                <p><strong>PWD:</strong> <span id="view-pwd"></span></p>
            </div>

            <div class="modal-row full-width">
                <p><strong>Contact:</strong> <span id="view-contact"></span></p>
                <p><strong>Status:</strong> <span id="view-status"></span></p>
                <p><strong>Remarks:</strong> <span id="view-remarks"></span></p>
            </div>
        </div>

        <div class="modal-footer full-width">
            <button type="button" class="btn btn-secondary" id="btn-close-view-resident-2">Close</button>
        </div>
    </div>
</div>

<!-- ADD BORROWING FOR RESIDENT MODAL -->
<div class="modal-backdrop" id="borrow-backdrop">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="borrowResidentTitle">

        <div class="modal-header">
            <h3 id="borrowResidentTitle">Add Borrowing</h3>
            <button type="button" class="modal-close" id="btn-close-borrow">&times;</button>
        </div>

        <form class="modal-body" method="POST" id="borrow-form" action="{{ route('borrowing.store') }}">
            @csrf

            {{-- Fixed Resident --}}
            <div class="modal-row">
                <label>Resident</label>
                <input type="text" id="borrow-resident-name" class="input" readonly>
                <input type="hidden" name="resident_id" id="borrow-resident-id">
            </div>

            {{-- Item --}}
            <div class="modal-row">
                <label>Item to Borrow</label>
                <select name="item_id" class="select" required>
                    <option value="" disabled selected>Select item</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}"
                            {{ $item->available_quantity == 0 ? 'disabled' : '' }}>
                            {{ $item->name }} (Avail: {{ $item->available_quantity }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 🔴 IMPORTANT: QUANTITY FIELD --}}
            <div class="modal-row">
                <label>Quantity</label>
                <input type="number" name="quantity" class="input" min="1" value="1" required>
            </div>

            {{-- Dates --}}
            <div class="modal-row">
                <label>Date Borrowed</label>
                <input type="date" name="date_borrowed" class="input"
                       value="{{ date('Y-m-d') }}" required>
            </div>

            <div class="modal-row">
                <label>Due Date</label>
                <input type="date" name="due_date" class="input">
            </div>

            {{-- Remarks --}}
            <div class="modal-row">
                <label>Remarks</label>
                <textarea name="remarks" class="input" rows="3"
                          placeholder="Reason / notes (optional)"></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btn-cancel-borrow">Cancel</button>
                <button type="submit" class="btn">Save Borrowing</button>
            </div>

        </form>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ============================================================
       ADD / EDIT RESIDENT MODAL
    ============================================================ */

    const addBtn       = document.getElementById('btn-open-add-resident');
    const closeBtn     = document.getElementById('btn-close-add-resident');
    const cancelBtn    = document.getElementById('btn-cancel-add-resident');
    const backdrop     = document.getElementById('add-resident-backdrop');
    const form         = document.getElementById('resident-form');
    const title        = document.getElementById('addResidentTitle');
    const idRow        = document.getElementById('resident-id-row');
    const idInput      = form ? form.querySelector('input[name="resident_id_display"]') : null;

    function openResidentModal() {
        if (backdrop) backdrop.classList.add('show');
    }
    function closeResidentModal() {
        if (backdrop) backdrop.classList.remove('show');
    }

    function setFormForCreate() {
        if (!form) return;

        title.textContent = 'Add New Resident';
        form.action = "{{ route('residents.store') }}";

        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();

        form.reset();
        if (idRow)   idRow.style.display = 'none';
        if (idInput) idInput.value = '';
    }

    if (addBtn)    addBtn.addEventListener('click', () => { setFormForCreate(); openResidentModal(); });
    if (closeBtn)  closeBtn.addEventListener('click', closeResidentModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeResidentModal);

    if (backdrop) {
        backdrop.addEventListener('click', e => {
            if (e.target === backdrop) closeResidentModal();
        });
    }

    // ESC key closes modals
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeResidentModal();
            closeViewModal();
            closeBorrowModal();
        }
    });

    // ====== EDIT RESIDENT ======
    function datasetValue(btn, key) {
        const v = btn.dataset[key];
        return (v === undefined || v === null || v === 'null') ? '' : v;
    }

    document.querySelectorAll('[data-edit-resident]').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!form) return;

            // base setup
            setFormForCreate();
            title.textContent = 'Edit Resident';

            const updateUrl = this.dataset.updateUrl;
            form.action = updateUrl;

            // _method=PUT
            const methodInput = document.createElement('input');
            methodInput.type  = 'hidden';
            methodInput.name  = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);

            // show ID
            if (idRow)   idRow.style.display = 'block';
            if (idInput) idInput.value = datasetValue(this, 'id');

            // fill fields
            form.elements['last_name'].value        = datasetValue(this, 'last_name');
            form.elements['first_name'].value       = datasetValue(this, 'first_name');
            form.elements['middle_name'].value      = datasetValue(this, 'middle_name');
            form.elements['alias'].value            = datasetValue(this, 'alias');
            form.elements['gender'].value           = datasetValue(this, 'gender');
            form.elements['marital_status'].value   = datasetValue(this, 'marital_status');
            form.elements['spouse_name'].value      = datasetValue(this, 'spouse_name');
            form.elements['purok'].value            = datasetValue(this, 'purok');
            form.elements['employment_status'].value= datasetValue(this, 'employment_status');
            form.elements['birthdate'].value        = datasetValue(this, 'birthdate');
            form.elements['place_of_birth'].value   = datasetValue(this, 'place_of_birth');
            form.elements['age'].value              = datasetValue(this, 'age');
            form.elements['age_month'].value        = datasetValue(this, 'age_month');
            form.elements['height_cm'].value        = datasetValue(this, 'height_cm');
            form.elements['weight_kg'].value        = datasetValue(this, 'weight_kg');
            form.elements['religion'].value         = datasetValue(this, 'religion');
            form.elements['voter_status'].value     = datasetValue(this, 'voter_status');
            form.elements['contact'].value          = datasetValue(this, 'contact');
            form.elements['status'].value           = datasetValue(this, 'status');
            form.elements['remarks'].value          = datasetValue(this, 'remarks');

            // checkbox PWD
            if (form.elements['is_pwd']) {
                form.elements['is_pwd'].checked = (datasetValue(this, 'is_pwd') == '1');
            }

            openResidentModal();
        });
    });


    /* ============================================================
       VIEW RESIDENT MODAL
    ============================================================ */

    const viewBtns     = document.querySelectorAll('[data-view-resident]');
    const viewBackdrop = document.getElementById('view-resident-backdrop');

    function openViewModal() {
        if (viewBackdrop) viewBackdrop.classList.add('show');
    }
    function closeViewModal() {
        if (viewBackdrop) viewBackdrop.classList.remove('show');
    }

    const safe = (value) => value ?? '';

    viewBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            let r = JSON.parse(btn.dataset.resident);

            document.getElementById('view-id').textContent          = safe(r.id);
            document.getElementById('view-last-name').textContent   = safe(r.last_name);
            document.getElementById('view-first-name').textContent  = safe(r.first_name);
            document.getElementById('view-middle-name').textContent = safe(r.middle_name);
            document.getElementById('view-alias').textContent       = safe(r.alias);
            document.getElementById('view-gender').textContent      = safe(r.gender);
            document.getElementById('view-marital').textContent     = safe(r.marital_status);
            document.getElementById('view-spouse').textContent      = safe(r.spouse_name);
            document.getElementById('view-purok').textContent       = safe(r.purok) || safe(r.sitio);
            document.getElementById('view-employment').textContent  = safe(r.employment_status);
            document.getElementById('view-birthdate').textContent   = safe(r.birthdate);
            document.getElementById('view-birthplace').textContent  = safe(r.place_of_birth);
            document.getElementById('view-age').textContent         = safe(r.age);
            document.getElementById('view-age-month').textContent   = safe(r.age_month);
            document.getElementById('view-height').textContent      = safe(r.height_cm);
            document.getElementById('view-weight').textContent      = safe(r.weight_kg);
            document.getElementById('view-religion').textContent    = safe(r.religion);
            document.getElementById('view-voter').textContent       = safe(r.voter_status);
            document.getElementById('view-pwd').textContent         = r.is_pwd ? 'Yes' : 'No';
            document.getElementById('view-contact').textContent     = safe(r.contact);
            document.getElementById('view-status').textContent      = safe(r.status);
            document.getElementById('view-remarks').textContent     = safe(r.remarks);

            openViewModal();
        });
    });

    ['btn-close-view-resident', 'btn-close-view-resident-2'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) btn.addEventListener('click', closeViewModal);
    });

    if (viewBackdrop) {
        viewBackdrop.addEventListener('click', e => {
            if (e.target === viewBackdrop) closeViewModal();
        });
    }


    /* ============================================================
       BORROWING MODAL FROM RESIDENTS
    ============================================================ */

    const borrowBackdrop  = document.getElementById('borrow-backdrop');
    const borrowForm      = document.getElementById('borrow-form');
    const borrowCloseBtn  = document.getElementById('btn-close-borrow');
    const borrowCancelBtn = document.getElementById('btn-cancel-borrow');
    const borrowNameInput = document.getElementById('borrow-resident-name');
    const borrowIdInput   = document.getElementById('borrow-resident-id');

    function openBorrowModal() {
        if (borrowBackdrop) borrowBackdrop.classList.add('show');
    }
    function closeBorrowModal() {
        if (borrowBackdrop) borrowBackdrop.classList.remove('show');
    }

    if (borrowCloseBtn)  borrowCloseBtn.addEventListener('click', closeBorrowModal);
    if (borrowCancelBtn) borrowCancelBtn.addEventListener('click', closeBorrowModal);

    if (borrowBackdrop) {
        borrowBackdrop.addEventListener('click', function(e){
            if (e.target === borrowBackdrop) closeBorrowModal();
        });
    }

    document.querySelectorAll('[data-borrow-resident]').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!borrowForm) return;

            const id   = this.dataset.id;
            const name = this.dataset.name;

            if (borrowIdInput)   borrowIdInput.value   = id;
            if (borrowNameInput) borrowNameInput.value = name;

            if (borrowForm.elements['item_id']) {
                borrowForm.elements['item_id'].value = '';
            }
            if (borrowForm.elements['quantity']) {
                borrowForm.elements['quantity'].value = 1;
            }
            if (borrowForm.elements['date_borrowed']) {
                borrowForm.elements['date_borrowed'].value = "{{ date('Y-m-d') }}";
            }
            if (borrowForm.elements['due_date']) {
                borrowForm.elements['due_date'].value = '';
            }
            if (borrowForm.elements['remarks']) {
                borrowForm.elements['remarks'].value = '';
            }

            openBorrowModal();
        });
    });

});
</script>
@endpush
