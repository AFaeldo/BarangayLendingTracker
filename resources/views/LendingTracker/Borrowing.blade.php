@extends('Layout.layout_lendingtracker')

@section('title', 'Borrowing Records — Brgy. San Antonio')
@section('page-title', 'Borrowing Records')

@section('content')

{{-- SUCCESS MESSAGE --}}
@if (session('success'))
    <div class="alert alert-success d-flex align-items-center mb-3">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
    </div>
@endif

{{-- VALIDATION ERRORS --}}
@if ($errors->any())
    <div class="alert alert-danger mb-3">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm mt-3">
    <div class="card-header">
        <h5 class="mb-0">Borrowing History</h5>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Resident</th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Date Borrowed</th>
                    <th>Date Returned</th>
                    <th>Status</th>
                    <th>Condition</th>
                    <th>Lost?</th>
                    <th>Remarks</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($borrowings as $b)
                <tr>
                    <td>{{ $b->id }}</td>
                    <td>{{ optional($b->resident)->last_name }}, {{ optional($b->resident)->first_name }}</td>
                    <td>{{ optional($b->item)->name }}</td>
                    <td>{{ $b->quantity }}</td>
                    <td>{{ $b->date_borrowed }}</td>
                    <td>{{ $b->returned_at ?? '—' }}</td>
                    <td>
                        @php
                            $statusClass = match($b->status) {
                                'Returned' => 'bg-success text-white',
                                'Lost'     => 'bg-danger text-white',
                                'Overdue'  => 'bg-warning text-dark',
                                default    => 'bg-secondary text-white',
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $b->status }}</span>
                    </td>
                    <td>{{ $b->condition_returned ?? '—' }}</td>
                    <td>
                        @if ($b->is_lost)
                            <span class="badge bg-danger">Yes</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </td>
                    <td>{{ $b->remarks ?? '—' }}</td>
                    <td class="text-center">
                        @if (!in_array($b->status, ['Returned', 'Lost']))
                            <form method="POST"
                                  action="{{ route('borrowing.return', $b) }}"
                                  class="d-flex flex-column gap-2"
                                  onsubmit="return confirm('Process this return/lost item?')">
                                @csrf
                                <select name="condition_returned" class="form-select form-select-sm" required>
                                    <option value="" disabled selected>Condition</option>
                                    <option value="Good">Good</option>
                                    <option value="Damaged">Damaged</option>
                                    <option value="Lost">Lost</option>
                                </select>
                                <input type="text" name="received_by" class="form-control form-control-sm" placeholder="Received by (Optional)">
                                <input type="text" name="remarks" class="form-control form-control-sm" placeholder="Remarks / Payment (Optional)">
                                <button type="submit" class="btn btn-secondary btn-sm w-100 mt-1">Process Return</button>
                            </form>
                        @else
                            <span class="text-muted small">
                                {{ $b->status === 'Lost' ? 'Processed as lost' : 'Already returned' }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center text-muted py-4">
                        No borrowing records yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if(method_exists($borrowings,'links'))
        <div class="card-footer d-flex justify-content-end">
            {{ $borrowings->withQueryString()->links() }}
        </div>
    @endif
</div>

@endsection
