<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
{
    $items = Item::with([
        'borrowings' => function ($q) {
            $q->with('resident')->orderByDesc('date_borrowed');
        }
    ])->orderBy('name')->get();

    return view('LendingTracker.Items', compact('items'));
}


    public function store(Request $request)
    {
        $data = $this->validateItem($request);

        // handle photo upload
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('items', 'public');
        }

        // when creating, available_quantity = quantity
        $data['available_quantity'] = $data['quantity'];

        Item::create($data);

        return redirect()
            ->route('items.index')
            ->with('success', 'Item has been added.');
    }

    public function update(Request $request, Item $item)
    {
        $data = $this->validateItem($request, $item->id);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('items', 'public');
        }

        // optional: adjust available_quantity if total quantity changed
        if (isset($data['quantity'])) {
            $difference = $data['quantity'] - $item->quantity;
            $data['available_quantity'] = max(0, $item->available_quantity + $difference);
        }

        $item->update($data);

        return redirect()
            ->route('items.index')
            ->with('success', 'Item has been updated.');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()
            ->route('items.index')
            ->with('success', 'Item has been deleted.');
    }

    protected function validateItem(Request $request, ?int $itemId = null): array
    {
        return $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'photo'       => ['nullable', 'image', 'max:2048'],
            'quantity'    => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'condition'   => ['required', 'in:Good,For Repair,Damaged'],
            'status'      => ['required', 'in:Available,Borrowed,Maintenance'],
        ]);
    }
}
