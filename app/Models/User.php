<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'surname',
        'username',
        'date_of_birth',
        'where_from',
        'bio',
        'password',
        'is_admin', // Added the admin user
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function userVolcanoes(): HasMany
    {
        return $this->hasMany(UserVolcano::class);
    }
    
    public function volcanoes()
    {
        return $this->belongsToMany(
            Volcano::class,
            'user_volcanoes',
            'user_id',
            'volcanoes_id'
        )->withPivot(['status', 'visited_at'])->withTimestamps();
    }

    //Many-to-Many relationship between achievements and users
    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class)
                    ->withTimestamps();
    }
}
