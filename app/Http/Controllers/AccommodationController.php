<?php

namespace App\Http\Controllers;

use App\Accommodation;
use App\AccommodationType;

class AccommodationController extends Controller
{
    public function index()
    {
        $typeId = (int) request('type') ?: null;
        $country = request('country');
        $search = trim((string) request('search'));

        $query = Accommodation::active()
            ->with(['type', 'media'])
            ->ofType($typeId)
            ->inCountry($country);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Admin drag-reorder takes precedence; newest first as tiebreaker.
        $accommodations = $query->orderBy('sort_order')->orderByDesc('id')->paginate(9)->withQueryString();

        return view('accommodation.index', [
            'accommodations' => $accommodations,
            'types' => AccommodationType::orderBy('name')->get(),
            'countries' => config('site.countries'),
            'activeType' => $typeId,
            'activeCountry' => $country,
            'activeSearch' => $search,
        ]);
    }

    public function show(Accommodation $accommodation)
    {
        abort_unless($accommodation->is_active, 404);
        $accommodation->load(['type', 'media']);

        $related = Accommodation::active()
            ->where('id', '!=', $accommodation->id)
            ->where(function ($q) use ($accommodation) {
                $q->where('accommodation_type_id', $accommodation->accommodation_type_id)
                  ->orWhere('country', $accommodation->country);
            })
            ->with('media')
            ->limit(3)
            ->get();

        return view('accommodation.show', [
            'accommodation' => $accommodation,
            'related' => $related,
        ]);
    }
}
