<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrafficAccident extends Model
{
    protected $guarded = [];

    protected $casts = [
        'reported_at' => 'datetime',
    ];
}
