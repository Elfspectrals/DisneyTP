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
}
