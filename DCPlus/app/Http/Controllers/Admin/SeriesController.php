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
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'release_year' => 'required|integer|min:1900|max:' . date('Y'),
            'seasons' => 'required|integer|min:1',
            'poster' => 'nullable|string',
            'backdrop' => 'nullable|string',
            'is_featured' => 'boolean',
            'genres' => 'array',
            'actors' => 'array',
            'directors' => 'array',
        ]);
        
        $series = Series::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'release_year' => $request->release_year,
            'seasons' => $request->seasons,
            'poster' => $request->poster,
            'backdrop' => $request->backdrop,
            'is_featured' => $request->is_featured ?? false,
        ]);
        
        if ($request->genres) {
            $series->genres()->attach($request->genres);
        }
        
        if ($request->actors) {
            $series->actors()->attach($request->actors);
        }
        
        if ($request->directors) {
            $series->directors()->attach($request->directors);
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
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'release_year' => 'required|integer|min:1900|max:' . date('Y'),
            'seasons' => 'required|integer|min:1',
            'poster' => 'nullable|string',
            'backdrop' => 'nullable|string',
            'is_featured' => 'boolean',
            'genres' => 'array',
            'actors' => 'array',
            'directors' => 'array',
        ]);
        
        $series->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'release_year' => $request->release_year,
            'seasons' => $request->seasons,
            'poster' => $request->poster,
            'backdrop' => $request->backdrop,
            'is_featured' => $request->is_featured ?? false,
        ]);
        
        if ($request->genres) {
            $series->genres()->sync($request->genres);
        }
        
        if ($request->actors) {
            $series->actors()->sync($request->actors);
        }
        
        if ($request->directors) {
            $series->directors()->sync($request->directors);
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
        $request->validate([
            'season_number' => 'required|integer|min:1',
            'episode_number' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'video_url' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'air_date' => 'nullable|date',
        ]);
        
        Episode::create([
            'series_id' => $series->id,
            'season_number' => $request->season_number,
            'episode_number' => $request->episode_number,
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration,
            'video_url' => $request->video_url,
            'thumbnail' => $request->thumbnail,
            'air_date' => $request->air_date,
        ]);
        
        return back()->with('success', 'Episode created!');
    }
}
