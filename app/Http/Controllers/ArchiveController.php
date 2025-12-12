<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrowing;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class ArchiveController extends Controller
{
    /**
     * Show all archived (returned/lost) borrowing records.
     */
    public function index(Request $request)
    {
        $q = $request->query('q');

        $query = Borrowing::with(['resident', 'item'])
            ->whereIn('status', ['Returned', 'Lost']);

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('resident', function ($res) use ($q) {
                    $res->where('last_name', 'like', "%{$q}%")
                        ->orWhere('first_name', 'like', "%{$q}%");
                })
                ->orWhereHas('item', function ($item) use ($q) {
                    $item->where('name', 'like', "%{$q}%");
                });
            });
        }

        $archives = $query->orderByDesc('updated_at')->paginate(15);

        return view('LendingTracker.Archive', compact('archives', 'q'));
    }

    /**
     * Restore archived record (Mark as Borrowed again).
     * CAUTION: This will deduct stock again.
     */
    public function restore($id)
    {
        $borrowing = Borrowing::findOrFail($id);

        if (!in_array($borrowing->status, ['Returned', 'Lost'])) {
            return back()->with('error', 'This record is already active.');
        }

        DB::transaction(function () use ($borrowing) {
            $item = Item::findOrFail($borrowing->item_id);

            // Check if enough stock to restore (re-borrow)
            // If it was lost, stock wasn't returned, so we don't need to deduct?
            // Wait, logic:
            // If Returned: stock was incremented. To restore (make borrowed), we must decrement stock.
            // If Lost: stock was NOT incremented. To restore (make borrowed), we don't change stock? 
            // Actually, if it was 'Lost', the item is gone physically. Marking it 'Borrowed' implies it's out with the resident.
            // The logic in BorrowingController::markReturned:
            // If Lost: Status=Lost. Stock NOT incremented.
            // If Returned: Status=Returned. Stock incremented.
            
            // So:
            // If reverting 'Returned' -> 'Borrowed': Decrement stock.
            // If reverting 'Lost' -> 'Borrowed': Stock remains same (it was effectively "out" and is still "out").

            if ($borrowing->status === 'Returned') {
                if ($item->available_quantity < $borrowing->quantity) {
                    throw new \Exception("Cannot restore: Insufficient stock to mark as borrowed.");
                }
                $item->decrement('available_quantity', $borrowing->quantity);
            }
            // If Lost, stock wasn't added back, so no need to deduct.

            $borrowing->update([
                'status' => 'Borrowed',
                'returned_at' => null,
                'is_lost' => false,
                'condition_returned' => null,
                'received_by' => null,
            ]);
        });

        return redirect()->route('archive.index')
            ->with('success', 'Record restored to Active Borrowings.');
    }

    /**
     * Permanently delete record.
     */
    public function destroy($id)
    {
        $borrowing = Borrowing::findOrFail($id);
        $borrowing->delete();

        return redirect()->route('archive.index')
            ->with('success', 'Record permanently deleted.');
    }
}