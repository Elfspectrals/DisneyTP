<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Series;
use App\Models\Episode;
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
        $nextEpisode = null;
        $currentEpisode = null;
        
        if (Auth::check() && Auth::user()->profiles()->exists()) {
            $profile = Auth::user()->currentProfile();
            
            if ($profile) {
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
                
                // For series, find the next episode to watch
                if ($content instanceof Series) {
                    $nextEpisode = $this->getNextEpisode($content, $profile);
                    $currentEpisode = $this->getCurrentEpisode($content, $profile);
                }
            }
        }
        
        return view('content.show', compact('content', 'isInWatchlist', 'watchProgress', 'userRating', 'nextEpisode', 'currentEpisode'));
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
        
        $profile = Auth::user()->currentProfile();
        
        if (!$profile) {
            return back()->with('error', 'Please select a profile first.');
        }
        
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
        
        $profile = Auth::user()->currentProfile();
        
        if (!$profile) {
            return back()->with('error', 'Please select a profile first.');
        }
        
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
            'episode_id' => 'nullable|exists:episodes,id',
        ]);
        
        $movie = Movie::where('slug', $slug)->first();
        $series = Series::where('slug', $slug)->first();
        $content = $movie ?? $series;
        
        if (!$content) {
            abort(404);
        }
        
        $profile = Auth::user()->currentProfile();
        
        if (!$profile) {
            return response()->json(['error' => 'Please select a profile first.'], 400);
        }
        
        // If episode_id is provided, track episode progress instead of series progress
        if ($request->episode_id && $content instanceof Series) {
            $episode = Episode::find($request->episode_id);
            
            if ($episode && $episode->series_id === $content->id) {
                WatchHistory::updateOrCreate(
                    [
                        'profile_id' => $profile->id,
                        'watchable_type' => Episode::class,
                        'watchable_id' => $episode->id,
                    ],
                    [
                        'progress' => $request->progress,
                        'completed' => $request->completed ?? false,
                        'last_watched_at' => now(),
                    ]
                );
                
                $nextEpisode = $this->getNextEpisode($content, $profile, $episode);
                $nextEpisodeData = null;
                if ($nextEpisode) {
                    $nextEpisodeData = [
                        'id' => $nextEpisode->id,
                        'title' => $nextEpisode->title,
                        'video_url' => $nextEpisode->video_url_display ?? $nextEpisode->video_url,
                        'season_number' => $nextEpisode->season_number,
                        'episode_number' => $nextEpisode->episode_number,
                    ];
                }
                
                return response()->json(['success' => true, 'next_episode' => $nextEpisodeData]);
            }
        }
        
        // For movies or series without episode_id, track content progress
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
    
    /**
     * Get the next episode to watch for a series
     */
    private function getNextEpisode(Series $series, $profile, ?Episode $currentEpisode = null)
    {
        // If current episode is provided, find the next one
        if ($currentEpisode) {
            $nextEpisode = Episode::where('series_id', $series->id)
                ->where(function($query) use ($currentEpisode) {
                    $query->where(function($q) use ($currentEpisode) {
                        $q->where('season_number', $currentEpisode->season_number)
                          ->where('episode_number', '>', $currentEpisode->episode_number);
                    })
                    ->orWhere('season_number', '>', $currentEpisode->season_number);
                })
                ->orderBy('season_number')
                ->orderBy('episode_number')
                ->first();
            
            if ($nextEpisode) {
                return $nextEpisode;
            }
        }
        
        // Otherwise, find the first unwatched or incomplete episode
        $episodeIds = Episode::where('series_id', $series->id)->pluck('id');
        
        $watchedEpisodes = WatchHistory::where('profile_id', $profile->id)
            ->where('watchable_type', Episode::class)
            ->whereIn('watchable_id', $episodeIds)
            ->where('completed', true)
            ->pluck('watchable_id');
        
        $nextEpisode = Episode::where('series_id', $series->id)
            ->whereNotIn('id', $watchedEpisodes)
            ->orderBy('season_number')
            ->orderBy('episode_number')
            ->first();
        
        // If all episodes are watched, return the last one
        if (!$nextEpisode) {
            $nextEpisode = Episode::where('series_id', $series->id)
                ->orderBy('season_number', 'desc')
                ->orderBy('episode_number', 'desc')
                ->first();
        }
        
        return $nextEpisode;
    }
    
    /**
     * Get the current episode being watched
     */
    private function getCurrentEpisode(Series $series, $profile)
    {
        $episodeIds = Episode::where('series_id', $series->id)->pluck('id');
        
        $currentWatch = WatchHistory::where('profile_id', $profile->id)
            ->where('watchable_type', Episode::class)
            ->whereIn('watchable_id', $episodeIds)
            ->where('completed', false)
            ->orderBy('last_watched_at', 'desc')
            ->first();
        
        if ($currentWatch) {
            return Episode::find($currentWatch->watchable_id);
        }
        
        return null;
    }
}
