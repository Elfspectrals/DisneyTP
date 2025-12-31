<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Series;
use App\Models\Episode;
use App\Models\Genre;
use App\Models\Actor;
use App\Models\Director;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SeriesController extends Controller
{
    public function index()
    {
        $series = Series::with(['genres', 'actors', 'directors'])->latest()->paginate(20);
        return view('admin.series.index', compact('series'));
    }
    
    public function create()
    {
        $genres = Genre::all();
        $actors = Actor::all();
        $directors = Director::all();
        return view('admin.series.create', compact('genres', 'actors', 'directors'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'release_year' => 'required|date|before_or_equal:' . date('Y-m-d'),
            'seasons' => 'required|integer|min:1',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'backdrop' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'video_url' => 'nullable|file|mimes:mp4,webm,mov|max:512000',
            'is_featured' => 'sometimes|boolean',
            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',
            'actors' => 'nullable|array',
            'actors.*' => 'exists:actors,id',
            'directors' => 'nullable|array',
            'directors.*' => 'exists:directors,id',
        ]);
        
        // Generate unique slug
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;
        while (Series::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        // Extract year from date
        $releaseYear = date('Y', strtotime($validated['release_year']));
        
        // Handle file uploads
        $posterPath = null;
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('series/posters', 'public');
        }
        
        $backdropPath = null;
        if ($request->hasFile('backdrop')) {
            $backdropPath = $request->file('backdrop')->store('series/backdrops', 'public');
        }
        
        $videoPath = null;
        if ($request->hasFile('video_url')) {
            $videoPath = $request->file('video_url')->store('series/videos', 'public');
        }
        
        $series = Series::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'],
            'release_year' => $releaseYear,
            'seasons' => $validated['seasons'],
            'poster' => $posterPath,
            'backdrop' => $backdropPath,
            'video_url' => $videoPath,
            'is_featured' => $request->has('is_featured') && $request->is_featured == '1',
        ]);
        
        if ($request->has('genres') && !empty($request->genres)) {
            $series->genres()->attach($request->genres);
        }
        
        if ($request->has('actors') && !empty($request->actors)) {
            $series->actors()->attach($request->actors);
        }
        
        if ($request->has('directors') && !empty($request->directors)) {
            $series->directors()->attach($request->directors);
        }
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Series created!']);
        }
        
        return redirect()->route('admin.series.index')->with('success', 'Series created!');
    }
    
    public function show(Series $series)
    {
        $series->load(['genres', 'actors', 'directors', 'episodes']);
        return view('admin.series.show', compact('series'));
    }
    
    public function edit(Series $series)
    {
        $genres = Genre::all();
        $actors = Actor::all();
        $directors = Director::all();
        $series->load(['genres', 'actors', 'directors']);
        return view('admin.series.edit', compact('series', 'genres', 'actors', 'directors'));
    }
    
    public function update(Request $request, Series $series)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'release_year' => 'required|date|before_or_equal:' . date('Y-m-d'),
            'seasons' => 'required|integer|min:1',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'backdrop' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'video_url' => 'nullable|file|mimes:mp4,webm,mov|max:512000',
            'is_featured' => 'sometimes|boolean',
            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',
            'actors' => 'nullable|array',
            'actors.*' => 'exists:actors,id',
            'directors' => 'nullable|array',
            'directors.*' => 'exists:directors,id',
        ]);
        
        // Extract year from date
        $releaseYear = date('Y', strtotime($validated['release_year']));
        
        $updateData = [
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'],
            'release_year' => $releaseYear,
            'seasons' => $validated['seasons'],
            'is_featured' => $request->has('is_featured') && $request->is_featured == '1',
        ];
        
        // Handle file uploads - only update if new file is provided
        if ($request->hasFile('poster')) {
            // Delete old poster if exists
            if ($series->poster && Storage::disk('public')->exists($series->poster)) {
                Storage::disk('public')->delete($series->poster);
            }
            $updateData['poster'] = $request->file('poster')->store('series/posters', 'public');
        }
        
        if ($request->hasFile('backdrop')) {
            // Delete old backdrop if exists
            if ($series->backdrop && Storage::disk('public')->exists($series->backdrop)) {
                Storage::disk('public')->delete($series->backdrop);
            }
            $updateData['backdrop'] = $request->file('backdrop')->store('series/backdrops', 'public');
        }
        
        if ($request->hasFile('video_url')) {
            // Delete old video if exists
            if ($series->video_url && Storage::disk('public')->exists($series->video_url)) {
                Storage::disk('public')->delete($series->video_url);
            }
            $updateData['video_url'] = $request->file('video_url')->store('series/videos', 'public');
        }
        
        $series->update($updateData);
        
        if ($request->has('genres') && !empty($request->genres)) {
            $series->genres()->sync($request->genres);
        } else {
            $series->genres()->detach();
        }
        
        if ($request->has('actors') && !empty($request->actors)) {
            $series->actors()->sync($request->actors);
        } else {
            $series->actors()->detach();
        }
        
        if ($request->has('directors') && !empty($request->directors)) {
            $series->directors()->sync($request->directors);
        } else {
            $series->directors()->detach();
        }
        
        return redirect()->route('admin.series.index')->with('success', 'Series updated!');
    }
    
    public function destroy(Series $series)
    {
        $series->delete();
        return redirect()->route('admin.series.index')->with('success', 'Series deleted!');
    }
    
    public function episodes(Series $series)
    {
        $episodes = $series->episodes()->orderBy('season_number')->orderBy('episode_number')->get();
        return view('admin.series.episodes', compact('series', 'episodes'));
    }
    
    public function storeEpisode(Request $request, Series $series)
    {
        try {
            $validated = $request->validate([
                'season_number' => 'required|integer|min:1',
                'episode_number' => 'required|integer|min:1',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'duration' => 'required|integer|min:1',
                'video_url' => 'nullable|file|mimes:mp4,webm,mov|max:512000',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'air_date' => 'nullable|date',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreurs de validation',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
        
        // Handle file uploads
        $videoPath = null;
        if ($request->hasFile('video_url')) {
            $videoPath = $request->file('video_url')->store('series/episodes', 'public');
        }
        
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('series/episodes/thumbnails', 'public');
        }
        
        Episode::create([
            'series_id' => $series->id,
            'season_number' => $validated['season_number'],
            'episode_number' => $validated['episode_number'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'duration' => $validated['duration'],
            'video_url' => $videoPath,
            'thumbnail' => $thumbnailPath,
            'air_date' => $validated['air_date'] ?? null,
        ]);
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Episode created!']);
        }
        
        return back()->with('success', 'Episode created!');
    }
}
