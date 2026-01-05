<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Genre;
use App\Models\Actor;
use App\Models\Director;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::with(['genres', 'actors', 'directors'])->latest()->paginate(20);
        return view('admin.movies.index', compact('movies'));
    }
    
    public function create()
    {
        $genres = Genre::all();
        $actors = Actor::all();
        $directors = Director::all();
        return view('admin.movies.create', compact('genres', 'actors', 'directors'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|integer|min:1',
            'release_year' => 'required|date|before_or_equal:' . date('Y-m-d'),
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
        while (Movie::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        // Extract year from date
        $releaseYear = date('Y', strtotime($validated['release_year']));
        
        // Handle file uploads
        $posterPath = null;
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('movies/posters', 'public');
        }
        
        $backdropPath = null;
        if ($request->hasFile('backdrop')) {
            $backdropPath = $request->file('backdrop')->store('movies/backdrops', 'public');
        }
        
        $videoPath = null;
        if ($request->hasFile('video_url')) {
            $videoPath = $request->file('video_url')->store('movies/videos', 'public');
        }
        
        $movie = Movie::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'],
            'duration' => $validated['duration'],
            'release_year' => $releaseYear,
            'poster' => $posterPath,
            'backdrop' => $backdropPath,
            'video_url' => $videoPath,
            'is_featured' => $request->has('is_featured') && $request->is_featured == '1',
        ]);
        
        if ($request->has('genres') && !empty($request->genres)) {
            $movie->genres()->attach($request->genres);
        }
        
        if ($request->has('actors') && !empty($request->actors)) {
            $movie->actors()->attach($request->actors);
        }
        
        if ($request->has('directors') && !empty($request->directors)) {
            $movie->directors()->attach($request->directors);
        }
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Movie created!']);
        }
        
        return redirect()->route('admin.movies.index')->with('success', 'Movie created!');
    }
    
    public function show(Movie $movie)
    {
        $movie->load(['genres', 'actors', 'directors']);
        return view('admin.movies.show', compact('movie'));
    }
    
    public function edit(Movie $movie)
    {
        $genres = Genre::all();
        $actors = Actor::all();
        $directors = Director::all();
        $movie->load(['genres', 'actors', 'directors']);
        return view('admin.movies.edit', compact('movie', 'genres', 'actors', 'directors'));
    }
    
    public function update(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|integer|min:1',
            'release_year' => 'required|date|before_or_equal:' . date('Y-m-d'),
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
            'duration' => $validated['duration'],
            'release_year' => $releaseYear,
            'is_featured' => $request->has('is_featured') && $request->is_featured == '1',
        ];
        
        // Handle file uploads - only update if new file is provided
        if ($request->hasFile('poster')) {
            // Delete old poster if exists
            if ($movie->poster && Storage::disk('public')->exists($movie->poster)) {
                Storage::disk('public')->delete($movie->poster);
            }
            $updateData['poster'] = $request->file('poster')->store('movies/posters', 'public');
        }
        
        if ($request->hasFile('backdrop')) {
            // Delete old backdrop if exists
            if ($movie->backdrop && Storage::disk('public')->exists($movie->backdrop)) {
                Storage::disk('public')->delete($movie->backdrop);
            }
            $updateData['backdrop'] = $request->file('backdrop')->store('movies/backdrops', 'public');
        }
        
        if ($request->hasFile('video_url')) {
            // Delete old video if exists
            if ($movie->video_url && Storage::disk('public')->exists($movie->video_url)) {
                Storage::disk('public')->delete($movie->video_url);
            }
            $updateData['video_url'] = $request->file('video_url')->store('movies/videos', 'public');
        }
        
        $movie->update($updateData);
        
        // Sync genres, actors, and directors (use empty array if not provided to allow removal)
        $movie->genres()->sync($request->genres ?? []);
        $movie->actors()->sync($request->actors ?? []);
        $movie->directors()->sync($request->directors ?? []);
        
        return redirect()->route('admin.movies.index')->with('success', 'Movie updated!');
    }
    
    public function destroy(Movie $movie)
    {
        $movie->delete();
        return redirect()->route('admin.movies.index')->with('success', 'Movie deleted!');
    }
}
