<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Series;
use App\Models\Watchlist;
use App\Models\WatchHistory;
use App\Models\Rating;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContentController extends Controller
{
    public function show($slug)
    {
        $movie = Movie::where('slug', $slug)->with(['genres', 'actors', 'directors', 'reviews.profile', 'ratings'])->first();
        $series = Series::where('slug', $slug)->with(['genres', 'actors', 'directors', 'episodes', 'reviews.profile', 'ratings'])->first();
        
        $content = $movie ?? $series;
        
        if (!$content) {
            abort(404);
        }
        
        $isInWatchlist = false;
        $watchProgress = null;
        $userRating = null;
        
        if (Auth::check() && Auth::user()->profiles()->exists()) {
            $profile = Auth::user()->profiles()->first();
            
            $isInWatchlist = Watchlist::where('profile_id', $profile->id)
                ->where('watchable_type', get_class($content))
                ->where('watchable_id', $content->id)
                ->exists();
                
            $watchProgress = WatchHistory::where('profile_id', $profile->id)
                ->where('watchable_type', get_class($content))
                ->where('watchable_id', $content->id)
                ->first();
                
            $userRating = Rating::where('profile_id', $profile->id)
                ->where('rateable_type', get_class($content))
                ->where('rateable_id', $content->id)
                ->first();
        }
        
        return view('content.show', compact('content', 'isInWatchlist', 'watchProgress', 'userRating'));
    }
    
    public function rate(Request $request, $slug)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);
        
        $movie = Movie::where('slug', $slug)->first();
        $series = Series::where('slug', $slug)->first();
        $content = $movie ?? $series;
        
        if (!$content) {
            abort(404);
        }
        
        $profile = Auth::user()->profiles()->first();
        
        Rating::updateOrCreate(
            [
                'profile_id' => $profile->id,
                'rateable_type' => get_class($content),
                'rateable_id' => $content->id,
            ],
            [
                'rating' => $request->rating,
            ]
        );
        
        // Update average rating
        $avgRating = Rating::where('rateable_type', get_class($content))
            ->where('rateable_id', $content->id)
            ->avg('rating');
            
        $content->update(['rating' => round($avgRating, 1)]);
        
        return back()->with('success', 'Rating saved!');
    }
    
    public function review(Request $request, $slug)
    {
        $request->validate([
            'comment' => 'required|string|min:10',
        ]);
        
        $movie = Movie::where('slug', $slug)->first();
        $series = Series::where('slug', $slug)->first();
        $content = $movie ?? $series;
        
        if (!$content) {
            abort(404);
        }
        
        $profile = Auth::user()->profiles()->first();
        
        Review::updateOrCreate(
            [
                'profile_id' => $profile->id,
                'reviewable_type' => get_class($content),
                'reviewable_id' => $content->id,
            ],
            [
                'comment' => $request->comment,
            ]
        );
        
        return back()->with('success', 'Review saved!');
    }
    
    public function updateWatchProgress(Request $request, $slug)
    {
        $request->validate([
            'progress' => 'required|integer|min:0',
            'completed' => 'boolean',
        ]);
        
        $movie = Movie::where('slug', $slug)->first();
        $series = Series::where('slug', $slug)->first();
        $content = $movie ?? $series;
        
        if (!$content) {
            abort(404);
        }
        
        $profile = Auth::user()->profiles()->first();
        
        WatchHistory::updateOrCreate(
            [
                'profile_id' => $profile->id,
                'watchable_type' => get_class($content),
                'watchable_id' => $content->id,
            ],
            [
                'progress' => $request->progress,
                'completed' => $request->completed ?? false,
                'last_watched_at' => now(),
            ]
        );
        
        return response()->json(['success' => true]);
    }
}
