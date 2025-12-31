<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
}
