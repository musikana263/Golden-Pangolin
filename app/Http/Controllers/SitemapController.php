<?php

namespace App\Http\Controllers;

use App\Accommodation;
use App\Blog;
use App\Destinations;
use App\Package;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $destinations = Destinations::published()
            ->select('id', 'updated_at')
            ->get();

        $blogs = Blog::published()
            ->select('id', 'updated_at')
            ->get();

        $accommodations = Accommodation::active()
            ->select('id', 'slug', 'updated_at')
            ->get();

        $packages = Package::active()
            ->select('id', 'slug', 'updated_at')
            ->get();

        $content = view('sitemap', [
            'destinations' => $destinations,
            'blogs' => $blogs,
            'accommodations' => $accommodations,
            'packages' => $packages,
        ])->render();

        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }
}
