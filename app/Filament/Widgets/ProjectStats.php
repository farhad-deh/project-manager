<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\ProjectPayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectStats extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'doing')->count();
        $completedProjects = Project::where('status', 'done')->count();
        $totalRevenue = ProjectPayment::sum('amount');

        return [
            Stat::make('کل پروژه‌ها', $totalProjects)
                ->color('info'),

            Stat::make('پروژه‌های در حال انجام', $activeProjects)
                ->color('warning'),

            Stat::make('پروژه‌های تکمیل‌شده', $completedProjects)
                ->color('success'),

            Stat::make('مجموع دریافتی', number_format($totalRevenue, 0, '.', ',') . ' IRR')
                ->color('primary'),
        ];
    }
}
