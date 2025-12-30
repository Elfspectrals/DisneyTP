<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Watchlist extends Model
{
    protected $fillable = [
        'profile_id',
        'watchable_type',
        'watchable_id',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function watchable(): MorphTo
    {
        return $this->morphTo();
    }
}
