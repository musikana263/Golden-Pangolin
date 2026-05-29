<?php

namespace App\Http\Controllers;

use App\Destinations;
use App\Package;

class TourPackagesController extends Controller
{
    public function index()
    {
        $destinationId = (int) request('destination') ?: null;
        $country = request('country') ?: null;
        $search = trim((string) request('search'));

        $query = Package::active()
            ->with([
                // Only include activities priced for the current visitor's tier
                // so package listings don't quote items the visitor can't book.
                'activities' => fn ($q) => $q->availableToVisitor()->with('destination'),
                'accommodation',
                'media',
                'destinations',
            ]);

        if ($destinationId) {
            $query->whereHas('destinations', fn ($q) => $q->where('destinations.id', $destinationId));
        }

        if ($country) {
            $query->inCountry($country);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        // Featured first, then admin drag-reorder; id desc as final tiebreaker.
        $packages = $query->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString();

        return view('tours.index', [
            'packages' => $packages,
            'countries' => config('site.countries'),
            'activeCountry' => $country,
            'activeDestination' => $destinationId,
            'activeSearch' => $search,
            'destinations' => Destinations::orderBy('title')->get(),
        ]);
    }

    public function show(Package $package)
    {
        abort_unless($package->is_active, 404);

        $package->load([
            'destinations',
            'activities' => fn ($q) => $q->availableToVisitor()->with('destination', 'media'),
            'accommodation.media',
            'media',
        ]);

        $related = Package::active()
            ->where('id', '!=', $package->id)
            ->whereHas('destinations', function ($q) use ($package) {
                $q->whereIn('destinations.id', $package->destinations->pluck('id'));
            })
            ->with([
                'media',
                'destinations',
                'activities' => fn ($q) => $q->availableToVisitor()->with('destination'),
            ])
            ->limit(3)
            ->get();

        return view('tours.show', [
            'package' => $package,
            'related' => $related,
        ]);
    }
}
