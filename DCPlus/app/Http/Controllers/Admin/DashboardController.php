<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Series;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'movies' => Movie::count(),
            'series' => Series::count(),
            'users' => User::count(),
            'profiles' => Profile::count(),
        ];
        
        $recentMovies = Movie::latest()->take(5)->get();
        $recentSeries = Series::latest()->take(5)->get();
        
        return view('admin.dashboard', compact('stats', 'recentMovies', 'recentSeries'));
    }
}
