<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Models\Item; 
use Illuminate\Http\Request;

class ResidentController extends Controller
{
     public function index()
    {
        $residents = Resident::orderBy('last_name')->orderBy('first_name')->get();
        $items = Item::orderBy('name')->get(); // items available to borrow

        return view('LendingTracker.Residents', compact('residents', 'items'));
    }


    public function store(Request $request)
    {
        $data = $this->validateResident($request);

        Resident::create($data);

        return redirect()
            ->route('residents.index')
            ->with('success', 'Resident has been added successfully.');
    }

    public function update(Request $request, Resident $resident)
    {
        $data = $this->validateResident($request);

        $resident->update($data);

        return redirect()
            ->route('residents.index')
            ->with('success', 'Resident information has been updated.');
    }

    public function destroy(Resident $resident)
    {
        $resident->delete();

        return redirect()
            ->route('residents.index')
            ->with('success', 'Resident has been deleted.');
    }

    protected function validateResident(Request $request): array
{
    return $request->validate([
        'last_name'        => ['required', 'string', 'max:255'],
        'first_name'       => ['required', 'string', 'max:255'],
        'middle_name'      => ['nullable', 'string', 'max:255'],
        'alias'            => ['nullable', 'string', 'max:255'],

        'gender'           => ['required', 'string', 'max:50'],
        'marital_status'   => ['nullable', 'string', 'max:50'],
        'spouse_name'      => ['nullable', 'string', 'max:255'],

        'birthdate'        => ['nullable', 'date'],
        'place_of_birth'   => ['nullable', 'string', 'max:255'],
        'age'              => ['nullable', 'integer', 'min:0'],
        'age_month'        => ['nullable', 'integer', 'min:0'],

        'height_cm'        => ['nullable', 'integer', 'min:0'],
        'weight_kg'        => ['nullable', 'integer', 'min:0'],

        'sitio'            => ['nullable', 'string', 'max:255'],
        'purok'            => ['nullable', 'string', 'max:255'],
        'contact'          => ['nullable', 'string', 'max:50'],

        'employment_status'=> ['nullable', 'string', 'max:100'],
        'religion'         => ['nullable', 'string', 'max:100'],
        'voter_status'     => ['nullable', 'string', 'max:100'],
        'is_pwd'           => ['nullable', 'boolean'],

        'status'           => ['required', 'in:Active,Inactive'],
        'remarks'          => ['nullable', 'string'],
    ]);
}
}
