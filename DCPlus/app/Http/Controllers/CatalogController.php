<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Series;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index()
    {
        $featuredMovies = Movie::where('is_featured', true)
            ->orderBy('rating', 'desc')
            ->take(10)
            ->get();
            
        $featuredSeries = Series::where('is_featured', true)
            ->orderBy('rating', 'desc')
            ->take(10)
            ->get();
            
        $latestMovies = Movie::latest()->take(10)->get();
        $latestSeries = Series::latest()->take(10)->get();
        
        return view('catalog.index', compact('featuredMovies', 'featuredSeries', 'latestMovies', 'latestSeries'));
    }

    public function movies()
    {
        $movies = Movie::with(['genres'])
            ->orderBy('release_year', 'desc')
            ->paginate(24)
            ->withQueryString();
        
        $featuredMovies = Movie::where('is_featured', true)
            ->orderBy('rating', 'desc')
            ->take(5)
            ->get();
        
        return view('catalog.movies', compact('movies', 'featuredMovies'));
    }

    public function series()
    {
        $series = Series::with(['genres'])
            ->orderBy('release_year', 'desc')
            ->paginate(24)
            ->withQueryString();
        
        $featuredSeries = Series::where('is_featured', true)
            ->orderBy('rating', 'desc')
            ->take(5)
            ->get();
        
        return view('catalog.series', compact('series', 'featuredSeries'));
    }
}
