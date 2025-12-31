<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

class Episode extends Model
{
    protected $fillable = [
        'series_id',
        'season_number',
        'episode_number',
        'title',
        'description',
        'duration',
        'video_url',
        'thumbnail',
        'air_date',
    ];

    protected function casts(): array
    {
        return [
            'season_number' => 'integer',
            'episode_number' => 'integer',
            'duration' => 'integer',
            'air_date' => 'date',
        ];
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function watchHistory(): MorphMany
    {
        return $this->morphMany(WatchHistory::class, 'watchable');
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
