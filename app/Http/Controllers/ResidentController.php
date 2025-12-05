<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $data = $this->validateResident($request);

        DB::transaction(function () use ($resident, $data) {
            $resident->update($data);
        });

        return redirect()->route('residents.index')
            ->with('success', 'Resident information has been updated.');
    }

    /**
     * Delete a resident.
     */
    public function destroy(Resident $resident)
    {
        DB::transaction(function () use ($resident) {
            $resident->delete();
        });

        return redirect()->route('residents.index')
            ->with('success', 'Resident has been deleted.');
    }

    /**
     * Validation rules for resident.
     */
    protected function validateResident(Request $request): array
    {
        return $request->validate([
            'last_name'        => ['required', 'string', 'max:255'],
            'first_name'       => ['required', 'string', 'max:255'],
            'middle_initial'   => ['nullable', 'string', 'max:5'],
            'gender'           => ['required', 'string', 'max:50'],
            'age'              => ['nullable', 'integer', 'min:0'],
            'birthdate'        => ['nullable', 'date'],
            'sitio'            => ['nullable', 'string', 'max:255'],
            'purok'            => ['nullable', 'string', 'max:255'],
            'contact'          => ['nullable', 'string', 'max:50'],
            'status'           => ['required', 'in:Active,Inactive'],
            'remarks'          => ['nullable', 'string'],
        ]);
    }
}
