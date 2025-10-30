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
        // If the image_url field contains a URL, return it directly
        if (is_string($this->image_url) && filter_var($this->image_url, FILTER_VALIDATE_URL)) {
            return $this->image_url;
        }
        
        // Look for image using the image_url field as filename
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        // First try using the image_url field directly
        if ($this->image_url) {
            foreach ($extensions as $ext) {
                $imagePath = "images/volcanoes/{$this->image_url}.{$ext}";
                if (file_exists(public_path($imagePath))) {
                    return asset($imagePath);
                }
            }
        }
        
        // Fallback: Look for any supported image format using volcano name
        $baseName = strtolower(str_replace(' ', '-', $this->name));
        foreach ($extensions as $ext) {
            $imagePath = "images/volcanoes/{$baseName}.{$ext}";
            if (file_exists(public_path($imagePath))) {
                return asset($imagePath);
            }
        }
        
        // If no image is found, return the placeholder
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
