<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

Route::get('/projects/{project}/export-tasks', [\App\Http\Controllers\ProjectExportController::class, 'exportTasks'])
    ->name('projects.exportTasks');
