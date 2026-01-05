<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Director;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DirectorController extends Controller
{
    public function index()
    {
        $directors = Director::latest()->paginate(20);
        return view('admin.directors.index', compact('directors'));
    }
    
    public function create()
    {
        return view('admin.directors.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'birth_date' => 'nullable|date|before:today',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);
        
        // Generate unique slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        while (Director::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('directors/photos', 'public');
        }
        
        Director::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'bio' => $validated['bio'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'photo' => $photoPath,
        ]);
        
        return redirect()->route('admin.directors.index')->with('success', 'Réalisateur créé avec succès!');
    }
    
    public function edit(Director $director)
    {
        return view('admin.directors.edit', compact('director'));
    }
    
    public function update(Request $request, Director $director)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'birth_date' => 'nullable|date|before:today',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);
        
        // Generate unique slug if name changed
        $slug = $director->slug;
        if ($validated['name'] !== $director->name) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;
            while (Director::where('slug', $slug)->where('id', '!=', $director->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        }
        
        $updateData = [
            'name' => $validated['name'],
            'slug' => $slug,
            'bio' => $validated['bio'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
        ];
        
        // Handle photo upload - only update if new file is provided
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($director->photo && Storage::disk('public')->exists($director->photo)) {
                Storage::disk('public')->delete($director->photo);
            }
            $updateData['photo'] = $request->file('photo')->store('directors/photos', 'public');
        }
        
        $director->update($updateData);
        
        return redirect()->route('admin.directors.index')->with('success', 'Réalisateur mis à jour avec succès!');
    }
    
    public function destroy(Director $director)
    {
        // Delete photo if exists
        if ($director->photo && Storage::disk('public')->exists($director->photo)) {
            Storage::disk('public')->delete($director->photo);
        }
        
        $director->delete();
        return redirect()->route('admin.directors.index')->with('success', 'Réalisateur supprimé avec succès!');
    }
}

