<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function workTimes()
    {
        return $this->hasMany(WorkTime::class);
    }

    public function subtasks()
    {
        return $this->hasMany(Subtask::class)->orderBy('sort_order');
    }
}
