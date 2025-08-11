<?php

namespace App\Exports;

use App\Models\Task;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProjectTasksExport implements FromCollection, WithHeadings
{
    protected int $projectId;

    public function __construct(int $projectId)
    {
        $this->projectId = $projectId;
    }

    public function collection()
    {
        $tasks = Task::with(['workTimes'])
            ->where('project_id', $this->projectId)
            ->get();

        $rows = collect();

        foreach ($tasks as $task) {
            $totalSeconds = 0;

            foreach ($task->workTimes as $wt) {
                if (!$wt->start_time || !$wt->end_time) {
                    continue;
                }

                $start = Carbon::parse($wt->work_date . ' ' . $wt->start_time);
                $end = Carbon::parse($wt->work_date . ' ' . $wt->end_time);

                if ($end->lt($start)) {
                    $end->addDay();
                }

                $totalSeconds += abs($end->diffInSeconds($start));
            }

            $totalHours = floor($totalSeconds / 3600);
            $totalMinutes = floor(($totalSeconds % 3600) / 60);

            $rows->push([
                'Task Title' => $task->title,
                'Description' => $task->description,
                'Start Date' => $task->start_date,
                'Due Date' => $task->due_date,
                'Total Time (H:M)' => sprintf('%02d:%02d', $totalHours, $totalMinutes),
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Task Title',
            'Description',
            'Start Date',
            'Due Date',
            'Total Time (H:M)',
        ];
    }
}
