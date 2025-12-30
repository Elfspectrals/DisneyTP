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
        
        $movie = Movie::create([
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
            $movie->genres()->attach($request->genres);
        }
        
        if ($request->actors) {
            $movie->actors()->attach($request->actors);
        }
        
        if ($request->directors) {
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
