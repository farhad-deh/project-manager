<?php

namespace App\Http\Controllers;

use App\Exports\ProjectTasksExport;
use App\Models\Project;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProjectExportController extends Controller
{
    public function exportTasks(int $projectId)
    {
        $project = Project::findOrFail($projectId);
        $fileName = $project->title . '_tasks.xlsx';

        return Excel::download(new ProjectTasksExport($projectId), $fileName);
    }
}
