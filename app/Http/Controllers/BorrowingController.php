<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Resident;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    /**
     * Display all borrowing records.
     */
    public function index()
    {
        $borrowings = Borrowing::with(['resident', 'item'])
            ->orderByDesc('date_borrowed')
            ->get();

        $residents = Resident::orderBy('last_name')->orderBy('first_name')->get();
        $items     = Item::orderBy('name')->get();

        return view('LendingTracker.Borrowing', compact('borrowings', 'residents', 'items'));
    }

    /**
     * Store a new borrowing record.
     */
    public function store(Request $request)
    {
        $data = $this->validateBorrowing($request);

        DB::transaction(function () use ($data) {
            $item = Item::findOrFail($data['item_id']);

            if ($data['quantity'] > $item->available_quantity) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'quantity' => "Only {$item->available_quantity} item(s) available."
                ]);
            }

            Borrowing::create([
                'resident_id'   => $data['resident_id'],
                'item_id'       => $data['item_id'],
                'quantity'      => $data['quantity'],
                'date_borrowed' => $data['date_borrowed'],
                'due_date'      => $data['due_date'] ?? null,
                'status'        => 'Borrowed',
                'remarks'       => $data['remarks'] ?? null,
                'is_lost'       => false,
                'condition_returned' => null,
                'received_by'       => null,
            ]);

            // Reduce stock
            $item->decrement('available_quantity', $data['quantity']);
        });

        return redirect()->route('borrowing.index')
            ->with('success', 'Borrowing record saved successfully.');
    }

    /**
     * Mark a borrowing as returned or lost.
     */
    public function markReturned(Request $request, Borrowing $borrowing)
    {
        if (in_array($borrowing->status, ['Returned', 'Lost'])) {
            return back()->with('success', 'This borrowing is already processed.');
        }

        $data = $request->validate([
            'condition_returned' => ['nullable', 'string', 'max:255'],
            'is_lost'            => ['nullable', 'boolean'],
            'received_by'        => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($borrowing, $data) {
            $isLost = (bool)($data['is_lost'] ?? false);

            $borrowing->returned_at        = now();
            $borrowing->is_lost            = $isLost;
            $borrowing->condition_returned = $data['condition_returned'] ?? null;
            $borrowing->received_by        = $data['received_by'] ?? (auth()->user()->name ?? null);
            $borrowing->status             = $isLost ? 'Lost' : 'Returned';

            $borrowing->save();

            // Return stock if item was not lost
            if (!$isLost && $borrowing->item) {
                $borrowing->item->increment('available_quantity', $borrowing->quantity);
            }
        });

        return back()->with('success', 'Borrowing marked as ' . ($data['is_lost'] ? 'lost' : 'returned') . ' successfully.');
    }

    /**
     * Validation rules for borrowing records.
     */
    protected function validateBorrowing(Request $request): array
    {
        return $request->validate([
            'resident_id'   => ['required', 'exists:residents,id'],
            'item_id'       => ['required', 'exists:items,id'],
            'quantity'      => ['required', 'integer', 'min:1'],
            'date_borrowed' => ['required', 'date'],
            'due_date'      => ['nullable', 'date', 'after_or_equal:date_borrowed'],
            'remarks'       => ['nullable', 'string'],
        ]);
    }
}
