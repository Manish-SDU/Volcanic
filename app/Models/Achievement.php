<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Achievement extends Model
{
    protected $fillable = [
        'name',
        'description',
        'metric',
        'dimensions',
        'aggregator',
        'threshold',
        'image_path',
        'locked_image_path',
    ];

    protected $casts = [
        'dimensions' => 'array',
        'threshold' => 'integer',
    ];

    // Many-to-Many relationship between achievements and users
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
                    ->withTimestamps();
    }
}