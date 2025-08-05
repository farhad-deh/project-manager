<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Subtask;

class SubtaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all tasks
        $tasks = Task::all();

        foreach ($tasks as $task) {
            // Create 2-4 subtasks for each task
            $subtaskCount = rand(2, 4);
            
            for ($i = 0; $i < $subtaskCount; $i++) {
                Subtask::create([
                    'task_id' => $task->id,
                    'description' => "Subtask " . ($i + 1) . " for " . $task->title,
                    'is_completed' => rand(0, 1),
                    'sort_order' => $i + 1,
                ]);
            }
        }
    }
}
