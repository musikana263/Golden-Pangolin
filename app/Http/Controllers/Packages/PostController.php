<?php

namespace App\Http\Controllers\Packages;

use App\Accommodation;
use App\Category;
use App\Destinations;
use App\Http\Controllers\Controller;
use App\Package;

class Postcontroller extends Controller
{
    public function show(Destinations $destination)
    {
        // Only show activities priced for the current visitor's tier.
        $destination->load(['activities' => fn ($q) => $q
            ->where('is_active', true)
            ->availableToVisitor()
            ->with('media', 'category')]);

        // Same filter applied to package-included activities so packages whose
        // only activities here are out-of-tier don't show empty rows.
        $relatedPackages = Package::active()
            ->where(function ($q) use ($destination) {
                $q->whereHas('destinations', fn ($qq) => $qq->where('destinations.id', $destination->id))
                  ->orWhereHas('activities', fn ($qq) => $qq
                      ->where('destination_id', $destination->id)
                      ->availableToVisitor());
            })
            ->with([
                'media',
                'destinations',
                'activities' => fn ($q) => $q->availableToVisitor()->with('destination'),
            ])
            ->limit(6)
            ->get();

        $nearbyStays = Accommodation::active()
            ->with('type', 'media')
            ->where(function ($q) use ($destination) {
                $q->where('country', $destination->country ?? 'Zimbabwe')
                  ->when($destination->title, function ($qq) use ($destination) {
                      $qq->orWhere('location', 'like', "%{$destination->title}%");
                  });
            })
            // Honour the admin drag-and-drop sort_order from the
            // /admin/accommodations list — so the destination page lists
            // stays in the same order admins see when re-ordering.
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        return view('desti.show')
            ->with('destinations', $destination)
            ->with('categories', Category::all())
            ->with('relatedPackages', $relatedPackages)
            ->with('nearbyStays', $nearbyStays);
    }
}
