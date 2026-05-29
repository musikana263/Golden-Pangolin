<?php

namespace App\Http\Controllers;

use App\Accommodation;
use App\Blog;
use App\Category;
use App\Destinations;
use App\Package;
use App\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $stats = [
            'destinations' => Destinations::count(),
            'packages' => Package::count(),
            'accommodations' => Accommodation::count(),
            'categories' => Category::count(),
            'blogs' => Blog::count(),
            'users' => User::count(),
        ];

        $recentDestinations = Destinations::latest()->take(5)->get();
        $recentBlogs = Blog::latest()->take(5)->get();

        return view('home', compact('stats', 'recentDestinations', 'recentBlogs'));
    }
}
