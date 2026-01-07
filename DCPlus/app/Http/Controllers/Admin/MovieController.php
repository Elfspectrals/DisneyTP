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
        // Check if POST data size exceeds PHP limits (file might be missing)
        $postMaxSize = $this->parseSize(ini_get('post_max_size'));
        $contentLength = $request->server('CONTENT_LENGTH');
        if ($contentLength && $contentLength > $postMaxSize) {
            $errorMessage = 'The total request size exceeds the post_max_size directive in php.ini (' . ini_get('post_max_size') . ')';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $errorMessage,
                    'errors' => ['video_url' => [$errorMessage]]
                ], 422);
            }
            return back()->withErrors(['video_url' => $errorMessage])->withInput();
        }
        
        // Check for PHP upload errors before validation
        if ($request->hasFile('video_url')) {
            $videoFile = $request->file('video_url');
            if ($videoFile->getError() !== UPLOAD_ERR_OK) {
                $errorMessage = $this->getUploadErrorMessage($videoFile->getError());
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'message' => $errorMessage,
                        'errors' => ['video_url' => [$errorMessage]]
                    ], 422);
                }
                return back()->withErrors(['video_url' => $errorMessage])->withInput();
            }
        } elseif ($request->has('video_url') && $request->input('video_url') === null) {
            // File field was present but file is missing (likely due to size limit)
            $errorMessage = 'The video file may be too large. Maximum allowed size: ' . ini_get('upload_max_filesize') . ' (upload_max_filesize) or ' . ini_get('post_max_size') . ' (post_max_size)';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $errorMessage,
                    'errors' => ['video_url' => [$errorMessage]]
                ], 422);
            }
            return back()->withErrors(['video_url' => $errorMessage])->withInput();
        }
        
        try {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Check if the error is related to file upload failure
            $errors = $e->errors();
            if (isset($errors['video_url'])) {
                foreach ($errors['video_url'] as $key => $error) {
                    if (str_contains($error, 'failed to upload') || str_contains($error, 'upload')) {
                        // Provide more helpful error message
                        $uploadMaxSize = ini_get('upload_max_filesize');
                        $postMaxSize = ini_get('post_max_size');
                        $errors['video_url'][$key] = "Video upload failed. This may be due to file size limits. Maximum file size: {$uploadMaxSize} (upload_max_filesize) or {$postMaxSize} (post_max_size). Please check your PHP configuration or reduce the file size.";
                    }
                }
            }
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $errors
                ], 422);
            }
            throw $e;
        }
        
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
            try {
                $posterPath = $request->file('poster')->store('movies/posters', 'public');
            } catch (\Exception $e) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'message' => 'Poster upload failed',
                        'errors' => ['poster' => ['Failed to upload poster: ' . $e->getMessage()]]
                    ], 422);
                }
                return back()->withErrors(['poster' => 'Failed to upload poster'])->withInput();
            }
        }
        
        $backdropPath = null;
        if ($request->hasFile('backdrop')) {
            try {
                $backdropPath = $request->file('backdrop')->store('movies/backdrops', 'public');
            } catch (\Exception $e) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'message' => 'Backdrop upload failed',
                        'errors' => ['backdrop' => ['Failed to upload backdrop: ' . $e->getMessage()]]
                    ], 422);
                }
                return back()->withErrors(['backdrop' => 'Failed to upload backdrop'])->withInput();
            }
        }
        
        $videoPath = null;
        if ($request->hasFile('video_url')) {
            try {
                $videoPath = $request->file('video_url')->store('movies/videos', 'public');
            } catch (\Exception $e) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'message' => 'Video upload failed',
                        'errors' => ['video_url' => ['Failed to upload video: ' . $e->getMessage()]]
                    ], 422);
                }
                return back()->withErrors(['video_url' => 'Failed to upload video'])->withInput();
            }
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
    
    /**
     * Get human-readable error message for PHP upload errors
     */
    private function getUploadErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The video file exceeds the upload_max_filesize directive in php.ini (' . ini_get('upload_max_filesize') . ')';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The video file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The video file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No video file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write video file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the video file upload';
            default:
                return 'Unknown upload error occurred';
        }
    }
    
    /**
     * Parse PHP size string (e.g., "128M") to bytes
     */
    private function parseSize($size)
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        return round($size);
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
