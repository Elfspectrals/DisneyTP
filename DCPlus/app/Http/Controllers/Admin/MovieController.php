<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Genre;
use App\Models\Actor;
use App\Models\Director;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'release_year' => 'required|integer|min:1900|max:' . date('Y'),
            'poster' => 'nullable|string|max:500',
            'backdrop' => 'nullable|string|max:500',
            'video_url' => 'nullable|string|max:500',
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
        
        $movie = Movie::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'],
            'duration' => $validated['duration'],
            'release_year' => $validated['release_year'],
            'poster' => $validated['poster'] ?? null,
            'backdrop' => $validated['backdrop'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
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
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|integer|min:1',
            'release_year' => 'required|integer|min:1900|max:' . date('Y'),
            'poster' => 'nullable|string',
            'backdrop' => 'nullable|string',
            'video_url' => 'nullable|string',
            'is_featured' => 'boolean',
            'genres' => 'array',
            'actors' => 'array',
            'directors' => 'array',
        ]);
        
        $movie->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'duration' => $request->duration,
            'release_year' => $request->release_year,
            'poster' => $request->poster,
            'backdrop' => $request->backdrop,
            'video_url' => $request->video_url,
            'is_featured' => $request->is_featured ?? false,
        ]);
        
        if ($request->genres) {
            $movie->genres()->sync($request->genres);
        }
        
        if ($request->actors) {
            $movie->actors()->sync($request->actors);
        }
        
        if ($request->directors) {
            $movie->directors()->sync($request->directors);
        }
        
        return redirect()->route('admin.movies.index')->with('success', 'Movie updated!');
    }
    
    public function destroy(Movie $movie)
    {
        $movie->delete();
        return redirect()->route('admin.movies.index')->with('success', 'Movie deleted!');
    }
}
