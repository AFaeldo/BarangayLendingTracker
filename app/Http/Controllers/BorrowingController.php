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

        $residents = Resident::where('status', 'Active')->orderBy('last_name')->orderBy('first_name')->get();
        $items     = Item::where('available_quantity', '>', 0)->orderBy('name')->get();

        return view('LendingTracker.Borrowing', compact('borrowings', 'residents', 'items'));
    }

    /**
     * Show the form for creating a new borrowing record.
     */
    public function create()
    {
        $residents = Resident::where('status', 'Active')->orderBy('last_name')->orderBy('first_name')->get();
        $items     = Item::where('available_quantity', '>', 0)->orderBy('name')->get();

        return view('LendingTracker.AddBorrowing', compact('residents', 'items'));
    }

    /**
     * Store a new borrowing record.
     */
    public function store(Request $request)
    {
        $data = $this->validateBorrowing($request);

        DB::transaction(function () use ($data) {
            $resident = Resident::findOrFail($data['resident_id']);
            if ($resident->status !== 'Active') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'resident_id' => "Resident is not Active and cannot borrow items."
                ]);
            }

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
        $request->validate([
            'condition_returned' => 'required|string',
            'received_by'        => 'nullable|string|max:255',
            'remarks'            => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request, $borrowing) {
            $condition = $request->condition_returned;
            
            // Determine final status
            // If condition is Lost, status is Lost. Otherwise Returned.
            $status = ($condition === 'Lost') ? 'Lost' : 'Returned';

            $borrowing->update([
                'returned_at'        => now(),
                'status'             => $status,
                'condition_returned' => $condition,
                'remarks'            => $request->remarks, // Save payment/remarks here
                // We can append received_by to remarks or save it if we had a column. 
                // Since migration didn't show 'received_by' column, let's append it to remarks for now 
                // or assume we only use remarks. The view sends 'remarks'.
                // If you want to strictly save received_by, ensure column exists. 
                // For now let's just save remarks.
            ]);

            // Stock Logic:
            // Only increment stock if the item is returned in GOOD condition.
            // Damaged or Lost items do NOT replenish the "Available" pool.
            if ($condition === 'Good') {
                $borrowing->item->increment('available_quantity', $borrowing->quantity);
            }
            
            // Note: If condition is 'Damaged', the physical item exists but is broken. 
            // It is NOT added back to 'available_quantity'.
            // You might want to update Item status to 'Maintenance' or similar if qty hits 0,
            // but for now we just don't add it back.
        });

        return back()->with('success', 'Item marked as returned.');
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