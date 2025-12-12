<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    /**
     * Display all items.
     */
    public function index()
    {
        $items = Item::orderBy('name')->get();
        return view('LendingTracker.Items', compact('items'));
    }

    /**
     * Store a new item.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:items,name'],
            'quantity'    => ['required', 'integer', 'min:0'],
            'condition'   => ['required', 'string', 'in:Good,Damaged,For Repair'],
            'status'      => ['required', 'string', 'in:Available,Borrowed,Maintenance'],
            'description' => ['nullable', 'string'],
        ]);

        // Initially, available quantity equals total quantity
        $data['available_quantity'] = $data['quantity'];

        DB::transaction(function () use ($data) {
            Item::create($data);
        });

        return redirect()->route('items.index')
            ->with('success', 'Item has been added successfully.');
    }

    /**
     * Update an existing item.
     */
    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255', Rule::unique('items')->ignore($item->id)],
            'quantity'    => ['required', 'integer', 'min:0'],
            'condition'   => ['required', 'string', 'in:Good,Damaged,For Repair'],
            'status'      => ['required', 'string', 'in:Available,Borrowed,Maintenance'],
            'description' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($item, $data) {
            // Calculate difference in total quantity
            $quantityDiff = $data['quantity'] - $item->quantity;
            
            // Calculate proposed new available quantity
            $newAvailable = $item->available_quantity + $quantityDiff;

            if ($newAvailable < 0) {
                 throw \Illuminate\Validation\ValidationException::withMessages([
                    'quantity' => "Cannot reduce total quantity to {$data['quantity']}. {$item->borrowings()->where('status', 'Borrowed')->sum('quantity')} items are currently borrowed.",
                ]);
            }

            $item->update([
                'name'               => $data['name'],
                'quantity'           => $data['quantity'],
                'available_quantity' => $newAvailable,
                'condition'          => $data['condition'],
                'status'             => $data['status'],
                'description'        => $data['description'],
            ]);
        });

        return redirect()->route('items.index')
            ->with('success', 'Item information has been updated.');
    }

    /**
     * Delete an item.
     */
    public function destroy(Item $item)
    {
        // Prevent deletion if items are borrowed
        if ($item->borrowings()->where('status', 'Borrowed')->exists()) {
             return back()->withErrors(['error' => 'Cannot delete item. It has active borrowing records.']);
        }

        DB::transaction(function () use ($item) {
            $item->delete();
        });

        return redirect()->route('items.index')
            ->with('success', 'Item has been deleted.');
    }
}