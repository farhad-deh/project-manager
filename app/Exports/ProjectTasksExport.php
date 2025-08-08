<?php

namespace App\Exports;

use App\Models\Task;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProjectTasksExport implements FromCollection, WithHeadings
{
    protected $projectId;

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
            $startDates = [];
            $endDates = [];

            foreach ($task->workTimes as $wt) {
                $start = Carbon::parse($wt->start_time);
                $end = Carbon::parse($wt->end_time);

                $totalSeconds += $end->diffInSeconds($start);
                $startDates[] = $wt->work_date;
                $endDates[] = $wt->work_date;
            }

            $totalHours = floor($totalSeconds / 3600);
            $totalMinutes = floor(($totalSeconds % 3600) / 60);

            $startDate = count($startDates) ? min($startDates) : null;
            $endDate = count($endDates) ? max($endDates) : null;

            $rows->push([
                'Task Title' => $task->title,
                'Description' => $task->description,
                'Start Date' => $task->start_date,
                'Due Date' => $task->due_date,
                'Work Start Date' => $startDate,
                'Work End Date' => $endDate,
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
            'Work Start Date',
            'Work End Date',
            'Total Time (H:M)',
        ];
    }
}
