<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrowing;
use App\Models\Resident;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class ArchiveController extends Controller
{
    /**
     * Show all archived (returned/lost) borrowing records AND inactive residents.
     */
    public function index(Request $request)
    {
        $q = $request->query('q');

        // --- Borrowing Archives ---
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
        $archives = $query->orderByDesc('updated_at')->paginate(10, ['*'], 'borrowings_page');


        // --- Resident Archives (Inactive) ---
        $resQuery = Resident::where('status', 'Inactive');

        if ($q) {
            $resQuery->where(function ($sub) use ($q) {
                $sub->where('last_name', 'like', "%{$q}%")
                    ->orWhere('first_name', 'like', "%{$q}%")
                    ->orWhere('middle_name', 'like', "%{$q}%");
            });
        }
        $archivedResidents = $resQuery->orderBy('last_name')->paginate(10, ['*'], 'residents_page');


        return view('LendingTracker.Archive', compact('archives', 'archivedResidents', 'q'));
    }

    /**
     * Restore archived borrowing record (Mark as Borrowed again).
     */
    public function restore($id)
    {
        $borrowing = Borrowing::findOrFail($id);

        if (!in_array($borrowing->status, ['Returned', 'Lost'])) {
            return back()->with('error', 'This record is already active.');
        }

        DB::transaction(function () use ($borrowing) {
            $item = Item::findOrFail($borrowing->item_id);

            if ($borrowing->status === 'Returned') {
                if ($item->available_quantity < $borrowing->quantity) {
                    throw new \Exception("Cannot restore: Insufficient stock to mark as borrowed.");
                }
                $item->decrement('available_quantity', $borrowing->quantity);
            }

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
     * Permanently delete borrowing record.
     */
    public function destroy($id)
    {
        $borrowing = Borrowing::findOrFail($id);
        $borrowing->delete();

        return redirect()->route('archive.index')
            ->with('success', 'Record permanently deleted.');
    }

    /**
     * Restore archived resident (Mark as Active).
     */
    public function restoreResident($id)
    {
        $resident = Resident::findOrFail($id);
        
        if ($resident->status === 'Active') {
             return back()->with('error', 'Resident is already active.');
        }

        $resident->update(['status' => 'Active']);

        return redirect()->route('archive.index')
            ->with('success', 'Resident restored to Active list.');
    }

    /**
     * Permanently delete resident.
     */
    public function destroyResident($id)
    {
        $resident = Resident::findOrFail($id);
        
        // Safety check: check for borrowings
        if ($resident->borrowings()->exists()) {
             return back()->withErrors(['error' => 'Cannot permanently delete resident. They have associated borrowing records.']);
        }

        $resident->delete();

        return redirect()->route('archive.index')
            ->with('success', 'Resident permanently deleted.');
    }
}
