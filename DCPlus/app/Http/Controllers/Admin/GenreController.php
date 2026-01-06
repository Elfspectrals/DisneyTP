<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::latest()->paginate(20);
        return view('admin.genres.index', compact('genres'));
    }
    
    public function create()
    {
        return view('admin.genres.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:genres,name',
            'description' => 'nullable|string',
        ]);
        
        // Generate unique slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        while (Genre::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        Genre::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);
        
        return redirect()->route('admin.genres.index')->with('success', 'Genre créé avec succès!');
    }
    
    public function edit(Genre $genre)
    {
        return view('admin.genres.edit', compact('genre'));
    }
    
    public function update(Request $request, Genre $genre)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:genres,name,' . $genre->id,
            'description' => 'nullable|string',
        ]);
        
        // Generate unique slug if name changed
        $slug = $genre->slug;
        if ($validated['name'] !== $genre->name) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;
            while (Genre::where('slug', $slug)->where('id', '!=', $genre->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        }
        
        $genre->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);
        
        return redirect()->route('admin.genres.index')->with('success', 'Genre mis à jour avec succès!');
    }
    
    public function destroy(Genre $genre)
    {
        $genre->delete();
        return redirect()->route('admin.genres.index')->with('success', 'Genre supprimé avec succès!');
    }
}


