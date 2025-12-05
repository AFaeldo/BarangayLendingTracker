@extends('Layout.layout_lendingtracker')

@section('title', 'Borrowing Records — Brgy. San Antonio')
@section('page-title', 'Borrowing Records')

@section('content')

    {{-- Success Message --}}
    @if (session('success'))
        <div class="success-message" style="margin-bottom:12px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="error-message" style="margin-bottom:12px;">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ===================== --}}
    {{-- BORROWING HISTORY ONLY --}}
    {{-- ===================== --}}

    <div class="card" style="margin-top:20px; padding:18px;">
        <h3>Borrowing History</h3>

        <table class="table" style="margin-top:10px;">
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
                                $status = $b->status;
                                $color  =
                                    $status === 'Returned' ? 'green' :
                                    ($status === 'Lost' ? 'red' :
                                    ($status === 'Overdue' ? 'orange' : '#555'));
                            @endphp
                            <span style="padding:2px 8px; border-radius:999px; font-size:0.8rem; background:rgba(0,0,0,0.04); color:{{ $color }};">
                                {{ $status }}
                            </span>
                        </td>

                        {{-- CONDITION ON RETURN --}}
                        <td>{{ $b->condition_returned ?? '—' }}</td>

                        {{-- LOST? --}}
                        <td>
                            @if ($b->is_lost)
                                <span style="color:red; font-weight:600;">Yes</span>
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
                                      style="display:flex; flex-direction:column; gap:4px; min-width:180px;"
                                      onsubmit="return confirm('Mark this as returned / lost?');">

                                    @csrf

                                    <select name="condition_returned" class="select" style="padding:4px 8px; font-size:0.8rem;">
                                        <option value="">Condition</option>
                                        <option value="Good">Good</option>
                                        <option value="Damaged">Damaged</option>
                                    </select>

                                    <input type="text"
                                           name="received_by"
                                           class="input"
                                           placeholder="Received by (optional)"
                                           style="padding:4px 8px; font-size:0.8rem;">

                                    <label style="font-size:0.8rem; display:flex; align-items:center; gap:4px; margin-top:2px;">
                                        <input type="checkbox" name="is_lost" value="1" style="width:auto; margin:0;">
                                        Mark as LOST (no stock returned)
                                    </label>

                                    <button type="submit"
                                            class="btn btn-secondary"
                                            style="padding:4px 8px;font-size:0.8rem; margin-top:4px;">
                                        Mark as Returned
                                    </button>
                                </form>
                            @else
                                {{-- Already processed --}}
                                <span style="font-size:0.8rem; color:gray;">
                                    {{ $b->status === 'Lost' ? 'Processed as lost' : 'Already returned' }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" style="text-align:center; padding:20px; color:gray;">
                            No borrowing records yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

@endsection
