<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = [
        'name',
        'description',
        'metric',
        'dimensions',
        'aggregator',
        'threshold',
    ];

    protected $casts = [
        'dimensions' => 'array',
        'threshold' => 'integer',
    ];
}
