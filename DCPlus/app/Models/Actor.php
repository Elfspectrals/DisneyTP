<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Actor extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'bio',
        'birth_date',
        'photo',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movie_actor');
    }

    public function series(): BelongsToMany
    {
        return $this->belongsToMany(Series::class, 'series_actor');
    }

    /**
     * Get the photo URL (handles both file paths and external URLs)
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }
        
        // If it's already a full URL, return it
        if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
            return $this->photo;
        }
        
        // Otherwise, it's a storage path
        return Storage::url($this->photo);
    }
}
