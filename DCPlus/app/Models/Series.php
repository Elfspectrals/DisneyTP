<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

class Series extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'release_year',
        'seasons',
        'poster',
        'backdrop',
        'video_url',
        'rating',
        'views',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'decimal:1',
            'views' => 'integer',
            'seasons' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'series_genre');
    }

    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class, 'series_actor');
    }

    public function directors(): BelongsToMany
    {
        return $this->belongsToMany(Director::class, 'series_director');
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }

    public function watchlists(): MorphMany
    {
        return $this->morphMany(Watchlist::class, 'watchable');
    }

    public function watchHistory(): MorphMany
    {
        return $this->morphMany(WatchHistory::class, 'watchable');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    /**
     * Get the poster URL (handles both file paths and external URLs)
     */
    public function getPosterUrlAttribute(): ?string
    {
        if (!$this->poster) {
            return null;
        }
        
        // If it's already a full URL, return it
        if (filter_var($this->poster, FILTER_VALIDATE_URL)) {
            return $this->poster;
        }
        
        // Otherwise, it's a storage path
        return Storage::url($this->poster);
    }

    /**
     * Get the backdrop URL (handles both file paths and external URLs)
     */
    public function getBackdropUrlAttribute(): ?string
    {
        if (!$this->backdrop) {
            return null;
        }
        
        // If it's already a full URL, return it
        if (filter_var($this->backdrop, FILTER_VALIDATE_URL)) {
            return $this->backdrop;
        }
        
        // Otherwise, it's a storage path
        return Storage::url($this->backdrop);
    }

    /**
     * Get the video URL for display (handles both file paths and external URLs)
     */
    public function getVideoUrlDisplayAttribute(): ?string
    {
        $value = $this->attributes['video_url'] ?? null;
        
        if (!$value) {
            return null;
        }
        
        // If it's already a full URL, return it
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        
        // Otherwise, it's a storage path
        return Storage::url($value);
    }
}
