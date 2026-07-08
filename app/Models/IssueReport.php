<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssueReport extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function asset()
    {
        return $this->belongsTo(InfrastructureAsset::class, 'asset_id');
    }

    public function roadSegment()
    {
        return $this->belongsTo(RoadSegment::class);
    }

    protected $casts = [
        'reported_at' => 'datetime',
    ];

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function originalReport()
    {
        return $this->belongsTo(IssueReport::class, 'duplicate_of_id');
    }

    public function duplicates()
    {
        return $this->hasMany(IssueReport::class, 'duplicate_of_id');
    }
}
