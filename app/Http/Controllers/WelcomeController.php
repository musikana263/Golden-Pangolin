<?php

namespace App\Http\Controllers;

use App\Accommodation;
use App\Blog;
use App\Category;
use App\Destinations;
use App\Package;
use App\Services\TravellerTier;

class WelcomeController extends Controller
{
    public function index(TravellerTier $tierService)
    {
        $search = request()->query('search');
        // Honour the admin-controlled drag-reorder for the homepage destination
        // strip — same `sort_order` used on the /destinations admin list.
        $destinations = $search
            ? Destinations::where('title', 'LIKE', "%{$search}%")->orderBy('sort_order')->simplePaginate(3)
            : Destinations::orderBy('sort_order')->simplePaginate(3);

        $tier = $tierService->currentOrDefault();

        $featuredTours = Package::active()
            ->availableTo($tier)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->with('destinations', 'media')
            ->limit(3)
            ->get();

        $featuredStays = Accommodation::active()
            ->with('type', 'media')
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        // Hero slot — admin marks one Package with is_hero=true. We only show
        // it if it's offered to the current visitor's tier; otherwise we fall
        // back to the next-best featured/active package that IS available to
        // them, so the hero never advertises something a visitor can't book.
        $heroPackage = Package::active()
            ->availableTo($tier)
            ->hero()
            ->with('destinations', 'media')
            ->first()
            ?: Package::active()
                ->availableTo($tier)
                ->orderByDesc('is_featured')
                ->latest()
                ->with('destinations', 'media')
                ->first();

        return view('welcome')
            ->with('destinations', $destinations)
            ->with('categories', Category::all())
            ->with('heroPackage', $heroPackage)
            ->with('featuredTours', $featuredTours)
            ->with('featuredStays', $featuredStays);
    }

    public function about()
    {
        return view('about');
    }

    public function packages()
    {
        $search = trim((string) request('search'));
        $query = Destinations::query()->orderBy('sort_order');
        if ($search !== '') {
            $query->where('title', 'LIKE', "%{$search}%");
        }
        $destinations = $query->paginate(9)->withQueryString();

        return view('packages')
            ->with('destinations', $destinations)
            ->with('categories', Category::all());
    }

    public function blog()
    {
        return view('blog')
            ->with('blogs', Blog::paginate(6))
            ->with('categories', Category::all());
    }

    public function contact()
    {
        return view('contact')
            ->with('categories', Category::all());
    }
}
