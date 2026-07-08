<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $guarded = [];

    public function workOrders()
    {
        return $this->belongsToMany(WorkOrder::class)
            ->withPivot('quantity', 'cost')
            ->withTimestamps();
    }
}
