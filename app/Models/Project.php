<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{

    protected $guarded = [];

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
