<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Actor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ActorController extends Controller
{
    public function index()
    {
        $actors = Actor::latest()->paginate(20);
        return view('admin.actors.index', compact('actors'));
    }
    
    public function create()
    {
        return view('admin.actors.create');
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
        while (Actor::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('actors/photos', 'public');
        }
        
        Actor::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'bio' => $validated['bio'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'photo' => $photoPath,
        ]);
        
        return redirect()->route('admin.actors.index')->with('success', 'Acteur créé avec succès!');
    }
    
    public function edit(Actor $actor)
    {
        return view('admin.actors.edit', compact('actor'));
    }
    
    public function update(Request $request, Actor $actor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'birth_date' => 'nullable|date|before:today',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);
        
        // Generate unique slug if name changed
        $slug = $actor->slug;
        if ($validated['name'] !== $actor->name) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;
            while (Actor::where('slug', $slug)->where('id', '!=', $actor->id)->exists()) {
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
            if ($actor->photo && Storage::disk('public')->exists($actor->photo)) {
                Storage::disk('public')->delete($actor->photo);
            }
            $updateData['photo'] = $request->file('photo')->store('actors/photos', 'public');
        }
        
        $actor->update($updateData);
        
        return redirect()->route('admin.actors.index')->with('success', 'Acteur mis à jour avec succès!');
    }
    
    public function destroy(Actor $actor)
    {
        // Delete photo if exists
        if ($actor->photo && Storage::disk('public')->exists($actor->photo)) {
            Storage::disk('public')->delete($actor->photo);
        }
        
        $actor->delete();
        return redirect()->route('admin.actors.index')->with('success', 'Acteur supprimé avec succès!');
    }
}

