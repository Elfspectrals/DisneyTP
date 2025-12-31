<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WatchHistory extends Model
{
    protected $table = 'watch_history';
    
    protected $fillable = [
        'profile_id',
        'watchable_type',
        'watchable_id',
        'progress',
        'completed',
        'last_watched_at',
    ];

    protected function casts(): array
    {
        return [
            'progress' => 'integer',
            'completed' => 'boolean',
            'last_watched_at' => 'datetime',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function watchable(): MorphTo
    {
        return $this->morphTo();
    }
}
