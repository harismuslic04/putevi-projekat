<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoadSegment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'installed_at' => 'date',
        'warranty_until' => 'date',
    ];

    public function issueReports()
    {
        return $this->hasMany(IssueReport::class);
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }
}
