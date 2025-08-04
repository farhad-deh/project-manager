<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{

    protected $guarded = [];

    public function getCalculatedTotalCostAttribute()
    {
        if ($this->is_permanent && $this->hourly_rate) {
            $totalMinutes = $this->workTimes->sum(function ($wt) {
                return $wt->start_time && $wt->end_time
                    ? \Carbon\Carbon::parse($wt->start_time)->diffInMinutes(\Carbon\Carbon::parse($wt->end_time))
                    : 0;
            });

            return round(($totalMinutes / 60) * $this->hourly_rate);
        }

        return $this->total_cost;
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments->sum('amount');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function workTimes()
    {
        return $this->hasManyThrough(
            \App\Models\WorkTime::class,
            \App\Models\Task::class,
            'project_id', // Foreign key on Task
            'task_id',    // Foreign key on WorkTime
            'id',         // Local key on Project
            'id'          // Local key on Task
        );
    }

    public function payments()
    {
        return $this->hasMany(ProjectPayment::class);
    }
}
