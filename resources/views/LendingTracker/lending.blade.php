@extends('Layout.layout_lendingtracker')

@section('title', 'Lending — Brgy. San Antonio')


@section('content')

<div class="container mt-4">

    <h2 class="mb-4">Borrow Item</h2>

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('lending.store') }}" method="POST">
                @csrf

                <!-- Borrower -->
                <div class="mb-3">
                    <label class="form-label">Borrower Name</label>
                    <select name="borrower_id" class="form-select" required>
                        <option value="">-- Select Borrower --</option>
                        @foreach ($borrowers as $borrower)
                            <option value="{{ $borrower->id }}">{{ $borrower->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Item -->
                <div class="mb-3">
                    <label class="form-label">Item to Borrow</label>
                    <select name="item_id" class="form-select" required>
                        <option value="">-- Select Item --</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->item_name }} (Available: {{ $item->available_qty }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Quantity -->
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="qty" class="form-control" min="1" required>
                </div>

                <!-- Due Date -->
                <div class="mb-3">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control" required>
                </div>

                <!-- Notes -->
                <div class="mb-3">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>

                <!-- Submit -->
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        Create Lending Record
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection
