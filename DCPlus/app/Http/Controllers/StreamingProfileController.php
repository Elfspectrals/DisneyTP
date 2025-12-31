<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StreamingProfileController extends Controller
{
    public function index()
    {
        $profiles = Auth::user()->profiles;
        return view('profiles.index', compact('profiles'));
    }
    
    public function create()
    {
        return view('profiles.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|string',
            'is_kid' => 'boolean',
        ]);
        
        $user = Auth::user();
        
        // Limit to 5 profiles per user
        if ($user->profiles()->count() >= 5) {
            return back()->with('error', 'Maximum 5 profiles allowed per account.');
        }
        
        $profile = Profile::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'avatar' => $request->avatar ?? 'default',
            'is_kid' => $request->is_kid ?? false,
        ]);
        
        // Set as current profile if it's the first one
        if ($user->profiles()->count() === 1) {
            session(['current_profile_id' => $profile->id]);
        }
        
        return redirect()->route('profiles.index')->with('success', 'Profile created!');
    }
    
    public function edit(Profile $profile)
    {
        if ($profile->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('profiles.edit', compact('profile'));
    }
    
    public function update(Request $request, Profile $profile)
    {
        if ($profile->user_id !== Auth::id()) {
            abort(403);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|string',
            'is_kid' => 'boolean',
        ]);
        
        $profile->update($request->only(['name', 'avatar', 'is_kid']));
        
        return redirect()->route('profiles.index')->with('success', 'Profile updated!');
    }
    
    public function destroy(Profile $profile)
    {
        if ($profile->user_id !== Auth::id()) {
            abort(403);
        }
        
        $profile->delete();
        
        return redirect()->route('profiles.index')->with('success', 'Profile deleted!');
    }
    
    public function switch(Profile $profile)
    {
        if ($profile->user_id !== Auth::id()) {
            abort(403);
        }
        
        session(['current_profile_id' => $profile->id]);
        
        return redirect()->route('catalog')->with('success', 'Profile switched!');
    }
}
