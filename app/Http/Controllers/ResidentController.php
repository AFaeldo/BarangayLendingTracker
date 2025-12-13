<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ResidentController extends Controller
{
    /**
     * Display all residents.
     */
    public function index()
    {
        $residents = Resident::orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('LendingTracker.Residents', compact('residents'));
    }

    /**
     * Store a new resident.
     */
    public function store(Request $request)
    {
        $data = $this->validateResident($request);

        DB::transaction(function () use ($data) {
            Resident::create($data);
        });

        return redirect()->route('residents.index')
            ->with('success', 'Resident has been added successfully.');
    }

    /**
     * Update resident information.
     */
    public function update(Request $request, Resident $resident)
    {
        $data = $this->validateResident($request, $resident);

        DB::transaction(function () use ($resident, $data) {
            $resident->update($data);
        });

        return redirect()->route('residents.index')
            ->with('success', 'Resident information has been updated.');
    }

    /**
     * Archive a resident (set status to Inactive).
     */
    public function archive(Resident $resident)
    {
        DB::transaction(function () use ($resident) {
            $resident->update(['status' => 'Inactive']);
        });

        return redirect()->route('residents.index')
            ->with('success', 'Resident has been archived (set to Inactive).');
    }

    /**
     * Delete a resident.
     */
    public function destroy(Resident $resident)
    {
        // Prevent hard deletion if they have history?
        // Usually good practice to soft delete or prevent if relation exists
        if ($resident->borrowings()->exists()) {
             return back()->withErrors(['error' => 'Cannot delete resident. They have borrowing records. Please archive instead.']);
        }

        DB::transaction(function () use ($resident) {
            $resident->delete();
        });

        return redirect()->route('residents.index')
            ->with('success', 'Resident has been deleted.');
    }

    /**
     * Validation rules for resident.
     */
    protected function validateResident(Request $request, ?Resident $resident = null): array
    {
        return $request->validate([
            'last_name'        => ['required', 'string', 'max:255'],
            'first_name'       => [
                'required',
                'string',
                'max:255',
                Rule::unique('residents')->where(function ($query) use ($request) {
                    return $query->where('last_name', $request->last_name);
                })->ignore($resident),
            ],
            'middle_name'      => ['nullable', 'string', 'max:255'],
            'gender'           => ['required', 'string', 'max:50'],
            'age'              => ['nullable', 'integer', 'min:18'],
            'birthdate'        => ['nullable', 'date'],
            'sitio'            => ['nullable', 'string', 'max:255'],
            'purok'            => ['nullable', 'string', 'max:255'],
            'contact'          => ['nullable', 'string', 'max:50'],
            'marital_status'   => ['nullable', 'string', 'max:50'],
            'status'           => ['required', 'in:Active,Inactive'],
            'remarks'          => ['nullable', 'string'],
        ], [
            'first_name.unique' => 'A resident with this First Name and Last Name already exists.',
            'age.min' => 'The resident must be an adult (at least 18 years old).',
        ]);
    }
}