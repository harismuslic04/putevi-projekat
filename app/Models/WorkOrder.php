<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    protected $guarded = [];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function issueReport()
    {
        return $this->belongsTo(IssueReport::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function roadSegment()
    {
        return $this->belongsTo(RoadSegment::class);
    }

    public function resources()
    {
        return $this->belongsToMany(Resource::class)
            ->withPivot('quantity', 'cost')
            ->withTimestamps();
    }
}
