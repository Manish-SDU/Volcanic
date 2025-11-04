<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Volcano extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'country',
        'continent',
        'activity',
        'latitude',
        'longitude',
        'elevation',
        'description',
        'type',
        'image_url'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'elevation' => 'integer',
    ];
    
    /**
     * Get the safe image URL attribute - directly from uploads folder.
     *
     * @return string
     */
    public function getSafeImageUrlAttribute()
    {
        // GitHub repository base URL
        $githubBaseUrl = 'https://raw.githubusercontent.com/Lara-Ghi/volcanic-images/main';

        // If the image_url field contains a URL, return it directly
        if (is_string($this->image_url) && filter_var($this->image_url, FILTER_VALIDATE_URL)) {
            return $this->image_url;
        }

        // If image_url exists, use it directly (now includes extension from database)
        if ($this->image_url) {
            return "{$githubBaseUrl}/{$this->image_url}";
        }

        // Fallback: return placeholder
        return asset('images/volcanoes/placeholder.png');
    }

    public function userVolcanoes(): HasMany
    {
        return $this->hasMany(UserVolcano::class, 'volcanoes_id');
    }

    public function isVisitedBy($user)
    {
        // If no user is logged in, return false
        if (!$user) {
            return false;
        }
        
        return $this->userVolcanoes()
            ->where('user_id', $user->id)
            ->where('status', 'visited')
            ->exists();
    }

    public function isWishlistedBy($user)
    {
        // If no user is logged in, return false
        if (!$user) {
            return false;
        }
        
        return $this->userVolcanoes()
            ->where('user_id', $user->id)
            ->where('status', 'wishlist')
            ->exists();
    }
}
