<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfrastructureAsset extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'properties_json' => 'array',
        'installed_at' => 'date',
        'warranty_until' => 'date',
    ];
}
