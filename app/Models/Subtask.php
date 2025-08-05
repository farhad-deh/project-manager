<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subtask extends Model
{
    protected $fillable = [
        'task_id',
        'description',
        'is_completed',
        'sort_order'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
