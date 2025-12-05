<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $data = $this->validateItem($request);

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
        $data = $this->validateItem($request);

        DB::transaction(function () use ($item, $data) {
            $item->update($data);
        });

        return redirect()->route('items.index')
            ->with('success', 'Item information has been updated.');
    }

    /**
     * Delete an item.
     */
    public function destroy(Item $item)
    {
        DB::transaction(function () use ($item) {
            $item->delete();
        });

        return redirect()->route('items.index')
            ->with('success', 'Item has been deleted.');
    }

    /**
     * Validation rules for item.
     */
    protected function validateItem(Request $request): array
    {
        return $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string'],
            'total_quantity'     => ['required', 'integer', 'min:0'],
            'available_quantity' => ['required', 'integer', 'min:0'],
            'category'           => ['nullable', 'string', 'max:100'],
        ]);
    }
}
