<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Resident;
use App\Models\Item;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    public function index()
    {
        // Fetch borrowings with resident & item data
        $borrowings = Borrowing::with(['resident', 'item'])
            ->orderByDesc('date_borrowed')
            ->get();

        // Residents and items for modal dropdowns
        $residents = Resident::orderBy('last_name')->orderBy('first_name')->get();
        $items     = Item::orderBy('name')->get();

        return view('LendingTracker.Borrowing', compact('borrowings', 'residents', 'items'));
    }

  public function store(Request $request)
{
    $data = $request->validate([
        'resident_id'   => ['required', 'exists:residents,id'],
        'item_id'       => ['required', 'exists:items,id'],
        'quantity'      => ['required','integer','min:1'],
        'date_borrowed' => ['required','date'],
        'due_date'      => ['nullable','date','after_or_equal:date_borrowed'],
        'remarks'       => ['nullable','string'],
    ]);

    $item = Item::findOrFail($data['item_id']);

    // Check stock availability
    if ($data['quantity'] > $item->available_quantity) {
        return back()->withErrors([
            'quantity' => "Only {$item->available_quantity} item(s) available."
        ])->withInput();
    }

    // Create borrowing
    $borrowing = Borrowing::create([
        'resident_id'   => $data['resident_id'],
        'item_id'       => $data['item_id'],
        'quantity'      => $data['quantity'],
        'date_borrowed' => $data['date_borrowed'],
        'due_date'      => $data['due_date'] ?? null,
        'status'        => 'Borrowed',
        'remarks'       => $data['remarks'] ?? null,

        // ✅ default values for return-related fields
        'is_lost'            => false,
        'condition_returned' => null,
        'received_by'        => null,
    ]);

    // Reduce item stock
    $item->decrement('available_quantity', $data['quantity']);

    return redirect()->route('borrowing.index')->with('success', 'Borrowing record saved.');
}


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

    $isLost = (bool)($data['is_lost'] ?? 0);

    $borrowing->returned_at        = now();
    $borrowing->is_lost            = $isLost;
    $borrowing->condition_returned = $data['condition_returned'] ?? null;
    $borrowing->received_by        = $data['received_by'] ?? (auth()->user()->name ?? null);
    $borrowing->status             = $isLost ? 'Lost' : 'Returned';

    $borrowing->save();

    if (! $isLost && $borrowing->item) {
        $borrowing->item->increment('available_quantity', $borrowing->quantity);
    }

    return back()->with('success', 'Borrowing marked as ' . ($isLost ? 'lost' : 'returned') . ' and stock updated.');
}


}
