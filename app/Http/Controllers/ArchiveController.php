<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resident;

class ArchiveController extends Controller
{
    /**
     * Show all archived residents.
     */
    public function index(Request $request)
    {
        $q = $request->query('q');

        $query = Resident::where('is_archived', 1); // or use ->onlyTrashed() if using soft deletes

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('first_name', 'like', "%{$q}%")
                    ->orWhere('middle_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('suffix', 'like', "%{$q}%")
                    ->orWhere('contact', 'like', "%{$q}%");
            });
        }

        $residents = $query->orderBy('last_name')->paginate(15);

        return view('archive.index', compact('residents', 'q'));
    }

    /**
     * Restore archived resident.
     */
    public function restore($id)
    {
        $resident = Resident::findOrFail($id);

        if ($resident->is_archived == 0) {
            return back()->with('error', 'This resident is not archived.');
        }

        $resident->update(['is_archived' => 0]);

        return redirect()->route('archive.index')
            ->with('success', 'Resident restored successfully.');
    }

    /**
     * Permanently delete resident.
     */
    public function destroy($id)
    {
        $resident = Resident::findOrFail($id);

        if ($resident->is_archived == 0) {
            return back()->with('error', 'Resident must be archived before permanent deletion.');
        }

        $resident->delete(); // permanent delete

        return redirect()->route('archive.index')
            ->with('success', 'Resident permanently deleted.');
    }
}
