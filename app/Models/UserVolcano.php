<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserVolcano extends Model
{
    // The table associated with the model.
    
    protected $table = 'user_volcanoes';

    
    // The primary key associated with the table.
    
    protected $primaryKey = 'list_id';

    // The attributes that are mass assignable.
    protected $fillable = [
        'user_id',
        'volcanoes_id',
        'note',
        'status',
        'visited_at',
    ];

    // The attributes that should be cast.
    protected $casts = [
        'status' => 'string',
        'visited_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Get the user that owns this volcano list entry.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Get the volcano associated with this list entry.
    public function volcano(): BelongsTo
    {
        return $this->belongsTo(Volcano::class, 'volcanoes_id');
    }
}