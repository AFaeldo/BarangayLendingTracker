{{-- resources/views/residents/index.blade.php --}}
@extends('Layout.layout_lendingtracker')

@section('title', 'Residents — Brgy. San Antonio')
@section('page-title', 'Residents')

@php
    // helper: convert resident to safe JSON for embedding
    function safe_json($obj) {
        return json_encode($obj, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT);
    }
@endphp

@section('content')

    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success" role="status">
            <i class="fas fa-check-circle" aria-hidden="true"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Validation Errors (Server Side) --}}
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Top bar: search + add -->
    <div class="top-bar d-flex justify-between align-center flex-gap-12 mb-14">
        <div>
            <input id="search-input" class="input w-320" type="text" placeholder="Search Resident name, contact or purok..." aria-label="Search residents">
        </div>

        <div class="d-flex flex-gap-8">
            <button id="btn-open-add-resident" class="btn" type="button" aria-haspopup="dialog">
                <i class="fas fa-user-plus" aria-hidden="true"></i> Add New Resident
            </button>
        </div>
    </div>

    <!-- Residents Table -->
    <div class="card table-card" role="region" aria-labelledby="resident-list-heading">
        <div class="card-header">
            <h3 id="resident-list-heading">Resident List</h3>
        </div>

        <div class="card-body overflow-x-auto">
            <table id="residents-table" class="table w-full" role="table" aria-describedby="resident-list-heading">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Sex</th>
                        <th scope="col">Age</th>
                        <th scope="col">Sitio / Purok</th>
                        <th scope="col">Contact</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($residents as $resident)
                        @php
                            $r = [
                                'id' => $resident->id,
                                'first_name' => $resident->first_name,
                                'middle_name' => $resident->middle_name,
                                'last_name' => $resident->last_name,
                                'gender' => $resident->gender,
                                'age' => $resident->age,
                                'purok' => $resident->purok ?? $resident->sitio,
                                'contact' => $resident->contact,
                                'status' => $resident->status,
                                'birthdate' => optional($resident->birthdate)->toDateString(),
                                'marital_status' => $resident->marital_status,
                                'remarks' => $resident->remarks,
                            ];
                        @endphp

                        <tr data-resident-id="{{ $resident->id }}" data-resident='@php echo safe_json($r); @endphp'>
                            <td>{{ $resident->id }}</td>
                            <td>
                                <strong>{{ $resident->last_name }}, {{ $resident->first_name }}</strong>
                                @if(!empty($resident->middle_name))
                                    <small class="text-muted"> {{ substr($resident->middle_name,0,1) }}.</small>
                                @endif
                            </td>
                            <td>{{ $resident->gender ?? '-' }}</td>
                            <td>{{ $resident->age ?? '-' }}</td>
                            <td>{{ $resident->purok ?? $resident->sitio ?? '-' }}</td>
                            <td>{{ $resident->contact ?? '-' }}</td>
                            <td>
                                <span class="status-badge {{ strtolower($resident->status) }}" aria-label="Status: {{ $resident->status }}">
                                    {{ $resident->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons d-inline-flex flex-gap-6 align-center">
                                    {{-- VIEW --}}
                                    <button
                                        type="button"
                                        class="btn btn-info"
                                        aria-label="View resident {{ $resident->id }}"
                                        data-action="view"
                                        title="View Details">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </button>

                                    {{-- EDIT --}}
                                    <button
                                        type="button"
                                        class="btn btn-warning"
                                        aria-label="Edit resident {{ $resident->id }}"
                                        data-action="edit"
                                        title="Edit resident">
                                        <i class="fas fa-edit" aria-hidden="true"></i>
                                    </button>

                                    {{-- ARCHIVE (soft delete) --}}
                                    <form method="POST" action="{{ route('residents.archive', $resident) }}" onsubmit="return confirm('Are you sure you want to ARCHIVE this resident?');" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-danger" title="Archive resident" aria-label="Archive resident {{ $resident->id }}">
                                            <i class="fas fa-archive" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">No residents found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Optionally include pagination controls here --}}
        @if(method_exists($residents,'links'))
            <div class="card-footer">
                {{ $residents->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- ================= MODALS ================= -->

    {{-- Add / Edit Resident Modal --}}
    <div id="resident-modal" class="modal-backdrop" aria-hidden="true" role="dialog" aria-modal="true">
        <div class="modal" role="document" aria-labelledby="resident-modal-title">
            <div class="modal-header">
                <h3 id="resident-modal-title">Add New Resident</h3>
                <button type="button" class="modal-close" id="btn-close-resident-modal" aria-label="Close">&times;</button>
            </div>

            <form id="resident-form" class="modal-body" method="POST" action="{{ route('residents.store') }}" novalidate>
                @csrf
                {{-- method spoofing input for edit will be inserted dynamically --}}
                <div id="method-spoof"></div>

                {{-- hidden id display (for edit only) --}}
                <div class="form-row d-none" id="resident-id-row">
                    <label>Resident ID</label>
                    <input type="text" name="resident_id_display" class="input" readonly>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                        <input id="last_name" type="text" name="last_name" class="input" required>
                    </div>

                    <div class="form-group">
                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                        <input id="first_name" type="text" name="first_name" class="input" required>
                    </div>

                    <div class="form-group">
                        <label for="middle_name">Middle Name</label>
                        <input id="middle_name" type="text" name="middle_name" class="input">
                    </div>

                    <div class="form-group">
                        <label for="gender">Sex <span class="text-danger">*</span></label>
                        <select id="gender" name="gender" class="select" required>
                            <option value="" disabled selected>Select Sex</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="purok">Purok / Sitio</label>
                        <input id="purok" type="text" name="purok" class="input">
                    </div>

                    <div class="form-group">
                        <label for="contact">Contact Number</label>
                        <input id="contact" type="text" name="contact" class="input">
                    </div>

                    <div class="form-group">
                        <label for="birthdate">Birthdate</label>
                        <input id="birthdate" type="date" name="birthdate" class="input">
                    </div>

                    <div class="form-group">
                        <label for="age">Age</label>
                        <input id="age" type="number" name="age" class="input" min="0">
                    </div>

                    <div class="form-group">
                        <label for="marital_status">Civil Status</label>
                        <select id="marital_status" name="marital_status" class="select">
                            <option value="">-- Select --</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Widowed">Widowed</option>
                            <option value="Separated">Separated</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select id="status" name="status" class="select" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="remarks">Remarks</label>
                        <textarea id="remarks" name="remarks" class="input" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="btn-cancel-resident" type="button" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Resident</button>
                </div>
            </form>
        </div>
    </div>

    {{-- View Resident Modal --}}
    <div id="resident-view-modal" class="modal-backdrop" aria-hidden="true" role="dialog">
        <div class="modal" role="document" aria-labelledby="resident-view-title">
            <div class="modal-header">
                <h3 id="resident-view-title">Resident Details</h3>
                <button type="button" class="modal-close" id="btn-close-view-modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="details-grid">
                    <div>
                        <p><strong>ID:</strong> <span id="view-id">-</span></p>
                        <p><strong>Name:</strong> <span id="view-name">-</span></p>
                        <p><strong>Gender:</strong> <span id="view-gender">-</span></p>
                        <p><strong>Status:</strong> <span id="view-status">-</span></p>
                        <p><strong>Civil Status:</strong> <span id="view-marital">-</span></p>
                    </div>
                    <div>
                        <p><strong>Purok:</strong> <span id="view-purok">-</span></p>
                        <p><strong>Contact:</strong> <span id="view-contact">-</span></p>
                        <p><strong>Birthdate:</strong> <span id="view-birthdate">-</span></p>
                        <p><strong>Age:</strong> <span id="view-age">-</span></p>
                    </div>
                    <div class="full-width">
                        <p><strong>Remarks:</strong> <span id="view-remarks">-</span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-close-view-2" type="button" class="btn btn-secondary">Close</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // --- Build resident map from table rows ---
    const residentRows = Array.from(document.querySelectorAll('#residents-table tbody tr[data-resident-id]'));
    const residentsMap = {};
    residentRows.forEach(row => {
        try {
            const id = row.dataset.residentId;
            const data = JSON.parse(row.dataset.resident);
            // normalize status for comparisons
            data.status = (data.status || '').toString();
            residentsMap[id] = data;
        } catch (err) {
            console.warn('Failed to parse resident JSON', err);
        }
    });

    // Public helper to check if resident is active
    window.isResidentActive = function(residentId) {
        if (!residentId) return false;
        const r = residentsMap[residentId.toString()];
        if (!r) return false;
        return (r.status || '').toLowerCase() === 'active';
    };

    // --- Search functionality (client-side) ---
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const v = this.value.trim().toLowerCase();
            residentRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = v === '' ? '' : (text.includes(v) ? '' : 'none');
            });
        });
    }

    // --- Modal helpers ---
    function showModal(el) { if (!el) return; el.style.display = 'flex'; el.setAttribute('aria-hidden','false'); }
    function hideModal(el) { if (!el) return; el.style.display = 'none'; el.setAttribute('aria-hidden','true'); }

    // Resident modal elements
    const residentModal = document.getElementById('resident-modal');
    const residentViewModal = document.getElementById('resident-view-modal');
    const residentForm = document.getElementById('resident-form');
    const methodSpoof = document.getElementById('method-spoof');
    const residentIdRow = document.getElementById('resident-id-row');
    const modalTitle = document.getElementById('resident-modal-title');

    // Open Add modal
    document.getElementById('btn-open-add-resident')?.addEventListener('click', () => {
        residentForm.reset();
        methodSpoof.innerHTML = '';
        residentForm.action = "{{ route('residents.store') }}";
        residentIdRow.style.display = 'none'; // Keep as inline style manipulation by JS if that's what it does, or switch to class toggle if we refactor JS. 
        // Note: JS here uses .style.display = 'none'. This will override the class. So it's fine.
        modalTitle.textContent = 'Add New Resident';
        showModal(residentModal);
    });

    // Open Edit / View using event delegation to reduce DOM handlers
    document.querySelector('#residents-table tbody')?.addEventListener('click', function (e) {
        const btn = e.target.closest('button');
        if (!btn) return;
        const tr = btn.closest('tr[data-resident-id]');
        if (!tr) return;
        let r;
        try {
            r = JSON.parse(tr.dataset.resident);
        } catch (err) {
            console.warn('Invalid resident data for row', err);
            return;
        }
        const action = btn.dataset.action;
        if (action === 'view') {
            // populate view modal
            document.getElementById('view-id').textContent = r.id ?? '-';
            document.getElementById('view-name').textContent = `${r.last_name}, ${r.first_name}`;
            document.getElementById('view-gender').textContent = r.gender ?? '-';
            document.getElementById('view-status').textContent = r.status ?? '-';
            document.getElementById('view-marital').textContent = r.marital_status ?? '-';
            document.getElementById('view-purok').textContent = r.purok ?? '-';
            document.getElementById('view-contact').textContent = r.contact ?? '-';
            document.getElementById('view-birthdate').textContent = r.birthdate ?? '-';
            document.getElementById('view-age').textContent = r.age ?? '-';
            document.getElementById('view-remarks').textContent = r.remarks ?? '-';
            showModal(residentViewModal);
            return;
        }

        if (action === 'edit') {
            // populate edit modal
            residentForm.reset();
            // insert method spoof only once
            methodSpoof.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            residentIdRow.style.display = 'flex'; // JS overrides style
            residentForm.action = "{{ url('/residents') }}/" + r.id;
            modalTitle.textContent = 'Edit Resident';
            // set fields (guard each selector)
            const setIf = (selector, value) => {
                const el = residentForm.querySelector(selector);
                if (el) el.value = value ?? '';
            };
            setIf('[name="resident_id_display"]', r.id ?? '');
            setIf('[name="last_name"]', r.last_name ?? '');
            setIf('[name="first_name"]', r.first_name ?? '');
            setIf('[name="middle_name"]', r.middle_name ?? '');
            setIf('[name="gender"]', r.gender ?? '');
            setIf('[name="purok"]', r.purok ?? '');
            setIf('[name="contact"]', r.contact ?? '');
            setIf('[name="birthdate"]', r.birthdate ?? '');
            setIf('[name="age"]', r.age ?? '');
            setIf('[name="marital_status"]', r.marital_status ?? '');
            setIf('[name="status"]', r.status ?? 'Active');
            setIf('[name="remarks"]', r.remarks ?? '');
            showModal(residentModal);
            return;
        }
    });

    // Modal close buttons
    document.getElementById('btn-close-resident-modal')?.addEventListener('click', () => hideModal(residentModal));
    document.getElementById('btn-cancel-resident')?.addEventListener('click', () => hideModal(residentModal));
    document.getElementById('btn-close-view-modal')?.addEventListener('click', () => hideModal(residentViewModal));
    document.getElementById('btn-close-view-2')?.addEventListener('click', () => hideModal(residentViewModal));

    // Close modals on ESC
    window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            hideModal(residentModal);
            hideModal(residentViewModal);
        }
    });

    // Click outside to close (improve accessibility: only close when clicking the backdrop)
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', function (evt) {
            if (evt.target === backdrop) hideModal(backdrop);
        });
    });

    // --- Guard for forms that require active resident ---
    // Usage: any form that must have an active resident should include attribute data-requires-active-resident
    document.querySelectorAll('form[data-requires-active-resident]').forEach(form => {
        form.addEventListener('submit', function (e) {
            const residentInput = form.querySelector('[name="resident_id"], [name="residentId"], [name="resident"]');
            if (!residentInput) return; // cannot check
            const residentId = residentInput.value;
            if (!isResidentActive(residentId)) {
                e.preventDefault();
                // show friendly message (insert temporary element)
                const message = document.createElement('div');
                message.className = 'alert alert-warning';
                message.textContent = 'Selected resident is not Active. Inactive residents cannot borrow items.';
                form.prepend(message);
                // remove message after 6s
                setTimeout(()=> message.remove(), 6000);
            }
        });
    });

    // Accessibility: focus the first input when modal opens (small enhancement)
    const observer = new MutationObserver(mutations => {
        mutations.forEach(m => {
            if (m.attributeName === 'style' || m.attributeName === 'aria-hidden') {
                if (residentModal.style.display !== 'none') {
                    residentForm.querySelector('input, select, textarea')?.focus();
                }
            }
        });
    });
    observer.observe(residentModal, { attributes: true, attributeFilter: ['style', 'aria-hidden'] });

});
</script>
@endpush