<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Series;
use App\Models\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller
{
    public function index()
    {
        $profile = Auth::user()->profiles()->first();
        
        if (!$profile) {
            return redirect()->route('profiles.create')
                ->with('error', 'Please create a profile first.');
        }
        
        $watchlists = Watchlist::where('profile_id', $profile->id)
            ->with('watchable')
            ->latest()
            ->get();
        
        return view('watchlist.index', compact('watchlists'));
    }
    
    public function add($type, $id)
    {
        $profile = Auth::user()->profiles()->first();
        
        if (!$profile) {
            return back()->with('error', 'Please create a profile first.');
        }
        
        $model = $type === 'movie' ? Movie::findOrFail($id) : Series::findOrFail($id);
        
        Watchlist::firstOrCreate([
            'profile_id' => $profile->id,
            'watchable_type' => get_class($model),
            'watchable_id' => $model->id,
        ]);
        
        return back()->with('success', 'Added to watchlist!');
    }
    
    public function remove($type, $id)
    {
        $profile = Auth::user()->profiles()->first();
        
        $model = $type === 'movie' ? Movie::findOrFail($id) : Series::findOrFail($id);
        
        Watchlist::where('profile_id', $profile->id)
            ->where('watchable_type', get_class($model))
            ->where('watchable_id', $model->id)
            ->delete();
        
        return back()->with('success', 'Removed from watchlist!');
    }
}
