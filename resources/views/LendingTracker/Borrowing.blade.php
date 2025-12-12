@extends('Layout.layout_lendingtracker')

@section('title', 'Borrowing Records — Brgy. San Antonio')
@section('page-title', 'Borrowing Records')

@section('content')

    {{-- Success Message --}}
    @if (session('success'))
        <div class="success-message mb-12">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="error-message mb-12">
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ===================== --}}
    {{-- BORROWING HISTORY ONLY --}}
    {{-- ===================== --}}

    <div class="card mt-20 p-18">
        <h3>Borrowing History</h3>

        <table class="table mt-10">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Resident</th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Date Borrowed</th>
                    <!-- <th>Due Date</th> -->
                    <th>Date Returned</th>
                    <th>Status</th>
                    <th>Condition on Return</th>
                    <th>Lost</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($borrowings as $b)
                    <tr>
                        <td>{{ $b->id }}</td>

                        {{-- RESIDENT --}}
                        <td>
                            {{ optional($b->resident)->last_name }},
                            {{ optional($b->resident)->first_name }}
                        </td>

                        {{-- ITEM --}}
                        <td>{{ optional($b->item)->name }}</td>

                        <td>{{ $b->quantity }}</td>
                        <td>{{ $b->date_borrowed }}</td>
                        <!-- <td>{{ $b->due_date ?? '—' }}</td> -->
                        <td>{{ $b->returned_at ?? '—' }}</td>

                        {{-- STATUS (Borrowed / Returned / Lost / Overdue) --}}
                        <td>
                            @php
                                $statusClass = match($b->status) {
                                    'Returned' => 'text-success',
                                    'Lost'     => 'text-danger',
                                    'Overdue'  => 'text-warning',
                                    default    => 'text-muted'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                {{ $b->status }}
                            </span>
                        </td>

                        {{-- CONDITION ON RETURN --}}
                        <td>{{ $b->condition_returned ?? '—' }}</td>

                        {{-- LOST? --}}
                        <td>
                            @if ($b->is_lost)
                                <span class="text-danger text-bold">Yes</span>
                            @else
                                <span>No</span>
                            @endif
                        </td>

        
                        {{-- REMARKS --}}
                        <td>{{ $b->remarks ?? '—' }}</td>

                        {{-- ACTIONS --}}
                        <td>
                            @if (! in_array($b->status, ['Returned', 'Lost']))
                                {{-- Form to mark as returned / lost with condition --}}
                                <form method="POST"
                                      action="{{ route('borrowing.return', $b) }}"
                                      class="d-flex flex-col flex-gap-4 min-w-180"
                                      onsubmit="return confirm('Mark this as returned / lost?');">

                                    @csrf

                                    <select name="condition_returned" class="select select-sm w-full" required>
                                        <option value="" disabled selected>Condition</option>
                                        <option value="Good">Good</option>
                                        <option value="Damaged">Damaged</option>
                                        <option value="Lost">Lost</option>
                                    </select>

                                    {{-- Optional: Received By --}}
                                    <input type="text"
                                           name="received_by"
                                           class="input input-sm w-full"
                                           placeholder="Received by (Optional)">

                                    {{-- Optional: Remarks / Payment --}}
                                    <input type="text"
                                           name="remarks"
                                           class="input input-sm w-full"
                                           placeholder="Payment / Remarks (Optional)">

                                    <button type="submit"
                                            class="btn btn-secondary btn-sm mt-4 w-full">
                                        Process Return
                                    </button>
                                </form>
                            @else
                                {{-- Already processed --}}
                                <span class="text-sm text-gray">
                                    {{ $b->status === 'Lost' ? 'Processed as lost' : 'Already returned' }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="text-center p-20 text-gray">
                            No borrowing records yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

@endsection