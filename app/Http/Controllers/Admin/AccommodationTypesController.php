<?php

namespace App\Http\Controllers\Admin;

use App\AccommodationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Accommodation\AccommodationTypeRequest;

class AccommodationTypesController extends Controller
{
    public function index()
    {
        return view('admin.accommodation-types.index')
            ->with('types', AccommodationType::withCount('accommodations')->orderBy('name')->get());
    }

    public function create()
    {
        return view('admin.accommodation-types.create');
    }

    public function store(AccommodationTypeRequest $request)
    {
        AccommodationType::create($request->validated());

        session()->flash('success', 'Accommodation type created.');

        return redirect(route('accommodation-types.index'));
    }

    public function edit(AccommodationType $accommodation_type)
    {
        return view('admin.accommodation-types.create')->with('type', $accommodation_type);
    }

    public function update(AccommodationTypeRequest $request, AccommodationType $accommodation_type)
    {
        $accommodation_type->update($request->validated());

        session()->flash('success', 'Accommodation type updated.');

        return redirect(route('accommodation-types.index'));
    }

    public function destroy(AccommodationType $accommodation_type)
    {
        if ($accommodation_type->accommodations()->exists()) {
            session()->flash('error', 'Cannot delete — accommodations are using this type.');

            return redirect()->back();
        }

        $accommodation_type->delete();

        session()->flash('success', 'Accommodation type deleted.');

        return redirect(route('accommodation-types.index'));
    }
}
